<?php
// Lấy dữ liệu cũ nếu có lỗi validate
$old = $_SESSION['old_data'] ?? [];
if (isset($_SESSION['old_data'])) unset($_SESSION['old_data']); 
?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<h2 class="mt-3">Thêm Nhà Cung Cấp</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label>Tên Nhà Cung Cấp (*)</label>
        <input type="text" class="form-control" name="tenNCC" required 
               value="<?= htmlspecialchars($old['tenNCC'] ?? '') ?>">
    </div>
    
    <div class="mb-3">
        <label>Số điện thoại</label>
        <input type="text" 
            class="form-control" 
            name="soDienThoai" 
            pattern="0[0-9]{9,10}" 
            maxlength="11" 
            title="Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 11 chữ số"
            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
            value="<?= htmlspecialchars($old['soDienThoai'] ?? '') ?>"> 
            <small class="text-muted">Nhập từ 10 đến 11 chữ số, bắt đầu bằng số 0.</small>
    </div>
    
    <div class="mb-3">
        <label>Địa chỉ</label>
        <input type="text" class="form-control" name="diaChi"
               value="<?= htmlspecialchars($old['diaChi'] ?? '') ?>"> </div>
    
    <button type="submit" class="btn btn-primary">Lưu</button>
    <a href="index.php?controller=provide&action=list" class="btn btn-secondary">Hủy</a>
</form>