<?php
class InvoiceService {
    private $model; // InvoiceModel
    private $detailModel; // InvoiceDetailModel
    private $productModel; // ProductModel

    public function __construct() {
        $this->model = new InvoiceModel();
        // Giả định InvoiceDetailModel và ProductModel đã được autoload
        $this->detailModel = new InvoiceDetailModel(); 
        $this->productModel = new ProductModel();
    }

    public function countAll() { return $this->model->countAll(); }
    public function getPaginated($limit, $offset) { return $this->model->getPaginated($limit, $offset); }
    public function getAll() { return $this->model->getAll(); }

    public function getById($id) {
        $details = $this->detailModel->getByInvoiceId($id);
        
        return [
            'info' => $this->model->getById($id),
            'details' => $details
        ];
    }

    public function createInvoice($data) {
        $maNV = $data['maNV']; // Lấy ID Nhân viên từ form
        $maKH = $data['maKH'];
        $ngayTao = date('Y-m-d H:i:s');
        $tongTien = 0;
        
        // 1. Tính Tổng Tiền (Dùng giá từ DB để bảo mật)
        if (isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $p) {
                $prodInfo = $this->productModel->getById($p['maSP']);
                if ($prodInfo) {
                    $tongTien += ($p['soLuong'] * $prodInfo['donGiaBan']); 
                }
            }
        }
        
        $tienKhachDua = $data['tienKhachDua'];
        $tienThoi = $tienKhachDua - $tongTien;
        $tienThoi = max(0, $tienThoi);
        
        // 2. Tạo Hóa Đơn Header và LẤY MAHD
        $maHD = $this->model->add($maNV, $maKH, $ngayTao, $tongTien, $tienKhachDua, $tienThoi); 

        // 3. Process Invoice Details
        if ($maHD > 0 && isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $prod) {
                if (empty($prod['maSP']) || empty($prod['soLuong'])) continue;
                
                $product = $this->productModel->getById($prod['maSP']);
                if ($product) {
                    $thanhTien = $prod['soLuong'] * $product['donGiaBan'];

                    // LƯU CHI TIẾT
                    $this->detailModel->add($maHD, $prod['maSP'], $prod['soLuong'], $product['donGiaBan'], $thanhTien);

                    // 4. Update Stock (Subtract quantity) - LOGIC TRỪ TỒN KHO
                    $newStock = $product['soLuongTon'] - $prod['soLuong'];
                    
                    // Cập nhật tồn kho (Gọi hàm update trong ProductModel)
                    $this->productModel->update(
                        $prod['maSP'], 
                        $product['maDM'], 
                        $product['maNCC'], 
                        $product['tenSP'], 
                        $product['donGiaBan'], 
                        $newStock, 
                        $product['donViTinh'], 
                        $product['hanSuDung'], 
                        $product['moTa']
                    );
                }
            }
            return true;
        }
        
        $_SESSION['error'] = "Lỗi: Không tạo được hóa đơn (ID=0) hoặc không có sản phẩm được chọn.";
        return false;
    }

    // =======================================================
    // ** HÀM DISABLE (Ẩn hóa đơn và CỘNG TỒN KHO) **
    // =======================================================
    public function delete($id) {
        // 1. Lấy chi tiết hóa đơn (số lượng sản phẩm đã bán)
        $details = $this->detailModel->getByInvoiceId($id);
        
        // 2. CỘNG tồn kho lại (Trả hàng về kho)
        if (is_array($details)) {
            foreach ($details as $prod) {
                $currentStock = $this->model->getExistingStock($prod['maSP']);
                $newStock = $currentStock + $prod['soLuong']; // CỘNG TỒN KHO
                $this->model->updateStock($prod['maSP'], $newStock);
            }
        }
        
        // 3. Ẩn hóa đơn
        return $this->model->delete($id); 
    }
    
    public function search($key) { return $this->model->search($key); }
}
?>