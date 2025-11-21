<?php
// Giả định Controller đã truyền:
// 1. $item: Thông tin Tài khoản cần sửa
// 2. $employees: Danh sách Nhân viên (để chọn maNV)
// 3. $roles: Danh sách Vai trò (để chọn maVaiTro)

$item = $item ?? []; 
$employees = $employees ?? [];
$roles = $roles ?? [];

if (empty($item)) {
    echo '<div class="alert alert-danger">Không tìm thấy Tài khoản này để chỉnh sửa!</div>';
    return;
}
?>

<h3>Chỉnh Sửa Tài Khoản: <?= $item['tenDangNhap'] ?></h3>

<form method="POST" action="index.php?controller=account&action=edit&id=<?= $item['maTK'] ?>">
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" name="tenDangNhap" class="form-control" value="<?= $item['tenDangNhap'] ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Password (Để trống nếu không đổi)</label>
            <input type="password" name="matKhau" class="form-control" placeholder="Nhập mật khẩu mới nếu cần">
        </div>
    </div>
    
    <div class="row mb-3">
        
        <div class="col-md-4">
            <label class="form-label">Mã NV / Nhân viên</label>
            <select name="maNV" class="form-select" required>
                <option value="">-- Chọn Nhân viên --</option>
                <?php 
                if (!empty($employees) && is_array($employees)): 
                    foreach ($employees as $emp): 
                        $selected = ($emp['maNV'] == $item['maNV']) ? 'selected' : '';
                    ?>
                    <option value="<?= $emp['maNV'] ?>" <?= $selected ?>><?= $emp['hoTenNV'] ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Vai Trò</label>
            <select name="maVaiTro" class="form-select" required>
                <option value="">-- Chọn Vai trò --</option>
                <?php 
                if (!empty($roles) && is_array($roles)): 
                    foreach ($roles as $role): 
                        $selected = ($role['maVaiTro'] == $item['maVaiTro']) ? 'selected' : '';
                    ?>
                    <option value="<?= $role['maVaiTro'] ?>" <?= $selected ?>><?= $role['tenVaiTro'] ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Trạng thái</label>
            <select name="trangThai" class="form-select">
                <option value="1" <?= $item['trangThai']==1?'selected':'' ?>>Hoạt động</option>
                <option value="0" <?= $item['trangThai']==0?'selected':'' ?>>Khóa</option>
            </select>
        </div>
        
    </div>
    
    <hr>
    <div class="d-flex justify-content-end gap-2">
        <a href="index.php?controller=account&action=list" class="btn btn-secondary">
            <i class="fa-solid fa-xmark me-2"></i> Hủy
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-check me-2"></i> Cập nhật
        </button>
    </div>
    
</form>