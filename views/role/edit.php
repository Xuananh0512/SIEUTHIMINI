<h2 class="mt-3">Cập Nhật Vai Trò</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label>Tên vai trò (*)</label>
        <input type="text" name="tenVaiTro" class="form-control" value="<?= $item['tenVaiTro'] ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Cập nhật</button>
    <a href="index.php?controller=role&action=list" class="btn btn-secondary">Hủy</a>
</form>