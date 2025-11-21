<?php
class CategoryService {
    private $model;

    public function __construct() {
        $this->model = new CategoryModel();
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
    public function getAll() {
        return $this->model->getAll();
    }

    public function getById($id) {
        return $this->model->getById($id);
    }

    public function add($data) {
        $tenDM = $data['tenDM'] ?? '';
        
        if (!empty(trim($tenDM))) {
            return $this->model->add($tenDM);
        }
        return false;
    }

    public function update($id, $data) {
        $tenDM = $data['tenDM'] ?? '';
        if (!empty(trim($tenDM))) {
            return $this->model->update($id, $tenDM);
        }
        return false;
    }

    public function delete($id) {
        return $this->model->delete($id);
    }

    public function search($keyword) {
        return $this->model->search($keyword);
    }
}
?>  