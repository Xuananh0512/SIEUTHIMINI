<?php

class RoleModel extends Database {
    
    // =======================================================
    // ** PHÂN TRANG: HÀM ĐẾM TỔNG SỐ BẢN GHI **
    // =======================================================
    public function countAll() {
        $sql = "SELECT COUNT(*) FROM vaitro";
        $result = $this->conn->query($sql)->fetch_row();
        return $result[0] ?? 0;
    }

    // =======================================================
    // ** PHÂN TRANG: HÀM LẤY DỮ LIỆU CÓ LIMIT & OFFSET **
    // =======================================================
    public function getPaginated($limit, $offset) {
        $sql = "SELECT * FROM vaitro ORDER BY maVaiTro DESC LIMIT ? OFFSET ?";
        
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
        $sql = "SELECT * FROM vaitro";
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getById($id) {
        $sql = "SELECT * FROM vaitro WHERE maVaiTro = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function add($tenVaiTro) {
        $sql = "INSERT INTO vaitro (tenVaiTro) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $tenVaiTro);
        return $stmt->execute();
    }

    public function update($id, $tenVaiTro) {
        $sql = "UPDATE vaitro SET tenVaiTro=? WHERE maVaiTro=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $tenVaiTro, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM vaitro WHERE maVaiTro=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function search($keyword) {
        $sql = "SELECT * FROM vaitro WHERE tenVaiTro LIKE ?";
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