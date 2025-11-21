<h2 class="mt-3">Thêm Nhà Cung Cấp</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label>Tên Nhà Cung Cấp (*)</label>
        <input type="text" class="form-control" name="tenNCC" required>
    </div>
    <div class="mb-3">
        <label>Số điện thoại</label>
        <input type="text" class="form-control" name="soDienThoai">
    </div>
    <div class="mb-3">
        <label>Địa chỉ</label>
        <input type="text" class="form-control" name="diaChi">
    </div>
    <button type="submit" class="btn btn-primary">Lưu</button>
    <a href="index.php?controller=provide&action=list" class="btn btn-secondary">Hủy</a>
</form>