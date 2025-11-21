<div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản lý Nhân Viên</h1>
    <a href="index.php?controller=employee&action=add" class="btn btn-primary">+ Thêm nhân viên</a>
</div>

<?php 
// Controller đã truyền vào các biến thông qua hàm extract() trong index.php.
// Biến employees là danh sách nhân viên.
$employees = $employees ?? []; 
$total_pages = $total_pages ?? 1;
$current_page = $current_page ?? 1;
$total_records = $total_records ?? 0;
$controller = 'employee'; 
$action = 'list';
?>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Mã NV</th>
                <th>Họ Tên</th>
                <th>Chức vụ</th>
                <th>Liên hệ</th>
                <th>Ngày vào làm</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($employees) && is_array($employees) && count($employees) > 0): ?>
                <?php foreach ($employees as $row): ?>
                    <tr class="<?= $row['trangThaiLamViec'] == 0 ? 'table-secondary text-muted' : '' ?>">
                        <td>NV<?= $row['maNV'] ?></td>
                        <td class="fw-bold"><?= $row['hoTenNV'] ?></td>
                        
                        <td><span class="badge bg-info text-dark"><?= $row['tenVaiTro'] ?? 'Chưa phân' ?></span></td>
                        
                        <td>
                            <small>
                                <i class="fa-solid fa-phone"></i> <?= $row['soDienThoai'] ?><br>
                                <i class="fa-solid fa-envelope"></i> <?= $row['email'] ?>
                            </small>
                        </td>
                        
                        <td><?= date('d/m/Y', strtotime($row['ngayVaoLam'])) ?></td>
                        
                        <td class="text-center">
                            <?php if($row['trangThaiLamViec'] == 1): ?>
                                <span class="badge bg-success">Đang làm</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Đã nghỉ</span>
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <a href="index.php?controller=employee&action=edit&id=<?= $row['maNV'] ?>" 
                               class="btn btn-sm btn-warning">Sửa</a>
                            
                            <?php if($row['trangThaiLamViec'] == 1): ?>
                                <a href="index.php?controller=employee&action=delete&id=<?= $row['maNV'] ?>&page=<?= $current_page ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Cho nhân viên này nghỉ việc?');">
                                   Cho nghỉ
                                </a>
                            <?php else: ?>
                                <a href="index.php?controller=employee&action=restore&id=<?= $row['maNV'] ?>&page=<?= $current_page ?>" 
                                   class="btn btn-sm btn-success"
                                   onclick="return confirm('Khôi phục nhân viên này đi làm lại?');">
                                   <i class="fa-solid fa-rotate-left"></i> Khôi phục
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">Chưa có nhân viên nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        Hiển thị <?= count($employees) ?> nhân viên trên tổng số <?= $total_records ?>
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