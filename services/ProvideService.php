<?php
class ProvideService {
    private $model;
    public function __construct() { $this->model = new ProvideModel(); }

    // =======================================================
    // ** THÊM: HÀM DỊCH VỤ PHÂN TRANG **
    // =======================================================
    public function countAll() {
        return $this->model->countAll();
    }

    public function getPaginated($limit, $offset) {
        return $this->model->getPaginated($limit, $offset);
    }

    // Các hàm còn lại
    public function getAll() { return $this->model->getAll(); }
    public function getById($id) { return $this->model->getById($id); }

    public function add($data) {
        return $this->model->add($data['tenNCC'], $data['soDienThoai'], $data['diaChi']);
    }

    public function update($id, $data) {
        return $this->model->update($id, $data['tenNCC'], $data['soDienThoai'], $data['diaChi']);
    }

    public function delete($id) { return $this->model->delete($id); }
    public function search($keyword) { return $this->model->search($keyword); }
}
?>