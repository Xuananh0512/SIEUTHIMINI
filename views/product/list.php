<div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Danh sách Sản Phẩm</h1>
    <a href="index.php?controller=product&action=add" class="btn btn-primary">+ Thêm mới</a>
</div>

<?php 
// Controller đã trả về các biến $products, $total_pages, $current_page, $total_records
// $products là danh sách sản phẩm đã được lọc theo trang.
$products = $products ?? []; 
$total_pages = $total_pages ?? 1;
$current_page = $current_page ?? 1;
$total_records = $total_records ?? 0;
$controller = 'product'; 
$action = 'list';
?>

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Mã SP</th>
                <th>Tên SP</th>
                <th>Danh mục</th>
                <th>Nhà Cung Cấp</th>
                <th>Giá Bán</th>
                <th>Tồn Kho</th>
                <th>ĐVT</th>
                <th>Hạn SD</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($products) && is_array($products) && count($products) > 0): ?>
                <?php foreach ($products as $row): ?>
                <tr>
                    <td>SP<?= $row['maSP'] ?></td>
                    <td class="fw-bold"><?= $row['tenSP'] ?></td>
                    
                    <td><span class="badge bg-info text-dark"><?= $row['tenDM'] ?? '---' ?></span></td>
                    <td><small><?= $row['tenNCC'] ?? '---' ?></small></td>
                    
                    <td class="text-end text-danger fw-bold"><?= number_format($row['donGiaBan']) ?> đ</td>
                    <td class="text-center">
                        <?php if($row['soLuongTon'] > 10): ?>
                            <span class="badge bg-success"><?= $row['soLuongTon'] ?></span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark"><?= $row['soLuongTon'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['donViTinh'] ?></td>
                    <td><?= !empty($row['hanSuDung']) ? date('d/m/Y', strtotime($row['hanSuDung'])) : '' ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="index.php?controller=product&action=edit&id=<?= $row['maSP'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                            <a href="index.php?controller=product&action=delete&id=<?= $row['maSP'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa sản phẩm này?');">Xóa</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" class="text-center text-muted">Chưa có sản phẩm nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        Hiển thị <?= count($products) ?> sản phẩm trên tổng số <?= $total_records ?>
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