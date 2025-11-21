<?php $info = $data['info']; $details = $data['details']; ?>
<div class="d-flex justify-content-between mb-3">
    <h3>Chi tiết Phiếu Nhập #<?= $info['maPN'] ?></h3>
    <a href="index.php?controller=import&action=list" class="btn btn-secondary">Quay lại</a>
</div>
<div class="card mb-3 p-3">
    <p><strong>Ngày nhập:</strong> <?= $info['ngayNhap'] ?> | <strong>Nhà cung cấp:</strong> <?= $info['tenNCC'] ?> | <strong>Nhân viên:</strong> <?= $info['hoTenNV'] ?></p>
</div>
<table class="table table-bordered">
    <thead><tr><th>Sản phẩm</th><th>SL</th><th>Giá nhập</th><th>Thành tiền</th></tr></thead>
    <tbody>
        <?php while($r = $details->fetch_assoc()): ?>
        <tr>
            <td><?= $r['tenSP'] ?></td>
            <td><?= $r['soLuong'] ?></td>
            <td><?= number_format($r['giaNhap']) ?></td>
            <td><?= number_format($r['thanhTienNhap']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
    <tfoot><tr><td colspan="3" class="text-end fw-bold">TỔNG:</td><td class="fw-bold text-danger"><?= number_format($info['tongGiaTri']) ?> đ</td></tr></tfoot>
</table>