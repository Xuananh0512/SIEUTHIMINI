<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản lý Khách Hàng</h1>
    <a href="index.php?controller=customer&action=add" class="btn btn-sm btn-primary">+ Thêm mới</a>
</div>

<?php 
// Controller đã truyền vào các biến thông qua hàm extract() trong index.php.
$customers = $customers ?? []; 
$total_pages = $total_pages ?? 1;
$current_page = $current_page ?? 1;
$total_records = $total_records ?? 0;
$controller = 'customer'; 
$action = 'list';
?>

<table class="table table-hover">
    <thead class="table-dark">
        <tr>
            <th>Mã KH</th>
            <th>Họ Tên</th>
            <th>SĐT</th>
            <th>Địa chỉ</th>
            <th>Điểm TL</th>
            <th>Trạng thái</th> 
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php if (isset($customers) && is_array($customers) && count($customers) > 0): ?>
            <?php foreach ($customers as $row): ?>
            <tr class="<?= ($row['trangThai'] ?? 1) == 0 ? 'table-secondary text-muted' : '' ?>">
                <td><?= $row['maKH'] ?></td>
                <td class="fw-bold"><?= $row['hoTenKH'] ?></td>
                <td><?= $row['soDienThoai'] ?></td>
                <td><?= $row['diaChi'] ?></td>
                <td class="text-center"><span class="badge bg-warning text-dark"><?= $row['diemTichLuy'] ?></span></td>
                
                <td>
                    <?php if (($row['trangThai'] ?? 1) == 1): ?>
                        <span class="badge bg-success">Hoạt động</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Đã ẩn</span>
                    <?php endif; ?>
                </td>
                
                <td>
                    <a href="index.php?controller=customer&action=edit&id=<?= $row['maKH'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                    
                    <?php if (($row['trangThai'] ?? 1) == 1): ?>
                        <a href="index.php?controller=customer&action=delete&id=<?= $row['maKH'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn ẩn khách hàng này không? Họ sẽ không xuất hiện trong danh sách bán hàng nữa.');">Ẩn</a>
                    <?php else: ?>
                        <a href="index.php?controller=customer&action=restore&id=<?= $row['maKH'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Bạn có muốn khôi phục khách hàng này không?');">Khôi phục</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center text-muted">Không tìm thấy khách hàng nào.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        Hiển thị <?= count($customers) ?> khách hàng trên tổng số <?= $total_records ?>
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