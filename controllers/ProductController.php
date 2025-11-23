<?php
class ProductController {
    private $service;

    public function __construct() {
        $this->service = new ProductService();
    }

    public function list() {
        $search_name = $_GET['search_name'] ?? null;
        $price_min   = $_GET['price_min'] ?? null;
        $price_max   = $_GET['price_max'] ?? null;

        $limit_per_page = 10;
        $current_page = $_GET['page'] ?? 1; 
        $current_page = max(1, (int)$current_page); 

        $total_records = $this->service->countAll($search_name, $price_min, $price_max); 
        $total_pages = ceil($total_records / $limit_per_page);
        
        if ($current_page > $total_pages && $total_pages > 0) {
            $current_page = $total_pages;
        }
        $offset = ($current_page - 1) * $limit_per_page;
        if ($offset < 0) $offset = 0;

        $products = $this->service->getPaginated($limit_per_page, $offset, $search_name, $price_min, $price_max);
        
        return [
            'products' => $products, 
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'total_records' => $total_records,
            'search_name' => $search_name,
            'price_min' => $price_min,
            'price_max' => $price_max
        ];
    }

    public function add($data) { return $this->service->add($data); }
    public function update($id, $data) { return $this->service->update($id, $data); }
    public function search($key) { return $this->service->search($key); }
    public function getById($id) { return $this->service->getById($id); }
    public function getAddData() { return $this->service->getAddData(); }

    // Hàm Ẩn sản phẩm
    public function delete($id) {
        $this->service->delete($id);
        header("Location: index.php?controller=product&action=list");
        exit;
    }

    // Hàm Khôi phục sản phẩm
    public function restore($id) {
        $this->service->restore($id);
        header("Location: index.php?controller=product&action=list");
        exit;
    }
}
?>