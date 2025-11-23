<?php
// Lấy các biến đã được truyền từ Controller
$employee = $employee ?? [];
$account = $account ?? [];
?>

<div class="row">
    <div class="col-12">
        <h2 class="border-bottom pb-2 mb-4 text-primary"><i class="fa-solid fa-id-card me-2"></i> Hồ Sơ Cá Nhân</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white fw-bold">Thông tin Nhân viên</div>
            <div class="card-body">
                <p><strong>Mã NV:</strong> <?= $employee['maNV'] ?? 'N/A' ?></p>
                <p><strong>Họ Tên:</strong> <?= $employee['hoTenNV'] ?? 'N/A' ?></p>
                <p><strong>Vai trò:</strong> <span class="badge bg-primary"><?= $_SESSION['role_name'] ?? 'N/A' ?></span></p>
                <p><strong>Ngày sinh:</strong> <?= $employee['ngaySinh'] ?? 'N/A' ?></p>
                <p><strong>Địa chỉ:</strong> <?= $employee['diaChi'] ?? 'N/A' ?></p>
                <p><strong>Điện thoại:</strong> <?= $employee['soDienThoai'] ?? 'N/A' ?></p>
                <p><strong>Email:</strong> <?= $employee['email'] ?? 'N/A' ?></p>
                <!-- <p><strong>Ngày vào làm:</strong> <?= $employee['ngayVaoLam'] ?? 'N/A' ?></p> -->
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">Thông tin Tài khoản</div>
            <div class="card-body">
                <p><strong>Tên tài khoản:</strong> <span class="badge bg-secondary"><?= $account['tenDangNhap'] ?? 'N/A' ?></span></p>
                <p><strong>Trạng thái:</strong> 
                    <?php if (($account['trangThai'] ?? 0) == 1): ?>
                        <span class="badge bg-success">Hoạt động</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Khóa</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark fw-bold">Đổi Mật Khẩu</div>
            <div class="card-body">
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <form action="index.php?controller=profile&action=edit_password" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu cũ (*)</label>
                        <input type="password" name="old_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới (*)</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu mới (*)</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-key me-2"></i> Đổi Mật Khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
