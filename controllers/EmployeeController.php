<?php
class EmployeeController {
    private $service;
    public function __construct() { $this->service = new EmployeeService(); }

    public function list() { 
        // =======================================================
        // ** LOGIC PHÂN TRANG MỚI (10 NHÂN VIÊN/TRANG) **
        // =======================================================
        $limit_per_page = 10;
        $current_page = $_GET['page'] ?? 1; 
        $current_page = max(1, (int)$current_page); 

        $total_records = $this->service->countAll(); 
        $total_pages = ceil($total_records / $limit_per_page);
        $offset = ($current_page - 1) * $limit_per_page;
        
        // Điều chỉnh page nếu vượt quá giới hạn
        if ($current_page > $total_pages && $total_pages > 0) {
            $current_page = $total_pages;
            $offset = ($current_page - 1) * $limit_per_page;
        }

        // Lấy dữ liệu phân trang
        $employees = $this->service->getPaginated($limit_per_page, $offset);
        
        // Trả về dữ liệu cho View
        return [
            'employees' => $employees, // Biến này sẽ được sử dụng trong list.php
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records
        ];
        // =======================================================
    }
    
    public function add($data) { return $this->service->add($data); }
    public function update($id, $data) { return $this->service->update($id, $data); }
    public function delete($id) { return $this->service->delete($id); }
    public function search($key) { return $this->service->search($key); }
    public function getById($id) { return $this->service->getById($id); }

    // --- HÀM MỚI: KHÔI PHỤC ---\
    public function restore($id) {
        $this->service->restore($id);
        header("Location: " . BASE_URL . "index.php?controller=employee&action=list");
        exit;
    }

    // --- QUAN TRỌNG: HÀM NÀY GIÚP HIỆN DROPDOWN CHỨC VỤ ---\
    public function getAddData() {
        $roleModel = new RoleModel();
        return ['roles' => $roleModel->getAll()];
    }
}
?>