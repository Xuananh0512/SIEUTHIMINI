<?php
// Lấy dữ liệu cũ nếu có lỗi validation
$old = $_SESSION['old_data'] ?? [];
if (isset($_SESSION['old_data'])) unset($_SESSION['old_data']); 

// Ưu tiên dữ liệu cũ (khi có lỗi), nếu không thì dùng dữ liệu từ database
$displayData = !empty($old) ? $old : $item;
?>

<div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 text-primary">
        <i class="fa-solid fa-user-edit me-2"></i> Cập Nhật Nhân Viên
    </h1>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm p-4">
    <form method="POST" action="">
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Họ tên (*)</label>
                <input type="text" class="form-control" name="hoTenNV" 
                       value="<?= htmlspecialchars($displayData['hoTenNV'] ?? '') ?>" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Chức vụ (*)</label>
                <select class="form-select" name="maVaiTro" required>
                    <option value="">-- Chọn chức vụ --</option>
                    <?php foreach ($roles ?? [] as $role): ?>
                        <option value="<?= $role['maVaiTro'] ?>" 
                            <?= ($displayData['maVaiTro'] ?? '') == $role['maVaiTro'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['tenVaiTro']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Số điện thoại (*)</label>
                <input type="text" class="form-control" name="soDienThoai" 
                       value="<?= htmlspecialchars($displayData['soDienThoai'] ?? '') ?>" 
                       required
                       pattern="0[0-9]{9,10}" 
                       maxlength="11"
                       title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <small class="text-muted">Nhập từ 10 đến 11 số, bắt đầu bằng 0.</small>
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Email (*)</label>
                <input type="email" class="form-control" name="email" 
                       value="<?= htmlspecialchars($displayData['email'] ?? '') ?>" 
                       required
                       placeholder="">
                <small class="text-muted">Ví dụ: nhanvien@gmail.com</small>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Ngày sinh</label>
                <input type="date" class="form-control" name="ngaySinh" 
                       value="<?= substr($displayData['ngaySinh'] ?? '', 0, 10) ?>">
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Ngày vào làm (*)</label>
                <input type="date" class="form-control" name="ngayVaoLam" 
                       value="<?= substr($displayData['ngayVaoLam'] ?? '', 0, 10) ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Địa chỉ</label>
            <input type="text" class="form-control" name="diaChi" 
                   value="<?= htmlspecialchars($displayData['diaChi'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Trạng thái làm việc</label>
            <select class="form-select" name="trangThaiLamViec">
                <option value="1" <?= ($displayData['trangThaiLamViec'] ?? 1) == 1 ? 'selected' : '' ?>>
                    Đang làm việc
                </option>
                <option value="0" <?= ($displayData['trangThaiLamViec'] ?? 1) == 0 ? 'selected' : '' ?>>
                    Đã nghỉ việc
                </option>
            </select>
        </div>

        <hr>

        <div class="d-flex justify-content-end gap-2">
            <a href="index.php?controller=employee&action=list" class="btn btn-secondary">
                <i class="fa-solid fa-xmark me-2"></i> Hủy
            </a>
            <button type="submit" class="btn btn-warning fw-bold">
                <i class="fa-solid fa-pen-to-square me-2"></i> Cập nhật
            </button>
        </div>
    </form>
</div>