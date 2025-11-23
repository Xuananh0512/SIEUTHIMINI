<?php
// views/customer/edit.php
// Biến $item chứa dữ liệu khách hàng hiện tại
?>

<div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 text-primary"><i class="fa-solid fa-user-edit me-2"></i> Cập Nhật Khách Hàng</h1>
</div>

<div class="card shadow-sm p-4">
    <form method="POST" action="">
        
        <div class="mb-3">
            <label class="form-label fw-bold">Họ tên (*)</label>
            <input type="text" class="form-control" name="hoTenKH" value="<?= $item['hoTenKH'] ?? '' ?>" required>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Số điện thoại (*)</label>
                <input type="tel" class="form-control" name="soDienThoai" value="<?= $item['soDienThoai'] ?? '' ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Email</label>
                <input type="email" class="form-control" name="email" value="<?= $item['email'] ?? '' ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Ngày sinh</label>
                <input type="date" class="form-control" name="ngaySinh" value="<?= substr($item['ngaySinh'] ?? '', 0, 10) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Điểm tích lũy</label>
                <input type="number" class="form-control" name="diemTichLuy" value="<?= $item['diemTichLuy'] ?? 0 ?>" min="0">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Địa chỉ</label>
            <input type="text" class="form-control" name="diaChi" value="<?= $item['diaChi'] ?? '' ?>">
        </div>

        <div class="d-flex justify-content-end">
            <a href="index.php?controller=customer&action=list" class="btn btn-secondary me-2">Hủy</a>
            <button type="submit" class="btn btn-warning fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i> Cập nhật</button>
        </div>
    </form>
</div>