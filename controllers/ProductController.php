<?php
class ProductController {
    private $service;

    public function __construct() {
        $this->service = new ProductService();
    }

    // Trong ProductController.php -> function list()

public function list() {
        // 1. Lấy tham số từ GET
        $search_name = $_GET['search_name'] ?? null;
        $price_min   = $_GET['price_min'] ?? null;
        $price_max   = $_GET['price_max'] ?? null;

        $limit_per_page = 10;
        $current_page = $_GET['page'] ?? 1; 
        $current_page = max(1, (int)$current_page); 

        // 2. Truyền đủ 3 tham số vào service
        $total_records = $this->service->countAll($search_name, $price_min, $price_max); 
        $total_pages = ceil($total_records / $limit_per_page);
        
        if ($current_page > $total_pages && $total_pages > 0) {
            $current_page = $total_pages;
        }
        $offset = ($current_page - 1) * $limit_per_page;
        if ($offset < 0) $offset = 0;

        // 3. Truyền đủ 3 tham số vào hàm lấy dữ liệu
        $products = $this->service->getPaginated($limit_per_page, $offset, $search_name, $price_min, $price_max);
        
        return [
            'products' => $products, 
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records,
            // Trả lại giá trị để View hiển thị
            'search_name' => $search_name,
            'price_min' => $price_min,
            'price_max' => $price_max
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