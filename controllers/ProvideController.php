<?php
class ProvideController {
    private $service;

    public function __construct() {
        $this->service = new ProvideService();
    }

    public function list() { 
        // =======================================================
        // ** LOGIC PHÂN TRANG MỚI (10 NCC/TRANG) **
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
        $providers = $this->service->getPaginated($limit_per_page, $offset);
        
        // Trả về dữ liệu cho View
        return [
            'providers' => $providers, // <- Biến này sẽ được sử dụng trong list.php
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records
        ];
        // =======================================================
    }

    public function add($data) {
        return $this->service->add($data);
    }

    public function update($id, $data) {
        return $this->service->update($id, $data);
    }

    public function delete($id) {
        try {
            $this->service->delete($id);
        } catch (Exception $e) {
            $_SESSION['error'] = "KHÔNG THỂ XÓA! Nhà cung cấp này đã có lịch sử Nhập hàng hoặc đang cung cấp Sản phẩm.";
        }
        header("Location: " . BASE_URL . "index.php?controller=provide&action=list");
        exit;
    }

    public function search($key) { return $this->service->search($key); }
    public function getById($id) { return $this->service->getById($id); }
}
?>