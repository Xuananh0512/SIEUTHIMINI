<?php
class ProductModel extends Database
{   
    

    // 1. Hàm buildWhere nhận thêm tham số minPrice, maxPrice
private function buildWhere($keyword, $minPrice, $maxPrice) {
    $conditions = [];
    $types = "";
    $params = [];

    // Lọc theo tên
    if (!empty($keyword)) {
        $conditions[] = "sp.tenSP LIKE ?";
        $types .= "s";
        $params[] = "%$keyword%";
    }

    // Lọc giá TỐI THIỂU (>=)
    if (!empty($minPrice) || $minPrice === '0') {
        $conditions[] = "sp.donGiaBan >= ?";
        $types .= "d";
        $params[] = $minPrice;
    }

    // Lọc giá TỐI ĐA (<=)
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

// 2. Cập nhật countAll
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

// 3. Cập nhật getPaginated
public function getPaginated($limit, $offset, $keyword = null, $minPrice = null, $maxPrice = null)
{
    list($whereSql, $types, $params) = $this->buildWhere($keyword, $minPrice, $maxPrice);

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
    if ($result) return $result->fetch_all(MYSQLI_ASSOC);
    return [];
}

    public function getAll()
    {
        $sql = "SELECT sp.*, dm.tenDM, ncc.tenNCC 
                FROM sanpham sp 
                LEFT JOIN danhmuc dm ON sp.maDM = dm.maDM 
                LEFT JOIN nhacungcap ncc ON sp.maNCC = ncc.maNCC
                ORDER BY sp.maSP DESC";

        $result = $this->conn->query($sql);

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // =======================================================
    // ** THÊM: HÀM LẤY DANH SÁCH ĐƠN VỊ TÍNH DUY NHẤT **
    // =======================================================
    public function getDistinctUnits()
    {
        $sql = "SELECT DISTINCT donViTinh FROM sanpham WHERE donViTinh IS NOT NULL AND donViTinh != '' ORDER BY donViTinh ASC";
        $result = $this->conn->query($sql);

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM sanpham WHERE maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function add($maDM, $maNCC, $tenSP, $donGiaBan, $soLuongTon, $donViTinh, $hanSuDung, $moTa)
    {
        $sql = "INSERT INTO sanpham (maDM, maNCC, tenSP, donGiaBan, soLuongTon, donViTinh, hanSuDung, moTa) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisdisss", $maDM, $maNCC, $tenSP, $donGiaBan, $soLuongTon, $donViTinh, $hanSuDung, $moTa);
        return $stmt->execute();
    }

    public function update($maSP, $maDM, $maNCC, $tenSP, $donGiaBan, $soLuongTon, $donViTinh, $hanSuDung, $moTa)
    {
        $sql = "UPDATE sanpham SET maDM=?, maNCC=?, tenSP=?, donGiaBan=?, soLuongTon=?, donViTinh=?, hanSuDung=?, moTa=? WHERE maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisdisssi", $maDM, $maNCC, $tenSP, $donGiaBan, $soLuongTon, $donViTinh, $hanSuDung, $moTa, $maSP);
        return $stmt->execute();
    }

    public function delete($maSP)
    {
        $sql = "DELETE FROM sanpham WHERE maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maSP);
        return $stmt->execute();
    }

    // =======================================================
    // ** THÊM: HÀM ĐẾM SẢN PHẨM THEO MÃ DANH MỤC **
    // =======================================================
    public function countByCategoryId($maDM)
    {
        $sql = "SELECT COUNT(*) FROM sanpham WHERE maDM = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDM); // 'i' cho maDM (integer)
        $stmt->execute();

        $result = $stmt->get_result()->fetch_row();
        return $result[0] ?? 0;
    }
    public function search($keyword)
    {
        $sql = "SELECT sp.*, dm.tenDM, ncc.tenNCC 
                FROM sanpham sp 
                LEFT JOIN danhmuc dm ON sp.maDM = dm.maDM 
                LEFT JOIN nhacungcap ncc ON sp.maNCC = ncc.maNCC
                WHERE sp.tenSP LIKE ?";
        $keyword = "%$keyword%";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) return $result->fetch_all(MYSQLI_ASSOC);
        return [];
    }
}
