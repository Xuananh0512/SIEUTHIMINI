
<?php
$reportData = $reportData ?? [];
$days = $days ?? 30;
?>

<h2 class="mt-3 border-bottom pb-2 text-danger">
    <i class="fa-solid fa-clock-rotate-left"></i> Báo Cáo Sản Phẩm Sắp Hết Hạn
</h2>

<div class="alert alert-warning">
    Hiển thị các sản phẩm có hạn sử dụng trong vòng **<?= $days ?> ngày** tới.
</div>

<div class="mb-3 d-flex justify-content-end">
    <form method="GET" action="index.php" class="d-flex">
        <input type="hidden" name="controller" value="report">
        <input type="hidden" name="action" value="expiringsoon">
        <div class="input-group">
            <input type="number" name="days" value="<?= $days ?>" min="7" class="form-control" placeholder="Số ngày" style="width: 120px;">
            <span class="input-group-text">ngày</span>
            <button class="btn btn-info text-white" type="submit">Lọc</button>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-danger">
            <tr>
                <th>Mã SP</th>
                <th>Tên Sản Phẩm</th>
                <th>Đơn Vị Tính</th>
                <th>Tồn Kho</th>
                <th>Hạn Sử Dụng</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($reportData)): ?>
                <?php foreach ($reportData as $row): ?>
                    <tr>
                        <td><?= $row['maSP'] ?></td>
                        <td class="fw-bold"><?= $row['tenSP'] ?></td>
                        <td><?= $row['donViTinh'] ?></td>
                        <td class="text-center fw-bold text-danger"><?= $row['soLuongTon'] ?></td>
                        <td class="text-danger fw-bold"><?= date('d/m/Y', strtotime($row['hanSuDung'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">Tuyệt vời! Không có sản phẩm nào sắp hết hạn trong vòng <?= $days ?> ngày tới.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>