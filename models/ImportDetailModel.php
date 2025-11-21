<?php

class ImportDetailModel extends Database
{
    public function getAll()
    {
        $sql = "SELECT * FROM ChiTietPhieuNhap";
        return $this->conn->query($sql);
    }

    public function getById($maSP, $maPN)
    {
        $sql = "SELECT * FROM ChiTietPhieuNhap WHERE maSP=? AND maPN=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $maSP, $maPN);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByImportId($maPN)
    {
        $sql = "SELECT ct.*, sp.tenSP, sp.donViTinh 
                FROM chitietphieunhap ct 
                JOIN sanpham sp ON ct.maSP = sp.maSP 
                WHERE ct.maPN = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maPN);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function add($maPN, $maSP, $soLuong, $giaNhap, $thanhTien)
    {
        $sql = "INSERT INTO chitietphieunhap (maPN, maSP, soLuong, giaNhap, thanhTien) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiidd", $maPN, $maSP, $soLuong, $giaNhap, $thanhTien);
        return $stmt->execute();
    }

    public function update($maSP, $maPN, $soLuong, $giaNhap, $thanhTien)
    {
        $sql = "UPDATE ChiTietPhieuNhap SET soLuong=?, giaNhap=?, thanhTien=? WHERE maSP=? AND maPN=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iddii", $soLuong, $giaNhap, $thanhTien, $maSP, $maPN);
        return $stmt->execute();
    }

    public function delete($maSP, $maPN)
    {
        $sql = "DELETE FROM ChiTietPhieuNhap WHERE maSP=? AND maPN=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $maSP, $maPN);
        return $stmt->execute();
    }

    public function search($maPN)
    {
        $sql = "SELECT * FROM ChiTietPhieuNhap WHERE maPN=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maPN);
        $stmt->execute();
        return $stmt->get_result();
    }
}
