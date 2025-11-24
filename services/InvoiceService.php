<?php
class InvoiceService {
    private $model; 
    private $detailModel; 
    private $productModel; 

    public function __construct() {
        $this->model = new InvoiceModel();
        $this->detailModel = new InvoiceDetailModel(); 
        $this->productModel = new ProductModel();
    }

    // --- 1. CÁC HÀM LỌC & TÌM KIẾM (QUAN TRỌNG - ĐỂ SỬA LỖI CỦA BẠN) ---
    
    // Hàm này đang bị thiếu gây ra lỗi Fatal Error
    public function searchAdvanced($filters) {
        return $this->model->searchAdvanced($filters);
    }

    public function countAll($dateFrom = null, $dateTo = null, $minTotal = null, $maxTotal = null) { 
        return $this->model->countAll($dateFrom, $dateTo, $minTotal, $maxTotal); 
    }

    public function getPaginated($limit, $offset, $dateFrom = null, $dateTo = null, $minTotal = null, $maxTotal = null) { 
        return $this->model->getPaginated($limit, $offset, $dateFrom, $dateTo, $minTotal, $maxTotal); 
    }
    // ------------------------------------------------------------------

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
        
        // Tính tổng tiền & check tồn kho
        if (isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $p) {
                if (empty($p['maSP']) || empty($p['soLuong'])) continue;
                $prodInfo = $this->productModel->getById($p['maSP']);
                $soLuongCanMua = (int)$p['soLuong'];
                $soLuongTon = (int)$prodInfo['soLuongTon'];
                
                if ($prodInfo && $soLuongCanMua > $soLuongTon) {
                    $_SESSION['error'] = "Lỗi: Sản phẩm **{$prodInfo['tenSP']}** chỉ còn **{$soLuongTon}**.";
                    return false;
                }
                if ($prodInfo) {
                    $tongTien += ($soLuongCanMua * $prodInfo['donGiaBan']); 
                }
            }
        } else {
            $_SESSION['error'] = "Lỗi: Không có sản phẩm nào.";
            return false;
        }
        
        $tienKhachDua = $data['tienKhachDua'];
        $tienThoi = max(0, $tienKhachDua - $tongTien);
        
        // Tạo Hóa đơn
        $maHD = $this->model->add($maNV, $maKH, $ngayTao, $tongTien, $tienKhachDua, $tienThoi); 

        // Lưu chi tiết & Trừ kho
        if ($maHD > 0 && isset($data['products'])) {
            foreach ($data['products'] as $prod) {
                if (empty($prod['maSP'])) continue;
                $product = $this->productModel->getById($prod['maSP']);
                $soLuong = (int)$prod['soLuong'];
                if ($product) {
                    $thanhTien = $soLuong * $product['donGiaBan'];
                    $this->detailModel->add($maHD, $prod['maSP'], $soLuong, $product['donGiaBan'], $thanhTien);
                    
                    $newStock = $product['soLuongTon'] - $soLuong;
                    $this->model->updateStock($prod['maSP'], $newStock);
                }
            }
            return true;
        }
        $_SESSION['error'] = "Lỗi tạo hóa đơn.";
        return false;
    }

    public function delete($id) {
        // Logic ẩn hóa đơn và trả hàng về kho
        $details = $this->detailModel->getByInvoiceId($id);
        if (is_array($details)) {
            foreach ($details as $prod) {
                $currentStock = $this->model->getExistingStock($prod['maSP']);
                $newStock = $currentStock + $prod['soLuong'];
                $this->model->updateStock($prod['maSP'], $newStock);
            }
        }
        return $this->model->delete($id); 
    }
    
    public function restore($id) {
        // Logic khôi phục hóa đơn và trừ lại kho
        $details = $this->detailModel->getByInvoiceId($id);
        if (is_array($details)) {
            foreach ($details as $prod) {
                $currentStock = $this->model->getExistingStock($prod['maSP']);
                $soLuong = $prod['soLuong'];
                if ($currentStock < $soLuong) {
                    $product = $this->productModel->getById($prod['maSP']);
                    $_SESSION['error'] = "Lỗi khôi phục: Sản phẩm {$product['tenSP']} không đủ tồn kho.";
                    return false;
                }
            }
            foreach ($details as $prod) {
                $currentStock = $this->model->getExistingStock($prod['maSP']);
                $newStock = $currentStock - $prod['soLuong'];
                $this->model->updateStock($prod['maSP'], $newStock); 
            }
        }
        return $this->model->restore($id);
    }
    
    public function search($key) { return $this->model->search($key); }
}
?>