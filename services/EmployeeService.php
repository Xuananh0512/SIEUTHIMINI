<?php
class EmployeeService {
    private $model;
    private $roleModel; // Bổ sung dependency để lấy danh sách vai trò

    public function __construct() { 
        $this->model = new EmployeeModel(); 
        // Giả định RoleModel tồn tại để lấy danh sách chức vụ cho filter
        $this->roleModel = new RoleModel(); 
    }

    // =======================================================
    // ** THÊM: HÀM DỊCH VỤ PHÂN TRANG CÓ LỌC **
    // =======================================================
    // Cập nhật để chấp nhận tham số lọc và truyền xuống Model
    public function countAll($keyword = '', $role_id = '', $start_date = '', $end_date = '') {
        return $this->model->countAll($keyword, $role_id, $start_date, $end_date);
    }

    public function getPaginated($limit, $offset, $keyword = '', $role_id = '', $start_date = '', $end_date = '') {
        return $this->model->getPaginated($limit, $offset, $keyword, $role_id, $start_date, $end_date);
    }
    
    // --- HÀM MỚI: LẤY DANH SÁCH VAI TRÒ (Dùng cho Filter View) ---
    public function getAllRoles() {
        return $this->roleModel->getAll();
    }
    
    // Các hàm còn lại giữ nguyên
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

    // Thêm vào trong class EmployeeService

    public function checkPhoneExists($sdt) {
        return $this->model->checkPhoneExists($sdt);
    }

    public function checkEmailExists($email) {
        return $this->model->checkEmailExists($email);
}
}