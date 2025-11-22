<?php
class InvoiceService {
    private $model; // InvoiceModel
    private $detailModel; // InvoiceDetailModel
    private $productModel; // ProductModel

    public function __construct() {
        $this->model = new InvoiceModel();
        $this->detailModel = new InvoiceDetailModel(); 
        $this->productModel = new ProductModel();
    }

    public function countAll() { return $this->model->countAll(); }
    public function getPaginated($limit, $offset) { return $this->model->getPaginated($limit, $offset); }
    public function getAll() { return $this->model->getAll(); }

    public function getById($id) {
        $details = $this->detailModel->getByInvoiceId($id);
        return ['info' => $this->model->getById($id), 'details' => $details];
    }

    public function createInvoice($data) {
        $maNV = $data['maNV'];
        $maKH = $data['maKH'];
        $ngayTao = date('Y-m-d H:i:s');
        $tongTien = 0;
        
        // 1. Tính Tổng Tiền và KIỂM TRA TỒN KHO TRƯỚC
        if (isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $p) {
                if (empty($p['maSP']) || empty($p['soLuong'])) continue;

                $prodInfo = $this->productModel->getById($p['maSP']);
                $soLuongCanMua = (int)$p['soLuong'];
                $soLuongTon = (int)$prodInfo['soLuongTon'];
                
                // ** LOGIC KIỂM TRA TỒN KHO **
                if ($prodInfo && $soLuongCanMua > $soLuongTon) {
                    $_SESSION['error'] = "Lỗi: Sản phẩm **{$prodInfo['tenSP']}** chỉ còn **{$soLuongTon}** sản phẩm, không đủ để bán **{$soLuongCanMua}**.";
                    return false;
                }
                // Nếu đủ, tính vào Tổng tiền
                if ($prodInfo) {
                    $tongTien += ($soLuongCanMua * $prodInfo['donGiaBan']); 
                }
            }
        } else {
            $_SESSION['error'] = "Lỗi: Không có sản phẩm nào được chọn.";
            return false;
        }
        
        $tienKhachDua = $data['tienKhachDua'];
        $tienThoi = $tienKhachDua - $tongTien;
        $tienThoi = max(0, $tienThoi);
        
        // 2. Tạo Hóa Đơn Header và LẤY MAHD
        $maHD = $this->model->add($maNV, $maKH, $ngayTao, $tongTien, $tienKhachDua, $tienThoi); 

        // 3. Process Invoice Details (LƯU VÀ TRỪ TỒN KHO)
        if ($maHD > 0 && isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $prod) {
                if (empty($prod['maSP']) || empty($prod['soLuong'])) continue;
                
                $product = $this->productModel->getById($prod['maSP']);
                $soLuong = (int)$prod['soLuong']; // Lấy lại số lượng đã được kiểm tra
                
                if ($product) {
                    $thanhTien = $soLuong * $product['donGiaBan'];

                    // LƯU CHI TIẾT
                    $this->detailModel->add($maHD, $prod['maSP'], $soLuong, $product['donGiaBan'], $thanhTien);

                    // 4. Update Stock (Subtract quantity) - LOGIC TRỪ TỒN KHO
                    $newStock = $product['soLuongTon'] - $soLuong;
                    
                    // Cập nhật tồn kho (Gọi hàm update trong ProductModel)
                    // Giả định $this->productModel->update là hàm trong ProductModel
                    // $this->productModel->update(
                    //     $prod['maSP'], 
                    //     $product['maDM'], 
                    //     $product['maNCC'], 
                    //     $product['tenSP'], 
                    //     $product['donGiaBan'], 
                    //     $newStock, 
                    //     $product['donViTinh'], 
                    //     $product['hanSuDung'], 
                    //     $product['moTa']
                    // );
                    // SỬ DỤNG HÀM UPDATE STOCK CÓ SẴN TRONG InvoiceModel ĐỂ ĐƠN GIẢN HOÁ
                    $this->model->updateStock($prod['maSP'], $newStock);
                }
            }
            return true;
        }
        
        $_SESSION['error'] = "Lỗi: Không tạo được hóa đơn (ID=0) hoặc không có sản phẩm được chọn.";
        return false;
    }

    // =======================================================
    // ** HÀM DELETE (Ẩn hóa đơn và CỘNG TỒN KHO) **
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
        
        // 3. Ẩn hóa đơn (UPDATE trangThai = 0)
        return $this->model->delete($id); 
    }
    
    // =======================================================
    // ** THÊM: HÀM RESTORE (Khôi phục hóa đơn và TRỪ tồn kho) **
    // =======================================================
    public function restore($id) {
        // 1. Lấy chi tiết hóa đơn
        $details = $this->detailModel->getByInvoiceId($id);
        
        // ** THÊM: KIỂM TRA TỒN KHO TRƯỚC KHI TRỪ (Khôi phục) **
        if (is_array($details)) {
            foreach ($details as $prod) {
                $currentStock = $this->model->getExistingStock($prod['maSP']);
                $soLuong = $prod['soLuong'];

                if ($currentStock < $soLuong) {
                    $product = $this->productModel->getById($prod['maSP']);
                    $_SESSION['error'] = "Lỗi khôi phục: Sản phẩm **{$product['tenSP']}** chỉ còn **{$currentStock}** sản phẩm, không đủ để trừ tồn kho **{$soLuong}**.";
                    return false;
                }
            }
        }

        // 2. TRỪ tồn kho lại (Ghi nhận lại việc bán hàng)
        if (is_array($details)) {
            foreach ($details as $prod) {
                $currentStock = $this->model->getExistingStock($prod['maSP']);
                $newStock = $currentStock - $prod['soLuong']; // TRỪ TỒN KHO
                $this->model->updateStock($prod['maSP'], $newStock); 
            }
        }
        
        // 3. Khôi phục trạng thái hóa đơn (UPDATE trangThai = 1)
        return $this->model->restore($id);
    }
    
    public function search($key) { return $this->model->search($key); }
}
?>