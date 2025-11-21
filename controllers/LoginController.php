    <?php
class LoginController {
    private $accountModel;

    public function __construct() {
        // Khởi tạo AccountModel để sử dụng các hàm login/getInfo
        $this->accountModel = new AccountModel(); 
    }

    // 1. Hiển thị trang đăng nhập
    public function index() {
        return [];
    }

    // 2. Xử lý logic đăng nhập (khi form được POST)
    public function authenticate($data) {
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? ''; 
        
        // --- 2.1. Xác thực người dùng ---
        // Giả định bạn có hàm login trong AccountModel để kiểm tra username/password (MD5)
        // ** Lưu ý: Nên dùng password_hash() và password_verify() thay vì MD5 trong thực tế **
        $user = $this->accountModel->login($username, MD5($password)); 

        if ($user) {
            if ($user['trangThai'] == 0) {
                 $_SESSION['error'] = "Tài khoản của bạn đã bị khóa hoặc ngừng hoạt động!";
                 // Quay lại trang login
                 header("Location: " . BASE_URL . "index.php?controller=login&action=index");
                 exit;
            }
            
            // --- 2.2. Đăng nhập thành công: Lưu thông tin vào Session ---
            $_SESSION['user_id'] = $user['maNV'];
            $_SESSION['username'] = $user['tenDangNhap'];
            
            // Lấy tên nhân viên và vai trò để hiển thị trên giao diện
            $employee = $this->accountModel->getEmployeeInfo($user['maNV']);
            $_SESSION['display_name'] = $employee['hoTenNV'] ?? $username;
            $_SESSION['role_name'] = $employee['tenVaiTro'] ?? 'Người dùng';
            
            // Chuyển hướng về trang chủ mặc định
            header("Location: " . BASE_URL . "index.php?controller=product&action=list");
            exit;

        } else {
            // Đăng nhập thất bại
            $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
            // Quay lại trang login
            header("Location: " . BASE_URL . "index.php?controller=login&action=index");
            exit;
        }
    }

    // 3. Xử lý đăng xuất
    public function logout() {
        session_destroy();
        // Chuyển hướng về trang login
        header("Location: " . BASE_URL . "index.php?controller=login&action=index");
        exit;
    }
}
?>