<h2 class="mt-3">Sửa Danh Mục</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label class="form-label">Tên danh mục (*)</label>
        <input type="text" class="form-control" name="tenDM" value="<?= $item['tenDM'] ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Cập nhật</button>
    <a href="index.php?controller=category&action=list" class="btn btn-secondary">Hủy</a>
</form>