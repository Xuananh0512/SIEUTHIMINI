<h2 class="mt-3 border-bottom pb-2">Cập nhật Nhân Viên</h2>
<form method="POST" action="">
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Họ và tên</label>
            <input type="text" name="hoTenNV" class="form-control" value="<?= $item['hoTenNV'] ?>" required>
        </div>
        
        <div class="col-md-6">
            <label class="form-label fw-bold">Chức vụ</label>
            <select name="maVaiTro" class="form-select" required>
                <option value="">-- Chọn chức vụ --</option>
                <?php if(isset($roles)): foreach($roles as $r): ?>
                    <option value="<?= $r['maVaiTro'] ?>" <?= ($r['maVaiTro'] == $item['maVaiTro']) ? 'selected' : '' ?>>
                        <?= $r['tenVaiTro'] ?>
                    </option>
                <?php endforeach; endif; ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Số điện thoại</label>
            <input type="text" name="soDienThoai" class="form-control" value="<?= $item['soDienThoai'] ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Email</label>
            <input type="email" name="email" class="form-control" value="<?= $item['email'] ?>">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Ngày sinh</label>
            <input type="date" name="ngaySinh" class="form-control" value="<?= $item['ngaySinh'] ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Trạng thái làm việc</label>
            <select name="trangThaiLamViec" class="form-select">
                <option value="1" <?= $item['trangThaiLamViec'] == 1 ? 'selected' : '' ?>>Đang làm việc</option>
                <option value="0" <?= $item['trangThaiLamViec'] == 0 ? 'selected' : '' ?>>Đã nghỉ việc</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Địa chỉ</label>
        <input type="text" name="diaChi" class="form-control" value="<?= $item['diaChi'] ?>">
    </div>

    <button type="submit" class="btn btn-primary">Cập nhật</button>
    <a href="index.php?controller=employee&action=list" class="btn btn-secondary">Hủy</a>
</form>