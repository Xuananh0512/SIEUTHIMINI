<?php
// --- ĐOẠN CODE CẤP CỨU: Bỏ đi nếu Controller/Model phụ đã được sửa lỗi ---
if (!isset($products) || !is_array($products)) { $products = (new ProductModel())->getAll(); }
if (!isset($customers) || !is_array($customers)) { $customers = (new CustomerModel())->getAll(); }
$employees = $employees ?? [];
// -------------------------------------------------------
?>
<h2 class="mt-3 border-bottom pb-2 text-primary">
    <i class="fa-solid fa-cart-shopping"></i> Tạo Hóa Đơn Bán Hàng
    <a href="index.php?controller=invoice&action=list" class="btn btn-secondary float-end">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>
</h2>

<form method="POST" action="index.php?controller=invoice&action=add" id="formInvoice" onsubmit="return checkForm()">
    
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Chi tiết đơn hàng</span>
                    <button type="button" id="addRow" class="btn btn-light btn-sm fw-bold text-dark">
                        <i class="fa-solid fa-plus"></i> Thêm sản phẩm
                    </button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0 align-middle" id="tblInvoice">
                        <thead class="table-light text-center">
                            <tr>
                                <th width="40%">Sản phẩm</th>
                                <th width="20%">Đơn giá</th>
                                <th width="15%">Số lượng</th>
                                <th width="20%">Thành tiền</th>
                                <th width="5%">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item-row">
                                <td>
                                    <select name="products[0][maSP]" class="form-select sp-select" required onchange="selectProduct(this)">
                                        <option value="" data-price="0">-- Chọn sản phẩm --</option>
                                        <?php if(isset($products) && is_array($products)): ?>
                                            <?php foreach($products as $p): ?>
                                                <option value="<?= $p['maSP'] ?>" 
                                                        data-price="<?= $p['donGiaBan'] ?>" 
                                                        data-stock="<?= $p['soLuongTon'] ?>">
                                                    <?= $p['tenSP'] ?> (Tồn: <?= $p['soLuongTon'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end price-display" value="0" disabled>
                                    <input type="hidden" name="products[0][donGia]" class="price-hidden" value="0"> 
                                </td>
                                <td>
                                    <input type="number" name="products[0][soLuong]" class="form-control text-center qty-input" value="1" required oninput="calcRow(this)">
                                </td>
                                <td class="text-end fw-bold text-primary row-total">0</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm delRow"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white fw-bold">Thông tin chung</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Nhân viên bán hàng (*)</label>
                        <select name="maNV" class="form-select" required>
                            <option value="">-- Chọn Nhân viên --</option>
                            <?php 
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
                    <div class="mb-3">
                        <label class="fw-bold">Khách hàng (*)</label>
                        <div class="input-group">
                            <select name="maKH" class="form-select" required>
                                <option value="">-- Chọn khách hàng --</option>
                                <?php if(isset($customers) && is_array($customers)): ?>
                                    <?php foreach($customers as $c): ?>
                                        <option value="<?= $c['maKH'] ?>"><?= $c['hoTenKH'] ?> - <?= $c['soDienThoai'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <!-- <a href="index.php?controller=customer&action=add" class="btn btn-outline-secondary" title="Thêm khách mới">+</a> -->
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Ngày bán</label>
                        <input type="text" class="form-control bg-light" value="<?= date('d/m/Y H:i') ?>" disabled>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fs-5">Tổng tiền hàng:</span>
                        <span class="fs-4 fw-bold text-danger" id="grandTotalDisplay">0</span>
                        <input type="hidden" name="tongTien" id="grandTotalValue" value="0">
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="fw-bold text-success">Tiền khách đưa:</label>
                        <input type="number" name="tienKhachDua" id="customerPay" class="form-control form-control-lg border-success" step="1000" placeholder="Nhập số tiền..." required oninput="calcChange()">
                    </div>
                    <div class="d-flex justify-content-between mt-3 p-2 bg-light rounded">
                        <span class="fw-bold">Tiền thừa trả khách:</span>
                        <span class="fw-bold text-primary fs-5" id="changeAmount">0</span>
                        <input type="hidden" name="tienThoi" id="changeAmountValue" value="0">
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold">
                            <i class="fa-solid fa-print me-2"></i> THANH TOÁN & LƯU
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Lấy options sản phẩm từ PHP để dùng cho dòng mới
    const productOptions = `<?php 
        if(is_array($products)): 
            foreach($products as $p): 
                $tenSPSafe = addslashes($p['tenSP']);
                echo "<option value='{$p['maSP']}' data-price='{$p['donGiaBan']}' data-stock='{$p['soLuongTon']}'>{$tenSPSafe} (Tồn: {$p['soLuongTon']})</option>";
            endforeach; 
        endif; 
    ?>`;
    
    // 1. Khi chọn sản phẩm -> Tự điền giá
    function selectProduct(select) {
        let option = select.options[select.selectedIndex];
        let price = parseFloat(option.getAttribute('data-price')) || 0;
        let stock = parseInt(option.getAttribute('data-stock')) || 0;
        
        let row = select.closest('tr');
        let qtyInput = row.querySelector('.qty-input');

        if(select.value !== "" && stock <= 0) {
            alert("Sản phẩm này đã hết hàng trong kho!");
            qtyInput.value = 0; // Đặt số lượng là 0 nếu hết hàng
        } else {
            qtyInput.value = 1; // Đặt lại số lượng là 1 khi chọn sản phẩm hợp lệ
        }

        row.querySelector('.price-display').value = new Intl.NumberFormat('vi-VN').format(price);
        row.querySelector('.price-hidden').value = price; // Lưu giá trị thực
        
        calcRow(qtyInput); // Gọi calcRow để kích hoạt kiểm tra và tính toán
    }

    // 2. Tính tiền từng dòng (Giá x Số lượng) - ** LOGIC KIỂM TRA TỒN KHO **
    function calcRow(inputElement) {
        let row = inputElement.closest('tr');
        let price = parseFloat(row.querySelector('.price-hidden').value) || 0;
        let qtyInput = row.querySelector('.qty-input');
        let qty = parseInt(qtyInput.value) || 0; // Dùng parseInt cho số lượng

        let selectElement = row.querySelector('.sp-select');
        let stock = 0;
        if (selectElement.value) {
            let option = selectElement.options[selectElement.selectedIndex];
            stock = parseInt(option.getAttribute('data-stock')) || 0;
        }

        if (qty < 0) { // Không cho phép nhập số âm
            qtyInput.value = 1;
            qty = 1;
        }

        if (qty > stock && stock > 0) {
            alert(`Số lượng mua không được vượt quá số lượng tồn kho (${stock}).`);
            qtyInput.value = stock; 
            qty = stock;
        }

        if (stock === 0 && qty > 0) {
            qtyInput.value = 0;
            qty = 0;
            alert("Sản phẩm này đã hết hàng trong kho. Vui lòng chọn sản phẩm khác.");
        }
        
        let total = price * qty;

        row.querySelector('.row-total').innerText = new Intl.NumberFormat('vi-VN').format(total);
        calcGrandTotal(); 
    }

    // 3. Tính tổng tiền cả hóa đơn
    function calcGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(function(row) {
            let price = parseFloat(row.querySelector('.price-hidden').value) || 0;
            let qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            grandTotal += (price * qty);
        });

        document.getElementById('grandTotalDisplay').innerText = new Intl.NumberFormat('vi-VN').format(grandTotal) + ' đ';
        document.getElementById('grandTotalValue').value = grandTotal;
        
        calcChange();
    }

    // 4. Tính tiền thừa trả khách
    function calcChange() {
        let total = parseFloat(document.getElementById('grandTotalValue').value) || 0;
        let pay = parseFloat(document.getElementById('customerPay').value) || 0;
        let change = pay - total;

        let displayChange = new Intl.NumberFormat('vi-VN').format(Math.max(0, change)) + ' đ';
        
        document.getElementById('changeAmount').innerText = displayChange;
        document.getElementById('changeAmountValue').value = (change > 0) ? change : 0;
    }

    // 5. Thêm dòng sản phẩm mới
    document.getElementById('addRow').addEventListener('click', function() {
        let tableBody = document.querySelector('#tblInvoice tbody');
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
        
        // Reset giá trị và sự kiện
        let selectElement = newRow.querySelector('.sp-select');
        selectElement.innerHTML = `<option value="" data-price="0">-- Chọn sản phẩm --</option>${productOptions}`;
        selectElement.value = "";
        
        newRow.querySelector('.price-display').value = "0";
        newRow.querySelector('.price-hidden').value = "0";
        newRow.querySelector('.qty-input').value = "1";
        newRow.querySelector('.row-total').innerText = "0";
        
        tableBody.appendChild(newRow);
        
        // Gán lại sự kiện cho các input mới
        newRow.querySelector('.sp-select').addEventListener('change', function() { selectProduct(this); });
        newRow.querySelector('.qty-input').addEventListener('input', function() { calcRow(this); });
    });

    // 6. Xóa dòng
    document.addEventListener('click', function(e) {
        if(e.target.closest('.delRow')) {
            let row = e.target.closest('tr');
            let tbody = document.querySelector('#tblInvoice tbody');
            if(tbody.querySelectorAll('tr').length > 1) {
                row.remove();
                calcGrandTotal();
            } else {
                alert("Đơn hàng phải có ít nhất 1 sản phẩm!");
            }
        }
    });

    // 7. Kiểm tra trước khi Lưu
    function checkForm() {
        let total = parseFloat(document.getElementById('grandTotalValue').value) || 0;
        let pay = parseFloat(document.getElementById('customerPay').value) || 0;
        let hasProduct = false;

        document.querySelectorAll('.item-row').forEach(function(row) {
            let selectElement = row.querySelector('.sp-select');
            let qty = parseInt(row.querySelector('.qty-input').value) || 0;
            if (selectElement.value && qty > 0) {
                hasProduct = true;
            }
        });

        if (!hasProduct) {
            alert("Vui lòng chọn ít nhất một sản phẩm và nhập số lượng lớn hơn 0!");
            return false;
        }

        if (total === 0) {
            alert("Tổng tiền hàng không hợp lệ. Vui lòng kiểm tra lại sản phẩm và số lượng.");
            return false;
        }
        if (pay < total) {
            alert("Khách chưa đưa đủ tiền!");
            return false;
        }
        return true;
    }

    // Tính tổng khi tải trang
    window.onload = function() {
        document.getElementById('customerPay').addEventListener('input', calcChange);
        calcGrandTotal(); 
    }
</script>