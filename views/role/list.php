<?php
if (isset($_SESSION['error'])) {
    echo "<script>
        alert('" . $_SESSION['error'] . "');
    </script>";
    unset($_SESSION['error']); // Hiện xong thì xóa lỗi đi để F5 không hiện lại
}
?>
<div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản lý Vai Trò / Chức Vụ</h1>
    <a href="index.php?controller=role&action=add" class="btn btn-primary">+ Thêm vai trò</a>
</div>

<?php 
// Controller đã truyền vào các biến thông qua hàm extract() trong index.php.
$roles = $roles ?? []; 
$total_pages = $total_pages ?? 1;
$current_page = $current_page ?? 1;
$total_records = $total_records ?? 0;
$controller = 'role'; 
$action = 'list';
?>

<table class="table table-striped">
    <thead><tr><th>ID</th><th>Tên Vai Trò</th><th>Thao tác</th></tr></thead>
    <tbody>
        <?php if (isset($roles) && is_array($roles) && count($roles) > 0): ?>
            <?php foreach ($roles as $row): ?>
            <tr>
                <td><?= $row['maVaiTro'] ?></td>
                <td><span class="badge bg-info text-dark"><?= $row['tenVaiTro'] ?></span></td>
                <td>
                    <a href="index.php?controller=role&action=edit&id=<?= $row['maVaiTro'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                    <a href="index.php?controller=role&action=delete&id=<?= $row['maVaiTro'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa vai trò này?');">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3" class="text-center">Chưa có dữ liệu.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        Hiển thị <?= count($roles) ?> vai trò trên tổng số <?= $total_records ?>
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