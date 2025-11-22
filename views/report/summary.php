<?php
$kpis = $kpis ?? [];
$top_selling = $top_selling ?? [];
$low_stock = $low_stock ?? [];
?>

<h2 class="border-bottom pb-2 mb-4 text-primary">
    <i class="fa-solid fa-gauge-high me-2"></i> Tổng Quan Hệ Thống (Dashboard)
</h2>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-money-bill-wave me-2"></i> Doanh Thu Ghi Nhận</h5>
                <p class="card-text fs-3 fw-bold"><?= number_format($kpis['total_revenue'] ?? 0) ?> đ</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-boxes-stacked me-2"></i> Tổng Giá Trị Tồn Kho</h5>
                <p class="card-text fs-3 fw-bold"><?= number_format($kpis['total_stock_value'] ?? 0) ?> đ</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-box me-2"></i> Tổng Sản Phẩm</h5>
                <p class="card-text fs-3 fw-bold"><?= number_format($kpis['total_products'] ?? 0) ?> loại</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-warning text-dark shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-users me-2"></i> Tổng Khách Hàng</h5>
                <p class="card-text fs-3 fw-bold"><?= number_format($kpis['total_customers'] ?? 0) ?> khách</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100 border-danger">
            <div class="card-header bg-danger text-white fw-bold">
                <i class="fa-solid fa-exclamation-triangle me-1"></i> Cảnh Báo Tồn Kho Thấp
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr><th>Tên SP</th><th class="text-center">Tồn Kho</th><th>Thao tác</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($low_stock)): ?>
                            <tr><td colspan="3" class="text-center text-success fw-bold">Không có cảnh báo tồn kho thấp!</td></tr>
                        <?php else: ?>
                            <?php foreach (array_slice($low_stock, 0, 5) as $item): ?>
                            <tr>
                                <td><?= $item['tenSP'] ?></td>
                                <td class="text-center text-danger fw-bold"><?= $item['soLuongTon'] ?></td>
                                <td><a href="index.php?controller=import&action=add" class="btn btn-sm btn-info">Nhập thêm</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if (count($low_stock) > 5): ?>
                    <div class="card-footer text-center"><small class="text-muted">Còn <?= count($low_stock) - 5 ?> cảnh báo khác.</small></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100 border-success">
            <div class="card-header bg-success text-white fw-bold">
                <i class="fa-solid fa-ranking-star me-1"></i> Top 5 Sản Phẩm Bán Chạy
            </div>
            <div class="card-body p-0">
                 <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Tên SP</th><th class="text-end">SL Bán</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($top_selling)): ?>
                            <tr><td colspan="3" class="text-center text-muted fw-bold">Chưa có dữ liệu bán hàng.</td></tr>
                        <?php else: ?>
                            <?php foreach ($top_selling as $index => $item): ?>
                            <tr>
                                <td class="fw-bold"><?= $index + 1 ?></td>
                                <td><?= $item['tenSP'] ?></td>
                                <td class="text-end fw-bold text-primary"><?= number_format($item['total_qty_sold']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>