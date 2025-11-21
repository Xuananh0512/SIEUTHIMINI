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
        // Trả về mảng (array)
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    
    // 1. HÀM LẤY DANH SÁCH - ✅ ĐÃ SỬA: Luôn trả về MẢNG
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
        $sql = "INSERT INTO DanhMuc (tenDM) VALUES (?)";
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

    public function delete($maDM) {
        $sql = "DELETE FROM DanhMuc WHERE maDM=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDM);
        return $stmt->execute();
    }

    // ✅ ĐÃ SỬA: Luôn trả về MẢNG
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