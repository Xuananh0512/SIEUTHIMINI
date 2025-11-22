<?php
// =================================================================================
// 1. SYSTEM CONFIGURATION & DEBUG
// =================================================================================
// Enable error reporting for debugging (Set display_errors to 0 in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start Session for flash messages (alerts)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set default timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Automatically detect Base URL
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['PHP_SELF']);
define('BASE_URL', $url);

// =================================================================================
// 2. AUTOLOAD (Automatically load Class files)
// =================================================================================
spl_autoload_register(function ($className) {
    $directories = [
        'config/',
        'models/',
        'services/',
        'controllers/'
    ];

    // __DIR__ is the absolute path to the directory containing index.php
    $baseDir = __DIR__ . '/';

    foreach ($directories as $directory) {
        $file = $baseDir . $directory . $className . '.php';

        if (file_exists($file)) {
            require_once $file;
            return; // Stop searching once found
        }
    }
});

// Load Database configuration file (if it's not a Class file but contains connection vars)
if (file_exists('config/configdb.php')) {
    require_once 'config/configdb.php';
}

// =================================================================================
// 0. AUTHENTICATION CHECK & LOGIN ROUTING
// =================================================================================

// Lấy tham số mặc định
$controllerParam = $_GET['controller'] ?? 'home';
$actionParam = $_GET['action'] ?? 'index';

// Nếu chưa đăng nhập, chuyển hướng đến trang login
if (!isset($_SESSION['user_id']) && $controllerParam !== 'login') {
    $controllerParam = 'login';
    $actionParam = 'index';
}

// =================================================================================
// 3. ROUTING (Controller Selection)
// =================================================================================

$ctrl = null;

// Khởi tạo Controller tương ứng
switch ($controllerParam) {
    case 'login': // Xử lý đăng nhập
        $ctrl = new LoginController();
        if ($actionParam === 'logout') {
            $actionParam = 'logout';
        } elseif ($actionParam !== 'index' && $actionParam !== 'authenticate') {
            $actionParam = 'index';
        }
        break;

    // =======================================================
// ** THÊM: ROUTING CHO TRANG PROFILE **
    // =======================================================
    case 'profile':
        $ctrl = new ProfileController();
        // Mặc định action là index nếu không có
        if (!in_array($actionParam, ['index', 'edit_password'])) {
            $actionParam = 'index';
        }
        break;

    // --- Các Controller cần đăng nhập mới truy cập được ---
    case 'category':
        $ctrl = new CategoryController();
        break;

    case 'product':
        $ctrl = new ProductController();
        break;

    case 'account':
        $ctrl = new AccountController();
        break;

    case 'role':
        $ctrl = new RoleController();
        break;

    case 'employee':
        $ctrl = new EmployeeController();
        break;

    case 'customer':
        $ctrl = new CustomerController();
        break;

    case 'provide':
        $ctrl = new ProvideController();
        break;

    case 'import':
        $ctrl = new ImportController();
        break;

    case 'import_detail':
        $ctrl = new ImportDetailController();
        break;

    case 'invoice':
        $ctrl = new InvoiceController();
        break;

    case 'invoice_detail':
        $ctrl = new InvoiceDetailController();
        break;
    case 'home':
        
        break;

    default:
        echo "<div style='text-align:center; margin-top:50px;'>";
        echo "<h3>Error 404: Controller '$controllerParam' does not exist!</h3>";
        echo "<a href='" . BASE_URL . "'>Return to Homepage</a>";
        echo "</div>";
        exit;
}

// =================================================================================
// 4. ACTION PROCESSING & VIEW SELECTION
// =================================================================================

$viewFile = ""; // Biến chứa đường dẫn View con
$data = [];     // Biến chứa dữ liệu truyền vào View

switch ($actionParam) {
    // --- Xử lý riêng cho Login & Profile ---
    case 'index': // Action mặc định cho trang chủ
        // Nếu controller là 'login', gán view login.php
        if ($controllerParam === 'login') {
            $viewFile = "views/login.php";
            break; // THÊM LỆNH BREAK
        }

        // Nếu controller là 'home'
        if ($controllerParam === 'home') {
            $viewFile = "views/home/homePage.php";
            break; // THÊM LỆNH BREAK
        }
        
        // Xử lý index cho Profile
        if ($controllerParam === 'profile') {
            $data = $ctrl->index();
            $viewFile = "views/profile/profile.php";
            break;
        }
        
        
        break;
    case 'authenticate':
        if ($controllerParam === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->authenticate($_POST);
            exit;
        }
        break;
    case 'logout':
        if ($controllerParam === 'login') {
            $ctrl->logout();
            exit;
        }
        break;

    case 'edit_password':
        if ($controllerParam === 'profile' && $_SERVER['REQUEST_METHOD'] === 'POST') {
$ctrl->edit_password($_POST);
            header("Location: " . BASE_URL . "index.php?controller=profile&action=index");
            exit;
        }
        break;

    // --- Các Action chung (chỉ chạy nếu đã đăng nhập) ---
    case 'list':
        $data = $ctrl->list();
        $viewFile = "views/$controllerParam/list.php";
        break;

    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->add($_POST);
            header("Location: " . BASE_URL . "index.php?controller=$controllerParam&action=list");
            exit;
        } else {
            if (method_exists($ctrl, 'getAddData')) {
                $data = $ctrl->getAddData();
            }
            $viewFile = "views/$controllerParam/add.php";
        }
        break;

    case 'edit':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("Error: ID is missing for edit action!");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->update($id, $_POST);
            header("Location: " . BASE_URL . "index.php?controller=$controllerParam&action=list");
            exit;
        } else {
            if (method_exists($ctrl, 'getAddData')) {
                $data = $ctrl->getAddData();
            }

            $item = $ctrl->getById($id);
            if (!empty($item)) {
                $data['item'] = $item;
            }

            $viewFile = "views/$controllerParam/edit.php";
        }
        break;

    // XỬ LÝ ACTION ĐẶC BIỆT (delete, restore, lock, unlock)
    case 'delete':
    case 'restore':
    case 'lock':
    case 'unlock':
        $id = $_GET['id'] ?? null;
        $page = $_GET['page'] ?? 1;

        if ($id && method_exists($ctrl, $actionParam)) {
            $ctrl->$actionParam($id);
        }

        if (!headers_sent()) {
            header("Location: " . BASE_URL . "index.php?controller=$controllerParam&action=list&page=$page");
            exit;
        }
        break;

    case 'detail':
        $id = $_GET['id'] ?? null;
        if (!$id) die("Error: ID is missing for detail action.");
        $data = $ctrl->detail($id);
        $viewFile = "views/$controllerParam/detail.php";
        break;

    case 'search':
        $keyword = $_GET['keyword'] ?? '';
        $data = $ctrl->search($keyword);
        $viewFile = "views/$controllerParam/list.php";
        break;

    default:
        echo "<h3>Error: Action '$actionParam' is invalid!</h3>";
        exit;
}

// =================================================================================
// 5. RENDER MAIN LAYOUT (MASTER LAYOUT)
// =================================================================================

// Extract data array into individual variables for the view
if (!empty($data) && is_array($data)) {
    extract($data);
}

// File mains/main.php is responsible for including $viewFile in the content area
if (file_exists('mains/main.php')) {
    require_once 'mains/main.php';
} else {
// Fallback: If no layout exists, run the sub-view directly
    if (file_exists($viewFile)) {
        require_once $viewFile;
    } else {
        if (!empty($viewFile)) {
            echo "Error: View file not found: $viewFile";
        }
    }
}