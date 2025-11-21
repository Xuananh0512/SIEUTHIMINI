<?php
class ProductController {
    private $service;

    public function __construct() {
        $this->service = new ProductService();
    }

    public function list() {
        // =======================================================
        // ** LOGIC PHÂN TRANG (10 SẢN PHẨM/TRANG) **
        // =======================================================
        
        $limit_per_page = 10;
        $current_page = $_GET['page'] ?? 1; 
        $current_page = max(1, (int)$current_page); 

        $total_records = $this->service->countAll(); 
        $total_pages = ceil($total_records / $limit_per_page);
        $offset = ($current_page - 1) * $limit_per_page;
        
        if ($current_page > $total_pages && $total_pages > 0) {
            $current_page = $total_pages;
            $offset = ($current_page - 1) * $limit_per_page;
        }

        $products = $this->service->getPaginated($limit_per_page, $offset);
        
        return [
            'products' => $products, 
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records
        ];
    }

    public function add($data) {
        return $this->service->add($data);
    }

    public function update($id, $data) {
        return $this->service->update($id, $data);
    }

    public function delete($id) {
        return $this->service->delete($id);
    }

    public function search($key) {
        return $this->service->search($key);
    }

    public function getById($id) {
        return $this->service->getById($id);
    }

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