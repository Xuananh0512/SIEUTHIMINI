<?php
class InvoiceDetailController {
    private $service;
    public function __construct() { $this->service = new InvoiceDetailService(); }
    public function list() { return $this->service->getAll(); }
    public function add($data) { return $this->service->add($data); }
    
    public function update($maHD, $maSP, $data) {
        return $this->service->update($maHD, $maSP, $data);
    }
    
    public function delete($maHD, $maSP) { return $this->service->delete($maHD, $maSP); }
    public function search($key) { return $this->service->search($key); }
    
    public function getById($maHD, $maSP) { return $this->service->getById($maHD, $maSP); }
}
?>