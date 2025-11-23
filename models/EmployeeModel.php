<?php
class EmployeeModel extends Database {
    
    // =======================================================
    // ** HELPER: XÂY DỰNG MỆNH ĐỀ WHERE VÀ THAM SỐ LỌC **
    // =======================================================
    private function _buildFilterQuery($keyword, $role_id, $start_date, $end_date, &$params, &$types) {
        $where = " WHERE 1 "; // Bắt đầu với điều kiện luôn đúng
        $params = [];
        $types = "";

        // 1. Keyword filter
        if (!empty($keyword)) {
            // Lọc theo hoTenNV, soDienThoai, email, tenVaiTro (cần JOIN)
            $where .= " AND (nv.hoTenNV LIKE ? OR nv.soDienThoai LIKE ? OR nv.email LIKE ? OR vt.tenVaiTro LIKE ?)";
            $like_keyword = "%" . $keyword . "%";
            $params[] = $like_keyword;
            $params[] = $like_keyword;
            $params[] = $like_keyword;
            $params[] = $like_keyword;
            $types .= "ssss";
        }

        // 2. Role filter
        if (!empty($role_id) && $role_id !== '0') {
            $where .= " AND nv.maVaiTro = ?";
            $params[] = $role_id;
            $types .= "i";
        }

        // 3. Start Date filter
        if (!empty($start_date)) {
            $where .= " AND nv.ngayVaoLam >= ?";
            $params[] = $start_date;
            $types .= "s";
        }

        // 4. End Date filter
        if (!empty($end_date)) {
            $where .= " AND nv.ngayVaoLam <= ?";
            $params[] = $end_date;
            $types .= "s";
        }
        
        return $where;
    }
    
    // =======================================================
    // ** PHÂN TRANG: HÀM ĐẾM TỔNG SỐ BẢN GHI (CÓ LỌC) **
    // =======================================================
    public function countAll($keyword = '', $role_id = '', $start_date = '', $end_date = '') {
        $params = [];
        $types = "";

        // JOIN với VaiTro là cần thiết vì keyword tìm kiếm trên tenVaiTro
        $sql_base = "SELECT COUNT(nv.maNV) FROM nhanvien nv LEFT JOIN vaitro vt ON nv.maVaiTro = vt.maVaiTro";
        
        $where = $this->_buildFilterQuery($keyword, $role_id, $start_date, $end_date, $params, $types);
        $sql = $sql_base . $where;

        if (empty($params)) {
            $result = $this->conn->query($sql)->fetch_row();
            return $result[0] ?? 0;
        } else {
            $stmt = $this->conn->prepare($sql);
            
            // Dùng helper để bind tham số động
            $bind_params = array_merge([$types], $params);
            call_user_func_array([$stmt, 'bind_param'], $this->_refValues($bind_params));
            
            $stmt->execute();
            $result = $stmt->get_result()->fetch_row();
            return $result[0] ?? 0;
        }
    }

    // =======================================================
    // ** PHÂN TRANG: HÀM LẤY DỮ LIỆU CÓ LIMIT & OFFSET (CÓ LỌC) **
    // =======================================================
    public function getPaginated($limit, $offset, $keyword = '', $role_id = '', $start_date = '', $end_date = '') {
        $params = [];
        $types = "";
        
        $sql_base = "SELECT nv.*, vt.tenVaiTro 
                     FROM nhanvien nv 
                     LEFT JOIN vaitro vt ON nv.maVaiTro = vt.maVaiTro";
        
        $where = $this->_buildFilterQuery($keyword, $role_id, $start_date, $end_date, $params, $types);
        
        $sql = $sql_base . $where . " ORDER BY nv.trangThaiLamViec DESC, nv.maNV DESC LIMIT ? OFFSET ?";

        // Thêm tham số limit và offset
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii"; 

        $stmt = $this->conn->prepare($sql);
        
        // Dùng helper để bind tham số động
        if (!empty($params)) {
            $bind_params = array_merge([$types], $params);
            call_user_func_array([$stmt, 'bind_param'], $this->_refValues($bind_params));
        }
        
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Helper function for dynamic bind_param (using reference for array)
    private function _refValues($arr) {
        if (strnatcmp(phpversion(), '5.3') >= 0) {
            $refs = [];
            foreach($arr as $key => $value) {
                $refs[$key] = &$arr[$key];
            }
            return $refs;
        }
        return $arr;
    }

    // 1. HÀM LẤY DANH SÁCH TẤT CẢ (Không dùng cho phân trang)
    public function getAll() {
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

    // 2. HÀM LẤY THEO ID
    public function getById($maNV) {
        $sql = "SELECT nv.*, vt.tenVaiTro 
                FROM nhanvien nv 
                LEFT JOIN vaitro vt ON nv.maVaiTro = vt.maVaiTro
                WHERE maNV=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maNV);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 3. HÀM THÊM MỚI
    public function add($maVaiTro, $hoTenNV, $ngaySinh, $diaChi, $soDienThoai, $email, $ngayVaoLam) {
        $sql = "INSERT INTO nhanvien (maVaiTro, hoTenNV, ngaySinh, diaChi, soDienThoai, email, ngayVaoLam, trangThaiLamViec) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issssss", $maVaiTro, $hoTenNV, $ngaySinh, $diaChi, $soDienThoai, $email, $ngayVaoLam);
        return $stmt->execute();
    }

    // 4. HÀM CẬP NHẬT
    public function update($maNV, $maVaiTro, $hoTenNV, $ngaySinh, $diaChi, $soDienThoai, $email, $ngayVaoLam, $trangThaiLamViec) {
        $sql = "UPDATE nhanvien SET maVaiTro=?, hoTenNV=?, ngaySinh=?, diaChi=?, soDienThoai=?, email=?, ngayVaoLam=?, trangThaiLamViec=? WHERE maNV=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issssssii", $maVaiTro, $hoTenNV, $ngaySinh, $diaChi, $soDienThoai, $email, $ngayVaoLam, $trangThaiLamViec, $maNV);
        return $stmt->execute();
    }

    // 5. HÀM XÓA (Cho nghỉ)
    public function delete($maNV) {
        $sql = "UPDATE nhanvien SET trangThaiLamViec = 0 WHERE maNV=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maNV);
        return $stmt->execute();
    }

    // 6. HÀM KHÔI PHỤC
    public function restore($maNV) {
        $sql = "UPDATE nhanvien SET trangThaiLamViec = 1 WHERE maNV=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maNV);
        return $stmt->execute();
    }
    
    // HÀM SEARCH CŨ (Giữ lại nếu vẫn dùng ở nơi khác, mặc dù đã có logic lọc tổng quát hơn)
    public function search($keyword) { 
        $sql = "SELECT nv.*, vt.tenVaiTro 
                FROM nhanvien nv 
                LEFT JOIN vaitro vt ON nv.maVaiTro = vt.maVaiTro
                WHERE nv.hoTenNV LIKE ? OR nv.soDienThoai LIKE ? OR nv.email LIKE ?";
        
        $stmt = $this->conn->prepare($sql);
        $like_keyword = "%" . $keyword . "%";
        $stmt->bind_param("sss", $like_keyword, $like_keyword, $like_keyword);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Thêm vào trong class EmployeeModel

    // Kiểm tra trùng số điện thoại (dùng cho THÊM MỚI)
public function checkPhoneExists($sdt) {
    $sql = "SELECT COUNT(*) FROM nhanvien WHERE soDienThoai = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $sdt);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_row();
    return $row[0] > 0;
}

    // Kiểm tra trùng Email (dùng cho THÊM MỚI)
    public function checkEmailExists($email) {
        $sql = "SELECT COUNT(*) FROM nhanvien WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_row();
        return $row[0] > 0;
    }

    // Kiểm tra trùng số điện thoại (loại trừ ID hiện tại - dùng cho SỬA)
    public function checkPhoneExistsExcept($sdt, $excludeId) {
        $sql = "SELECT COUNT(*) FROM nhanvien WHERE soDienThoai = ? AND maNV != ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $sdt, $excludeId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_row();
        return $row[0] > 0;
    }

    // Kiểm tra trùng Email (loại trừ ID hiện tại - dùng cho SỬA)
    public function checkEmailExistsExcept($email, $excludeId) {
        $sql = "SELECT COUNT(*) FROM nhanvien WHERE email = ? AND maNV != ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $email, $excludeId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_row();
        return $row[0] > 0;
    }
}