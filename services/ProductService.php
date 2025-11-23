<?php
class ProductService {
    private $model;

    // --- QUAN TRỌNG: Phải có Constructor để khởi tạo Model ---
    public function __construct() {
        $this->model = new ProductModel();
    }

    public function countAll($keyword = null, $minPrice = null, $maxPrice = null) { 
        return $this->model->countAll($keyword, $minPrice, $maxPrice); 
    }

    public function getPaginated($limit, $offset, $keyword = null, $minPrice = null, $maxPrice = null) { 
        return $this->model->getPaginated($limit, $offset, $keyword, $minPrice, $maxPrice); 
    }
    
    public function getAll() { return $this->model->getAll(); }
    public function getById($id) { return $this->model->getById($id); }
    
    public function add($data) {
        return $this->model->add(
            $data['maDM'], $data['maNCC'], $data['tenSP'], 
            $data['dongiaBan'], $data['soLuongTon'] ?? 0, 
            $data['donViTinh'], $data['hanSuDung'], $data['moTa']
        );
    }

    public function update($id, $data) {
        return $this->model->update(
            $id, $data['maDM'], $data['maNCC'], $data['tenSP'], 
            $data['dongiaBan'], $data['soLuongTon'] ?? 0, 
            $data['donViTinh'], $data['hanSuDung'], $data['moTa']
        );
    }

    public function delete($id) { return $this->model->delete($id); }
    public function restore($id) { return $this->model->restore($id); }
    public function search($key) { return $this->model->search($key); }
    
    // Hàm này sửa lại để KHÔNG gọi Model, tránh lỗi "undefined method"
    public function getAddData() {
        $catModel = new CategoryModel();
        $provModel = new ProvideModel();
        
        return [
            'categories' => $catModel->getAll(),
            'providers' => $provModel->getAll(),
            'units' => $this->model->getDistinctUnits()
        ];
    }
}
?>