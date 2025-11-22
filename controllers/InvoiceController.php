<?php
class InvoiceController {
    private $service;

    public function __construct() {
        $this->service = new InvoiceService();
    }

    public function list() { 
        // 1. Lấy tham số tìm kiếm
        $date_from = $_GET['date_from'] ?? null;
        $date_to   = $_GET['date_to'] ?? null;
        $min_total = $_GET['min_total'] ?? null;
        $max_total = $_GET['max_total'] ?? null;

        // Phân trang settings
        $limit_per_page = 10;
        $current_page = $_GET['page'] ?? 1; 
        $current_page = max(1, (int)$current_page); 

        // 2. Gọi Service với tham số lọc
        $total_records = $this->service->countAll($date_from, $date_to, $min_total, $max_total); 
        $total_pages = ceil($total_records / $limit_per_page);
        
        if ($current_page > $total_pages && $total_pages > 0) {
            $current_page = $total_pages;
        }
        $offset = ($current_page - 1) * $limit_per_page;
        if ($offset < 0) $offset = 0;

        // 3. Lấy dữ liệu chi tiết với tham số lọc
        $invoices = $this->service->getPaginated($limit_per_page, $offset, $date_from, $date_to, $min_total, $max_total);
        
        return [
            'invoices' => $invoices, 
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records,
            // Trả lại tham số để view hiển thị
            'date_from' => $date_from,
            'date_to' => $date_to,
            'min_total' => $min_total,
            'max_total' => $max_total
        ];
    }
    
    public function detail($id) { return $this->service->getById($id); }
    public function search($key) { return $this->service->search($key); }
    public function getById($id) { return $this->service->getById($id); }

    public function add($data) {
        return $this->service->createInvoice($data);
    }
    
    // HÀM DELETE (gọi disable)
    public function delete($id) { 
        $this->service->delete($id);
        $page = $_GET['page'] ?? 1;
        header("Location: " . BASE_URL . "index.php?controller=invoice&action=list&page=$page");
        exit;
    }
    
    // ✅ THÊM: HÀM RESTORE (Khôi phục)
    public function restore($id) {
        $this->service->restore($id);
        $page = $_GET['page'] ?? 1;
        header("Location: " . BASE_URL . "index.php?controller=invoice&action=list&page=$page");
        exit;
    }

    public function getAddData() {
        $prodModel = new ProductModel();
        $custModel = new CustomerModel();
        $empModel = new EmployeeModel();
        
        return [
            'products' => $prodModel->getAll(),
            'customers' => $custModel->getAll(),
            'employees' => $empModel->getAll()
        ];
    }
}
?>