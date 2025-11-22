<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Siêu Thị Mini</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* CSS Dark Mode (Giữ nguyên) */
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #212529 !important;
            color: #f8f9fa;
        }
        
        /* 1. Navbar trên cùng luôn Fixed */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030; /* Đảm bảo Navbar ở trên cùng */
        }

        .wrapper {
            display: flex;
            flex: 1;
            /* Thêm padding top bằng chiều cao của Navbar (~56px) */
            padding-top: 56px; 
        }

        /* 2. Sidebar luôn Fixed và Cuộn nếu nội dung dài */
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #2c3034;
            transition: all 0.3s;
            position: fixed; /* Fixed */
            top: 56px; /* Bắt đầu ngay dưới Navbar trên cùng */
            bottom: 0; /* Kéo dài xuống cuối màn hình */
            height: calc(100vh - 56px); /* Chiều cao bằng màn hình trừ Navbar */
            overflow-y: auto; /* Cho phép cuộn khi nội dung dài */
            z-index: 1000;
        }

        #sidebar .nav-link {
            color: rgba(255, 255, 255, .8);
            padding: 15px 20px;
            border-bottom: 1px solid #4b545c;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            color: #fff;
            background: #495057;
        }

        #sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* 3. Đẩy Content sang phải để tránh bị Sidebar Fixed che */
        #content-wrapper {
            width: 100%;
            padding: 20px;
            background-color: #212529;
            /* Đẩy nội dung sang phải bằng chiều rộng Sidebar */
            margin-left: 250px; 
        }

        #content-wrapper>.container-fluid {
            background-color: #ffffff !important;
            color: #212529;
        }

        .table {
            color: #212529;
        }

        .table-striped>tbody>tr:nth-of-type(odd)>* {
            --bs-table-accent-bg: #f5f5f5;
        }

        .footer {
            background: #1c1f23;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: auto;
        }
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

                    <a href="index.php?controller=profile&action=index"
                        class="btn btn-outline-light me-3 btn-sm text-decoration-none"
                        title="Xem Hồ sơ cá nhân">
                        <i class="fa-solid fa-user-circle me-1"></i>
                        <?= $_SESSION['display_name'] ?? 'Admin' ?>
                        (<small><?= $_SESSION['role_name'] ?? 'Vai trò' ?></small>)
                    </a>

                    <a href="index.php?controller=login&action=logout"
                        class="btn btn-outline-light btn-sm"
                        onclick="return confirm('Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?');">
                        Đăng xuất <i class="fa-solid fa-right-from-bracket"></i>
                    </a>

                </div>
            <?php endif; ?>
        </div>
    </nav>

    <?php if (isset($_SESSION['user_id'])): // CHỈ HIỂN THỊ SIDEBAR VÀ CONTENT NẾU ĐÃ ĐĂNG NHẬP 
    
        // Bổ sung: Lấy biến controllerParam và actionParam từ URL để kiểm tra trạng thái active
        $controllerParam = $_GET['controller'] ?? 'home'; 
        $actionParam = $_GET['action'] ?? ($controllerParam === 'home' ? 'index' : 'list');
        
        // Kiểm tra xem có đang ở trang Báo cáo hay không
        $reportMenuStatus = ($controllerParam === 'report') ? 'show' : '';
        $reportMenuExpanded = ($controllerParam === 'report') ? 'true' : 'false';
    ?>

        <div class="wrapper">

            <nav id="sidebar">
                <div class="p-3 text-center fw-bold border-bottom">
                    MENU QUẢN LÝ
                </div>
                <ul class="nav flex-column list-unstyled components">
                    <li class="nav-item">
                        <a href="index.php?controller=home&action=index" class="nav-link">
                            <i class="fa-solid fa-house"></i> Trang chủ
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?controller=category&action=list" class="nav-link">
                            <i class="fa-solid fa-list"></i> Danh Mục Sản Phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=provide&action=list" class="nav-link">
                            <i class="fa-solid fa-truck"></i> Nhà Cung Cấp
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=product&action=list" class="nav-link">
                            <i class="fa-solid fa-box-open"></i> Sản Phẩm
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?controller=import&action=list" class="nav-link ">
                            <i class="fa-solid fa-file-import"></i> Nhập Hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=invoice&action=list" class="nav-link ">
                            <i class="fa-solid fa-cart-shopping"></i> Bán Hàng
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?controller=customer&action=list" class="nav-link">
                            <i class="fa-solid fa-users"></i> Khách Hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=employee&action=list" class="nav-link">
                            <i class="fa-solid fa-id-badge"></i> Nhân Viên
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?controller=account&action=list" class="nav-link">
                            <i class="fa-solid fa-user-gear"></i> Tài Khoản
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=role&action=list" class="nav-link">
                            <i class="fa-solid fa-shield-halved"></i> Phân Quyền
                        </a>
                    </li>
                    
                    <li class="nav-item ">
                        <a href="#reportSubmenu" 
                           data-bs-toggle="collapse" 
                           data-bs-auto-close="false" 
                           aria-expanded="<?= $reportMenuExpanded ?>" 
                           class="nav-link dropdown-toggle  ">
                            <i class="fa-solid fa-chart-bar"></i> Báo Cáo - Thống Kê
                        </a>
                        <ul class="collapse list-unstyled <?= $reportMenuStatus ?>" id="reportSubmenu">
                            <li class="nav-item">
                                <a href="index.php?controller=report&action=revenue" class="nav-link ps-5">
                                    <i class="fa-solid fa-sack-dollar"></i> Doanh thu
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?controller=report&action=topselling" class="nav-link ps-5">
                                    <i class="fa-solid fa-ranking-star"></i> Bán chạy
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?controller=report&action=employeeperformance" class="nav-link ps-5">
                                    <i class="fa-solid fa-user-check"></i> Hiệu suất nhân viên
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?controller=report&action=importreport" class="nav-link ps-5">
                                    <i class="fa-solid fa-chart-pie"></i> Báo cáo Nhập hàng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?controller=report&action=lowstock" class="nav-link ps-5">
                                    <i class="fa-solid fa-battery-empty"></i> Tồn kho thấp
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?controller=report&action=expiringsoon" class="nav-link ps-5">
                                    <i class="fa-solid fa-clock-rotate-left"></i> Sắp hết hạn
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <div id="content-wrapper">
                <div class="container-fluid bg-white p-4 rounded shadow-sm" style="min-height: 85vh;">

                    <?php
                    // Logic to display content
                    if (isset($viewFile) && file_exists($viewFile)) {
                        // 1. If a specific view is requested (e.g., Product List), show it
                        require_once $viewFile;
                    } else {
                        // 2. If no view is requested (Base URL), show the Home Page (Dashboard)
                        $homeView = 'views/home/homePage.php';
                        if (file_exists($homeView)) {
                            require_once $homeView;
                        } else {
                            // Fallback if home view is missing
                            echo "<div class='text-center mt-5'>";
                            echo "<h3>Dashboard</h3>";
                            echo "<p class='text-muted'>Please create views/home/homePage.php to see the dashboard.</p>";
                            echo "</div>";
                        }
                    }
                    ?>
                </div>
                
                <div class="footer">
                    <p class="mb-0">&copy; 2025 Phần mềm Quản lý Siêu Thị Mini - Phiên bản 1.0</p>
                </div>
            </div>

        </div>

    <?php else: // HIỂN THỊ CHỈ VIEW NẾU CHƯA ĐĂNG NHẬP (dành cho trang Login) 
    ?>
        <div id="content" class="d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 56px);">
            <div class="container-fluid p-4">
                <?php
                if (isset($viewFile) && file_exists($viewFile)) {
                    require_once $viewFile;
                }
                ?>
            </div>
        </div>
        <div class="footer">
            <p class="mb-0">&copy; 2025 Phần mềm Quản lý Siêu Thị Mini - Phiên bản 1.0</p>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Đoạn này giúp menu sáng lên CHÍNH XÁC link bạn đang ở
        const currentUrl = window.location.href;
        const navLinks = document.querySelectorAll('#sidebar .nav-link');
        
        // 1. Chuẩn hóa URL hiện tại thành chuỗi "controller=X&action=Y"
        const urlParams = new URLSearchParams(window.location.search);
        const currentController = urlParams.get('controller') || 'home';
        // Action mặc định của 'home' là 'index', các controller khác thường là 'list'
        const currentAction = urlParams.get('action') || (currentController === 'home' ? 'index' : 'list');
        const currentPath = `controller=${currentController}&action=${currentAction}`;

        navLinks.forEach(link => {
            let linkHref = link.getAttribute('href');
            
            if (linkHref) {
                // 2. Lấy chuỗi "controller=X&action=Y" từ href của liên kết
                let linkPath = linkHref.split('?')[1] || '';
                
                // Loại bỏ các tham số phụ (page, limit, days, etc.) để so sánh chỉ dựa trên controller và action
                linkPath = new URLSearchParams(linkPath).toString().split('&').filter(p => p.startsWith('controller') || p.startsWith('action')).join('&');
                
                // 3. So sánh tuyệt đối
                if (linkPath === currentPath) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active'); // Đảm bảo loại bỏ class 'active' khỏi các link khác
                }
            }
        });
    </script>
</body>

</html>