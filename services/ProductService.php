<?php
class ProductService {
    private $model;
    public function __construct() { $this->model = new ProductModel(); }

    // Cập nhật hàm countAll nhận tham số
// Trong ProductService.php

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
            $data['maDM'], 
            $data['maNCC'], 
            $data['tenSP'], 
            $data['dongiaBan'], 
            $data['soLuongTon'] ?? 0, 
            $data['donViTinh'], 
            $data['hanSuDung'], 
            $data['moTa']
        );
    }

    public function update($id, $data) {
        return $this->model->update(
            $id,
            $data['maDM'], 
            $data['maNCC'], 
            $data['tenSP'], 
            $data['dongiaBan'], 
            $data['soLuongTon'] ?? 0, 
            $data['donViTinh'], 
            $data['hanSuDung'], 
            $data['moTa']
        );
    }
    public function delete($id) { return $this->model->delete($id); }
    public function search($key) { return $this->model->search($key); }
    
    // Hàm này chỉ được gọi bởi Controller để lấy data cho form
    public function getAddData() {
        $catModel = new CategoryModel();
        $provModel = new ProvideModel();
        $prodModel = new ProductModel();
        
        return [
            'categories' => $catModel->getAll(),
            'providers' => $provModel->getAll(),
            'units' => $prodModel->getDistinctUnits()
        ];
    }

    
}
?>