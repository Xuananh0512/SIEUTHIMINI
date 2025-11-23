<?php
// =================================================================================
// 1. CẤU HÌNH HỆ THỐNG
// =================================================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['PHP_SELF']);
define('BASE_URL', $url);

// =================================================================================
// 2. AUTOLOAD
// =================================================================================
spl_autoload_register(function ($className) {
    $directories = ['config/', 'models/', 'services/', 'controllers/'];
    $baseDir = __DIR__ . '/';
    foreach ($directories as $directory) {
        $file = $baseDir . $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

if (file_exists('config/configdb.php')) {
    require_once 'config/configdb.php';
}

// =================================================================================
// 0. KIỂM TRA ĐĂNG NHẬP & PHÂN QUYỀN
// =================================================================================

$controllerParam = $_GET['controller'] ?? 'home';
$actionParam = $_GET['action'] ?? 'index';

// 1. Chưa đăng nhập -> Đá về Login
if (!isset($_SESSION['user_id']) && $controllerParam !== 'login') {
    $controllerParam = 'login';
    $actionParam = 'index';
}

// 2. LOGIC PHÂN QUYỀN: 
// - Role 1 (Admin): Full quyền.
// - Role 2-5 (Nhân viên): Full quyền TRỪ (Tài khoản, Nhân viên, Phân quyền).

// Nếu ĐÃ đăng nhập và KHÔNG PHẢI Role 1
if (isset($_SESSION['user_id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] != 1) {
    
    // Danh sách các trang CẤM nhân viên truy cập
    $cam_truy_cap = [
        'account',  // Cấm vào Tài khoản
        'employee', // Cấm vào Nhân viên
        'role'      // Cấm vào Phân quyền
    ];

    // Nếu cố tình vào trang cấm -> Chặn lại
    if (in_array($controllerParam, $cam_truy_cap)) {
        echo "<div style='height:100vh; display:flex; flex-direction:column; justify-content:center; align-items:center; font-family:Arial;'>";
        echo "<h1 style='color:red;'>⛔ KHÔNG CÓ QUYỀN TRUY CẬP!</h1>";
        echo "<p>Chức năng quản trị hệ thống (Nhân viên, Tài khoản, Phân quyền) chỉ dành cho Quản trị viên cấp cao.</p>";
        echo "<a href='index.php?controller=home' style='padding:10px 20px; background:#0d6efd; color:white; text-decoration:none; border-radius:5px;'>Quay lại Trang chủ</a>";
        echo "</div>";
        exit;
    }
}

// =================================================================================
// 3. KHỞI TẠO CONTROLLER
// =================================================================================

$ctrl = null;

switch ($controllerParam) {
    case 'login':
        $ctrl = new LoginController();
        if ($actionParam === 'logout') $actionParam = 'logout';
        elseif ($actionParam !== 'index' && $actionParam !== 'authenticate') $actionParam = 'index';
        break;

    case 'profile':
        $ctrl = new ProfileController();
        if (!in_array($actionParam, ['index', 'edit_password'])) $actionParam = 'index';
        break;

    case 'category': $ctrl = new CategoryController(); break;
    case 'product': $ctrl = new ProductController(); break;
    case 'account': $ctrl = new AccountController(); break;
    case 'role': $ctrl = new RoleController(); break;
    case 'employee': $ctrl = new EmployeeController(); break;
    case 'customer': $ctrl = new CustomerController(); break;
    case 'provide': $ctrl = new ProvideController(); break;
    case 'import': $ctrl = new ImportController(); break;
    case 'import_detail': $ctrl = new ImportDetailController(); break;
    case 'invoice': $ctrl = new InvoiceController(); break;
    case 'invoice_detail': $ctrl = new InvoiceDetailController(); break;
    case 'home': $ctrl = new HomeController(); break;

    case 'report':
        $ctrl = new ReportController();
        $validReportActions = ['importreport', 'revenue', 'topselling', 'employeeperformance', 'expiringsoon', 'lowstock'];
        if (!in_array($actionParam, $validReportActions)) $actionParam = 'revenue';
        break;

    default:
        echo "<h3 style='text-align:center; margin-top:50px;'>Error 404: Controller không tồn tại!</h3>";
        exit;
}

// =================================================================================
// 4. XỬ LÝ ACTION VÀ VIEW
// =================================================================================

$viewFile = ""; 
$data = [];    

switch ($actionParam) {
    case 'index': 
        if ($controllerParam === 'login') { $viewFile = "views/login.php"; break; }
        if ($controllerParam === 'home') { $data = $ctrl->index(); $viewFile = "views/home/homePage.php"; break; }
        if ($controllerParam === 'profile') { $data = $ctrl->index(); $viewFile = "views/profile/profile.php"; break; }
        break;

    case 'authenticate':
        if ($controllerParam === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->authenticate($_POST); exit;
        }
        break;
    case 'logout':
        if ($controllerParam === 'login') { $ctrl->logout(); exit; }
        break;
    case 'edit_password':
        if ($controllerParam === 'profile' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->edit_password($_POST);
            header("Location: " . BASE_URL . "index.php?controller=profile&action=index"); exit;
        }
        break;

    case 'list':
        $data = $ctrl->list();
        $viewFile = "views/$controllerParam/list.php";
        break;

    // Report Actions
    case 'importreport': case 'revenue': case 'topselling': case 'employeeperformance': case 'expiringsoon': case 'lowstock':
        if ($controllerParam === 'report' && method_exists($ctrl, $actionParam)) {
            $data = $ctrl->$actionParam(); 
            $viewFile = "views/report/$actionParam.php";
        }
        break;

    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->add($_POST);
            header("Location: " . BASE_URL . "index.php?controller=$controllerParam&action=list"); exit;
        } else {
            if (method_exists($ctrl, 'getAddData')) $data = $ctrl->getAddData();
            $viewFile = "views/$controllerParam/add.php";
        }
        break;

    case 'edit':
        $id = $_GET['id'] ?? null;
        if (!$id) die("Error: ID is missing!");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->update($id, $_POST);
            header("Location: " . BASE_URL . "index.php?controller=$controllerParam&action=list"); exit;
        } else {
            if (method_exists($ctrl, 'getAddData')) $data = $ctrl->getAddData();
            $item = $ctrl->getById($id);
            if (!empty($item)) $data['item'] = $item;
            $viewFile = "views/$controllerParam/edit.php";
        }
        break;

    case 'delete': case 'restore': case 'lock': case 'unlock':
        $id = $_GET['id'] ?? null;
        $page = $_GET['page'] ?? 1;
        if ($id && method_exists($ctrl, $actionParam)) $ctrl->$actionParam($id);
        if (!headers_sent()) {
            header("Location: " . BASE_URL . "index.php?controller=$controllerParam&action=list&page=$page"); exit;
        }
        break;

    case 'detail':
        $id = $_GET['id'] ?? null;
        if (!$id) die("Error: ID missing.");
        $data = $ctrl->detail($id);
        $viewFile = "views/$controllerParam/detail.php";
        break;

    case 'search':
        $keyword = $_GET['keyword'] ?? '';
        $data = $ctrl->search($keyword);
        $viewFile = "views/$controllerParam/list.php";
        break;

    default: echo "<h3>Error: Action invalid!</h3>"; exit;
}

// =================================================================================
// 5. RENDER MAIN LAYOUT
// =================================================================================

if (!empty($data) && is_array($data)) extract($data);

// Kiểm tra quyền Admin (Role = 1) -> Dùng để ẩn 3 mục nhạy cảm
$isSuperAdmin = (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1);

// Kiểm tra trạng thái menu Báo cáo để mở/đóng
$reportMenuStatus = ($controllerParam === 'report') ? 'show' : '';
$reportMenuExpanded = ($controllerParam === 'report') ? 'true' : 'false';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Siêu Thị Mini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { min-height: 100vh; display: flex; flex-direction: column; background-color: #212529 !important; color: #f8f9fa; }
        .navbar { position: fixed; top: 0; width: 100%; z-index: 1030; }
        .wrapper { display: flex; flex: 1; padding-top: 56px; }
        #sidebar { min-width: 250px; max-width: 250px; background: #2c3034; transition: all 0.3s; position: fixed; top: 56px; bottom: 0; height: calc(100vh - 56px); overflow-y: auto; z-index: 1000; }
        #sidebar .nav-link { color: rgba(255, 255, 255, .8); padding: 15px 20px; border-bottom: 1px solid #4b545c; }
        #sidebar .nav-link:hover, #sidebar .nav-link.active { color: #fff; background: #495057; }
        #sidebar .nav-link i { margin-right: 10px; width: 20px; text-align: center; }
        #content-wrapper { width: 100%; padding: 20px; background-color: #212529; margin-left: 250px; }
        #content-wrapper>.container-fluid { background-color: #ffffff !important; color: #212529; }
        .footer { background: #1c1f23; color: white; text-align: center; padding: 10px 0; margin-top: auto; }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php?controller=home&action=index">
                <i class="fa-solid fa-store me-2"></i>SIÊU THỊ MINI
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="d-flex align-items-center">
                    <a href="index.php?controller=profile&action=index" class="btn btn-outline-light me-3 btn-sm text-decoration-none">
                        <i class="fa-solid fa-user-circle me-1"></i>
                        <?= $_SESSION['display_name'] ?? 'Admin' ?>
                        (<small><?= $_SESSION['role_name'] ?? 'Vai trò' ?></small>)
                    </a>
                    <a href="index.php?controller=login&action=logout" class="btn btn-outline-light btn-sm" onclick="return confirm('Đăng xuất?');">
                        Đăng xuất <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="wrapper">
            <nav id="sidebar">
                <div class="p-3 text-center fw-bold border-bottom">MENU QUẢN LÝ</div>
                <ul class="nav flex-column list-unstyled components">
                    
                    <li class="nav-item">
                        <a href="index.php?controller=home&action=index" class="nav-link"><i class="fa-solid fa-house"></i> Trang chủ</a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?controller=category&action=list" class="nav-link"><i class="fa-solid fa-list"></i> Danh Mục Sản Phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=provide&action=list" class="nav-link"><i class="fa-solid fa-truck"></i> Nhà Cung Cấp</a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=product&action=list" class="nav-link"><i class="fa-solid fa-box-open"></i> Sản Phẩm</a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="index.php?controller=import&action=list" class="nav-link "><i class="fa-solid fa-file-import"></i> Nhập Hàng</a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=invoice&action=list" class="nav-link "><i class="fa-solid fa-cart-shopping"></i> Bán Hàng</a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=customer&action=list" class="nav-link"><i class="fa-solid fa-users"></i> Khách Hàng</a>
                    </li>
                    
                    <?php if ($isSuperAdmin): ?>
                        <!-- <div class="text-uppercase small text-muted px-3 mt-3 mb-1">Hệ thống</div> -->
                        <li class="nav-item">
                            <a href="index.php?controller=employee&action=list" class="nav-link"><i class="fa-solid fa-id-badge"></i> Nhân Viên</a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?controller=account&action=list" class="nav-link"><i class="fa-solid fa-user-gear"></i> Tài Khoản</a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?controller=role&action=list" class="nav-link"><i class="fa-solid fa-shield-halved"></i> Phân Quyền</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a href="#reportSubmenu" 
                           data-bs-toggle="collapse" 
                           data-bs-auto-close="false" 
                           aria-expanded="<?= $reportMenuExpanded ?>" 
                           class="nav-link dropdown-toggle">
                           <i class="fa-solid fa-chart-bar"></i> Báo Cáo - Thống Kê
                        </a>
                        <ul class="collapse list-unstyled <?= $reportMenuStatus ?>" id="reportSubmenu">
                            <li class="nav-item"><a href="index.php?controller=report&action=revenue" class="nav-link ps-5"><i class="fa-solid fa-sack-dollar"></i> Doanh thu</a></li>
                            <li class="nav-item"><a href="index.php?controller=report&action=topselling" class="nav-link ps-5"><i class="fa-solid fa-ranking-star"></i> Bán chạy</a></li>
                            <li class="nav-item"><a href="index.php?controller=report&action=employeeperformance" class="nav-link ps-5"><i class="fa-solid fa-user-check"></i> Hiệu suất nhân viên</a></li>
                            <li class="nav-item"><a href="index.php?controller=report&action=importreport" class="nav-link ps-5"><i class="fa-solid fa-chart-pie"></i> Báo cáo Nhập hàng</a></li>
                            <li class="nav-item"><a href="index.php?controller=report&action=lowstock" class="nav-link ps-5"><i class="fa-solid fa-battery-empty"></i> Tồn kho thấp</a></li>
                            <li class="nav-item"><a href="index.php?controller=report&action=expiringsoon" class="nav-link ps-5"><i class="fa-solid fa-clock-rotate-left"></i> Sắp hết hạn</a></li>
                        </ul>
                    </li>

                </ul>
            </nav>

            <div id="content-wrapper">
                <div class="container-fluid bg-white p-4 rounded shadow-sm" style="min-height: 85vh;">
                    <?php
                    if (isset($viewFile) && file_exists($viewFile)) {
                        require_once $viewFile;
                    } else {
                        $homeView = 'views/home/homePage.php';
                        if (file_exists($homeView)) require_once $homeView;
                        else echo "<div class='text-center mt-5'><h3>Dashboard</h3></div>";
                    }
                    ?>
                </div>
                <div class="footer"><p class="mb-0">&copy; 2025 Phần mềm Quản lý Siêu Thị Mini</p></div>
            </div>
        </div>
    <?php else: ?>
        <div id="content" class="d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 56px);">
            <div class="container-fluid p-4">
                <?php if (isset($viewFile) && file_exists($viewFile)) require_once $viewFile; ?>
            </div>
        </div>
        <div class="footer"><p class="mb-0">&copy; 2025 Phần mềm Quản lý Siêu Thị Mini</p></div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const currentUrl = window.location.href;
        const navLinks = document.querySelectorAll('#sidebar .nav-link');
        const urlParams = new URLSearchParams(window.location.search);
        const currentController = urlParams.get('controller') || 'home';
        const currentAction = urlParams.get('action') || (currentController === 'home' ? 'index' : 'list');
        const currentPath = `controller=${currentController}&action=${currentAction}`;

        navLinks.forEach(link => {
            let linkHref = link.getAttribute('href');
            if (linkHref) {
                let linkPath = linkHref.split('?')[1] || '';
                linkPath = new URLSearchParams(linkPath).toString().split('&').filter(p => p.startsWith('controller') || p.startsWith('action')).join('&');
                if (linkPath === currentPath) link.classList.add('active');
                else link.classList.remove('active');
            }
        });
    </script>
</body>
</html>