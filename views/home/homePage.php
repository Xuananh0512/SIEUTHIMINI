
<div class="dashboard">

    <!-- Banner -->
    <div class="dashboard-banner mb-4">
        <img src="https://images.unsplash.com/photo-1506619216599-9d16d0903dfd?q=80&w=1500&auto=format&fit=crop"
             class="banner-img" alt="Dashboard Banner">
        <div class="banner-text">
            <h2>Chào mừng đến Hệ thống Quản Lý Siêu Thị Mini</h2>
            <p>Quản lý linh hoạt • Giao diện hiện đại • Hiệu suất tối ưu</p>
        </div>
    </div>

    <!-- Thống kê -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-blue">
                <i class="fa-solid fa-box-open"></i>
                <h4>Sản phẩm</h4>
                <p>1,250</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-green">
                <i class="fa-solid fa-list"></i>
                <h4>Danh mục</h4>
                <p>32</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-yellow">
                <i class="fa-solid fa-users"></i>
                <h4>Nhân viên</h4>
                <p>18</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-red">
                <i class="fa-solid fa-coins"></i>
                <h4>Doanh Thu</h4>
                <p>350.000.000đ</p>
            </div>
        </div>
    </div>

    <!-- Nội dung 2 cột -->
    <div class="row g-4">

        <!-- Biểu đồ -->
        <div class="col-lg-8">
            <div class="box bg-white p-3 rounded shadow-sm">
                <h5 class="mb-3">Biểu đồ doanh thu</h5>
                <img src="https://www.phatdat.com.vn/wp-content/uploads/2020/10/doanh-thu-va-loi-nhuan-pdr.png" alt="Biểu đồ" class="w-100 rounded">
            </div>
        </div>

        <!-- Hoạt động -->
        <div class="col-lg-4">
            <div class="box bg-white p-3 rounded shadow-sm">
                <h5>Hoạt động gần đây</h5>
                <ul class="recent-list mt-3">
                    <li><i class="fa-solid fa-check text-success"></i> Nhập 200 sản phẩm mới.</li>
                    <li><i class="fa-solid fa-user-plus text-primary"></i> Thêm nhân viên Nguyễn Văn A.</li>
                    <li><i class="fa-solid fa-cart-shopping text-warning"></i> 38 hóa đơn được tạo hôm nay.</li>
                    <li><i class="fa-solid fa-truck text-info"></i> Nhà cung cấp ABC đã giao hàng.</li>
                </ul>
            </div>
        </div>

    </div>

</div>

<style>
    .dashboard { animation: fadeIn 0.4s ease-in-out; }

    /* Banner */
    .dashboard-banner {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
    }

    .banner-img {
        width: 100%;
        height: 260px;
        object-fit: cover;
        filter: brightness(70%);
    }

    .banner-text {
        position: absolute;
top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        text-align: center;
        text-shadow: 0px 0px 10px black;
    }

    .banner-text h2 {
        font-size: 30px;
        font-weight: bold;
    }

    .stat-card {
        padding: 20px;
        border-radius: 10px;
        color: white;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: transform .2s;
    }

    .stat-card:hover { transform: translateY(-6px); }

    .stat-card i { font-size: 40px; margin-bottom: 10px; }

    .stat-blue   { background: #0d6efd; }
    .stat-green  { background: #198754; }
    .stat-yellow { background: #ffc107; color: black; }
    .stat-red    { background: #dc3545; }

    .box img { border: 1px solid #ddd; }

    .recent-list li {
        margin-bottom: 10px;
        font-size: 15px;
        list-style: none;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>