<?php
// Đảm bảo biến không bị lỗi undefined
$reportData = $reportData ?? [];
$threshold = $threshold ?? 5;
?>

<h2 class="mt-3 border-bottom pb-2 text-primary">
    <i class="fa-solid fa-battery-empty"></i> Báo Cáo Sản Phẩm Tồn Kho Thấp
</h2>

<div class="alert alert-info">
    Hiển thị các sản phẩm có số lượng tồn kho **nhỏ hơn hoặc bằng <?= $threshold ?>**. Cần xem xét nhập thêm!
</div>

<div class="mb-3 d-flex justify-content-end">
    <form method="GET" action="index.php" class="d-flex">
        <input type="hidden" name="controller" value="report">
        <input type="hidden" name="action" value="lowstock">
        <div class="input-group">
            <input type="number" name="threshold" value="<?= $threshold ?>" min="0" class="form-control" placeholder="Ngưỡng tồn kho" style="width: 150px;">
            <span class="input-group-text">sản phẩm</span>
            <button class="btn btn-primary" type="submit">Lọc</button>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-primary">
            <tr>
                <th>Mã SP</th>
                <th>Tên Sản Phẩm</th>
                <th>Đơn Vị Tính</th>
                <th>Đơn Giá Bán</th>
                <th class="text-center">Tồn Kho</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($reportData)): ?>
                <?php foreach ($reportData as $row): 
                    // Xử lý nếu database trả về NULL thì coi là 0
                    $tonKho = $row['soLuongTon'] ?? 0;
                ?>
                    <tr class="<?= $tonKho <= 0 ? 'table-danger' : '' ?>">
                        <td>SP<?= $row['maSP'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['tenSP']) ?></td>
                        <td><?= $row['donViTinh'] ?></td>
                        <td class="text-end"><?= number_format($row['donGiaBan'], 0, ',', '.') ?> đ</td>
                        
                        <td class="text-center fw-bold">
                            <?php if ($tonKho <= 0): ?>
                                <span class="badge bg-danger p-2">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Hết hàng (0)
                                </span>
                            <?php else: ?>
                                <span class="text-danger"><?= $tonKho ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center py-3 text-muted">Tất cả sản phẩm đều có lượng tồn kho ổn định (trên <?= $threshold ?>).</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>