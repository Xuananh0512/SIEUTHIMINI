
<?php
class ReportService {
    private $model;

    public function __construct() {
        $this->model = new ReportModel();
    }
    
    // Báo cáo 1: Nhập hàng
    public function getImportReportData($startDate, $endDate) {
        return $this->model->getImportReport($startDate, $endDate);
    }

    // Báo cáo 2: Doanh thu
    public function getRevenueReportData($startDate, $endDate) {
        return $this->model->getRevenueReport($startDate, $endDate);
    }
    
    // Báo cáo 3: Bán chạy
    public function getTopSellingData($limit = 10) {
        return $this->model->getTopSellingProducts($limit);
    }
    
    // Báo cáo 4: Hiệu suất NV
    public function getEmployeePerformanceData($startDate, $endDate) {
        return $this->model->getEmployeePerformance($startDate, $endDate);
    }

    // Báo cáo 5: Tồn kho thấp
    public function getLowStockData($threshold = 5) {
        return $this->model->getLowStock($threshold);
    }

    // Báo cáo 6: Sắp hết hạn
    public function getExpiringSoonData($days = 30) {
        return $this->model->getExpiringSoon($days);
    }
}
?>