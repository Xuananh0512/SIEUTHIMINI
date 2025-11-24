<?php
class ProvideController {
    private $service;

    public function __construct() {
        $this->service = new ProvideService();
    }

    public function list() { 
        // =======================================================
        // ** LOGIC PHÂN TRANG (10 NCC/TRANG) **
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

        $providers = $this->service->getPaginated($limit_per_page, $offset);
        
        return [
            'providers' => $providers,
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records
        ];
    }
    
    // Trong file ProvideController.php -> function add()

    // Trong ProvideController.php

public function add($data) {
    // 1. Lấy và làm sạch dữ liệu
    $sdt = trim($data['soDienThoai'] ?? '');
    $ten = trim($data['tenNCC'] ?? '');
    $diaChi = trim($data['diaChi'] ?? '');
    
    $error = null;

    // 2. VALIDATION (Kiểm tra)
    if (empty($ten)) {
        $error = "Tên nhà cung cấp không được để trống.";
    } 
    // Chỉ kiểm tra SĐT nếu người dùng có nhập
    elseif (!empty($sdt)) {
        // Check Regex
        if (!preg_match('/^0[0-9]{9,10}$/', $sdt)) {
            $error = "Số điện thoại không hợp lệ (Phải bắt đầu bằng 0 và dài 10-11 số).";
        } 
        // Check Trùng lặp (GỌI SERVICE VỪA THÊM)
        elseif ($this->service->checkPhoneExists($sdt)) {
            $error = "Số điện thoại này đã được đăng ký cho nhà cung cấp khác.";
        }
    }

    // 3. XỬ LÝ LỖI
    if ($error) {
        $_SESSION['error'] = $error;
        $_SESSION['old_data'] = $data; // Lưu lại dữ liệu để form không bị trắng trơn
        
        // Quay lại trang thêm mới
        header("Location: index.php?controller=provide&action=add");
        exit; 
    }

    // 4. THÀNH CÔNG -> LƯU VÀ VỀ LIST
    if ($this->service->add($data)) {
        $_SESSION['success'] = "Thêm nhà cung cấp thành công!";
        if(isset($_SESSION['old_data'])) unset($_SESSION['old_data']); // Xóa dữ liệu cũ
        
        header("Location: index.php?controller=provide&action=list");
        exit;
    } else {
        $_SESSION['error'] = "Có lỗi xảy ra khi lưu dữ liệu.";
        header("Location: index.php?controller=provide&action=add");
        exit;
    }
}

    public function update($id, $data) {
        // 1. Lấy dữ liệu và làm sạch
        $sdt = trim($data['soDienThoai'] ?? '');
        $ten = trim($data['tenNCC'] ?? '');
        $diaChi = trim($data['diaChi'] ?? '');
        
        $error = null;

        // 2. VALIDATION
        // Kiểm tra tên
        if (empty($ten)) {
            $error = "Tên nhà cung cấp không được để trống.";
        }
        // Kiểm tra SĐT nếu người dùng có nhập
        elseif (!empty($sdt)) {
            // Check Regex
            if (!preg_match('/^0[0-9]{9,10}$/', $sdt)) {
                $error = "Số điện thoại không hợp lệ (Phải bắt đầu bằng 0 và dài 10-11 số).";
            } 
            // Check Trùng lặp (loại trừ chính nó)
            elseif ($this->service->checkPhoneExistsExcept($sdt, $id)) {
                $error = "Số điện thoại này đã được đăng ký cho nhà cung cấp khác.";
            }
        }

        // 3. Xử lý khi có lỗi
        if ($error) {
            $_SESSION['error'] = $error;
            $_SESSION['old_data'] = $data;
            
            header("Location: index.php?controller=provide&action=edit&id=" . $id);
            exit;
        }

        // 4. Nếu hợp lệ -> Gọi service cập nhật
        try {
            $this->service->update($id, $data);
            $_SESSION['success'] = "Cập nhật nhà cung cấp thành công!";
            
            if(isset($_SESSION['old_data'])) unset($_SESSION['old_data']);

            header("Location: index.php?controller=provide&action=list");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
            $_SESSION['old_data'] = $data;
            header("Location: index.php?controller=provide&action=edit&id=" . $id);
            exit;
        }
    }

    public function delete($id) {
        try {
            $this->service->delete($id);
            $_SESSION['success'] = "Ẩn Nhà cung cấp thành công!";
        } catch (Exception $e) {
            $_SESSION['error'] = "KHÔNG THỂ ẨN: " . $e->getMessage();
        }
        
        $page = $_GET['page'] ?? 1;
        header("Location: " . BASE_URL . "index.php?controller=provide&action=list&page=$page");
        exit;
    }
    
    public function restore($id) {
        $this->service->restore($id);
        $_SESSION['success'] = "Khôi phục Nhà cung cấp thành công!";
        
        $page = $_GET['page'] ?? 1;
        header("Location: " . BASE_URL . "index.php?controller=provide&action=list&page=$page");
        exit;
    }

    public function search($key) { return $this->service->search($key); }
    public function getById($id) { return $this->service->getById($id); }
}
?>