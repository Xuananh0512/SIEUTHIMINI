<?php
class ProfileController {
    private $employeeModel;
    private $accountModel;

    public function __construct() {
        // Giả định EmployeeModel và AccountModel đã được autoload
        $this->employeeModel = new EmployeeModel(); 
        $this->accountModel = new AccountModel(); 
    }

    public function index() {
        $maNV = $_SESSION['user_id'] ?? null; // Lấy Mã NV từ Session khi đăng nhập

        if (!$maNV) {
            // Nếu không có ID, chuyển hướng về trang login
            header("Location: " . BASE_URL . "index.php?controller=login&action=index");
            exit;
        }

        // 1. Lấy thông tin cơ bản của Nhân viên (địa chỉ, SĐT, ngày sinh...)
        $employee = $this->employeeModel->getById($maNV);
        
        // 2. Lấy thông tin Tài khoản liên quan (Username, trạng thái)
        $account = $this->accountModel->getByEmployeeId($maNV); // Giả định hàm này đã được tạo trong AccountModel
        
        return [
            'employee' => $employee,
            'account' => $account
        ];
    }

    public function edit_password($data) {
        $maNV = $_SESSION['user_id'] ?? null;
        if (!$maNV) {
            $_SESSION['error'] = "Phiên đăng nhập đã hết hạn.";
            return false;
        }
        
        $old_password = $data['old_password'] ?? '';
        $new_password = $data['new_password'] ?? '';
        $confirm_password = $data['confirm_password'] ?? '';
        
        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
            return false;
        }
        
        // GIẢ ĐỊNH: Tạo hàm updatePassword trong AccountModel để kiểm tra mật khẩu cũ và update
        $result = $this->accountModel->updatePassword($maNV, $old_password, $new_password);
        
        if ($result === true) {
            $_SESSION['success'] = "Đổi mật khẩu thành công!";
        } else {
            // Giả định Model trả về chuỗi thông báo lỗi nếu fail
            $_SESSION['error'] = $result; 
        }
        
        return true;
    }
}
?>