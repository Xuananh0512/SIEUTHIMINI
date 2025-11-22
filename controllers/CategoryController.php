<?php
class CategoryController {
    private $service;

    public function __construct() {
        $this->service = new CategoryService();
    }

    public function list() { 
        // =======================================================
        // ** LOGIC PHÂN TRANG (10 DANH MỤC/TRANG) **
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

        $categories = $this->service->getPaginated($limit_per_page, $offset);
        
        return [
            'categories' => $categories, 
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records
        ];
    }
    
    public function add($data) { return $this->service->add($data); }
    public function update($id, $data) { return $this->service->update($id, $data); }

    // HÀM DELETE (gọi disable)
    public function delete($id) {
        try {
            $this->service->disable($id); // Gọi hàm disable trong Service
            $_SESSION['success'] = "Ẩn danh mục thành công!";
        } catch (Exception $e) {
            $_SESSION['error'] = "KHÔNG THỂ ẨN: " . $e->getMessage();
        }
        
        $page = $_GET['page'] ?? 1;
        header("Location: " . BASE_URL . "index.php?controller=category&action=list&page=$page");
        exit;
    }
    
    // THÊM: HÀM RESTORE
    public function restore($id) {
        $this->service->restore($id);
        $_SESSION['success'] = "Khôi phục danh mục thành công!";
        
        $page = $_GET['page'] ?? 1;
        header("Location: " . BASE_URL . "index.php?controller=category&action=list&page=$page");
        exit;
    }

    public function search($key) { return $this->service->search($key); }
    public function getById($id) { return $this->service->getById($id); }
}
?>