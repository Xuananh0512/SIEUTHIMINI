<?php
class Productmodel extends Database
{   
    // 1. Xây dựng câu điều kiện tìm kiếm và lọc
    private function buildWhere($keyword, $minPrice, $maxPrice) {
        $conditions = [];
        $types = "";
        $params = [];

        if (!empty($keyword)) {
            $conditions[] = "sp.tenSP LIKE ?";
            $types .= "s";
            $params[] = "%$keyword%";
        }
        if (!empty($minPrice) || $minPrice === '0') {
            $conditions[] = "sp.donGiaBan >= ?";
            $types .= "d";
            $params[] = $minPrice;
        }
        if (!empty($maxPrice)) {
            $conditions[] = "sp.donGiaBan <= ?";
            $types .= "d";
            $params[] = $maxPrice;
        }

        $whereSql = "";
        if (count($conditions) > 0) {
            $whereSql = " WHERE " . implode(" AND ", $conditions);
        }
        return [$whereSql, $types, $params];
    }

    public function countAll($keyword = null, $minPrice = null, $maxPrice = null)
    {
        list($whereSql, $types, $params) = $this->buildWhere($keyword, $minPrice, $maxPrice);
        $sql = "SELECT COUNT(*) FROM sanpham sp" . $whereSql;
        $stmt = $this->conn->prepare($sql);
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_row();
        return $result[0] ?? 0;
    }

    public function getPaginated($limit, $offset, $keyword = null, $minPrice = null, $maxPrice = null)
    {
        list($whereSql, $types, $params) = $this->buildWhere($keyword, $minPrice, $maxPrice);
        
        // Lấy cả cột trangThai
        $sql = "SELECT sp.*, dm.tenDM, ncc.tenNCC 
                FROM sanpham sp
                LEFT JOIN danhmuc dm ON sp.maDM = dm.maDM
                LEFT JOIN nhacungcap ncc ON sp.maNCC = ncc.maNCC
                $whereSql
                ORDER BY sp.maSP DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        $types .= "ii"; 
        $params[] = $limit;
        $params[] = $offset;
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAll() { 
        $sql = "SELECT sp.*, dm.tenDM, ncc.tenNCC FROM sanpham sp LEFT JOIN danhmuc dm ON sp.maDM = dm.maDM LEFT JOIN nhacungcap ncc ON sp.maNCC = ncc.maNCC ORDER BY sp.maSP DESC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getDistinctUnits() { 
        $sql = "SELECT DISTINCT donViTinh FROM sanpham WHERE donViTinh IS NOT NULL AND donViTinh != '' ORDER BY donViTinh ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($id) { 
        $sql = "SELECT * FROM sanpham WHERE maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function add($maDM, $maNCC, $tenSP, $donGiaBan, $soLuongTon, $donViTinh, $hanSuDung, $moTa) { 
        $sql = "INSERT INTO sanpham (maDM, maNCC, tenSP, donGiaBan, soLuongTon, donViTinh, hanSuDung, moTa, trangThai) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisdisss", $maDM, $maNCC, $tenSP, $donGiaBan, $soLuongTon, $donViTinh, $hanSuDung, $moTa);
        return $stmt->execute();
    }

    public function update($maSP, $maDM, $maNCC, $tenSP, $donGiaBan, $soLuongTon, $donViTinh, $hanSuDung, $moTa) { 
        $sql = "UPDATE sanpham SET maDM=?, maNCC=?, tenSP=?, donGiaBan=?, soLuongTon=?, donViTinh=?, hanSuDung=?, moTa=? WHERE maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisdisssi", $maDM, $maNCC, $tenSP, $donGiaBan, $soLuongTon, $donViTinh, $hanSuDung, $moTa, $maSP);
        return $stmt->execute();
    }

    // Hàm ẨN sản phẩm
    public function delete($maSP) {
        $sql = "UPDATE sanpham SET trangThai = 0 WHERE maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maSP);
        return $stmt->execute();
    }

    // Hàm HIỆN sản phẩm
    public function restore($maSP) {
        $sql = "UPDATE sanpham SET trangThai = 1 WHERE maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maSP);
        return $stmt->execute();
    }
    
    public function search($keyword) {
        // Logic search đơn giản nếu cần dùng
        $sql = "SELECT * FROM sanpham WHERE tenSP LIKE ?";
        $keyword = "%$keyword%";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $keyword);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>