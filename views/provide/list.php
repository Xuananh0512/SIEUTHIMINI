<?php
// Kiểm tra thông báo lỗi từ Session
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success" role="alert">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản lý Nhà Cung Cấp</h1>
    <a href="index.php?controller=provide&action=add" class="btn btn-sm btn-primary">+ Thêm mới</a>
</div>

<?php
// Controller đã truyền vào các biến thông qua hàm extract() trong index.php.
$providers = $providers ?? [];
$total_pages = $total_pages ?? 1;
$current_page = $current_page ?? 1;
$total_records = $total_records ?? 0;
$controller = 'provide';
$action = 'list';
?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Mã NCC</th>
                <th>Tên Nhà Cung Cấp</th>
                <th>SĐT</th>
                <th>Địa chỉ</th>
                <th class="text-center">Trạng Thái</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($providers) && is_array($providers) && count($providers) > 0): ?>
                <?php foreach ($providers as $row): ?>
                    <tr class="<?= ($row['trangThai'] ?? 1) == 0 ? 'table-secondary text-muted' : '' ?>">
                        <td>NCC<?= $row['maNCC'] ?></td>
                        <td class="fw-bold"><?= $row['tenNCC'] ?></td>
                        <td><?= $row['soDienThoai'] ?></td>
                        <td><?= $row['diaChi'] ?></td>

                        <td class="text-center">
                            <?php if (($row['trangThai'] ?? 1) == 1): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Đã ẩn</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">
                            <a href="index.php?controller=provide&action=edit&id=<?= $row['maNCC'] ?>" class="btn btn-sm btn-warning">Sửa</a>

                            <?php if (($row['trangThai'] ?? 1) == 1): ?>
                                <a href="index.php?controller=provide&action=delete&id=<?= $row['maNCC'] ?>&page=<?= $current_page ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Ẩn nhà cung cấp này?');">Ẩn</a>
                            <?php else: ?>
                                <a href="index.php?controller=provide&action=restore&id=<?= $row['maNCC'] ?>&page=<?= $current_page ?>"
                                    class="btn btn-sm btn-success"
                                    onclick="return confirm('Khôi phục nhà cung cấp này?');">Khôi phục</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Chưa có dữ liệu.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        Hiển thị <?= count($providers) ?> nhà cung cấp trên tổng số <?= $total_records ?>
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