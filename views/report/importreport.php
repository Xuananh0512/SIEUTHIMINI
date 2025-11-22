
<?php
$reportData = $reportData ?? [];
$startDate = $startDate ?? date('Y-m-01');
$endDate = $endDate ?? date('Y-m-d');
?>

<h2 class="mt-3 border-bottom pb-2 text-info">
    <i class="fa-solid fa-file-import"></i> Báo Cáo Nhập Hàng
</h2>

<div class="mb-3">
    <form method="GET" action="index.php" class="d-flex align-items-end justify-content-end">
        <input type="hidden" name="controller" value="report">
        <input type="hidden" name="action" value="importreport">
        
        <div class="me-3">
            <label class="form-label fw-bold">Từ ngày:</label>
            <input type="date" name="start_date" value="<?= $startDate ?>" class="form-control">
        </div>
        <div class="me-3">
            <label class="form-label fw-bold">Đến ngày:</label>
            <input type="date" name="end_date" value="<?= $endDate ?>" class="form-control">
        </div>
        <button class="btn btn-info text-white" type="submit">Xem Báo Cáo</button>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-info text-white">
            <tr>
                <th>Mã PN</th>
                <th>Ngày Nhập</th>
                <th>Nhà Cung Cấp</th>
                <th>Nhân Viên Nhập</th>
                <th class="text-end">Tổng Chi Phí</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grandTotalCost = 0;
            if (!empty($reportData)): ?>
                <?php foreach ($reportData as $row): 
                    $cost = $row['tongChi'] ?? 0;
                    $grandTotalCost += $cost;
                ?>
                    <tr>
                        <td>PN<?= $row['maPN'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['ngayNhap'])) ?></td>
                        <td><?= $row['tenNCC'] ?></td>
                        <td><?= $row['hoTenNV'] ?></td>
                        <td class="text-end fw-bold text-danger">
                            <?= number_format($cost, 0, ',', '.') ?> đ
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="table-secondary">
                    <td colspan="4" class="text-end fw-bold fs-5">TỔNG CHI PHÍ NHẬP HÀNG:</td>
                    <td class="text-end fw-bold fs-5 text-danger">
                        <?= number_format($grandTotalCost, 0, ',', '.') ?> đ
                    </td>
                </tr>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">Không có phiếu nhập nào trong khoảng thời gian này.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>