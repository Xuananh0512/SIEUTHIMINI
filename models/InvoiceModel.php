<?php
class InvoiceModel extends Database {
    
    // 1. Hàm hỗ trợ tạo câu WHERE động
private function buildWhere($dateFrom, $dateTo, $minTotal, $maxTotal) {
    $conditions = [];
    $types = "";
    $params = [];

    // Lọc theo NGÀY TẠO (Từ ngày)
    if (!empty($dateFrom)) {
        $conditions[] = "hd.ngayTao >= ?";
        $types .= "s";
        $params[] = $dateFrom . " 00:00:00"; // Bắt đầu từ đầu ngày
    }

    // Lọc theo NGÀY TẠO (Đến ngày)
    if (!empty($dateTo)) {
        $conditions[] = "hd.ngayTao <= ?";
        $types .= "s";
        $params[] = $dateTo . " 23:59:59"; // Kết thúc vào cuối ngày
    }

    // Lọc theo TỔNG TIỀN (Tối thiểu)
    if (!empty($minTotal) || $minTotal === '0') {
        $conditions[] = "hd.tongTien >= ?";
        $types .= "d"; // double/decimal
        $params[] = $minTotal;
    }

    // Lọc theo TỔNG TIỀN (Tối đa)
    if (!empty($maxTotal)) {
        $conditions[] = "hd.tongTien <= ?";
        $types .= "d";
        $params[] = $maxTotal;
    }

    $whereSql = "";
    if (count($conditions) > 0) {
        $whereSql = " WHERE " . implode(" AND ", $conditions);
    }

    return [$whereSql, $types, $params];
}

// 2. Cập nhật hàm countAll
public function countAll($dateFrom = null, $dateTo = null, $minTotal = null, $maxTotal = null)
{
    list($whereSql, $types, $params) = $this->buildWhere($dateFrom, $dateTo, $minTotal, $maxTotal);
    
    $sql = "SELECT COUNT(*) FROM hoadon hd" . $whereSql;
    $stmt = $this->conn->prepare($sql);
    
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result()->fetch_row();
    return $result[0] ?? 0;
}

// 3. Cập nhật hàm getPaginated
public function getPaginated($limit, $offset, $dateFrom = null, $dateTo = null, $minTotal = null, $maxTotal = null)
{
    list($whereSql, $types, $params) = $this->buildWhere($dateFrom, $dateTo, $minTotal, $maxTotal);

    $sql = "SELECT hd.*, nv.hoTenNV, kh.hoTenKH 
            FROM hoadon hd 
            LEFT JOIN nhanvien nv ON hd.maNV = nv.maNV 
            LEFT JOIN khachhang kh ON hd.maKH = kh.maKH 
            $whereSql
            ORDER BY hd.ngayTao DESC
            LIMIT ? OFFSET ?";
    
    $stmt = $this->conn->prepare($sql);
    
    // Thêm tham số limit, offset vào cuối
    $types .= "ii"; 
    $params[] = $limit;
    $params[] = $offset;

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

    public function getAll() {
        $sql = "SELECT hd.*, nv.hoTenNV, kh.hoTenKH 
                FROM hoadon hd 
                LEFT JOIN nhanvien nv ON hd.maNV = nv.maNV 
                LEFT JOIN khachhang kh ON hd.maKH = kh.maKH 
                ORDER BY hd.ngayTao DESC";
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // ✅ SỬA LỖI: Hàm ADD trả về ID hóa đơn vừa tạo
    public function add($maNV, $maKH, $ngayTao, $tongTien, $tienKhachDua, $tienThoi) {
        $sql = "INSERT INTO hoadon (maNV, maKH, ngayTao, tongTien, tienKhachDua, tienThoi) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisddd", $maNV, $maKH, $ngayTao, $tongTien, $tienKhachDua, $tienThoi);
        
        $success = $stmt->execute();
        
        if ($success) {
            return $this->conn->insert_id; // TRẢ VỀ ID VỪA TẠO
        }
        return false;
    }

    public function getById($id) {
        $sql = "SELECT hd.*, nv.hoTenNV, kh.hoTenKH 
                FROM hoadon hd 
                LEFT JOIN nhanvien nv ON hd.maNV = nv.maNV 
                LEFT JOIN khachhang kh ON hd.maKH = kh.maKH 
                WHERE hd.maHD=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function update($maHD, $maNV, $maKH, $ngayTao, $tongTien, $tienKhachDua, $tienThoi) {
        $sql = "UPDATE hoadon SET maNV=?, maKH=?, ngayTao=?, tongTien=?, tienKhachDua=?, tienThoi=? WHERE maHD=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisdddi", $maNV, $maKH, $ngayTao, $tongTien, $tienKhachDua, $tienThoi, $maHD);
        return $stmt->execute();
    }

    public function delete($maHD) {
        // Hàm này thực hiện ẩn hóa đơn (set trangThai=0)
        $sql = "UPDATE hoadon SET trangThai=0 WHERE maHD=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maHD);
        return $stmt->execute();
    }
    
    // =======================================================
    // ** BỔ SUNG: HÀM RESTORE (Khôi phục hóa đơn) **
    // =======================================================
    public function restore($maHD) {
        $sql = "UPDATE hoadon SET trangThai=1 WHERE maHD=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maHD);
        return $stmt->execute();
    }
    
    // --- HÀM HỖ TRỢ TỒN KHO (Cần cho việc trừ/cộng tồn kho trong Service) ---
    public function getExistingStock($maSP) {
        $sql = "SELECT soLuongTon FROM sanpham WHERE maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maSP);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['soLuongTon'] ?? 0;
    }
    
    public function updateStock($maSP, $newStock) {
        $sql = "UPDATE sanpham SET soLuongTon = ? WHERE maSP = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $newStock, $maSP);
        return $stmt->execute();
    }

    public function search($keyword) {
        $sql = "SELECT hd.*, nv.hoTenNV, kh.hoTenKH 
                FROM hoadon hd 
                LEFT JOIN nhanvien nv ON hd.maNV = nv.maNV 
                LEFT JOIN khachhang kh ON hd.maKH = kh.maKH 
                WHERE hd.maHD LIKE ? OR kh.hoTenKH LIKE ?";
        $keyword = "%$keyword%";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}
?>