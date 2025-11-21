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

        .wrapper {
            display: flex;
            flex: 1;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #2c3034;
            color: #fff;
            transition: all 0.3s;
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

        #content {
            width: 100%;
            padding: 20px;
            background-color: #212529;
        }

        #content>.container-fluid {
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
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>">
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
    ?>

        <div class="wrapper">

            <nav id="sidebar">
                <div class="p-3 text-center fw-bold border-bottom">
                    MENU QUẢN LÝ
                </div>
                <ul class="nav flex-column list-unstyled components">
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>" class="nav-link">
                            <i class="fa-solid fa-house"></i> Trang chủ
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?controller=category&action=list" class="nav-link">
                            <i class="fa-solid fa-list"></i> Danh Mục
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
                        <a href="index.php?controller=import&action=list" class="nav-link text-warning">
                            <i class="fa-solid fa-file-import"></i> Nhập Hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=invoice&action=list" class="nav-link text-warning">
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
                </ul>
            </nav>

            <div id="content">
                <div class="container-fluid bg-white p-4 rounded shadow-sm" style="min-height: 85vh;">
                    <?php
                    if (isset($viewFile) && file_exists($viewFile)) {
                        require_once $viewFile;
                    } else {
                        echo "<div class='text-center mt-5'>";
                        echo "<h3>Chào mừng đến với Hệ thống Quản lý Siêu thị</h3>";
                        echo "<p class='text-muted'>Vui lòng chọn chức năng từ menu bên trái.</p>";
                        echo "</div>";
                    }
                    ?>
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
    <?php endif; ?>

    <div class="footer">
        <p class="mb-0">&copy; 2025 Phần mềm Quản lý Siêu Thị Mini - Phiên bản 1.0</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Đoạn này giúp menu sáng lên khi bạn đang ở trang tương ứng
        const currentUrl = window.location.href;
        const navLinks = document.querySelectorAll('#sidebar .nav-link');
        navLinks.forEach(link => {
            if (currentUrl.includes(link.getAttribute('href'))) {
                link.classList.add('active');
            }
        });
    </script>
</body>

</html>