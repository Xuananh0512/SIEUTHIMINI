<?php
class EmployeeController {
    private $service;
    public function __construct() { $this->service = new EmployeeService(); }

    public function list() { 
        // 1. Lấy tham số LỌC từ URL
        $keyword = $_GET['keyword'] ?? '';
        $role_id = $_GET['role'] ?? '';
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';
        
        // Cần đảm bảo Role ID là string '0' (Tất cả) hoặc ID hợp lệ
        if ($role_id === '0') {
            $role_id = ''; // Đặt lại rỗng nếu người dùng chọn "Tất cả chức vụ" (giá trị '0')
        }

        // =======================================================
        // ** LOGIC PHÂN TRANG (10 NHÂN VIÊN/TRANG) CÓ LỌC **
        // =======================================================
        $limit_per_page = 10;
        $current_page = $_GET['page'] ?? 1; 
        $current_page = max(1, (int)$current_page); 

        // Tính tổng records CÓ KÈM THEO LỌC
        $total_records = $this->service->countAll($keyword, $role_id, $start_date, $end_date); 
        $total_pages = ceil($total_records / $limit_per_page);
        $offset = ($current_page - 1) * $limit_per_page;
        
        // Điều chỉnh page nếu vượt quá giới hạn
        if ($current_page > $total_pages && $total_pages > 0) {
            $current_page = $total_pages;
            $offset = ($current_page - 1) * $limit_per_page;
        }

        // Lấy dữ liệu phân trang CÓ KÈM THEO LỌC
        $employees = $this->service->getPaginated($limit_per_page, $offset, $keyword, $role_id, $start_date, $end_date);
        
        // Lấy danh sách Vai trò cho bộ lọc (Dropdown) - Cần RoleModel và Service có hàm getAllRoles()
        $roles = $this->service->getAllRoles(); 

        // Trả về dữ liệu cho View (kèm theo các tham số lọc)
        return [
            'employees' => $employees, 
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records,
            'roles' => $roles, // Truyền danh sách vai trò
            'search_keyword' => $keyword, // Truyền lại các giá trị lọc
            'search_role' => $role_id,
            'search_start_date' => $start_date,
            'search_end_date' => $end_date
        ];
    }
    
    public function add($data) { return $this->service->add($data); }
    public function update($id, $data) { return $this->service->update($id, $data); }
    
    // HÀM DELETE (gọi disable)
    public function delete($id) { 
        $this->service->delete($id);
        $page = $_GET['page'] ?? 1;
        header("Location: " . BASE_URL . "index.php?controller=employee&action=list&page=$page");
        exit;
    }
    
    // HÀM RESTORE (Khôi phục)
    public function restore($id) {
        $this->service->restore($id);
        $page = $_GET['page'] ?? 1;
        header("Location: " . BASE_URL . "index.php?controller=employee&action=list&page=$page");
        exit;
    }
    
    // Các hàm khác giữ nguyên
    public function search($key) { return $this->service->search($key); }
    public function getById($id) { return $this->service->getById($id); }
}