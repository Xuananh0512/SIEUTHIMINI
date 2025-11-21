<h2 class="mt-3">Thêm Khách Hàng</h2>
<form method="POST" action="index.php?controller=customer&action=add">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Họ tên (*)</label>
            <input type="text" class="form-control" name="hoTenKH" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Số điện thoại (*)</label>
            <input type="text" class="form-control" name="soDienThoai" required>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Địa chỉ</label>
        <input type="text" class="form-control" name="diaChi">
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Ngày sinh</label>
            <input type="date" class="form-control" name="ngaySinh">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email">
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