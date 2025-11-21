<?php
// Kiểm tra thông báo lỗi/thành công từ Session
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']); 
}
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success" role="alert">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
?>

<div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 text-primary"><i class="fa-solid fa-file-arrow-down me-2"></i> Danh Sách Phiếu Nhập</h1>
    <a href="index.php?controller=import&action=add" class="btn btn-success">
        <i class="fa-solid fa-plus me-2"></i> Tạo Phiếu Nhập Mới
    </a>
</div>

<?php 
// Controller đã truyền vào các biến thông qua hàm extract() trong index.php.
$imports = $imports ?? []; 
$total_pages = $total_pages ?? 1;
$current_page = $current_page ?? 1;
$total_records = $total_records ?? 0;
$controller = 'import'; 
$action = 'list';
?>

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Mã PN</th>
                <th>Ngày Nhập</th>
                <th>Nhà Cung Cấp</th>
                <th>Nhân Viên</th>
                <th class="text-end">Tổng Giá Trị</th>
                <th class="text-center">Trạng Thái</th>
                <th class="text-center">Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($imports) && is_array($imports) && count($imports) > 0): ?>
                <?php foreach ($imports as $row): ?>
                    <tr class="<?= ($row['trangThai'] ?? 1) == 0 ? 'table-secondary text-muted' : '' ?>">
                        <td>PN<?= $row['maPN'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['ngayNhap'])) ?></td>
                        <td><?= $row['tenNCC'] ?? 'N/A' ?></td>
                        <td><?= $row['hoTenNV'] ?? 'N/A' ?></td>
                        <td class="text-end fw-bold text-danger"><?= number_format($row['tongGiaTri']) ?> đ</td>
                        
                        <td class="text-center">
                            <?php if (($row['trangThai'] ?? 1) == 1): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Đã ẩn</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">
                            <div class="btn-group">
                                <a href="index.php?controller=import&action=detail&id=<?= $row['maPN'] ?>" class="btn btn-sm btn-info me-2" title="Xem Chi Tiết">
                                    <i class="fa-solid fa-eye"></i> Xem
                                </a>

                                <?php if (($row['trangThai'] ?? 1) == 1): ?>
                                    <a href="index.php?controller=import&action=delete&id=<?= $row['maPN'] ?>&page=<?= $current_page ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Xác nhận ẩn Phiếu nhập PN<?= $row['maPN'] ?>? (Sẽ TRỪ tồn kho)');" title="Ẩn Phiếu">
                                        <i class="fa-solid fa-ban"></i> Ẩn
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?controller=import&action=restore&id=<?= $row['maPN'] ?>&page=<?= $current_page ?>" 
                                       class="btn btn-sm btn-success" 
                                       onclick="return confirm('Xác nhận khôi phục Phiếu nhập PN<?= $row['maPN'] ?>? (Sẽ CỘNG tồn kho)');" title="Khôi phục Phiếu">
                                        <i class="fa-solid fa-rotate-left"></i> Khôi phục
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">Không có phiếu nhập nào trong hệ thống.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        Hiển thị <?= count($imports) ?> phiếu nhập trên tổng số <?= $total_records ?>
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
            
            if ($current_page <= 3) { $end_page = min($total_pages, 5); }
            if ($current_page > $total_pages - 3) { $start_page = max(1, $total_pages - 4); }
            
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