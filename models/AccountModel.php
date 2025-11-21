<?php

class AccountModel extends Database
{

    // =======================================================
    // ** PHÂN TRANG: HÀM ĐẾM TỔNG SỐ BẢN GHI **
    // =======================================================
    public function countAll()
    {
        $sql = "SELECT COUNT(*) FROM TaiKhoan";
        $result = $this->conn->query($sql)->fetch_row();
        return $result[0] ?? 0;
    }

    // =======================================================
    // ** PHÂN TRANG: HÀM LẤY DỮ LIỆU CÓ LIMIT & OFFSET **
    // =======================================================
    public function getPaginated($limit, $offset)
    {
        $sql = "SELECT 
                    tk.*, 
                    nv.hoTenNV, 
                    vt.tenVaiTro 
                FROM 
                    TaiKhoan tk
                LEFT JOIN 
                    NhanVien nv ON tk.maNV = nv.maNV
                LEFT JOIN 
                    VaiTro vt ON tk.maVaiTro = vt.maVaiTro
                ORDER BY tk.maTK DESC
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
    
    public function getAll()
    {
        $sql = "SELECT 
                    tk.*, 
                    nv.hoTenNV, 
                    vt.tenVaiTro 
                FROM 
                    TaiKhoan tk
                LEFT JOIN 
                    NhanVien nv ON tk.maNV = nv.maNV
                LEFT JOIN 
                    VaiTro vt ON tk.maVaiTro = vt.maVaiTro
                ORDER BY tk.maTK DESC";
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getById($maTK)
    {
        $sql = "SELECT * FROM TaiKhoan WHERE maTK=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maTK);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function add($maVaiTro, $maNV, $tenDangNhap, $matKhau, $trangThai = 1)
    {
        $sql = "INSERT INTO TaiKhoan (maVaiTro, maNV, tenDangNhap, matKhau, trangThai) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iissi", $maVaiTro, $maNV, $tenDangNhap, $matKhau, $trangThai);
        return $stmt->execute();
    }

    public function update($maTK, $maVaiTro, $maNV, $tenDangNhap, $matKhau, $trangThai)
    {
        $sql = "UPDATE TaiKhoan SET maVaiTro=?, maNV=?, tenDangNhap=?, matKhau=?, trangThai=? WHERE maTK=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iissii", $maVaiTro, $maNV, $tenDangNhap, $matKhau, $trangThai, $maTK);
        return $stmt->execute();
    }

    // THAY THẾ: HÀM LOCK (Khóa)
    public function lock($maTK)
    {
        $sql = "UPDATE TaiKhoan SET trangThai = 0 WHERE maTK=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maTK);
        return $stmt->execute();
    }
    
    // THÊM: HÀM UNLOCK (Mở Khóa)
    public function unlock($maTK)
    {
        $sql = "UPDATE TaiKhoan SET trangThai = 1 WHERE maTK=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maTK);
        return $stmt->execute();
    }

    public function search($keyword)
    {
        $sql = "SELECT * FROM TaiKhoan WHERE tenDangNhap LIKE ?";
        $keyword = "%$keyword%";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) return $result->fetch_all(MYSQLI_ASSOC);
        return [];
    }
    
    // HÀM ĐĂNG NHẬP
    public function login($username, $hashedPassword)
    {
        $sql = "SELECT maTK, maNV, tenDangNhap, trangThai FROM TaiKhoan WHERE tenDangNhap=? AND matKhau=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashedPassword);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // HÀM LẤY THÔNG TIN NHÂN VIÊN
    public function getEmployeeInfo($maNV)
    {
        $sql = "SELECT nv.hoTenNV, vt.tenVaiTro 
             FROM NhanVien nv 
             JOIN VaiTro vt ON nv.maVaiTro = vt.maVaiTro
             WHERE nv.maNV = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maNV);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // =======================================================
    // ** THÊM: HÀM LẤY TÀI KHOẢN THEO MÃ NV (Dùng cho Profile) **
    // =======================================================
    public function getByEmployeeId($maNV)
    {
        $sql = "SELECT maTK, tenDangNhap, trangThai, matKhau FROM TaiKhoan WHERE maNV=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maNV);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // =======================================================
    // ** THÊM: HÀM ĐỔI MẬT KHẨU (Dùng cho Profile) **
    // =======================================================
    public function updatePassword($maNV, $oldPassword, $newPassword) 
    {
        // 1. Kiểm tra mật khẩu cũ
        $sql_check = "SELECT matKhau FROM TaiKhoan WHERE maNV=? AND matKhau=?";
        $stmt_check = $this->conn->prepare($sql_check);
        // MD5($oldPassword) vì bạn đang lưu mật khẩu bằng MD5
        $hashedOld = MD5($oldPassword); 
        $stmt_check->bind_param("is", $maNV, $hashedOld);
        $stmt_check->execute();
        
        if ($stmt_check->get_result()->num_rows === 0) {
            return "Mật khẩu cũ không chính xác.";
        }
        
        // 2. Cập nhật mật khẩu mới
        $sql_update = "UPDATE TaiKhoan SET matKhau=? WHERE maNV=?";
        $stmt_update = $this->conn->prepare($sql_update);
        $hashedNew = MD5($newPassword);
        $stmt_update->bind_param("si", $hashedNew, $maNV);
        
        if ($stmt_update->execute()) {
            return true;
        } else {
            return "Lỗi khi cập nhật mật khẩu mới.";
        }
    }
}
?>