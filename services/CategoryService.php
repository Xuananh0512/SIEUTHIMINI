<?php
class CategoryService {
    private $model;

    public function __construct() {
        $this->model = new CategoryModel();
    }

    public function countAll() { return $this->model->countAll(); }
    public function getPaginated($limit, $offset) { return $this->model->getPaginated($limit, $offset); }

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

    // =======================================================
    // ** THAY THẾ: HÀM DELETE THÀNH DISABLE (Ẩn) **
    // =======================================================
    public function disable($id) {
        // Kiểm tra logic nghiệp vụ: Danh mục còn sản phẩm không?
        $productModel = new ProductModel(); // Giả định ProductModel được autoload
        
        if ($productModel->countByCategoryId($id) > 0) {
             throw new Exception("Danh mục này vẫn còn sản phẩm liên quan.");
        }
        
        return $this->model->disable($id);
    }
    
    // =======================================================
    // ** THÊM: HÀM RESTORE (Khôi phục) **
    // =======================================================
    public function restore($id) {
        return $this->model->restore($id);
    }

    public function delete($id) { return $this->disable($id); } // Giữ tên delete cho Controller gọi
    public function search($keyword) { return $this->model->search($keyword); }
}
?>