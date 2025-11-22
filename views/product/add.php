<h2 class="mt-3">Thêm Sản Phẩm</h2>
<form method="POST" action="index.php?controller=product&action=add">
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Tên sản phẩm (*)</label>
            <input type="text" name="tenSP" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Danh mục (*)</label>
            <select name="maDM" class="form-select" required>
                <option value="">-- Chọn Danh mục --</option>
                <?php if(isset($categories) && is_array($categories)) foreach($categories as $c): ?>
                    <option value="<?= $c['maDM'] ?>"><?= $c['tenDM'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Nhà cung cấp (*)</label>
            <select name="maNCC" class="form-select" required>
                <option value="">-- Chọn Nhà cung cấp --</option>
                <?php if(isset($providers) && is_array($providers)) foreach($providers as $p): ?>
                    <option value="<?= $p['maNCC'] ?>"><?= $p['tenNCC'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Giá bán (*)</label>
            <input type="number" name="dongiaBan" class="form-control" min="0" step="1" required>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Tồn kho ban đầu</label>
            <input type="number" name="soLuongTon" class="form-control" min="0" step="1" value="0">
        </div>
        
        <div class="col-md-4">
            <label class="form-label">Đơn vị tính</label>
            <select name="donViTinh" class="form-select">
                <option value="">-- Chọn ĐVT --</option>
                <?php 
                $units = $units ?? []; 
                if(isset($units) && is_array($units)) foreach($units as $u): ?>
                    <option value="<?= $u['donViTinh'] ?>"><?= $u['donViTinh'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="col-md-4">
            <label class="form-label">Hạn sử dụng</label>
            <input type="date" name="hanSuDung" class="form-control">
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="moTa" class="form-control" rows="3"></textarea>
    </div>
    
    <hr>
    
    <div class="d-flex justify-content-end gap-2">
        
        <a href="index.php?controller=product&action=list" class="btn btn-secondary">
            <i class="fa-solid fa-xmark me-2"></i> Hủy
        </a>
        
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save me-2"></i> Lưu Sản Phẩm
        </button>
        
    </div>
</form>