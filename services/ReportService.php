<?php
class ReportService {
    private $db; 

    public function __construct() {
        $this->db = new Database(); // Khởi tạo kết nối DB
    }

    // =======================================================
    // HÀM HỖ TRỢ: Lấy KPI TỔNG HỢP
    // =======================================================
    public function getSummaryKPIs() {
        $ioData = $this->getIOReport(); 
        // Lấy Tổng tồn kho và giá trị (chức năng 7.3)
        $stockData = $this->getTotalStockValue(); 

        $totalCustomers = $this->db->conn->query("SELECT COUNT(maKH) FROM khachhang WHERE trangThai=1")->fetch_row()[0] ?? 0;
        $totalProducts = $this->db->conn->query("SELECT COUNT(maSP) FROM sanpham")->fetch_row()[0] ?? 0;
        
        return [
            'total_revenue' => $ioData['total_export'],
            'total_import_value' => $ioData['total_import'],
            // Thêm các KPI về tồn kho
            'total_stock_value' => $stockData['total_value'],
            'total_stock_qty' => $stockData['total_qty'],
            'total_customers' => $totalCustomers,
            'total_products' => $totalProducts
        ];
    }

    // HÀM HỖ TRỢ: Tổng tồn kho và giá trị (Dùng cho KPI)
    public function getTotalStockValue() {
        $sql = "SELECT SUM(soLuongTon * donGiaBan) AS total_value, SUM(soLuongTon) AS total_qty
                FROM sanpham";
        $result = $this->db->conn->query($sql);
        if ($result) return $result->fetch_assoc();
        return ['total_value' => 0, 'total_qty' => 0];
    }


    // =======================================================
    // 1. Báo cáo Doanh thu (Mục 7.1)
    // =======================================================
    public function getRevenueReport() {
        $sql = "SELECT 
                    SUM(tongTien) AS total_revenue,
                    DATE_FORMAT(ngayTao, '%Y-%m') AS sale_month
                FROM 
                    hoadon 
                WHERE 
                    trangThai = 1
                GROUP BY 
                    sale_month
                ORDER BY 
                    sale_month DESC";
        $result = $this->db->conn->query($sql);
        if ($result) return $result->fetch_all(MYSQLI_ASSOC);
        return [];
    }
    
    // =======================================================
    // 2. Sản phẩm bán chạy (Mục 7.2)
    // =======================================================
    public function getTopSellingProducts($limit = 10) {
        $sql = "SELECT 
                    cthd.maSP, 
                    sp.tenSP, 
                    SUM(cthd.soLuong) AS total_qty_sold 
                FROM 
                    chitiethoadon cthd
                JOIN 
                    sanpham sp ON cthd.maSP = sp.maSP
                JOIN 
                    hoadon hd ON cthd.maHD = hd.maHD
                WHERE 
                    hd.trangThai = 1
                GROUP BY 
                    cthd.maSP
                ORDER BY 
                    total_qty_sold DESC
                LIMIT ?";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        if ($result = $stmt->get_result()) return $result->fetch_all(MYSQLI_ASSOC);
        return [];
    }

    // =======================================================
    // 3. Tồn kho thấp (Hàng sắp hết) (Mục 7.4)
    // =======================================================
    public function getLowStockAlerts($threshold = 20) {
        $sql = "SELECT maSP, tenSP, soLuongTon 
                FROM sanpham 
                WHERE soLuongTon <= ? AND soLuongTon > 0 
                ORDER BY soLuongTon ASC";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bind_param("i", $threshold);
        $stmt->execute();
        if ($result = $stmt->get_result()) return $result->fetch_all(MYSQLI_ASSOC);
        return [];
    }
    
    // =======================================================
    // 4. Hàng sắp hết hạn (Mục 7.5)
    // =======================================================
    public function getExpiredAlerts($days = 90) {
        $sql = "SELECT maSP, tenSP, hanSuDung, soLuongTon 
                FROM sanpham 
                WHERE hanSuDung IS NOT NULL AND hanSuDung BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                ORDER BY hanSuDung ASC";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bind_param("i", $days);
        $stmt->execute();
        if ($result = $stmt->get_result()) return $result->fetch_all(MYSQLI_ASSOC);
        return [];
    }
    
    // =======================================================
    // 5. Hiệu suất Nhân viên (Mục 7.6)
    // =======================================================
    public function getEmployeePerformance() {
        $sql = "SELECT 
                    nv.maNV,
                    nv.hoTenNV,
                    vt.tenVaiTro,
                    COUNT(hd.maHD) AS total_invoices,
                    SUM(hd.tongTien) AS total_sales
                FROM 
                    nhanvien nv
                LEFT JOIN 
                    hoadon hd ON nv.maNV = hd.maNV AND hd.trangThai = 1
                LEFT JOIN 
                    vaitro vt ON nv.maVaiTro = vt.maVaiTro
                GROUP BY 
                    nv.maNV, nv.hoTenNV, vt.tenVaiTro
                ORDER BY 
                    total_sales DESC";
        $result = $this->db->conn->query($sql);
        if ($result) return $result->fetch_all(MYSQLI_ASSOC);
        return [];
    }
    
    // =======================================================
    // 6. Báo cáo Nhập Xuất (Mục 7.7)
    // =======================================================
    public function getIOReport() {
        $sql_import = "SELECT SUM(tongGiaTri) FROM phieunhap WHERE trangThai = 1";
        $sql_export = "SELECT SUM(tongTien) FROM hoadon WHERE trangThai = 1";
        
        $totalImport = $this->db->conn->query($sql_import)->fetch_row()[0] ?? 0;
        $totalExport = $this->db->conn->query($sql_export)->fetch_row()[0] ?? 0;

        return [
            'total_import' => $totalImport,
            'total_export' => $totalExport,
            'net_revenue_estimate' => $totalExport - $totalImport
        ];
    }
}
?>