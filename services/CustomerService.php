<?php
class CustomerService {
    private $model;
    public function __construct() { $this->model = new CustomerModel(); }
    
    public function countAll() { return $this->model->countAll(); }
    public function getPaginated($limit, $offset) { return $this->model->getPaginated($limit, $offset); }
    
    public function getAll() { return $this->model->getAll(); }
    public function getById($id) { return $this->model->getById($id); }

    public function add($data) {
        return $this->model->add(
            $data['hoTenKH'], 
            $data['soDienThoai'], 
            $data['diaChi'], 
            $data['ngaySinh'], 
            $data['email'],
            $data['diemTichLuy'] ?? 0
        );
    }

    public function update($id, $data) {
   
    // maKH, hoTenKH, soDienThoai, diaChi, ngaySinh, email, diemTichLuy, trangThai
    return $this->model->update(
        $id,
        $data['hoTenKH'], 
        $data['soDienThoai'], 
        $data['diaChi'], 
        $data['ngaySinh'], 
        $data['email'],
        $data['diemTichLuy'],
        $data['trangThai']
    );
}

    // =======================================================
    // ** THAY THẾ: HÀM DELETE THÀNH DISABLE **
    // =======================================================
    public function disable($id) { 
        return $this->model->disable($id); 
    }
    
    // =======================================================
    // ** THÊM: HÀM KHÔI PHỤC **
    // =======================================================
    public function restore($id) { 
        return $this->model->restore($id); 
    }
    
    public function search($keyword) { return $this->model->search($keyword); }

    // Thêm vào trong class CustomerService

    public function checkPhoneExistsExcept($sdt, $excludeId) {
    return $this->model->checkPhoneExistsExcept($sdt, $excludeId);
}

public function checkEmailExistsExcept($email, $excludeId) {
    return $this->model->checkEmailExistsExcept($email, $excludeId);
}
}
?>