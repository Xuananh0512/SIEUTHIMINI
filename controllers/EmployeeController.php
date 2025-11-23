<?php
class EmployeeController {
    private $service;
    
    public function __construct() { 
        $this->service = new EmployeeService(); 
    }

    // =======================================================
    // 1. HÀM CHUẨN BỊ DỮ LIỆU CHO FORM (ADD & EDIT)
    // index.php sẽ tự động gọi hàm này khi vào trang Thêm hoặc Sửa
    // =======================================================
    public function getAddData() {
        // Lấy danh sách chức vụ để hiển thị trong Dropdown
        $roles = $this->service->getAllRoles();
        
        // Trả về mảng dữ liệu
        return [
            'roles' => $roles
        ];
    }

    // =======================================================
    // 2. HÀM XỬ LÝ LƯU KHI THÊM MỚI (POST)
    // index.php gọi: $ctrl->add($_POST);
    // =======================================================
    public function add($data) {
        // Gọi Service để thêm mới
        // $data ở đây chính là $_POST được truyền từ index.php
        $this->service->add($data);
        
        // Không cần header location ở đây vì index.php đã xử lý redirect rồi
    }

    // =======================================================
    // 3. HÀM XỬ LÝ LƯU KHI CẬP NHẬT (POST)
    // index.php gọi: $ctrl->update($id, $_POST);
    // =======================================================
    public function update($id, $data) {
        // Gọi service update
        // index.php truyền $id và $_POST vào đây
        $this->service->update($id, $data);
    }

    // =======================================================
    // 4. HÀM HIỂN THỊ DANH SÁCH (GIỮ NGUYÊN)
    // =======================================================
    public function list() { 
        $keyword = $_GET['keyword'] ?? '';
        $role_id = $_GET['role'] ?? '';
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';
        
        if ($role_id === '0') $role_id = '';

        $limit_per_page = 10;
        $current_page = $_GET['page'] ?? 1; 
        $current_page = max(1, (int)$current_page); 

        $total_records = $this->service->countAll($keyword, $role_id, $start_date, $end_date); 
        $total_pages = ceil($total_records / $limit_per_page);
        $offset = ($current_page - 1) * $limit_per_page;
        
        if ($current_page > $total_pages && $total_pages > 0) {
            $current_page = $total_pages;
            $offset = ($current_page - 1) * $limit_per_page;
        }

        $employees = $this->service->getPaginated($limit_per_page, $offset, $keyword, $role_id, $start_date, $end_date);
        $roles = $this->service->getAllRoles(); 

        return [
            'employees' => $employees, 
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records,
            'roles' => $roles,
            'search_keyword' => $keyword,
            'search_role' => $role_id,
            'search_start_date' => $start_date,
            'search_end_date' => $end_date
        ];
    }
    
    // =======================================================
    // CÁC HÀM KHÁC (GIỮ NGUYÊN ĐỂ INDEX.PHP GỌI)
    // =======================================================
    
    public function delete($id) { 
        $this->service->delete($id);
        // index.php sẽ lo phần redirect
    }
    
    public function restore($id) {
        $this->service->restore($id);
    }
    
    public function search($key) { return $this->service->search($key); }
    public function getById($id) { return $this->service->getById($id); }
}
?>