
<?php

class ReportModel extends Database {
    
    // =======================================================
    // ** BÁO CÁO 1: NHẬP HÀNG (importreport) **
    // =======================================================
    public function getImportReport($startDate, $endDate) {
        // Lấy tổng quan các phiếu nhập hàng trong khoảng thời gian
        $sql = "SELECT pn.maPN, pn.ngayNhap, nv.hoTenNV, ncc.tenNCC, SUM(ctpn.thanhTien) AS tongChi
                FROM phieunhap pn
                LEFT JOIN nhanvien nv ON pn.maNV = nv.maNV
                LEFT JOIN nhacungcap ncc ON pn.maNCC = ncc.maNCC
                LEFT JOIN chitietphieunhap ctpn ON pn.maPN = ctpn.maPN
                WHERE DATE(pn.ngayNhap) BETWEEN ? AND ?
                GROUP BY pn.maPN, pn.ngayNhap, nv.hoTenNV, ncc.tenNCC
                ORDER BY pn.ngayNhap DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $startDate, $endDate); 
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // =======================================================
    // ** BÁO CÁO 2: DOANH THU (revenue) **
    // =======================================================
    public function getRevenueReport($startDate, $endDate) {
        // Tổng doanh thu theo ngày
        $sql = "SELECT DATE(ngayTao) AS ngay, SUM(tongTien) AS tongDoanhThu
                FROM hoadon
                WHERE DATE(ngayTao) BETWEEN ? AND ? AND trangThai=1
                GROUP BY DATE(ngayTao)
                ORDER BY ngay ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $startDate, $endDate); 
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // =======================================================
    // ** BÁO CÁO 3: BÁN CHẠY (topselling) **
    // =======================================================
    public function getTopSellingProducts($limit = 10) {
        // Top N sản phẩm bán chạy nhất mọi thời điểm
        $sql = "SELECT sp.tenSP, sp.donViTinh, SUM(cthd.soLuong) AS tongSoLuongBan
                FROM chitiethoadon cthd
                LEFT JOIN sanpham sp ON cthd.maSP = sp.maSP
                GROUP BY sp.maSP, sp.tenSP, sp.donViTinh
                ORDER BY tongSoLuongBan DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit); 
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // =======================================================
    // ** BÁO CÁO 4: HIỆU SUẤT NV (employeeperformance) **
    // =======================================================
    public function getEmployeePerformance($startDate, $endDate) {
        // Tổng số hóa đơn và tổng doanh thu theo từng nhân viên
        $sql = "SELECT nv.hoTenNV, COUNT(hd.maHD) AS tongHoaDon, SUM(hd.tongTien) AS tongDoanhThu
                FROM hoadon hd
                LEFT JOIN nhanvien nv ON hd.maNV = nv.maNV
                WHERE DATE(hd.ngayTao) BETWEEN ? AND ? AND hd.trangThai=1
                GROUP BY nv.maNV, nv.hoTenNV
                ORDER BY tongDoanhThu DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $startDate, $endDate); 
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // =======================================================
    // ** BÁO CÁO 5: TỒN KHO THẤP (lowstock) **
    // =======================================================
    public function getLowStock($threshold = 5) {
        // Lấy sản phẩm có số lượng tồn < ngưỡng và số lượng tồn > 0
        $sql = "SELECT maSP, tenSP, donViTinh, soLuongTon, donGiaBan
                FROM sanpham
                WHERE soLuongTon > 0 AND soLuongTon <= ?
                ORDER BY soLuongTon ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $threshold); 
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // =======================================================
    // ** BÁO CÁO 6: SẮP HẾT HẠN (expiringsoon) **
    // =======================================================
    public function getExpiringSoon($days = 30) {
        $dateLimit = date('Y-m-d', strtotime("+$days days"));
        
        $sql = "SELECT maSP, tenSP, donViTinh, soLuongTon, hanSuDung
                FROM sanpham
                WHERE hanSuDung IS NOT NULL 
                AND hanSuDung BETWEEN CURDATE() AND ?
                AND soLuongTon > 0 
                ORDER BY hanSuDung ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $dateLimit); 
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>