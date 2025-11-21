<?php
class RoleService {
    private $model;

    public function __construct() {
        $this->model = new RoleModel();
    }

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
        return $this->model->add($data['tenVaiTro']);
    }

    public function update($id, $data) {
        return $this->model->update($id, $data['tenVaiTro']);
    }

    public function delete($id) { return $this->model->delete($id); }
    public function search($keyword) { return $this->model->search($keyword); }
}
?>