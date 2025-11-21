<h2 class="mt-3">Sửa Nhà Cung Cấp</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label>Tên Nhà Cung Cấp (*)</label>
        <input type="text" class="form-control" name="tenNCC" value="<?= $item['tenNCC'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Số điện thoại</label>
        <input type="text" class="form-control" name="soDienThoai" value="<?= $item['soDienThoai'] ?>">
    </div>
    <div class="mb-3">
        <label>Địa chỉ</label>
        <input type="text" class="form-control" name="diaChi" value="<?= $item['diaChi'] ?>">
    </div>
    <button type="submit" class="btn btn-primary">Cập nhật</button>
</form>