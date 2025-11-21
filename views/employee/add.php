<h2 class="mt-3 border-bottom pb-2">Thêm Nhân Viên Mới</h2>
<form method="POST" action="">
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Họ và tên (*)</label>
            <input type="text" name="hoTenNV" class="form-control" required>
        </div>
        
        <div class="col-md-6">
            <label class="form-label fw-bold">Chức vụ / Vai trò (*)</label>
            <select name="maVaiTro" class="form-select" required>
                <option value="">-- Chọn chức vụ --</option>
                <?php if(isset($roles)): foreach($roles as $r): ?>
                    <option value="<?= $r['maVaiTro'] ?>"><?= $r['tenVaiTro'] ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Số điện thoại</label>
            <input type="text" name="soDienThoai" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Email</label>
            <input type="email" name="email" class="form-control">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Ngày sinh</label>
            <input type="date" name="ngaySinh" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Ngày vào làm</label>
            <input type="date" name="ngayVaoLam" class="form-control" value="<?= date('Y-m-d') ?>">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Địa chỉ</label>
        <input type="text" name="diaChi" class="form-control">
    </div>

    <div class="d-flex justify-content-end">
        <a href="index.php?controller=employee&action=list" class="btn btn-secondary me-2">Hủy</a>
        <button type="submit" class="btn btn-primary px-4">Lưu Nhân Viên</button>
    </div>
</form>