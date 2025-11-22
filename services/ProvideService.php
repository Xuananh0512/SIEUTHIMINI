<?php
class ProvideService {
    private $model;
    public function __construct() { $this->model = new ProvideModel(); }

    public function countAll() { return $this->model->countAll(); }
    public function getPaginated($limit, $offset) { return $this->model->getPaginated($limit, $offset); }
    public function getAll() { return $this->model->getAll(); }
    public function getById($id) { return $this->model->getById($id); }

    public function add($data) { return $this->model->add($data['tenNCC'], $data['soDienThoai'], $data['diaChi']); }
    public function update($id, $data) { return $this->model->update($id, $data['tenNCC'], $data['soDienThoai'], $data['diaChi']); }

    // =======================================================
    // ** HÀM DELETE (Thực hiện kiểm tra nghiệp vụ) **
    // =======================================================
    public function delete($id) { 
        $productCount = $this->model->countRelatedProducts($id);
        $importCount = $this->model->countRelatedImports($id);

        if ($productCount > 0 || $importCount > 0) {
             throw new Exception("Nhà cung cấp này vẫn còn liên kết: $productCount sản phẩm và $importCount phiếu nhập.");
        }
        
        return $this->model->disable($id); 
    }
    
    // =======================================================
    // ** THÊM: HÀM RESTORE **
    // =======================================================
    public function restore($id) {
        return $this->model->restore($id);
    }
    
    public function search($keyword) { return $this->model->search($keyword); }
}
?>