<?php
class ImportModel extends Database {
    
    // =======================================================
    // ** PHÂN TRANG: HÀM ĐẾM TỔNG SỐ BẢN GHI **
    // =======================================================
    public function countAll() {
        $sql = "SELECT COUNT(*) FROM phieunhap";
        $result = $this->conn->query($sql)->fetch_row();
        return $result[0] ?? 0;
    }

    // =======================================================
    // ** PHÂN TRANG: HÀM LẤY DỮ LIỆU CÓ LIMIT & OFFSET **
    // =======================================================
    public function getPaginated($limit, $offset) {
        $sql = "SELECT pn.*, nv.hoTenNV, ncc.tenNCC 
                FROM phieunhap pn 
                LEFT JOIN nhanvien nv ON pn.maNV = nv.maNV 
                LEFT JOIN nhacungcap ncc ON pn.maNCC = ncc.maNCC
                ORDER BY pn.ngayNhap DESC
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
    
    public function getAll() {
        $sql = "SELECT pn.*, nv.hoTenNV, ncc.tenNCC 
                FROM phieunhap pn 
                LEFT JOIN nhanvien nv ON pn.maNV = nv.maNV 
                LEFT JOIN nhacungcap ncc ON pn.maNCC = ncc.maNCC
                ORDER BY pn.ngayNhap DESC";
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getById($id) {
        $sql = "SELECT pn.*, nv.hoTenNV, ncc.tenNCC 
                FROM phieunhap pn 
                LEFT JOIN nhanvien nv ON pn.maNV = nv.maNV 
                LEFT JOIN nhacungcap ncc ON pn.maNCC = ncc.maNCC
                WHERE pn.maPN=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Hàm ADD trả về insert_id
    public function add($maNV, $maNCC, $ngayNhap, $tongGiaTri) {
        $sql = "INSERT INTO phieunhap (maNV, maNCC, ngayNhap, tongGiaTri) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisd", $maNV, $maNCC, $ngayNhap, $tongGiaTri);
        
        $success = $stmt->execute();
        if ($success) {
            return $this->conn->insert_id; // TRẢ VỀ ID VỪA TẠO
        }
        return false;
    }

    public function update($maPN, $maNV, $maNCC, $ngayNhap, $tongGiaTri) {
        $sql = "UPDATE phieunhap SET maNV=?, maNCC=?, ngayNhap=?, tongGiaTri=? WHERE maPN=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisdi", $maNV, $maNCC, $ngayNhap, $tongGiaTri, $maPN); 
        return $stmt->execute();
    }
    
    // Hàm hỗ trợ lấy tồn kho hiện tại
    public function getExistingStock($maSP) {
        $sql = "SELECT soLuongTon FROM sanpham WHERE maSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maSP);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['soLuongTon'] ?? 0;
    }
    
    // Hàm hỗ trợ cập nhật tồn kho
    public function updateStock($maSP, $newStock) {
        $sql = "UPDATE sanpham SET soLuongTon = ? WHERE maSP = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $newStock, $maSP);
        return $stmt->execute();
    }

    // DISABLE (Ẩn/Vô hiệu hóa)
    public function disable($maPN) {
        $sql = "UPDATE phieunhap SET trangThai=0 WHERE maPN=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maPN);
        return $stmt->execute();
    }
    
    // RESTORE (Khôi phục)
    public function restore($maPN) {
        $sql = "UPDATE phieunhap SET trangThai=1 WHERE maPN=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maPN);
        return $stmt->execute();
    }

    public function search($keyword) {
        $sql = "SELECT pn.*, nv.hoTenNV, ncc.tenNCC 
                FROM phieunhap pn 
                LEFT JOIN nhanvien nv ON pn.maNV = nv.maNV 
                LEFT JOIN nhacungcap ncc ON pn.maNCC = ncc.maNCC
                WHERE pn.maPN LIKE ? OR ncc.tenNCC LIKE ?";
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