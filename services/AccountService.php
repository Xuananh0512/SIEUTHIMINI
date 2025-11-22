<?php
class AccountService {
    private $model;
    public function __construct() { $this->model = new AccountModel(); }
    
    public function countAll() { return $this->model->countAll(); }
    public function getPaginated($limit, $offset) { return $this->model->getPaginated($limit, $offset); }
    
    public function getAll() { return $this->model->getAll(); }
    public function getById($id) { return $this->model->getById($id); }

    public function add($data) {
        return $this->model->add(
            $data['maVaiTro'], 
            $data['maNV'], 
            $data['tenDangNhap'], 
            $data['matKhau'],
            $data['trangThai'] ?? 1
        );
    }

    public function update($id, $data) {
        return $this->model->update(
            $id,
            $data['maVaiTro'], 
            $data['maNV'], 
            $data['tenDangNhap'], 
            $data['matKhau'],
            $data['trangThai']
        );
    }

    // =======================================================
    // ** THAY THẾ: HÀM DELETE BẰNG LOCK **
    // =======================================================
    public function lock($id) { return $this->model->lock($id); }
    
    // =======================================================
    // ** THÊM: HÀM UNLOCK **
    // =======================================================
    public function unlock($id) { return $this->model->unlock($id); }
    
    public function search($keyword) { return $this->model->search($keyword); }
}
?>