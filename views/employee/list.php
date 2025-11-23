<div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản lý Nhân Viên</h1>
    <a href="index.php?controller=employee&action=add" class="btn btn-primary">+ Thêm nhân viên</a>
</div>

<?php
// Controller đã truyền vào các biến thông qua hàm extract() trong index.php.
// Các biến mặc định cho view
$employees = $employees ?? [];
$total_pages = $total_pages ?? 1;
$current_page = $current_page ?? 1;
$total_records = $total_records ?? 0;
$controller = 'employee';
$action = 'list';

// --- BIẾN TÌM KIẾM/LỌC (ĐƯỢC TRUYỀN TỪ CONTROLLER) ---
$roles = $roles ?? []; // Danh sách vai trò để tạo dropdown
$search_keyword = $search_keyword ?? '';
$search_role = $search_role ?? '';
$search_start_date = $search_start_date ?? '';
$search_end_date = $search_end_date ?? '';

// === TẠO CHUỖI QUERY STRING CHO PHÂN TRANG VÀ THAO TÁC ===
// Giữ lại các tham số tìm kiếm/lọc khi chuyển trang
$filter_query = http_build_query([
    'keyword' => $search_keyword,
    'role' => $search_role,
    'start_date' => $search_start_date,
    'end_date' => $search_end_date
]);
// Loại bỏ các tham số rỗng để URL gọn hơn
$filter_query = preg_replace('/(&[^=]+)=$/', '', $filter_query);
$filter_query = !empty($filter_query) ? '&' . $filter_query : '';

$reset_url = "index.php?controller={$controller}&action={$action}";
?>

<div class="card card-body bg-light mb-4 shadow-sm">
    <form method="GET" action="index.php" class="row g-3 align-items-end">
        <input type="hidden" name="controller" value="<?= $controller ?>">
        <input type="hidden" name="action" value="<?= $action ?>">

        <div class="col-md-4">
            <label for="keyword" class="form-label fw-bold">Tìm kiếm chung (Tên, SĐT, Email)</label>
            <input type="text" name="keyword" id="keyword" class="form-control"
                placeholder="Nhập từ khóa tìm kiếm..." value="<?= htmlspecialchars($search_keyword) ?>">
        </div>

        <div class="col-md-3">
            <label for="role" class="form-label fw-bold">Lọc theo Chức vụ</label>
            <select name="role" id="role" class="form-select">
                <option value="">-- Tất cả chức vụ --</option>
                <?php
                if (is_array($roles) && !empty($roles)):
                    foreach ($roles as $role):
                        // Giả định $roles có 'maVaiTro' và 'tenVaiTro'
                        $role_id = $role['maVaiTro'] ?? '';
                        $role_name = $role['tenVaiTro'] ?? '';
                        $selected = ($search_role == $role_id) ? 'selected' : '';
                ?>
                        <option value="<?= $role_id ?>" <?= $selected ?>>
                            <?= htmlspecialchars($role_name) ?>
                        </option>
                <?php
                    endforeach;
                endif;
                ?>
            </select>
        </div>

        <div class="col-md-2">
            <label for="start_date" class="form-label fw-bold">Ngày vào làm (Từ)</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($search_start_date) ?>">
        </div>

        <div class="col-md-2">
            <label for="end_date" class="form-label fw-bold">Ngày vào làm (Đến)</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($search_end_date) ?>">
        </div>

        <div class="col-md-1 d-flex flex-column justify-content-end">
            <button type="submit" class="btn btn-primary w-100 mb-2">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
            <a href="<?= $reset_url ?>" class="btn btn-secondary w-100">
                <i class="fa-solid fa-sync-alt"></i> Xóa lọc
            </a>
        </div>
    </form>
</div>
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Mã NV</th>
                <th>Họ Tên</th>
                <th>Chức vụ</th>
                <th>Liên hệ</th>
                <th>Ngày vào làm</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($employees) && is_array($employees) && count($employees) > 0): ?>
                <?php foreach ($employees as $row): ?>
                    <tr class="<?= $row['trangThaiLamViec'] == 0 ? 'table-secondary text-muted' : '' ?>">
                        <td>NV<?= $row['maNV'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['hoTenNV']) ?></td>
                        <td><?= htmlspecialchars($row['tenVaiTro'] ?? 'N/A') ?></td>
                        <td>
                            SĐT: <?= htmlspecialchars($row['soDienThoai']) ?><br>
                            Email: <small><?= htmlspecialchars($row['email']) ?></small>
                        </td>
                        <td><?= date('d/m/Y', strtotime($row['ngayVaoLam'])) ?></td>
                        <td>
                            <span class="badge bg-<?= $row['trangThaiLamViec'] == 1 ? 'success' : 'danger' ?>">
                                <?= $row['trangThaiLamViec'] == 1 ? 'Đang làm' : 'Đã nghỉ' ?>
                            </span>
                        </td>
                        <td>
                            <a href="index.php?controller=employee&action=edit&id=<?= $row['maNV'] ?>"
                                class="btn btn-sm btn-info text-white me-1" title="Sửa">
                                Sửa
                            </a>
                            <?php if ($row['trangThaiLamViec'] == 1): ?>
                                <a href="index.php?controller=employee&action=delete&id=<?= $row['maNV'] ?>"
                                    class="btn btn-sm btn-danger me-1" title="Cho nghỉ"
                                    onclick="return confirm('Xác nhận cho nhân viên <?= htmlspecialchars($row['hoTenNV']) ?> nghỉ việc?');">
                                    Cho nghỉ
                                </a>
                            <?php else: ?>
                                <a href="index.php?controller=employee&action=restore&id=<?= $row['maNV'] ?>"
                                    class="btn btn-sm btn-success me-1" title="Khôi phục"
                                    onclick="return confirm('Xác nhận khôi phục làm việc cho nhân viên <?= htmlspecialchars($row['hoTenNV']) ?>?');">
                                    Khôi phục
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">
                        Không tìm thấy nhân viên nào <?= !empty($filter_query) ? 'với điều kiện lọc này.' : '.' ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($total_pages > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">

            <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="index.php?controller=<?= $controller ?>&action=<?= $action ?>&page=<?= $current_page - 1 ?><?= $filter_query ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <?php
            // Logic hiển thị tối đa 5 nút trang
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);

            if ($current_page <= 3) {
                $end_page = min($total_pages, 5);
            }
            if ($current_page > $total_pages - 3) {
                $start_page = max(1, $total_pages - 4);
            }

            for ($i = $start_page; $i <= $end_page; $i++): ?>
                <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                    <a class="page-link" href="index.php?controller=<?= $controller ?>&action=<?= $action ?>&page=<?= $i ?><?= $filter_query ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="index.php?controller=<?= $controller ?>&action=<?= $action ?>&page=<?= $current_page + 1 ?><?= $filter_query ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>

<div class="text-center mt-3">
    <p class="text-muted">Tổng cộng: <span class="fw-bold text-black"><?= $total_records ?></span> nhân viên</p>
</div>