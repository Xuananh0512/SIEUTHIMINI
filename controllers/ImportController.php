<?php
class ImportController {
    private $service;

    public function __construct() {
        $this->service = new ImportService();
    }

    public function list() { 
        // =======================================================
        // ** LOGIC PHÂN TRANG (10 PHIẾU NHẬP/TRANG) **
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

        $imports = $this->service->getPaginated($limit_per_page, $offset);
        
        return [
            'imports' => $imports, 
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records
        ];
    }
    
    public function detail($id) { return $this->service->getById($id); }
    public function search($key) { return $this->service->search($key); }
    public function getById($id) { return $this->service->getById($id); }
    public function add($data) { return $this->service->createImport($data); }

    // THAY THẾ: HÀM DELETE (gọi disable)
    public function delete($id) { 
        $this->service->disable($id);
        $page = $_GET['page'] ?? 1;
        header("Location: " . BASE_URL . "index.php?controller=import&action=list&page=$page");
        exit;
    }
    
    // THÊM: HÀM RESTORE
    public function restore($id) {
        $this->service->restore($id);
        $page = $_GET['page'] ?? 1;
        header("Location: " . BASE_URL . "index.php?controller=import&action=list&page=$page");
        exit;
    }

    public function getAddData() {
        $prodModel = new ProductModel();
        $provModel = new ProvideModel();
        $empModel = new EmployeeModel(); // THÊM EmployeeModel để lấy danh sách người nhập
        
        return [
            'products' => $prodModel->getAll(),
            'providers' => $provModel->getAll(),
            'employees' => $empModel->getAll() // Truyền danh sách nhân viên cho dropdown
        ];
    }
}
?>