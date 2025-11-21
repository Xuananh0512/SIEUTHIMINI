<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-lg border-0 border-top border-primary border-5 rounded-3">
            <div class="card-header bg-primary text-white text-center py-3 rounded-top-3">
                <h3 class="mb-0 fw-bold">ĐĂNG NHẬP HỆ THỐNG</h3>
                <small>Quản Lý Siêu Thị Mini</small>
            </div>
            <div class="card-body p-4">
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        <?= $_SESSION['error']; ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="index.php?controller=login&action=authenticate" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label text-muted fw-semibold">
                            <i class="fa-solid fa-user me-2"></i> Tên đăng nhập:
                        </label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus placeholder="Nhập tên đăng nhập...">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label text-muted fw-semibold">
                            <i class="fa-solid fa-lock me-2"></i> Mật khẩu:
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Nhập mật khẩu...">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                            <i class="fa-solid fa-right-to-bracket me-2"></i> Đăng Nhập
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-light text-muted border-0 rounded-bottom-3">
                <small>&copy; 2025 Hệ Thống Quản Lý STMN</small>
            </div>
        </div>
    </div>
</div>