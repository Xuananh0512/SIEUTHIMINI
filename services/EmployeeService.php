<?php
class EmployeeService {
    private $model;
    public function __construct() { $this->model = new EmployeeModel(); }

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
    public function getAll() { return $this->model->getAll(); }
    public function getById($id) { return $this->model->getById($id); }

    public function add($data) {
        return $this->model->add(
            $data['maVaiTro'], $data['hoTenNV'], $data['ngaySinh'],
            $data['diaChi'], $data['soDienThoai'], $data['email'], $data['ngayVaoLam']
        );
    }

    public function update($id, $data) {
        return $this->model->update(
            $id, $data['maVaiTro'], $data['hoTenNV'], $data['ngaySinh'],
            $data['diaChi'], $data['soDienThoai'], $data['email'], $data['ngayVaoLam'], $data['trangThaiLamViec']
        );
    }

    public function delete($id) { return $this->model->delete($id); }
    public function restore($id) { return $this->model->restore($id); }
    public function search($key) { return $this->model->search($key); }
}
?>