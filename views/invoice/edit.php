<h2>Edit Invoice</h2>
<form method="post" action="index.php?controller=invoice&action=edit&id=<?= $item['maHD'] ?>">
    Employee ID: <input type="number" name="maNV" value="<?= $item['maNV'] ?>" required><br>
    Customer ID: <input type="number" name="maKH" value="<?= $item['maKH'] ?>" required><br>
    Date: <input type="datetime-local" name="ngayTao" value="<?= $item['ngayTao'] ?>"><br>
    Total: <input type="number" step="0.01" name="tongTien" value="<?= $item['tongTien'] ?>"><br>
    Paid: <input type="number" step="0.01" name="tienKhachDua" value="<?= $item['tienKhachDua'] ?>"><br>
    Change: <input type="number" step="0.01" name="tienThoi" value="<?= $item['tienThoi'] ?>"><br>
    <button type="submit">Update</button>
    <a href="index.php?controller=invoice&action=list">Cancel</a>
</form>
