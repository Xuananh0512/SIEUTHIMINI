<?php
class InvoiceModel extends Database {
    
    // ---------------------------------------------------------
    // PHẦN 1: CÁC HÀM LỌC & TÌM KIẾM (QUAN TRỌNG)
    // ---------------------------------------------------------

    // HÀM FIX LỖI CỦA BẠN: Tìm kiếm nâng cao theo mảng bộ lọc
    public function searchAdvanced($f) {
        $sql = "SELECT hd.*, nv.hoTenNV, kh.hoTenKH 
                FROM hoadon hd 
                LEFT JOIN nhanvien nv ON hd.maNV = nv.maNV 
                LEFT JOIN khachhang kh ON hd.maKH = kh.maKH 
                WHERE 1=1"; 
        
        $params = [];
        $types = "";

        // 1. Lọc theo Ngày
        if (!empty($f['date_from'])) {
            $sql .= " AND DATE(hd.ngayTao) >= ?";
            $types .= "s"; $params[] = $f['date_from'];
        }
        if (!empty($f['date_to'])) {
            $sql .= " AND DATE(hd.ngayTao) <= ?";
            $types .= "s"; $params[] = $f['date_to'];
        }

        // 2. Lọc theo Tiền
        if (!empty($f['price_min']) || $f['price_min'] === '0') {
            $sql .= " AND hd.tongTien >= ?";
            $types .= "d"; $params[] = $f['price_min'];
        }
        if (!empty($f['price_max'])) {
            $sql .= " AND hd.tongTien <= ?";
            $types .= "d"; $params[] = $f['price_max'];
        }

        // 3. Lọc theo Tên Khách
        if (!empty($f['customer_name'])) {
            $sql .= " AND kh.hoTenKH LIKE ?";
            $types .= "s"; $params[] = "%" . $f['customer_name'] . "%";
        }

        // 4. Lọc theo Tên Nhân Viên
        if (!empty($f['employee_name'])) {
            $sql .= " AND nv.hoTenNV LIKE ?";
            $types .= "s"; $params[] = "%" . $f['employee_name'] . "%";
        }

        $sql .= " ORDER BY hd.ngayTao DESC";

        // Thực thi
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result) return $result->fetch_all(MYSQLI_ASSOC);
        return [];
    }

    // HÀM HỖ TRỢ: Tạo câu WHERE cho phân trang (Tránh lặp code)
    private function buildWhere($dateFrom, $dateTo, $minTotal, $maxTotal) {
        $conditions = [];
        $types = "";
        $params = [];

        if (!empty($dateFrom)) {
            $conditions[] = "hd.ngayTao >= ?"; $types .= "s"; $params[] = $dateFrom . " 00:00:00";
        }
        if (!empty($dateTo)) {
            $conditions[] = "hd.ngayTao <= ?"; $types .= "s"; $params[] = $dateTo . " 23:59:59";
        }
        if (!empty($minTotal) || $minTotal === '0') {
            $conditions[] = "hd.tongTien >= ?"; $types .= "d"; $params[] = $minTotal;
        }
        if (!empty($maxTotal)) {
            $conditions[] = "hd.tongTien <= ?"; $types .= "d"; $params[] = $maxTotal;
        }

        $whereSql = "";
        if (count($conditions) > 0) {
            $whereSql = " WHERE " . implode(" AND ", $conditions);
        }
        return [$whereSql, $types, $params];
    }

    // ĐẾM TỔNG SỐ BẢN GHI (Có lọc)
    public function countAll($dateFrom = null, $dateTo = null, $minTotal = null, $maxTotal = null) {
        list($whereSql, $types, $params) = $this->buildWhere($dateFrom, $dateTo, $minTotal, $maxTotal);
        $sql = "SELECT COUNT(*) FROM hoadon hd" . $whereSql;
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($types)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_row();
        return $result[0] ?? 0;
    }

    // LẤY DỮ LIỆU PHÂN TRANG (Có lọc)
    public function getPaginated($limit, $offset, $d1, $d2, $m1, $m2) {
        list($whereSql, $types, $params) = $this->buildWhere($d1, $d2, $m1, $m2);
        $sql = "SELECT hd.*, nv.hoTenNV, kh.hoTenKH 
                FROM hoadon hd 
                LEFT JOIN nhanvien nv ON hd.maNV = nv.maNV 
                LEFT JOIN khachhang kh ON hd.maKH = kh.maKH 
                $whereSql
                ORDER BY hd.ngayTao DESC LIMIT ? OFFSET ?";
        
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

    // ---------------------------------------------------------
    // PHẦN 2: CÁC HÀM CRUD CƠ BẢN (THÊM, SỬA, XÓA, KHÔI PHỤC)
    // ---------------------------------------------------------

    public function getAll() {
        $sql = "SELECT hd.*, nv.hoTenNV, kh.hoTenKH FROM hoadon hd LEFT JOIN nhanvien nv ON hd.maNV = nv.maNV LEFT JOIN khachhang kh ON hd.maKH = kh.maKH ORDER BY hd.ngayTao DESC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($id) {
        $sql = "SELECT hd.*, nv.hoTenNV, kh.hoTenKH FROM hoadon hd LEFT JOIN nhanvien nv ON hd.maNV = nv.maNV LEFT JOIN khachhang kh ON hd.maKH = kh.maKH WHERE hd.maHD=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function add($maNV, $maKH, $ngayTao, $tongTien, $tienKhachDua, $tienThoi) {
        $sql = "INSERT INTO hoadon (maNV, maKH, ngayTao, tongTien, tienKhachDua, tienThoi) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisddd", $maNV, $maKH, $ngayTao, $tongTien, $tienKhachDua, $tienThoi);
        return $stmt->execute();
    }
    
    // Xóa mềm (Ẩn)
    public function delete($id) {
        $sql = "UPDATE hoadon SET trangThai=0 WHERE maHD=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Khôi phục
    public function restore($id) {
        $sql = "UPDATE hoadon SET trangThai=1 WHERE maHD=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function update($maHD, $maNV, $maKH, $ngayTao, $tongTien, $tienKhachDua, $tienThoi) {
        $sql = "UPDATE hoadon SET maNV=?, maKH=?, ngayTao=?, tongTien=?, tienKhachDua=?, tienThoi=? WHERE maHD=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisdddi", $maNV, $maKH, $ngayTao, $tongTien, $tienKhachDua, $tienThoi, $maHD);
        return $stmt->execute();
    }

    // ---------------------------------------------------------
    // PHẦN 3: HÀM HỖ TRỢ TỒN KHO (QUAN TRỌNG KHI XÓA/KHÔI PHỤC)
    // ---------------------------------------------------------
    
    public function getExistingStock($maSP) {
        $sql = "SELECT soLuongTon FROM sanpham WHERE maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maSP);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ? $res['soLuongTon'] : 0;
    }
    
    public function updateStock($maSP, $sl) {
        $sql = "UPDATE sanpham SET soLuongTon=? WHERE maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $sl, $maSP);
        return $stmt->execute();
    }

    // Hàm search cũ (để tránh lỗi nếu còn chỗ nào gọi)
    public function search($k){ return []; }
}
?>