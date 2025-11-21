<?php
class CustomerModel extends Database {
    
    // =======================================================
    // ** PHÂN TRANG: HÀM ĐẾM TỔNG SỐ BẢN GHI **
    // =======================================================
    public function countAll() {
       $sql = "SELECT COUNT(*) FROM khachhang";
       $result = $this->conn->query($sql)->fetch_row();
       return $result[0] ?? 0;
    }

    // =======================================================
    // ** PHÂN TRANG: HÀM LẤY DỮ LIỆU CÓ LIMIT & OFFSET **
    // =======================================================
    public function getPaginated($limit, $offset) {
        $sql = "SELECT * FROM khachhang ORDER BY maKH DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset); 
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

   // Lấy tất cả khách hàng đang hoạt động (cho các dropdown như khi tạo hóa đơn)
   public function getAll() {
        $sql = "SELECT * FROM khachhang WHERE trangThai = 1 ORDER BY maKH DESC"; 
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getById($id) {
        $sql = "SELECT * FROM khachhang WHERE maKH=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function add($hoTenKH, $soDienThoai, $diaChi, $ngaySinh, $email, $diemTichLuy = 0) {
        // Thêm trạng thái mặc định là 1 (Hoạt động)
        $trangThai = 1; 
        $sql = "INSERT INTO khachhang (hoTenKH, soDienThoai, diaChi, ngaySinh, email, diemTichLuy, trangThai) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        // Lưu ý: TrangThai là i (integer)
        $stmt->bind_param("sssssii", $hoTenKH, $soDienThoai, $diaChi, $ngaySinh, $email, $diemTichLuy, $trangThai);
        return $stmt->execute();
    }

    public function update($maKH, $hoTenKH, $soDienThoai, $diaChi, $ngaySinh, $email, $diemTichLuy, $trangThai) {
        // Update cả cột trangThai
        $sql = "UPDATE KhachHang SET hoTenKH=?, soDienThoai=?, diaChi=?, ngaySinh=?, email=?, diemTichLuy=?, trangThai=? WHERE maKH=?";
        $stmt = $this->conn->prepare($sql);
        // Lưu ý: trangThai (i) và maKH (i)
        $stmt->bind_param("ssssiiii", $hoTenKH, $soDienThoai, $diaChi, $ngaySinh, $email, $diemTichLuy, $trangThai, $maKH);
        return $stmt->execute();
    }

    // =======================================================
    // ** THAY THẾ: HÀM DELETE THÀNH DISABLE (Ẩn) **
    // =======================================================
    public function disable($maKH) {
        $sql = "UPDATE KhachHang SET trangThai = 0 WHERE maKH=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maKH);
        return $stmt->execute();
    }
    
    // =======================================================
    // ** THÊM: HÀM KHÔI PHỤC (RESTORE) **
    // =======================================================
    public function restore($maKH) {
        $sql = "UPDATE KhachHang SET trangThai = 1 WHERE maKH=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maKH);
        return $stmt->execute();
    }

    public function search($keyword) { /* ... giữ nguyên ... */ }
}
?>