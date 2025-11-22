<?php
class ImportService {
    private $importModel;
    private $detailModel;

    public function __construct() {
        $this->importModel = new ImportModel();
        // Giả định ImportDetailModel đã được định nghĩa
        $this->detailModel = new ImportDetailModel(); 
    }

    public function countAll() { return $this->importModel->countAll(); }
    public function getPaginated($limit, $offset) { return $this->importModel->getPaginated($limit, $offset); }
    public function getAll() { return $this->importModel->getAll(); }

    public function getById($id) {
        // LƯU Ý: Đã sửa lỗi gọi hàm cho chi tiết phiếu nhập
        $details = $this->detailModel->getByImportId($id);
        
        return [
            'info' => $this->importModel->getById($id),
            'details' => $details
        ];
    }

    public function createImport($data) {
        $maNV = $data['maNV']; // LẤY TỪ FORM
        $maNCC = $data['maNCC'];
        $ngayNhap = date('Y-m-d H:i:s');
        $tongGiaTri = 0;

        // 1. Tạo phiếu và LẤY MA PN
        $maPN = $this->importModel->add($maNV, $maNCC, $ngayNhap, 0);
        
        if ($maPN && isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $prod) {
                if (empty($prod['maSP']) || empty($prod['giaNhap']) || empty($prod['soLuong']) || $prod['soLuong'] <= 0) continue;
                
                $thanhTien = $prod['soLuong'] * $prod['giaNhap'];
                $tongGiaTri += $thanhTien;

                // 2. Lưu chi tiết
                $this->detailModel->add($maPN, $prod['maSP'], $prod['soLuong'], $prod['giaNhap'], $thanhTien);
                
                // 3. LOGIC CỘNG TỒN KHO (Tăng tồn khi tạo phiếu)
                $currentStock = $this->importModel->getExistingStock($prod['maSP']);
                $newStock = $currentStock + $prod['soLuong'];
                $this->importModel->updateStock($prod['maSP'], $newStock);
            }
            
            // 4. Cập nhật tổng tiền vào phiếu
            $this->importModel->update($maPN, $maNV, $maNCC, $ngayNhap, $tongGiaTri);
            return true;
        }
        return false;
    }

    // =======================================================
    // ** HÀM DISABLE (Ẩn phiếu và TRỪ tồn kho) **
    // =======================================================
    public function disable($id) { 
        // 1. Lấy chi tiết phiếu nhập
        $details = $this->detailModel->getByImportId($id);
        
        // 2. TRỪ tồn kho cho từng sản phẩm
        if (is_array($details)) {
            foreach ($details as $prod) {
                $currentStock = $this->importModel->getExistingStock($prod['maSP']);
                $newStock = $currentStock - $prod['soLuong'];
                $newStock = max(0, $newStock);
                $this->importModel->updateStock($prod['maSP'], $newStock);
            }
        }
        
        // 3. Ẩn phiếu nhập
        return $this->importModel->disable($id); 
    }
    
    // =======================================================
    // ** HÀM RESTORE (Khôi phục phiếu và CỘNG tồn kho) **
    // =======================================================
    public function restore($id) { 
        // 1. Lấy chi tiết phiếu nhập
        $details = $this->detailModel->getByImportId($id);
        
        // 2. CỘNG tồn kho cho từng sản phẩm
        if (is_array($details)) {
            foreach ($details as $prod) {
                $currentStock = $this->importModel->getExistingStock($prod['maSP']);
                $newStock = $currentStock + $prod['soLuong'];
                $this->importModel->updateStock($prod['maSP'], $newStock);
            }
        }
        
        // 3. Khôi phục phiếu nhập
        return $this->importModel->restore($id); 
    }
    
    // Hàm này được Controller gọi khi action=delete
    public function delete($id) { return $this->disable($id); } 
  
    public function search($key) { return $this->importModel->search($key); }
}
?>