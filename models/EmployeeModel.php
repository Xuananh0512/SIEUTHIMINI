<?php
class EmployeeModel extends Database {
    
    // =======================================================
    // ** PHÂN TRANG: HÀM ĐẾM TỔNG SỐ BẢN GHI **
    // =======================================================
    public function countAll() {
        $sql = "SELECT COUNT(*) FROM nhanvien";
        $result = $this->conn->query($sql)->fetch_row();
        return $result[0] ?? 0;
    }

    // =======================================================
    // ** PHÂN TRANG: HÀM LẤY DỮ LIỆU CÓ LIMIT & OFFSET **
    // =======================================================
    public function getPaginated($limit, $offset) {
        $sql = "SELECT nv.*, vt.tenVaiTro 
                FROM nhanvien nv 
                LEFT JOIN vaitro vt ON nv.maVaiTro = vt.maVaiTro
                ORDER BY nv.trangThaiLamViec DESC, nv.maNV DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset); 
        $stmt->execute();
        
        $result = $stmt->get_result();
        // Trả về mảng (array)
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    
    // 1. HÀM LẤY DANH SÁCH (Đã sửa để trả về Mảng)
    public function getAll() {
        // Lấy cả tên chức vụ
        $sql = "SELECT nv.*, vt.tenVaiTro 
                FROM nhanvien nv 
                LEFT JOIN vaitro vt ON nv.maVaiTro = vt.maVaiTro
                ORDER BY nv.trangThaiLamViec DESC, nv.maNV DESC"; 
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getById($id) {
        $sql = "SELECT * FROM nhanvien WHERE maNV=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function add($maVaiTro, $hoTenNV, $ngaySinh, $diaChi, $soDienThoai, $email, $ngayVaoLam) {
        $trangThai = 1;
        $sql = "INSERT INTO nhanvien (maVaiTro, hoTenNV, ngaySinh, diaChi, soDienThoai, email, ngayVaoLam, trangThaiLamViec) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issssssi", $maVaiTro, $hoTenNV, $ngaySinh, $diaChi, $soDienThoai, $email, $ngayVaoLam, $trangThai);
        return $stmt->execute();
    }

    public function update($maNV, $maVaiTro, $hoTenNV, $ngaySinh, $diaChi, $soDienThoai, $email, $ngayVaoLam, $trangThaiLamViec) {
        $sql = "UPDATE nhanvien SET maVaiTro=?, hoTenNV=?, ngaySinh=?, diaChi=?, soDienThoai=?, email=?, ngayVaoLam=?, trangThaiLamViec=? WHERE maNV=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issssssii", $maVaiTro, $hoTenNV, $ngaySinh, $diaChi, $soDienThoai, $email, $ngayVaoLam, $trangThaiLamViec, $maNV);
        return $stmt->execute();
    }

    // Chức năng ẩn (Cho nghỉ)
    public function delete($maNV) {
        $sql = "UPDATE nhanvien SET trangThaiLamViec = 0 WHERE maNV=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maNV);
        return $stmt->execute();
    }

    // --- HÀM KHÔI PHỤC NHÂN VIÊN ---\
    public function restore($maNV) {
        $sql = "UPDATE nhanvien SET trangThaiLamViec = 1 WHERE maNV=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maNV);
        return $stmt->execute();
    }
    
    public function search($keyword) { 
        $sql = "SELECT nv.*, vt.tenVaiTro 
                FROM nhanvien nv 
                LEFT JOIN vaitro vt ON nv.maVaiTro = vt.maVaiTro
                WHERE nv.hoTenNV LIKE ? OR nv.soDienThoai LIKE ?
                ORDER BY nv.trangThaiLamViec DESC, nv.maNV DESC"; 
        
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