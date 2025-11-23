<?php
class HomeModel extends Database {
    
    // 1. Đếm tổng số lượng (Dùng chung cho sp, nv, dm)
    public function countTable($tableName) {
        $sql = "SELECT COUNT(*) FROM $tableName";
        // Nếu là bảng nhân viên hoặc sản phẩm, chỉ đếm cái đang hoạt động (trangThai = 1)
        if ($tableName === 'nhanvien') {
            $sql .= " WHERE trangThaiLamViec = 1";
        }
        if ($tableName === 'sanpham' || $tableName === 'hoadon') {
            $sql .= " WHERE trangThai = 1"; // Giả sử bạn đã thêm cột trangThai như bài trước
        }
        
        $result = $this->conn->query($sql);
        $row = $result->fetch_row();
        return $row[0] ?? 0;
    }

    // 2. Tính tổng doanh thu (Chỉ tính hóa đơn đã hoàn thành/không bị ẩn)
    public function getTotalRevenue() {
        $sql = "SELECT SUM(tongTien) FROM hoadon WHERE trangThai = 1";
        $result = $this->conn->query($sql);
        $row = $result->fetch_row();
        return $row[0] ?? 0;
    }

    // 3. Lấy dữ liệu cho Biểu đồ (Doanh thu 12 tháng trong năm nay)
    public function getMonthlyRevenue($year) {
        $data = array_fill(1, 12, 0); // Tạo mảng mặc định 12 tháng = 0
        
        $sql = "SELECT MONTH(ngayTao) as thang, SUM(tongTien) as tong 
                FROM hoadon 
                WHERE YEAR(ngayTao) = ? AND trangThai = 1 
                GROUP BY MONTH(ngayTao)";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $data[(int)$row['thang']] = (float)$row['tong'];
        }
        return array_values($data); // Trả về mảng chuẩn index 0-11
    }

    // 4. Lấy hoạt động gần đây (Vd: 5 hóa đơn mới nhất)
    public function getRecentActivities() {
        $sql = "SELECT hd.maHD, hd.tongTien, hd.ngayTao, nv.hoTenNV 
                FROM hoadon hd
                LEFT JOIN nhanvien nv ON hd.maNV = nv.maNV
                WHERE hd.trangThai = 1
                ORDER BY hd.ngayTao DESC LIMIT 5";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>