<?php
class LoginController {
    private $accountModel;

    public function __construct() {
        $this->accountModel = new AccountModel(); 
    }

    // 1. Hiển thị trang đăng nhập (Giữ nguyên)
    public function index() {
        return [];
    }

    // 2. Xử lý logic đăng nhập
    public function authenticate($data) {
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? ''; 
        
        // Kiểm tra trong bảng tài khoản
        $user = $this->accountModel->login($username, MD5($password)); 

        if ($user) {
            // Kiểm tra trạng thái khóa
            if ($user['trangThai'] == 0) {
                 $_SESSION['error'] = "Tài khoản của bạn đã bị khóa hoặc ngừng hoạt động!";
                 header("Location: " . BASE_URL . "index.php?controller=login&action=index");
                 exit;
            }
            
            // --- LƯU SESSION ---
            $_SESSION['user_id'] = $user['maNV'];
            $_SESSION['username'] = $user['tenDangNhap'];
            
            // QUAN TRỌNG: Lưu mã vai trò trực tiếp từ bảng tài khoản (1=Admin, 2=NV...)
            $_SESSION['role_id'] = $user['maVaiTro']; 
            
            // Lấy thêm thông tin nhân viên để hiển thị tên đẹp hơn
            $employee = $this->accountModel->getEmployeeInfo($user['maNV']);
            $_SESSION['display_name'] = $employee['hoTenNV'] ?? $username;
            $_SESSION['role_name'] = $employee['tenVaiTro'] ?? 'Người dùng';
            
            // Chuyển hướng về trang chủ
            header("Location: " . BASE_URL . "index.php?controller=home&action=index");
            exit;

        } else {
            $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
            header("Location: " . BASE_URL . "index.php?controller=login&action=index");
            exit;
        }
    }

    // 3. Đăng xuất (Giữ nguyên)
    public function logout() {
        session_destroy();
        header("Location: " . BASE_URL . "index.php?controller=login&action=index");
        exit;
    }
}
?>