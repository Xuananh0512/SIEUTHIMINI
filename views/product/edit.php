<?php
// Lấy các biến đã được truyền (đảm bảo là mảng)
$item = $item ?? [];
$categories = $categories ?? [];
$providers = $providers ?? [];
$units = $units ?? [];
$min_price_qty = 0;

if (empty($item)) {
    echo '<div class="alert alert-danger">Không tìm thấy Sản phẩm này để chỉnh sửa!</div>';
    return;
}
?>

<h2 class="mt-3">Chỉnh Sửa Sản Phẩm: <?= $item['tenSP'] ?></h2>
<form method="POST" action="index.php?controller=product&action=edit&id=<?= $item['maSP'] ?>">
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Tên sản phẩm (*)</label>
            <input type="text" name="tenSP" class="form-control" value="<?= $item['tenSP'] ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Giá bán (*)</label>
            <input type="number" name="dongiaBan" class="form-control" value="<?= $item['donGiaBan'] ?>" min="<?= $min_price_qty ?>" step="1" required>
        </div>
    </div>
    
    <div class="row mb-3">
        
        <div class="col-md-4">
            <label class="form-label">Danh mục (*)</label>
            <select name="maDM" class="form-select" required>
                <option value="">-- Chọn Danh mục --</option>
                <?php 
                // KHẮC PHỤC LỖI: Đảm bảo kiểm tra và lặp đúng qua MẢNG
                if (is_array($categories)) foreach ($categories as $c): 
                    $selected = ($c['maDM'] == $item['maDM']) ? 'selected' : '';
                ?>
                    <option value="<?= $c['maDM'] ?>" <?= $selected ?>><?= $c['tenDM'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="col-md-4">
            <label class="form-label">Nhà cung cấp (*)</label>
            <select name="maNCC" class="form-select" required>
                <option value="">-- Chọn NCC --</option>
                <?php 
                // KHẮC PHỤC LỖI: Đảm bảo kiểm tra và lặp đúng qua MẢNG
                if (is_array($providers)) foreach ($providers as $p): 
                    $selected = ($p['maNCC'] == $item['maNCC']) ? 'selected' : '';
                ?>
                    <option value="<?= $p['maNCC'] ?>" <?= $selected ?>><?= $p['tenNCC'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="col-md-4">
            <label class="form-label">Đơn vị tính</label>
            <select name="donViTinh" class="form-select">
                <option value="">-- Chọn ĐVT --</option>
                <?php if (is_array($units)) foreach ($units as $u): 
                    $selected = ($u['donViTinh'] == $item['donViTinh']) ? 'selected' : '';
                ?>
                    <option value="<?= $u['donViTinh'] ?>" <?= $selected ?>><?= $u['donViTinh'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Tồn kho (Hạn chế sửa)</label>
            <input type="number" name="soLuongTon" class="form-control" value="<?= $item['soLuongTon'] ?>" min="<?= $min_price_qty ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Hạn SD</label>
            <input type="date" name="hanSuDung" class="form-control" value="<?= $item['hanSuDung'] ?>">
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="moTa" class="form-control" rows="3"><?= $item['moTa'] ?></textarea>
    </div>
    
    <hr>
    
    <div class="d-flex justify-content-end gap-2">
        <a href="index.php?controller=product&action=list" class="btn btn-secondary">
            <i class="fa-solid fa-xmark me-2"></i> Hủy
        </a>
        
        <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-check me-2"></i> Cập nhật
        </button>
    </div>
</form>