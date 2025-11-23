<h2 class="mt-3 border-bottom pb-2">Sửa Nhà Cung Cấp</h2>

<form method="POST" action="index.php?controller=provide&action=edit&id=<?= $item['maNCC'] ?>">
    
    <div class="mb-3">
        <label class="form-label fw-bold">Tên Nhà Cung Cấp (*)</label>
        <input type="text" class="form-control" name="tenNCC" value="<?= htmlspecialchars($item['tenNCC']) ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label fw-bold">Số điện thoại</label>
        <input type="text" class="form-control" name="soDienThoai" value="<?= htmlspecialchars($item['soDienThoai']) ?>">
    </div>
    
    <div class="mb-3">
        <label class="form-label fw-bold">Địa chỉ</label>
        <input type="text" class="form-control" name="diaChi" value="<?= htmlspecialchars($item['diaChi']) ?>">
    </div>
    
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save"></i> Cập nhật
        </button>
        
        <a href="index.php?controller=provide&action=list" class="btn btn-secondary">
            Hủy
        </a>
    </div>
</form>