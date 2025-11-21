<h2 class="mt-3 border-bottom pb-2">Tạo Phiếu Nhập Kho</h2>

<form method="POST" action="index.php?controller=import&action=add" id="formImport">
    
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">Thông tin phiếu nhập</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Nhà cung cấp (*)</label>
                    <select name="maNCC" class="form-select" required>
                        <option value="">-- Chọn nhà cung cấp --</option>
                        <?php 
                        $providers = $providers ?? []; 
                        if(is_array($providers)): 
                            foreach($providers as $p): ?>
                                <option value="<?= $p['maNCC'] ?>"><?= $p['tenNCC'] ?> - <?= $p['soDienThoai'] ?></option>
                            <?php endforeach; 
                        endif; ?>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="fw-bold">Người nhập (*)</label>
                    <select name="maNV" class="form-select" required>
                        <option value="">-- Chọn nhân viên --</option>
                        <?php 
                        $employees = $employees ?? []; 
                        $current_user_id = $_SESSION['user_id'] ?? null;
                        
                        if(is_array($employees)): 
                            foreach($employees as $e): 
                                $selected = ($e['maNV'] == $current_user_id) ? 'selected' : '';
                                ?>
                                <option value="<?= $e['maNV'] ?>" <?= $selected ?>>
                                    <?= $e['hoTenNV'] ?> (ID: <?= $e['maNV'] ?>)
                                </option>
                            <?php endforeach; 
                        endif; ?>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="fw-bold">Ngày nhập</label>
                    <input type="text" class="form-control" value="<?= date('d/m/Y H:i') ?>" disabled>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <span>Danh sách sản phẩm</span>
            <button type="button" id="addRow" class="btn btn-light btn-sm fw-bold text-success">+ Thêm dòng</button>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0" id="tblImport">
                <thead class="table-light">
                    <tr>
                        <th width="40%">Sản phẩm</th>
                        <th width="20%">Giá nhập (đ)</th>
                        <th width="15%">Số lượng</th>
                        <th width="20%">Thành tiền</th>
                        <th width="5%">#</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="item-row">
                        <td>
                            <select name="products[0][maSP]" class="form-select product-select" required onchange="calcRow(this)">
                                <option value="">-- Chọn sản phẩm --</option>   
                                <?php 
                                $products = $products ?? [];
                                if(is_array($products)): 
                                    foreach($products as $sp): ?>
                                        <option value="<?= $sp['maSP'] ?>" data-stock="<?= $sp['soLuongTon'] ?>">
                                            <?= $sp['tenSP'] ?> (Tồn: <?= $sp['soLuongTon'] ?>)
                                        </option>
                                    <?php endforeach; 
                                endif; ?>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="products[0][giaNhap]" class="form-control price-input" min="0" step="1" required oninput="calcRow(this)">
                        </td>
                        <td>
                            <input type="number" name="products[0][soLuong]" class="form-control qty-input" min="1" value="1" required oninput="calcRow(this)">
                        </td>
                        <td class="align-middle text-end fw-bold text-primary row-total">0 đ</td>
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-danger btn-sm delRow"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-bold fs-5">TỔNG TIỀN PHIẾU NHẬP:</td>
                        <td colspan="2" class="fw-bold fs-4 text-danger" id="grandTotal">0 đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer text-end">
            <a href="index.php?controller=import&action=list" class="btn btn-secondary me-2">Hủy bỏ</a>
            <button type="submit" class="btn btn-primary btn-lg px-5">LƯU PHIẾU</button>
        </div>
    </div>
</form>

<script>
    // Lấy options sản phẩm từ PHP để dùng cho dòng mới
    const productOptions = `<?php 
        if(is_array($products)): 
            foreach($products as $sp): 
                $tenSPSafe = addslashes($sp['tenSP']);
                echo "<option value='{$sp['maSP']}' data-stock='{$sp['soLuongTon']}'>{$tenSPSafe} (Tồn: {$sp['soLuongTon']})</option>";
            endforeach; 
        endif; 
    ?>`;

    // Hàm tính tiền từng dòng
    function calcRow(element) {
        let row = element.closest('tr');
        let price = parseFloat(row.querySelector('.price-input').value) || 0;
        let qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        let total = price * qty;
        
        row.querySelector('.row-total').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
        calcGrandTotal();
    }

    // Hàm tính tổng cả phiếu
    function calcGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(function(row) {
            let price = parseFloat(row.querySelector('.price-input').value) || 0;
            let qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            grandTotal += (price * qty);
        });
        document.getElementById('grandTotal').innerText = new Intl.NumberFormat('vi-VN').format(grandTotal) + ' đ';
    }

    // Thêm dòng mới
    document.getElementById('addRow').addEventListener('click', function() {
        let tableBody = document.querySelector('#tblImport tbody');
        let baseRow = tableBody.querySelector('.item-row');
        
        let newRow = baseRow.cloneNode(true);
        let rowIdx = tableBody.querySelectorAll('tr').length;

        // Cập nhật tên input và reset giá trị
        newRow.querySelectorAll('select, input').forEach(element => {
            let nameAttr = element.name;
            if (nameAttr) {
                element.name = nameAttr.replace(/\[\d+\]/g, `[${rowIdx}]`);
            }
        });
        
        newRow.querySelector('.product-select').innerHTML = `<option value="">-- Chọn sản phẩm --</option>${productOptions}`;
        newRow.querySelector('.product-select').value = ""; // Reset chọn SP
        newRow.querySelector('.price-input').value = "0";
        newRow.querySelector('.qty-input').value = "1";
        newRow.querySelector('.row-total').innerText = "0 đ";
        
        tableBody.appendChild(newRow);
        calcGrandTotal();
    });

    // Xóa dòng
    document.addEventListener('click', function(e) {
        if(e.target.closest('.delRow')) {
            let row = e.target.closest('tr');
            let tbody = document.querySelector('#tblImport tbody');
            if(tbody.querySelectorAll('tr').length > 1) {
                row.remove();
                calcGrandTotal(); // Tính lại tiền sau khi xóa
            } else {
                alert("Phải có ít nhất 1 dòng sản phẩm!");
            }
        }
    });

    // Khởi tạo sự kiện oninput/onchange cho dòng đầu tiên khi tải trang
    window.onload = function() {
        document.querySelectorAll('.item-row input, .item-row select').forEach(input => {
            input.addEventListener('input', function() { calcRow(this); });
            input.addEventListener('change', function() { calcRow(this); });
        });
        calcGrandTotal();
    }
</script>