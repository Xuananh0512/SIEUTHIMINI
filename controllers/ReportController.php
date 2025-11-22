<?php
class ReportController {
    private $service;

    public function __construct() {
        $this->service = new ReportService(); 
    }

    public function index() {
        return $this->summary();
    }
    
    // =======================================================
    // ** 0. TRANG TỔNG HỢP (SUMMARY) - views/report/summary.php **
    // =======================================================
    public function summary() {
        $kpis = $this->service->getSummaryKPIs();
        $top_selling = $this->service->getTopSellingProducts(5);
        
        return [
            'kpis' => $kpis,
            'top_selling' => $top_selling,
            'report_title' => 'Tổng quan Hệ thống',
            'view_file' => 'summary.php' 
        ];
    }
    
    // 1. Báo cáo Doanh thu (Mục 7.1) - views/report/revenue.php
    public function revenue() {
        $reportData = $this->service->getRevenueReport();
        return [
            'report_data' => $reportData,
            'report_title' => '1. Báo cáo Doanh thu',
            'view_file' => 'revenue.php'
        ];
    }

    // 2. Sản phẩm bán chạy (Mục 7.2) - views/report/topselling.php
    public function top_selling() {
        $reportData = $this->service->getTopSellingProducts();
        return [
            'report_data' => $reportData,
            'report_title' => '2. Sản phẩm bán chạy',
            'view_file' => 'topselling.php'
        ];
    }
    
    // =======================================================
    // ** 3. HÀM CẢNH BÁO TỒN KHO & HẾT HẠN **
    // =======================================================
    public function alerts_stock() {
        $lowStock = $this->service->getLowStockAlerts();
        $expired = $this->service->getExpiredAlerts();
        
        // Trả về cả hai loại cảnh báo cho View tổng hợp (ví dụ: stock_alerts.php)
        return [
            'low_stock' => $lowStock,
            'expired' => $expired,
            'report_title' => '3. Cảnh báo Tồn kho & Hạn sử dụng',
            'view_file' => 'stock_alerts.php' 
        ];
    }
    
    // 4. Hàng sắp hết (lowstock.php)
    public function lowstock() {
        $lowStock = $this->service->getLowStockAlerts();
        return [
            'low_stock' => $lowStock,
            'report_title' => '4. Cảnh báo Hàng sắp hết',
            'view_file' => 'lowstock.php'
        ];
    }

    // 5. Hàng sắp hết hạn (expiringsoon.php)
    public function expiringsoon() {
        $expired = $this->service->getExpiredAlerts();
        return [
            'expired' => $expired,
            'report_title' => '5. Cảnh báo Hàng sắp hết hạn',
            'view_file' => 'expiringsoon.php'
        ];
    }
    
    // 6. Hiệu suất Nhân viên (employeeperformance.php)
    public function employee_performance() {
        $performanceData = $this->service->getEmployeePerformance();
        return [
            'performance_data' => $performanceData,
            'report_title' => '6. Báo cáo Hiệu suất nhân viên',
            'view_file' => 'employeeperformance.php'
        ];
    }
    
    // 7. Báo cáo Nhập Xuất (importreport.php)
    public function io_report() {
        $ioData = $this->service->getIOReport();
        return [
            'io_data' => $ioData,
            'report_title' => '7. Báo cáo Nhập xuất tổng hợp',
            'view_file' => 'importreport.php'
        ];
    }
}
?>