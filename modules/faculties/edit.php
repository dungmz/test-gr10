<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('faculties');

// Kiểm tra có mã khoa không
if (!isset($_GET['maKhoa'])) {
    echo "<script>" .
         "Swal.fire({" .
         "icon: 'warning'," .
         "title: " . json_encode(t('faculties.missing_title')) . "," .
         "text: " . json_encode(t('faculties.missing_text')) .
         "}).then(() => { window.location.href = 'index.php'; });" .
         "</script>";
    include "../../layout/footer.php";
    exit;
}

$maKhoa = $_GET['maKhoa'];

// Lấy dữ liệu hiện tại
$stmt = $conn->prepare("SELECT * FROM faculties WHERE maKhoa = ?");
$stmt->bind_param("s", $maKhoa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>" .
         "Swal.fire({" .
         "icon: 'error'," .
         "title: " . json_encode(t('faculties.not_found_title')) . "," .
         "text: " . json_encode(t('faculties.not_found_text')) .
         "}).then(() => { window.location.href = 'index.php'; });" .
         "</script>";
    include "../../layout/footer.php";
    exit;
}

$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tenKhoa = trim($_POST["tenKhoa"]);
    $matruong = trim($_POST["matruong"]);

    $update = $conn->prepare("UPDATE faculties SET tenKhoa=?, matruong=? WHERE maKhoa=?");
    $update->bind_param("sss", $tenKhoa, $matruong, $maKhoa);

    if ($update->execute()) {
        echo "<script>" .
             "Swal.fire({" .
             "icon: 'success'," .
             "title: " . json_encode(t('faculties.create_success_title')) . "," .
             "text: " . json_encode(t('faculties.update_success_text')) . "," .
             "showConfirmButton: false," .
             "timer: 2000" .
             "}).then(() => { window.location.href = 'index.php'; });" .
             "</script>";
    } else {
        echo "<script>" .
             "Swal.fire({" .
             "icon: 'error'," .
             "title: " . json_encode(t('faculties.exists_title')) . "," .
             "text: " . json_encode(t('faculties.update_error_text')) .
             "});" .
             "</script>";
    }
}
?>

<div class="container mt-4">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <h4 class="fw-bold text-primary mb-3"><i class="bi bi-pencil-square"></i> <?= t('faculties.edit_title') ?></h4>

     <form method="POST" action="save.php">

        <div class="mb-3">
          <label class="form-label"><?= t('faculties.code') ?></label>
          <input type="text" name="maKhoa" class="form-control" value="<?= htmlspecialchars($row['maKhoa']) ?>" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('faculties.name') ?></label>
          <input type="text" name="tenKhoa" class="form-control" value="<?= htmlspecialchars($row['tenKhoa']) ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('faculties.school') ?></label>
          <select name="matruong" class="form-select" required>
            <?php
            $schools = $conn->query("SELECT matruong, name FROM schools ORDER BY name ASC");
            while ($s = $schools->fetch_assoc()) {
              $selected = ($s['matruong'] == $row['matruong']) ? "selected" : "";
                 $schoolName = t_data('schools', (string)$s['matruong'], 'name', (string)$s['name']);
                 echo "<option value='{$s['matruong']}' $selected>{$schoolName}</option>";
            }
            ?>
          </select>
        </div>

        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save"></i> <?= t('common.save_changes') ?></button>
        <a href="index.php" class="btn btn-secondary px-4"><i class="bi bi-arrow-left"></i> <?= t('common.back') ?></a>
      </form>
    </div>
  </div>
</div>

<?php include "../../layout/footer.php"; ?>
