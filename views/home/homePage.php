<div class="dashboard">

    <div class="dashboard-banner mb-4">
        <img src="https://images.unsplash.com/photo-1506619216599-9d16d0903dfd?q=80&w=1500&auto=format&fit=crop"
             class="banner-img" alt="Dashboard Banner">
        <div class="banner-text">
            <h2>Chào mừng đến Hệ thống Quản Lý Siêu Thị Mini</h2>
            <p>Quản lý linh hoạt • Giao diện hiện đại • Hiệu suất tối ưu</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-blue">
                <i class="fa-solid fa-box-open"></i>
                <h4>Sản phẩm</h4>
                <p><?= number_format($countProduct ?? 0) ?></p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-green">
                <i class="fa-solid fa-list"></i>
                <h4>Danh mục</h4>
                <p><?= number_format($countCategory ?? 0) ?></p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-yellow">
                <i class="fa-solid fa-users"></i>
                <h4>Nhân viên</h4>
                <p><?= number_format($countEmployee ?? 0) ?></p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-red">
                <i class="fa-solid fa-coins"></i>
                <h4>Doanh Thu</h4>
                <p><?= number_format($totalRevenue ?? 0) ?>đ</p>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-lg-8">
            <div class="box bg-white p-3 rounded shadow-sm">
                <h5 class="mb-3">Biểu đồ doanh thu năm <?= date('Y') ?></h5>
                <canvas id="revenueChart" style="width:100%; height:350px;"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="box bg-white p-3 rounded shadow-sm">
                <h5>Giao dịch gần đây</h5>
                <ul class="recent-list mt-3 list-unstyled">
                    <?php if (!empty($recentActivities)): ?>
                        <?php foreach ($recentActivities as $act): ?>
                            <li class="mb-3 pb-2 border-bottom">
                                <i class="fa-solid fa-cart-shopping text-success me-2"></i> 
                                <strong>HD<?= $act['maHD'] ?></strong> 
                                - <?= number_format($act['tongTien']) ?>đ
                                <br>
                                <small class="text-muted ms-4">
                                    <i class="fa-regular fa-clock"></i> <?= date('d/m H:i', strtotime($act['ngayTao'])) ?> 
                                    | NV: <?= $act['hoTenNV'] ?>
                                </small>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="text-muted">Chưa có giao dịch nào.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Dữ liệu từ PHP truyền sang JS
    const revenueData = <?= $chartData ?? '[]' ?>;

    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line', // Dạng biểu đồ đường
        data: {
            labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: revenueData,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4 // Độ cong của đường
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<style>
    .dashboard { animation: fadeIn 0.5s ease; }
    .dashboard-banner { position: relative; border-radius: 10px; overflow: hidden; }
    .banner-img { width: 100%; height: 220px; object-fit: cover; filter: brightness(60%); }
    .banner-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; text-align: center; text-shadow: 0 2px 4px rgba(0,0,0,0.7); width: 100%; }
    .stat-card { padding: 20px; border-radius: 10px; color: white; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-card i { font-size: 35px; margin-bottom: 10px; opacity: 0.8; }
    .stat-blue { background: linear-gradient(45deg, #4099ff, #73b4ff); }
    .stat-green { background: linear-gradient(45deg, #2ed8b6, #59e0c5); }
    .stat-yellow { background: linear-gradient(45deg, #FFB64D, #ffcb80); }
    .stat-red { background: linear-gradient(45deg, #FF5370, #ff869a); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>