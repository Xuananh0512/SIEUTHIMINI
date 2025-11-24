<?php
class InvoiceController {
    private $service;

    public function __construct() {
        $this->service = new InvoiceService();
    }

    // Hàm dùng chung để đóng gói dữ liệu trả về cho View (tránh lặp code)
    private function prepareData($invoices, $total_records, $current_page, $limit_per_page) {
        $total_pages = ceil($total_records / $limit_per_page);
        
        return [
            'invoices'      => $invoices,
            'total_pages'   => $total_pages,
            'current_page'  => $current_page,
            'total_records' => $total_records,
            // Trả lại các tham số lọc để View hiển thị lại trên form
            'date_from'     => $_GET['date_from'] ?? '',
            'date_to'       => $_GET['date_to'] ?? '',
            'min_total'     => $_GET['min_total'] ?? '', // Chú ý tên biến khớp với form
            'max_total'     => $_GET['max_total'] ?? '',
            'customer_name' => $_GET['customer_name'] ?? '',
            'employee_name' => $_GET['employee_name'] ?? ''
        ];
    }

    public function list() { 
        $limit_per_page = 10;
        $current_page = max(1, (int)($_GET['page'] ?? 1)); 
        $offset = ($current_page - 1) * $limit_per_page;

        // Lấy tham số lọc từ URL
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo   = $_GET['date_to'] ?? null;
        $minTotal = $_GET['min_total'] ?? null;
        $maxTotal = $_GET['max_total'] ?? null;

        // Gọi Service để lấy dữ liệu
        $total_records = $this->service->countAll($dateFrom, $dateTo, $minTotal, $maxTotal); 
        $invoices = $this->service->getPaginated($limit_per_page, $offset, $dateFrom, $dateTo, $minTotal, $maxTotal);
        
        return $this->prepareData($invoices, $total_records, $current_page, $limit_per_page);
    }

    // Hàm tìm kiếm nâng cao
    public function search() {
        $limit_per_page = 10;
        $current_page = max(1, (int)($_GET['page'] ?? 1)); 
        
        // Gom toàn bộ tham số tìm kiếm thành mảng
        $filters = [
            'date_from'     => $_GET['date_from'] ?? '',
            'date_to'       => $_GET['date_to'] ?? '',
            'price_min'     => $_GET['min_total'] ?? '', 
            'price_max'     => $_GET['max_total'] ?? '',
            'customer_name' => $_GET['customer_name'] ?? '',
            'employee_name' => $_GET['employee_name'] ?? ''
        ];

        // Gọi Service xử lý tìm kiếm
        $invoices = $this->service->searchAdvanced($filters);
        $total_records = count($invoices); // Đếm tổng số kết quả tìm được

        // Trả về dữ liệu đúng cấu trúc để View không bị lỗi
        return $this->prepareData($invoices, $total_records, 1, $total_records); 
    }

    public function add($data) {
        return $this->service->createInvoice($data);
    }

    // --- HÀM LẤY DỮ LIỆU CHO FORM THÊM MỚI (Sản phẩm, Khách, Nhân viên) ---
    public function getAddData() {
        $prodModel = new ProductModel();
        $custModel = new CustomerModel();
        $empModel = new EmployeeModel(); 
        
        return [
            'products'  => $prodModel->getAll(),
            'customers' => $custModel->getAll(),
            'employees' => $empModel->getAll()
        ];
    }

    public function detail($id) { return $this->service->getById($id); }
    public function getById($id) { return $this->service->getById($id); }
    
    public function delete($id) { 
        $this->service->delete($id);
        header("Location: " . BASE_URL . "index.php?controller=invoice&action=list");
        exit;
    }
    
    public function restore($id) {
        $this->service->restore($id);
        header("Location: " . BASE_URL . "index.php?controller=invoice&action=list");
        exit;
    }
}
?>