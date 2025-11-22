
<?php
class ReportController {
    private $service;

    public function __construct() {
        $this->service = new ReportService();
    }
    
    // Hàm hỗ trợ lấy ngày bắt đầu/kết thúc mặc định
    private function getDateRange() {
        $startDate = $_GET['start_date'] ?? date('Y-m-01'); // Mặc định là đầu tháng
        $endDate = $_GET['end_date'] ?? date('Y-m-d');      // Mặc định là ngày hiện tại
        return ['startDate' => $startDate, 'endDate' => $endDate];
    }

    // =======================================================
    // ** BÁO CÁO 1: NHẬP HÀNG (importreport) **
    // =======================================================
    public function importreport() {
        $range = $this->getDateRange();
        $data = $this->service->getImportReportData($range['startDate'], $range['endDate']);
        
        return [
            'reportData' => $data,
            'startDate' => $range['startDate'],
            'endDate' => $range['endDate']
        ];
    }
    
    // =======================================================
    // ** BÁO CÁO 2: DOANH THU (revenue) **
    // =======================================================
    public function revenue() {
        $range = $this->getDateRange();
        $data = $this->service->getRevenueReportData($range['startDate'], $range['endDate']);
        
        return [
            'reportData' => $data,
            'startDate' => $range['startDate'],
            'endDate' => $range['endDate']
        ];
    }

    // =======================================================
    // ** BÁO CÁO 3: BÁN CHẠY (topselling) **
    // =======================================================
    public function topselling() {
        $limit = $_GET['limit'] ?? 10;
        $limit = max(5, (int)$limit);

        $data = $this->service->getTopSellingData($limit);
        
        return [
            'reportData' => $data,
            'limit' => $limit
        ];
    }

    // =======================================================
    // ** BÁO CÁO 4: HIỆU SUẤT NV (employeeperformance) **
    // =======================================================
    public function employeeperformance() {
        $range = $this->getDateRange();
        $data = $this->service->getEmployeePerformanceData($range['startDate'], $range['endDate']);
        
        return [
            'reportData' => $data,
            'startDate' => $range['startDate'],
            'endDate' => $range['endDate']
        ];
    }

    // =======================================================
    // ** BÁO CÁO 5: TỒN KHO THẤP (lowstock) **
    // =======================================================
    public function lowstock() {
        $threshold = $_GET['threshold'] ?? 5; // Cho phép người dùng tùy chỉnh ngưỡng
        $threshold = max(1, (int)$threshold);

        $data = $this->service->getLowStockData($threshold);

        return [
            'reportData' => $data,
            'threshold' => $threshold
        ];
    }

    // =======================================================
    // ** BÁO CÁO 6: SẮP HẾT HẠN (expiringsoon) **
    // =======================================================
    public function expiringsoon() {
        $days = $_GET['days'] ?? 30; // Cho phép người dùng tùy chỉnh số ngày
        $days = max(7, (int)$days); // Đảm bảo không quá ít

        $data = $this->service->getExpiringSoonData($days);
        
        return [
            'reportData' => $data,
            'days' => $days
        ];
    }
}
?>