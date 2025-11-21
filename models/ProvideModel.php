<?php

class ProvideModel extends Database {

    // =======================================================
    // ** PHÂN TRANG: HÀM ĐẾM TỔNG SỐ BẢN GHI **
    // =======================================================
    public function countAll() {
        $sql = "SELECT COUNT(*) FROM nhacungcap";
        $result = $this->conn->query($sql)->fetch_row();
        return $result[0] ?? 0;
    }

    // =======================================================
    // ** PHÂN TRANG: HÀM LẤY DỮ LIỆU CÓ LIMIT & OFFSET **
    // =======================================================
    public function getPaginated($limit, $offset) {
        $sql = "SELECT * FROM nhacungcap ORDER BY maNCC DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset); 
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // ✅ ĐÃ SỬA: Luôn trả về MẢNG
    public function getAll() {
        $sql = "SELECT * FROM nhacungcap"; 
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    
    // ... Các hàm khác giữ nguyên ...

    public function add($tenNCC, $soDienThoai, $diaChi) {
        $sql = "INSERT INTO NhaCungCap (tenNCC, soDienThoai, diaChi) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $tenNCC, $soDienThoai, $diaChi);
        return $stmt->execute();
    }

    public function update($maNCC, $tenNCC, $soDienThoai, $diaChi) {
        $sql = "UPDATE NhaCungCap SET tenNCC=?, soDienThoai=?, diaChi=? WHERE maNCC=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $tenNCC, $soDienThoai, $diaChi, $maNCC);
        return $stmt->execute();
    }

    public function delete($maNCC) {
        $sql = "DELETE FROM NhaCungCap WHERE maNCC=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maNCC);
        return $stmt->execute();
    }

    // ✅ ĐÃ SỬA: Luôn trả về MẢNG
    public function search($keyword) {
        $sql = "SELECT * FROM NhaCungCap WHERE tenNCC LIKE ?";
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

    public function getById($maNCC) {
        $sql = "SELECT * FROM nhacungcap WHERE maNCC=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maNCC);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>