<?php
class InvoiceDetailService {
    private $model;
    // Sửa tên class Model cho đúng chuẩn Viết Hoa
    public function __construct() { $this->model = new InvoiceDetailModel(); }
    public function getAll() { return $this->model->getAll(); }

    // Bảng chi tiết cần 2 khóa chính để xác định
    public function getById($maHD, $maSP) { return $this->model->getById($maHD, $maSP); }

    public function add($data) {
        return $this->model->add($data['maHD'], $data['maSP'], $data['soLuong'], $data['donGiaLucMua'], $data['thanhTien']);
    }

    public function update($maHD, $maSP, $data) {
        return $this->model->update($maHD, $maSP, $data['soLuong'], $data['donGiaLucMua'], $data['thanhTien']);
    }

    public function delete($maHD, $maSP) { return $this->model->delete($maHD, $maSP); }
    public function search($maHD) { return $this->model->search($maHD); }
}
?>