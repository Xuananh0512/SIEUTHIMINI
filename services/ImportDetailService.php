<?php
class ImportDetailService {
    private $model;
    // Sửa lỗi tên class Model bị thừa chữ Model
    public function __construct() { $this->model = new ImportDetailModel(); } 
    public function getAll() { return $this->model->getAll(); }
    
    public function getById($maSP, $maPN) { return $this->model->getById($maSP, $maPN); }

    public function add($data) {
        return $this->model->add($data['maSP'], $data['maPN'], $data['soLuong'], $data['giaNhap'], $data['thanhTien']);
    }

    public function update($maSP, $maPN, $data) {
        return $this->model->update($maSP, $maPN, $data['soLuong'], $data['giaNhap'], $data['thanhTien']);
    }

    public function delete($maSP, $maPN) { return $this->model->delete($maSP, $maPN); }
    public function search($maPN) { return $this->model->search($maPN); }
}
?>