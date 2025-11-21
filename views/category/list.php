<?php
// Kiểm tra thông báo lỗi từ Session
if (isset($_SESSION['error'])) {
    echo "<script>alert('" . $_SESSION['error'] . "');</script>";
    unset($_SESSION['error']);
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản lý Danh Mục</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?controller=category&action=add" class="btn btn-sm btn-primary">+ Thêm mới</a>
    </div>
</div>

<?php 
// Controller đã truyền vào các biến thông qua hàm extract() trong index.php.
// Biến categories là danh sách danh mục.
$categories = $categories ?? []; 
$total_pages = $total_pages ?? 1;
$current_page = $current_page ?? 1;
$total_records = $total_records ?? 0;
$controller = 'category'; 
$action = 'list';
?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Mã DM</th>
                <th>Tên Danh Mục</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($categories) && is_array($categories) && count($categories) > 0): ?>
                <?php foreach ($categories as $row): ?>
                    <tr>
                        <td>DM<?= $row['maDM'] ?></td>
                        <td class="fw-bold"><?= $row['tenDM'] ?></td>
                        <td>
                            <a href="index.php?controller=category&action=edit&id=<?= $row['maDM'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                            <a href="index.php?controller=category&action=delete&id=<?= $row['maDM'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn xóa danh mục này?');">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="text-center">Chưa có dữ liệu.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        Hiển thị <?= count($categories) ?> danh mục trên tổng số <?= $total_records ?>
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