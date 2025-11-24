<?php

// --- ĐOẠN FIX LỖI ---
$invoices = $invoices ?? []; // Nếu chưa có thì gán mảng rỗng
$total_records = $total_records ?? 0;
$total_pages = $total_pages ?? 1;
$current_page = $current_page ?? 1;
// --------------------

// ... Các đoạn code hiển thị thông báo Session giữ nguyên ...
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
    <h1 class="h2 text-primary"><i class="fa-solid fa-file-invoice me-2"></i> Danh Sách Hóa Đơn</h1>
    <a href="index.php?controller=invoice&action=add" class="btn btn-success">
        <i class="fa-solid fa-plus me-2"></i> Thêm Hóa Đơn Mới
    </a>
</div>

<?php 
// Lấy lại giá trị tìm kiếm để điền vào form
$date_from = $_GET['date_from'] ?? '';
$date_to   = $_GET['date_to'] ?? '';
$min_total = $_GET['min_total'] ?? '';
$max_total = $_GET['max_total'] ?? '';
$customer_name = $_GET['customer_name'] ?? ''; // Tên khách
$employee_name = $_GET['employee_name'] ?? ''; // Tên nhân viên

// Xử lý query string cho phân trang
$query_params = $_GET;
unset($query_params['page']); 
$query_string = http_build_query($query_params);
?>

<div class="card mb-4 bg-light border-secondary shadow-sm">
    <div class="card-header fw-bold text-uppercase text-secondary">
        <i class="fa-solid fa-filter me-1"></i> Bộ lọc tìm kiếm
    </div>
    <div class="card-body">
        <form action="index.php" method="GET">
            <input type="hidden" name="controller" value="invoice">
            <input type="hidden" name="action" value="search"> <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Từ ngày</label>
                    <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($date_from) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Đến ngày</label>
                    <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($date_to) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Tổng tiền từ</label>
                    <input type="number" name="min_total" class="form-control" placeholder="0" value="<?= htmlspecialchars($min_total) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Đến số tiền</label>
                    <input type="number" name="max_total" class="form-control" placeholder="Max" value="<?= htmlspecialchars($max_total) ?>">
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold small">Tên Khách hàng</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="customer_name" class="form-control" 
                               placeholder="Nhập tên khách..." value="<?= htmlspecialchars($customer_name) ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold small">Tên Nhân viên</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-id-badge"></i></span>
                        <input type="text" name="employee_name" class="form-control" 
                               placeholder="Nhập tên nhân viên..." value="<?= htmlspecialchars($employee_name) ?>">
                    </div>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
                        </button>
                        <a href="index.php?controller=invoice&action=list" class="btn btn-secondary">
                            <i class="fa-solid fa-rotate-left"></i> Xóa
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Mã HD</th>
                <th>Ngày Tạo</th>
                <th>Khách Hàng</th>
                <th>Nhân Viên</th>
                <th class="text-end">Tổng Tiền</th>
                <th class="text-center">Trạng Thái</th> 
                <th class="text-center">Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($invoices) && is_array($invoices) && count($invoices) > 0): ?>
                <?php foreach ($invoices as $row): ?>
                    <tr class="<?= ($row['trangThai'] ?? 1) == 0 ? 'table-secondary text-muted' : '' ?>">
                        <td>HD<?= $row['maHD'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['ngayTao'])) ?></td>
                        <td class="fw-bold"><?= $row['hoTenKH'] ?? 'Khách lẻ' ?></td>
                        <td><?= $row['hoTenNV'] ?? 'N/A' ?></td>
                        <td class="text-end fw-bold text-danger"><?= number_format($row['tongTien']) ?> đ</td>
                        
                        <td class="text-center">
                            <?php if (($row['trangThai'] ?? 1) == 1): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Đã ẩn</span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="index.php?controller=invoice&action=detail&id=<?= $row['maHD'] ?>" class="btn btn-sm btn-info me-2" title="Xem Chi Tiết">
                                    <i class="fa-solid fa-eye"></i> Xem
                                </a>

                                <?php if (($row['trangThai'] ?? 1) == 1): ?>
                                    <a href="index.php?controller=invoice&action=delete&id=<?= $row['maHD'] ?>&page=<?= $current_page ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận ẩn Hóa đơn HD<?= $row['maHD'] ?>? (Sẽ CỘNG tồn kho)');" title="Ẩn Hóa Đơn">
                                        <i class="fa-solid fa-ban"></i> Ẩn
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?controller=invoice&action=restore&id=<?= $row['maHD'] ?>&page=<?= $current_page ?>" class="btn btn-sm btn-success" onclick="return confirm('Xác nhận khôi phục Hóa đơn HD<?= $row['maHD'] ?>? (Sẽ TRỪ tồn kho)');" title="Khôi phục Hóa Đơn">
                                        <i class="fa-solid fa-rotate-left"></i> Khôi phục
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fa-solid fa-file-circle-xmark fa-2x mb-2"></i><br>
                        Không tìm thấy hóa đơn nào phù hợp với điều kiện lọc.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        Hiển thị <?= count($invoices) ?> hóa đơn trên tổng số <?= $total_records ?>
    </div>
    
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination mb-0">
            <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="index.php?<?= $query_string ?>&page=<?= $current_page - 1 ?>" aria-label="Previous">
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
                    <a class="page-link" href="index.php?<?= $query_string ?>&page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
            
            <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="index.php?<?= $query_string ?>&page=<?= $current_page + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>