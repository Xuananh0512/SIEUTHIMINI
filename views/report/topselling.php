
<?php
$reportData = $reportData ?? [];
$limit = $limit ?? 10;
?>

<h2 class="mt-3 border-bottom pb-2 text-warning">
    <i class="fa-solid fa-ranking-star"></i> Top Sản Phẩm Bán Chạy
</h2>

<div class="mb-3 d-flex justify-content-between align-items-end">
    <div class="alert alert-warning py-2 mb-0 shadow-sm">
        Hiển thị <span class="fw-bold">Top <?= $limit ?></span> sản phẩm có số lượng bán cao nhất (tính từ trước đến nay).
    </div>
    <form method="GET" action="index.php" class="d-flex">
        <input type="hidden" name="controller" value="report">
        <input type="hidden" name="action" value="topselling">
        <div class="input-group">
            <span class="input-group-text">Hiển thị Top</span>
            <input type="number" name="limit" value="<?= $limit ?>" min="5" class="form-control" style="width: 80px;">
            <button class="btn btn-warning text-white" type="submit">Lọc</button>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-warning text-center text-dark">
            <tr>
                <th width="10%">Hạng</th>
                <th width="40%">Tên Sản Phẩm</th>
                <th width="20%">Đơn Vị Tính</th>
                <th width="30%" >Tổng Số Lượng Đã Bán</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (!empty($reportData)):
                $rank = 1;
                foreach ($reportData as $row): ?>
                    <tr>
                        <td class="text-center fw-bold fs-5 text-warning"><?= $rank++ ?></td>
                        <td class="text-center fw-bold "><?= $row['tenSP'] ?></td>
                        <td class="text-center"><?= $row['donViTinh'] ?></td>
                        <td class="text-center fw-bold text-primary">
                            <?= number_format($row['tongSoLuongBan'], 0, ',', '.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">Chưa có dữ liệu bán hàng.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>