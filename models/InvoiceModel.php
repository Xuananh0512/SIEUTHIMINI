<?php
class InvoiceModel extends Database {
    
    // =======================================================
    // ** PHÂN TRANG: HÀM ĐẾM TỔNG SỐ BẢN GHI **
    // =======================================================
    public function countAll() {
        $sql = "SELECT COUNT(*) FROM hoadon";
        $result = $this->conn->query($sql)->fetch_row();
        return $result[0] ?? 0;
    }

    // =======================================================
    // ** PHÂN TRANG: HÀM LẤY DỮ LIỆU CÓ LIMIT & OFFSET **
    // =======================================================
    public function getPaginated($limit, $offset) {
        $sql = "SELECT hd.*, nv.hoTenNV, kh.hoTenKH 
                FROM hoadon hd 
                LEFT JOIN nhanvien nv ON hd.maNV = nv.maNV 
                LEFT JOIN khachhang kh ON hd.maKH = kh.maKH 
                ORDER BY hd.ngayTao DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset); 
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