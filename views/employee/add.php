<?php
// Lấy dữ liệu cũ nếu có lỗi, sau đó xóa ngay để không hiện lại lần sau
$old = $_SESSION['old_data'] ?? [];
if (isset($_SESSION['old_data'])) unset($_SESSION['old_data']); // Dùng xong xóa luôn
?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<h2 class="mt-3 border-bottom pb-2">Thêm Nhân Viên Mới</h2>
<form method="POST" action="">
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Họ và tên (*)</label>
            <input type="text" name="hoTenNV" class="form-control" 
                   value="<?= htmlspecialchars($old['hoTenNV'] ?? '') ?>" required>
        </div>
        
        <div class="col-md-6">
            <label class="form-label fw-bold">Chức vụ (*)</label>
            <select name="maVaiTro" class="form-select" required>
                <option value="">-- Chọn chức vụ --</option>
                <?php if(isset($roles)): foreach($roles as $r): ?>
                    <option value="<?= $r['maVaiTro'] ?>" 
                        <?= (isset($old['maVaiTro']) && $old['maVaiTro'] == $r['maVaiTro']) ? 'selected' : '' ?>>
                        <?= $r['tenVaiTro'] ?>
                    </option>
                <?php endforeach; endif; ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Số điện thoại (*)</label>
            <input type="text" name="soDienThoai" class="form-control" 
                   required pattern="0[0-9]{9,10}" maxlength="11"
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   value="<?= htmlspecialchars($old['soDienThoai'] ?? '') ?>"> 
        </div>
                <small class="text-muted">Nhập từ 10 đến 11 chữ số, bắt đầu bằng số 0.</small>
        <div class="col-md-6">
            <label class="form-label fw-bold">Email (*)</label>
            <input type="email" name="email" class="form-control" required
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                <small class="text-muted">Ví dụ: nhanvien@gmail.com</small>
        </div>
                
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Ngày sinh</label>
            <input type="date" name="ngaySinh" class="form-control"
                   value="<?= htmlspecialchars($old['ngaySinh'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Ngày vào làm</label>
            <input type="date" name="ngayVaoLam" class="form-control" 
                   value="<?= htmlspecialchars($old['ngayVaoLam'] ?? date('Y-m-d')) ?>">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Địa chỉ</label>
        <input type="text" name="diaChi" class="form-control"
               value="<?= htmlspecialchars($old['diaChi'] ?? '') ?>">
    </div>

    <div class="d-flex justify-content-end">
        <a href="index.php?controller=employee&action=list" class="btn btn-secondary me-2">Hủy</a>
        <button type="submit" class="btn btn-primary px-4">Lưu Nhân Viên</button>
    </div>
</form>