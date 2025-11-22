<?php

class CategoryModel extends Database {
    
    // =======================================================
    // ** PHÂN TRANG: HÀM ĐẾM TỔNG SỐ BẢN GHI **
    // =======================================================
    public function countAll() {
        $sql = "SELECT COUNT(*) FROM DanhMuc";
        $result = $this->conn->query($sql)->fetch_row();
        return $result[0] ?? 0;
    }

    // =======================================================
    // ** PHÂN TRANG: HÀM LẤY DỮ LIỆU CÓ LIMIT & OFFSET **
    // =======================================================
    public function getPaginated($limit, $offset) {
        $sql = "SELECT * FROM DanhMuc ORDER BY maDM DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset); 
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    
    // 1. HÀM LẤY DANH SÁCH - Luôn trả về MẢNG
    public function getAll() {
        $sql = "SELECT * FROM DanhMuc";
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getById($maDM) {
        $sql = "SELECT * FROM DanhMuc WHERE maDM=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDM);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function add($tenDM) {
        $sql = "INSERT INTO DanhMuc (tenDM, trangThai) VALUES (?, 1)"; // Mặc định trạng thái 1
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $tenDM);
        return $stmt->execute();
    }

    public function update($maDM, $tenDM) {
        $sql = "UPDATE DanhMuc SET tenDM=? WHERE maDM=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $tenDM, $maDM);
        return $stmt->execute();
    }

    // =======================================================
    // ** THAY THẾ: HÀM DELETE THÀNH DISABLE (Ẩn) **
    // =======================================================
    public function disable($maDM) {
        $sql = "UPDATE DanhMuc SET trangThai=0 WHERE maDM=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDM);
        return $stmt->execute();
    }
    
    // =======================================================
    // ** THÊM: HÀM RESTORE (Khôi phục) **
    // =======================================================
    public function restore($maDM) {
        $sql = "UPDATE DanhMuc SET trangThai=1 WHERE maDM=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDM);
        return $stmt->execute();
    }
    
    // Hàm hỗ trợ kiểm tra nghiệp vụ trong Service
    public function countByCategoryId($maDM) {
        // Giả định bảng sanpham có maDM
        $sql = "SELECT COUNT(*) FROM sanpham WHERE maDM = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDM);
        $stmt->execute();
        return $stmt->get_result()->fetch_row()[0] ?? 0;
    }

    public function search($keyword) {
        $sql = "SELECT * FROM DanhMuc WHERE tenDM LIKE ?";
        $keyword = "%$keyword%";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}
?>