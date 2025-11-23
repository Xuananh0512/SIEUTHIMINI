<?php
class HomeController {
    private $model;

    public function __construct() {
        // Đảm bảo bạn đã có file HomeModel.php
        $this->model = new HomeModel();
    }

    public function index() {
        // 1. Lấy số liệu thống kê
        $countProduct  = $this->model->countTable('sanpham');
        $countCategory = $this->model->countTable('danhmuc');
        $countEmployee = $this->model->countTable('nhanvien');
        $totalRevenue  = $this->model->getTotalRevenue();

        // 2. Lấy dữ liệu biểu đồ năm nay
        $currentYear = date('Y');
        $chartData   = $this->model->getMonthlyRevenue($currentYear);

        // 3. Lấy hoạt động gần đây
        $recentActivities = $this->model->getRecentActivities();

        // 4. Trả về dữ liệu cho View
        return [
            'countProduct' => $countProduct,
            'countCategory' => $countCategory,
            'countEmployee' => $countEmployee,
            'totalRevenue' => $totalRevenue,
            'chartData' => json_encode($chartData), // Chuyển sang JSON để JS đọc được
            'recentActivities' => $recentActivities
        ];
    }
}
?>