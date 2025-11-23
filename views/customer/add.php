<?php
// Lấy dữ liệu cũ (nếu có lỗi validation trước đó)
$old = $_SESSION['old_data'] ?? [];
if (isset($_SESSION['old_data'])) unset($_SESSION['old_data']); 
?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<h2 class="mt-3 border-bottom pb-2">Thêm Khách Hàng Mới</h2>
<form method="POST" action="index.php?controller=customer&action=add">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Họ tên (*)</label>
            <input type="text" class="form-control" name="hoTenKH" required
                   value="<?= htmlspecialchars($old['hoTenKH'] ?? '') ?>">
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Số điện thoại (*)</label>
            <input type="text" class="form-control" name="soDienThoai" required
                   pattern="0[0-9]{9,10}" 
                   maxlength="11"
                   title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số"
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   value="<?= htmlspecialchars($old['soDienThoai'] ?? '') ?>"
                   placeholder="">
            <small class="text-muted">Nhập từ 10 đến 11 số, bắt đầu bằng 0.</small>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Địa chỉ</label>
        <input type="text" class="form-control" name="diaChi"
               value="<?= htmlspecialchars($old['diaChi'] ?? '') ?>">
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Ngày sinh</label>
            <input type="date" class="form-control" name="ngaySinh"
                   value="<?= htmlspecialchars($old['ngaySinh'] ?? '') ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Email (*)</label>
            <input type="email" class="form-control" name="email" required
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                   placeholder="">
            <small class="text-muted">Ví dụ: khachhang@gmail.com</small>
        </div>
    </div>
    
    <input type="hidden" name="diemTichLuy" value="0">
    
    <hr>

    <div class="d-flex justify-content-end gap-2">
        <a href="index.php?controller=customer&action=list" class="btn btn-secondary">
            <i class="fa-solid fa-xmark me-2"></i> Hủy
        </a>
        
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save me-2"></i> Lưu khách hàng
        </button>
    </div>
</form>