<?php
class CustomerController {
    private $service;
    
    public function __construct() { $this->service = new CustomerService(); }
    
    public function list() { 
        // =======================================================
        // ** LOGIC PHÂN TRANG (10 KHÁCH HÀNG/TRANG) **
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

        $customers = $this->service->getPaginated($limit_per_page, $offset);
        
        return [
            'customers' => $customers,
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records
        ];
    }
    
    public function add($data) { return $this->service->add($data); }
    public function update($id, $data) { return $this->service->update($id, $data); }
    
    // =======================================================
    // ** THAY THẾ: HÀM DELETE THÀNH DISABLE (Ẩn) **
    // =======================================================
    public function delete($id) { 
        $this->service->disable($id); 
        header("Location: " . BASE_URL . "index.php?controller=customer&action=list");
        exit;
    }
    
    // =======================================================
    // ** THÊM: HÀM KHÔI PHỤC **
    // =======================================================
    public function restore($id) { 
        $this->service->restore($id); 
        header("Location: " . BASE_URL . "index.php?controller=customer&action=list");
        exit;
    }
    
    public function search($key) { return $this->service->search($key); }
    public function getById($id) { return $this->service->getById($id); }
}
?>