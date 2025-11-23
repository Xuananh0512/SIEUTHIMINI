<?php
class Database {
    // Cấu hình kết nối dựa trên XAMPP mặc định và ảnh bạn gửi
    private $host = "localhost";
    private $user = "root";        // User mặc định của XAMPP
    private $pass = "";            // Mật khẩu mặc định của XAMPP là rỗng
    private $dbname = "qlstmndb";    // Tên CSDL chính xác trong ảnh bạn gửi
    
    public $conn;

    public function __construct() {
        // Tạo kết nối
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        
        // Kiểm tra kết nối
        if ($this->conn->connect_error) {
            die("Kết nối CSDL thất bại: " . $this->conn->connect_error);
        }
        
        // Thiết lập font chữ tiếng Việt
        $this->conn->set_charset("utf8mb4");
    }
    
    // Hàm đóng kết nối (nếu cần)
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>