
<?php
$reportData = $reportData ?? [];
$startDate = $startDate ?? date('Y-m-01');
$endDate = $endDate ?? date('Y-m-d');
?>

<h2 class="mt-3 border-bottom pb-2 text-primary">
    <i class="fa-solid fa-user-check"></i> Báo Cáo Hiệu Suất Nhân Viên
</h2>

<div class="mb-3">
    <form method="GET" action="index.php" class="d-flex align-items-end justify-content-end">
        <input type="hidden" name="controller" value="report">
        <input type="hidden" name="action" value="employeeperformance">
        
        <div class="me-3">
            <label class="form-label fw-bold">Từ ngày:</label>
            <input type="date" name="start_date" value="<?= $startDate ?>" class="form-control">
        </div>
        <div class="me-3">
            <label class="form-label fw-bold">Đến ngày:</label>
            <input type="date" name="end_date" value="<?= $endDate ?>" class="form-control">
        </div>
        <button class="btn btn-primary" type="submit">Xem Báo Cáo</button>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-primary text-white">
            <tr>
                <th width="50%">Tên Nhân Viên</th>
                <th width="25%" class="text-center">Tổng Số Hóa Đơn</th>
                <th width="25%" class="text-end">Tổng Doanh Thu</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grandTotalInvoices = 0;
            $grandTotalRevenue = 0;
            if (!empty($reportData)): ?>
                <?php foreach ($reportData as $row): 
                    $invoices = $row['tongHoaDon'] ?? 0;
                    $revenue = $row['tongDoanhThu'] ?? 0;
                    $grandTotalInvoices += $invoices;
                    $grandTotalRevenue += $revenue;
                ?>
                    <tr>
                        <td class="fw-bold"><?= $row['hoTenNV'] ?></td>
                        <td class="text-center"><?= number_format($invoices, 0, ',', '.') ?></td>
                        <td class="text-end fw-bold text-success">
                            <?= number_format($revenue, 0, ',', '.') ?> đ
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="table-secondary">
                    <td class="text-end fw-bold fs-5">TỔNG CỘNG:</td>
                    <td class="text-center fw-bold fs-5">
                        <?= number_format($grandTotalInvoices, 0, ',', '.') ?>
                    </td>
                    <td class="text-end fw-bold fs-5 text-success">
                        <?= number_format($grandTotalRevenue, 0, ',', '.') ?> đ
                    </td>
                </tr>
            <?php else: ?>
                <tr><td colspan="3" class="text-center">Không có nhân viên nào phát sinh doanh thu trong khoảng thời gian này.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>