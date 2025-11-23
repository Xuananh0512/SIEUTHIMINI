<?php
// Giả định Controller đã truyền:
// 1. $employees: Danh sách Nhân viên
// 2. $roles: Danh sách Vai trò

$employees = $employees ?? [];
$roles = $roles ?? [];
?>

<h3>Cấp Tài Khoản Mới</h3>
<form method="POST" action="index.php?controller=account&action=add">
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Tên tài khoản (*)</label>
            <input type="text" name="tenDangNhap" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Mật khẩu (*)</label>
            <input type="password" name="matKhau" class="form-control" required>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Nhân viên (*)</label>
            <select name="maNV" class="form-select" required>
                <option value="">-- Chọn Nhân viên --</option>
                <?php if(isset($employees) && is_array($employees)): 
                    foreach($employees as $e): ?>
                        <option value="<?= $e['maNV'] ?>"><?= $e['maNV'] ?> - <?= $e['hoTenNV'] ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
        
        <div class="col-md-6">
            <label class="form-label">Vai trò (*)</label>
            <select name="maVaiTro" class="form-select" required>
                <option value="">-- Chọn Vai trò --</option>
                <?php if(isset($roles) && is_array($roles)): 
                    foreach($roles as $r): ?>
                        <option value="<?= $r['maVaiTro'] ?>"><?= $r['maVaiTro'] ?> - <?= $r['tenVaiTro'] ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <label class="form-label">Trạng thái (*)</label>
            <select name="trangThai" class="form-select">
                <option value="1">Hoạt động</option>
                <option value="0">Khóa</option>
            </select>
        </div>
        <div class="col-md-6"></div>
    </div>
    
    <hr>
    <div class="d-flex justify-content-end gap-2">
        <a href="index.php?controller=account&action=list" class="btn btn-secondary">
            <i class="fa-solid fa-xmark me-2"></i> Hủy
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save me-2"></i> Tạo Tài Khoản
        </button>
    </div>
</form>
