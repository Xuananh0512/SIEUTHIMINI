<?php
class CustomerModel extends Database
{

    // =======================================================
    // ** PHÂN TRANG: HÀM ĐẾM TỔNG SỐ BẢN GHI **
    // =======================================================
    public function countAll()
    {
        $sql = "SELECT COUNT(*) FROM khachhang";
        $result = $this->conn->query($sql)->fetch_row();
        return $result[0] ?? 0;
    }

    // =======================================================
    // ** PHÂN TRANG: HÀM LẤY DỮ LIỆU CÓ LIMIT & OFFSET **
    // =======================================================
    public function getPaginated($limit, $offset)
    {
        $sql = "SELECT * FROM khachhang ORDER BY maKH DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Lấy tất cả khách hàng đang hoạt động (cho các dropdown như khi tạo hóa đơn)
    public function getAll()
    {
        $sql = "SELECT * FROM khachhang WHERE trangThai = 1 ORDER BY maKH DESC";
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM khachhang WHERE maKH=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function add($hoTenKH, $soDienThoai, $diaChi, $ngaySinh, $email, $diemTichLuy = 0)
    {
        // Thêm trạng thái mặc định là 1 (Hoạt động)
        $trangThai = 1;
        $sql = "INSERT INTO khachhang (hoTenKH, soDienThoai, diaChi, ngaySinh, email, diemTichLuy, trangThai) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        // Lưu ý: TrangThai là i (integer)
        $stmt->bind_param("sssssii", $hoTenKH, $soDienThoai, $diaChi, $ngaySinh, $email, $diemTichLuy, $trangThai);
        return $stmt->execute();
    }

    public function update($maKH, $hoTenKH, $sdt, $diaChi, $ngaySinh, $email, $diemTichLuy, $trangThai)
    {
        // 1. Câu lệnh SQL
        $sql = "UPDATE KhachHang SET 
            hoTenKH = ?, 
            soDienThoai = ?, 
            diaChi = ?, 
            ngaySinh = ?, 
            email = ?, 
            diemTichLuy = ?, 
            trangThai = ? 
            WHERE maKH = ?";

        $stmt = $this->conn->prepare($sql);

        // 2. Liên kết tham số (bind_param)
        // Chuỗi định dạng: sssssiii
        // hoTenKH(s), sdt(s), diaChi(s), ngaySinh(s), email(s), diemTichLuy(i), trangThai(i), maKH(i)

        $stmt->bind_param(
            "sssssiii",
            $hoTenKH,
            $sdt,
            $diaChi,
            $ngaySinh,
            $email, // <-- Đã sửa thành 's' trong format string
            $diemTichLuy,
            $trangThai,
            $maKH
        );

        // 3. Thực thi và trả về kết quả
        return $stmt->execute();
    }

    // =======================================================
    // ** THAY THẾ: HÀM DELETE THÀNH DISABLE (Ẩn) **
    // =======================================================
    public function disable($maKH)
    {
        $sql = "UPDATE KhachHang SET trangThai = 0 WHERE maKH=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maKH);
        return $stmt->execute();
    }

    // =======================================================
    // ** THÊM: HÀM KHÔI PHỤC (RESTORE) **
    // =======================================================
    public function restore($maKH)
    {
        $sql = "UPDATE KhachHang SET trangThai = 1 WHERE maKH=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maKH);
        return $stmt->execute();
    }

    public function search($keyword)
    { /* ... giữ nguyên ... */
    }

    // Thêm vào trong class CustomerModel

    // Kiểm tra trùng số điện thoại
    public function checkPhoneExists($sdt)
    {
        $sql = "SELECT COUNT(*) FROM khachhang WHERE soDienThoai = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $sdt);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_row();
        return $row[0] > 0;
    }

    // Kiểm tra trùng Email
    public function checkEmailExists($email)
    {
        $sql = "SELECT COUNT(*) FROM khachhang WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_row();
        return $row[0] > 0;
    }
}
