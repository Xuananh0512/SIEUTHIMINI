<?php
class AccountController {
    private $service;

    public function __construct() {
        $this->service = new AccountService();
    }

    public function list() { 
        // =======================================================
        // ** LOGIC PHÂN TRANG (10 TÀI KHOẢN/TRANG) **
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

        $accounts = $this->service->getPaginated($limit_per_page, $offset);
        
        return [
            'accounts' => $accounts,
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records
        ];
    }
    
    public function add($data) { return $this->service->add($data); }
    public function update($id, $data) { return $this->service->update($id, $data); }
    public function search($key) { return $this->service->search($key); }
    public function getById($id) { return $this->service->getById($id); }

    // =======================================================
    // ** THAY THẾ: HÀM DELETE THÀNH LOCK (Khóa TK) **
    // =======================================================
    public function delete($id) { // Giữ tên action delete để phù hợp với index.php
        $this->service->lock($id);
        header("Location: " . BASE_URL . "index.php?controller=account&action=list");
        exit;
    }
    
    // =======================================================
    // ** THÊM: HÀM UNLOCK (Mở Khóa TK) **
    // =======================================================
    public function unlock($id) {
        $this->service->unlock($id);
        header("Location: " . BASE_URL . "index.php?controller=account&action=list");
        exit;
    }

    public function getAddData() {
        // Cần gọi Model của Nhân viên và Vai trò để lấy list
        $empModel = new EmployeeModel();
        $roleModel = new RoleModel();

        return [
            'employees' => $empModel->getAll(),
            'roles' => $roleModel->getAll()
        ];
    }
}
?>