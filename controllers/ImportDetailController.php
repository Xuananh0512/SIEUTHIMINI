<?php
class ImportDetailController {
    private $service;
    
    public function __construct() { $this->service = new ImportDetailService(); }
    public function list() { return $this->service->getAll(); }
    public function add($data) { return $this->service->add($data); }
    
    public function update($maSP, $maPN, $data) {
        // Lưu ý: controller này cần nhận 2 ID
        return $this->service->update($maSP, $maPN, $data); 
    }
    
    public function delete($maSP, $maPN) { return $this->service->delete($maSP, $maPN); }
    public function search($key) { return $this->service->search($key); }
    
    // Hàm getById cho bảng chi tiết cần 2 khóa chính
    public function getById($maSP, $maPN) { return $this->service->getById($maSP, $maPN); }
}
?>