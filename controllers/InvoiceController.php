<?php
class InvoiceController {
    private $service;

    public function __construct() {
        $this->service = new InvoiceService();
    }

    public function list() { 
        // =======================================================
        // ** LOGIC PHÂN TRANG (10 HÓA ĐƠN/TRANG) **
        // =======================================================
        $limit_per_page = 10;
        $current_page = $_GET['page'] ?? 1; 
        $current_page = max(1, (int)$current_page); 

        $total_records = $this->service->countAll(); 
        $total_pages = ceil($total_records / $limit_per_page);
        $offset = ($current_page - 1) * $limit_per_page;
        
        if ($current_page > $total_pages && $total_pages > 0) {
            $current_page = $total_pages;
            $offset = ($current_page - 1) * $limit_per_page;
        }

        $invoices = $this->service->getPaginated($limit_per_page, $offset);
        
        return [
            'invoices' => $invoices, 
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records
        ];
    }
    
    public function detail($id) { return $this->service->getById($id); }
    public function delete($id) { return $this->service->delete($id); }
    public function search($key) { return $this->service->search($key); }
    public function getById($id) { return $this->service->getById($id); }

    public function add($data) {
        // Sau khi xử lý xong, Controller sẽ chuyển hướng (trong index.php)
        return $this->service->createInvoice($data);
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