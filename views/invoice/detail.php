<?php
// Lấy dữ liệu
$info = $data['info'] ?? null;
$details = $data['details'] ?? null;

if (!$info) {
    echo "<div class='alert alert-danger'>Không tìm thấy thông tin hóa đơn!</div>";
    return;
}
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h2 class="text-primary">Chi tiết Hóa đơn #<?= $info['maHD'] ?></h2>
    <a href="index.php?controller=invoice&action=list" class="btn btn-secondary">Quay lại</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Khách hàng:</strong> <?= $info['hoTenKH'] ?? 'Khách lẻ' ?></p>
                <p><strong>Ngày mua:</strong> <?= date('d/m/Y H:i', strtotime($info['ngayTao'])) ?></p>
            </div>
            <div class="col-md-6 text-end">
                <p><strong>Nhân viên:</strong> <?= $info['hoTenNV'] ?? 'Admin' ?></p>
                <h4 class="text-danger">Tổng tiền: <?= number_format($info['tongTien']) ?> đ</h4>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Sản phẩm</th>
            <th class="text-center">Số lượng</th>
            <th class="text-end">Đơn giá</th>
            <th class="text-end">Thành tiền</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($details): foreach ($details as $row): ?>
        <tr>
            <td><?= $row['tenSP'] ?></td>
            <td class="text-center"><?= $row['soLuong'] ?></td>
            <td class="text-end"><?= number_format($row['donGiaLucMua']) ?> đ</td>
            <td class="text-end fw-bold"><?= number_format($row['thanhTien']) ?> đ</td>
        </tr>
        <?php endforeach; endif; ?>
    </tbody>
</table>