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
    
    // Thay thế hàm add() trong CustomerController.php

    public function add($data) {
        // 1. Lấy dữ liệu và làm sạch
        $sdt = trim($data['soDienThoai'] ?? '');
        $email = trim($data['email'] ?? '');
        $hoTen = trim($data['hoTenKH'] ?? '');

        $error = null;

        // 2. VALIDATION
        // Kiểm tra tên
        if (empty($hoTen)) {
            $error = "Vui lòng nhập họ tên khách hàng.";
        }
        // Kiểm tra SĐT: Rỗng? Regex? Trùng?
        elseif (empty($sdt)) {
            $error = "Vui lòng nhập số điện thoại.";
        } elseif (!preg_match('/^0[0-9]{9,10}$/', $sdt)) {
            $error = "Số điện thoại không hợp lệ (Phải bắt đầu bằng 0 và có 10-11 số).";
        } elseif ($this->service->checkPhoneExists($sdt)) {
            $error = "Số điện thoại này đã được đăng ký cho khách hàng khác.";
        }
        // Kiểm tra Email (Nếu bắt buộc nhập)
        elseif (empty($email)) {
            $error = "Vui lòng nhập Email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Định dạng Email không hợp lệ.";
        } elseif ($this->service->checkEmailExists($email)) {
            $error = "Email này đã được sử dụng bởi khách hàng khác.";
        }

        // 3. XỬ LÝ KHI CÓ LỖI
        if ($error) {
            $_SESSION['error'] = $error;
            $_SESSION['old_data'] = $data; // Lưu lại dữ liệu để điền lại form
            
            // Quay lại trang thêm mới ngay lập tức
            header("Location: index.php?controller=customer&action=add");
            exit; 
        }

        // 4. NẾU HỢP LỆ -> GỌI SERVICE LƯU
        try {
            $this->service->add($data);
            $_SESSION['success'] = "Thêm khách hàng thành công!";
            
            // Xóa dữ liệu cũ session nếu có
            if(isset($_SESSION['old_data'])) unset($_SESSION['old_data']);

            header("Location: index.php?controller=customer&action=list");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
            $_SESSION['old_data'] = $data;
            header("Location: index.php?controller=customer&action=add");
            exit;
        }
    }
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