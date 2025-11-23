<?php 
// Các biến tìm kiếm (giữ nguyên để không lỗi)
$search_name = $search_name ?? '';
$price_min   = $price_min ?? '';
$price_max   = $price_max ?? '';

// Giữ tham số URL phân trang
$query_params = $_GET;
unset($query_params['page']); 
$query_string = http_build_query($query_params);
?>

<div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Danh sách Sản Phẩm</h1>
    
    <a href="index.php?controller=product&action=add" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Thêm mới
    </a>
</div>

<div class="card mb-4 bg-light">
    <div class="card-body">
        <form action="index.php" method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="controller" value="product">
            <input type="hidden" name="action" value="list">

            <div class="col-md-4">
                <label class="form-label fw-bold">Tên sản phẩm</label>
                <input type="text" name="search_name" class="form-control" 
                       placeholder="Nhập tên..." value="<?= htmlspecialchars($search_name) ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label fw-bold">Khoảng giá (VNĐ)</label>
                <div class="input-group">
                    <input type="number" name="price_min" class="form-control" 
                           placeholder="Từ giá..." min="0" value="<?= htmlspecialchars($price_min) ?>" step="1000">
                    <span class="input-group-text">-</span>
                    <input type="number" name="price_max" class="form-control" 
                           placeholder="Đến giá..." min="0" value="<?= htmlspecialchars($price_max) ?>" step="1000">
                </div>
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa-solid fa-filter"></i> Lọc
                </button>
            </div>
            
            <div class="col-md-2">
                <a href="index.php?controller=product&action=list" class="btn btn-outline-secondary w-100">
                    <i class="fa-solid fa-rotate-left"></i> Xóa lọc
                </a>
            </div>
        </form>
    </div>
</div>

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
                <?php foreach ($products as $row): 
                    // Kiểm tra trạng thái (0 là ẩn, 1 là hiện)
                    $trangThai = isset($row['trangThai']) ? $row['trangThai'] : 1;
                    $isHidden = ($trangThai == 0);
                ?>
                <tr class="<?= $isHidden ? 'table-secondary text-muted' : '' ?>" 
                    style="<?= $isHidden ? 'opacity: 0.75;' : '' ?>">
                    
                    <td>SP<?= $row['maSP'] ?></td>
                    
                    <td class="fw-bold">
                        <?= $row['tenSP'] ?>
                        <?php if($isHidden): ?>
                            <span class="badge bg-secondary ms-1" style="font-size: 0.7em;">Đã ẩn</span>
                        <?php endif; ?>
                    </td>
                    
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
                            <a href="index.php?controller=product&action=edit&id=<?= $row['maSP'] ?>" 
                               class="btn btn-sm btn-warning">
                                Sửa
                            </a>
                            
                            <?php if($isHidden): ?>
                                <a href="index.php?controller=product&action=restore&id=<?= $row['maSP'] ?>" 
                                   class="btn btn-sm btn-success" 
                                   onclick="return confirm('Hiển thị lại sản phẩm này?');">
                                Hiện
                                </a>
                            <?php else: ?>
                                <a href="index.php?controller=product&action=delete&id=<?= $row['maSP'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bạn có chắc muốn ẩn sản phẩm này?');">
                                    Ẩn
                                </a>
                            <?php endif; ?>
                            
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