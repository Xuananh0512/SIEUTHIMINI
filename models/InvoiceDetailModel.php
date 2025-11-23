<?php
class InvoiceDetailModel extends Database {
    public function getAll() {
        $sql = "SELECT * FROM chitiethoadon"; // Lowercase table name
        return $this->conn->query($sql);
    }

    // NEW METHOD: Fetch details JOINed with Product table for display
    public function getByInvoiceId($maHD) {
        $sql = "SELECT ct.*, sp.tenSP, sp.donViTinh 
                FROM chitiethoadon ct 
                JOIN sanpham sp ON ct.maSP = sp.maSP 
                WHERE ct.maHD = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maHD);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Return as Array for View compatibility
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getById($maHD, $maSP) {
        $sql = "SELECT * FROM chitiethoadon WHERE maHD=? AND maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $maHD, $maSP);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function add($maHD, $maSP, $soLuong, $donGiaLucMua, $thanhTien) {
        $sql = "INSERT INTO chitiethoadon (maHD, maSP, soLuong, donGiaLucMua, thanhTien) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiidd", $maHD, $maSP, $soLuong, $donGiaLucMua, $thanhTien);
        return $stmt->execute();
    }

    public function update($maHD, $maSP, $soLuong, $donGiaLucMua, $thanhTien) {
        $sql = "UPDATE chitiethoadon SET soLuong=?, donGiaLucMua=?, thanhTien=? WHERE maHD=? AND maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iddii", $soLuong, $donGiaLucMua, $thanhTien, $maHD, $maSP);
        return $stmt->execute();
    }

    public function delete($maHD, $maSP) {
        $sql = "DELETE FROM chitiethoadon WHERE maHD=? AND maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $maHD, $maSP);
        return $stmt->execute();
    }

    // Basic search (kept for compatibility, but getByInvoiceId is preferred for UI)
    public function search($maHD) {
        $sql = "SELECT * FROM chitiethoadon WHERE maHD=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maHD);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>