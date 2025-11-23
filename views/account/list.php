<div class="d-flex justify-content-between pt-3 border-bottom mb-3">
    <h2>Quản lý Tài Khoản</h2>
    <a href="index.php?controller=account&action=add" class="btn btn-primary">+ Cấp tài khoản</a>
</div>

<?php 
// Controller đã truyền vào các biến thông qua hàm extract() trong index.php.
$accounts = $accounts ?? []; 
$total_pages = $total_pages ?? 1;
$current_page = $current_page ?? 1;
$total_records = $total_records ?? 0;
$controller = 'account'; 
$action = 'list';
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Mã TK</th>
            <th>Tên đăng nhập</th>
            <th>Nhân viên</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th>Thao Tác</th>
        </tr>
    </thead>
    <tbody>
        <?php if (isset($accounts) && is_array($accounts) && count($accounts) > 0): ?>
            <?php foreach ($accounts as $row): ?>
            <tr class="<?= ($row['trangThai'] ?? 1) == 0 ? 'table-secondary text-muted' : '' ?>">
                <td><?= $row['maTK'] ?></td>
                <td><span class="badge bg-secondary"><?= $row['tenDangNhap'] ?></span></td>
                <td><?= $row['hoTenNV'] ?? $row['maNV'] ?></td>
                <td><span class="badge bg-info text-dark"><?= $row['tenVaiTro'] ?? $row['maVaiTro'] ?></span></td>
                <td>
                    <?php if (($row['trangThai'] ?? 1) == 1): ?>
                        <span class="badge bg-success">Hoạt động</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Khóa</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="index.php?controller=account&action=edit&id=<?= $row['maTK'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                    
                    <?php if (($row['trangThai'] ?? 1) == 1): ?>
                        <a href="index.php?controller=account&action=delete&id=<?= $row['maTK'] ?>&page=<?= $current_page ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Bạn có chắc chắn muốn KHÓA tài khoản này không? Họ sẽ không đăng nhập được.');">
                           Khóa
                        </a>
                    <?php else: ?>
                        <a href="index.php?controller=account&action=unlock&id=<?= $row['maTK'] ?>&page=<?= $current_page ?>" 
                           class="btn btn-sm btn-success" 
                           onclick="return confirm('Bạn có chắc chắn muốn MỞ KHÓA tài khoản này không?');">
                           Mở Khóa
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center text-muted">Không tìm thấy tài khoản nào.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        Hiển thị <?= count($accounts) ?> tài khoản trên tổng số <?= $total_records ?>
    </div>
    
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination mb-0">
            
            <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="index.php?controller=<?= $controller ?>&action=<?= $action ?>&page=<?= $current_page - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            
            <?php 
            // Logic hiển thị tối đa 5 nút trang
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            if ($current_page <= 3) {
                $end_page = min($total_pages, 5);
            }
            if ($current_page > $total_pages - 3) {
                $start_page = max(1, $total_pages - 4);
            }
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
                <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                    <a class="page-link" href="index.php?controller=<?= $controller ?>&action=<?= $action ?>&page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
            
            <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="index.php?controller=<?= $controller ?>&action=<?= $action ?>&page=<?= $current_page + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            
        </ul>
    </nav>
    <?php endif; ?>
</div>
