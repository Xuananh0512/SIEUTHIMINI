
<?php
$reportData = $reportData ?? [];
$startDate = $startDate ?? date('Y-m-01');
$endDate = $endDate ?? date('Y-m-d');
?>

<h2 class="mt-3 border-bottom pb-2 text-success">
    <i class="fa-solid fa-sack-dollar"></i> Báo Cáo Doanh Thu
</h2>

<div class="mb-3">
    <form method="GET" action="index.php" class="d-flex align-items-end justify-content-end">
        <input type="hidden" name="controller" value="report">
        <input type="hidden" name="action" value="revenue">
        
        <div class="me-3">
            <label class="form-label fw-bold">Từ ngày:</label>
            <input type="date" name="start_date" value="<?= $startDate ?>" class="form-control">
        </div>
        <div class="me-3">
            <label class="form-label fw-bold">Đến ngày:</label>
            <input type="date" name="end_date" value="<?= $endDate ?>" class="form-control">
        </div>
        <button class="btn btn-success" type="submit">Xem Báo Cáo</button>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-success text-white">
            <tr>
                <th width="70%">Ngày</th>
                <th class="text-end">Tổng Doanh Thu</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grandTotalRevenue = 0;
            if (!empty($reportData)): ?>
                <?php foreach ($reportData as $row): 
                    $revenue = $row['tongDoanhThu'] ?? 0;
                    $grandTotalRevenue += $revenue;
                ?>
                    <tr>
                        <td class="fw-bold"><?= date('d/m/Y', strtotime($row['ngay'])) ?></td>
                        <td class="text-end fw-bold text-success">
                            <?= number_format($revenue, 0, ',', '.') ?> đ
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="table-secondary">
                    <td class="text-end fw-bold fs-5">TỔNG DOANH THU CỘNG DỒN:</td>
                    <td class="text-end fw-bold fs-5 text-success">
                        <?= number_format($grandTotalRevenue, 0, ',', '.') ?> đ
                    </td>
                </tr>
            <?php else: ?>
                <tr><td colspan="2" class="text-center">Không có doanh thu trong khoảng thời gian này.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>