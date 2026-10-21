<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('schools');

// --- Lấy mã trường từ URL ---
if (!isset($_GET['matruong'])) {
    echo "<script>" .
         "Swal.fire({" .
         "icon: 'warning'," .
         "title: " . json_encode(t('schools.missing_title')) . "," .
         "text: " . json_encode(t('schools.missing_text')) .
         "}).then(() => { window.location.href = 'index.php'; });" .
         "</script>";
    include "../../layout/footer.php";
    exit;
}

$matruong = $_GET['matruong'];

// --- Lấy dữ liệu hiện tại ---
$stmt = $conn->prepare("SELECT * FROM schools WHERE matruong = ?");
$stmt->bind_param("s", $matruong);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>" .
         "Swal.fire({" .
         "icon: 'error'," .
         "title: " . json_encode(t('schools.not_found_title')) . "," .
         "text: " . json_encode(t('schools.not_found_text')) .
         "}).then(() => { window.location.href = 'index.php'; });" .
         "</script>";
    include "../../layout/footer.php";
    exit;
}

$row = $result->fetch_assoc();

// --- Cập nhật dữ liệu ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"]);
  $maDH = trim($_POST["maDH"]);

    $update = $conn->prepare("UPDATE schools SET name=?, maDH=? WHERE matruong=?");
    $update->bind_param("sss", $name, $maDH, $matruong);

    if ($update->execute()) {
        echo "<script>" .
             "Swal.fire({" .
             "icon: 'success'," .
             "title: " . json_encode(t('schools.create_success_title')) . "," .
             "text: " . json_encode(t('schools.update_success_text')) . "," .
             "showConfirmButton: false," .
             "timer: 2000" .
             "}).then(() => { window.location.href = 'index.php'; });" .
             "</script>";
    } else {
        echo "<script>" .
             "Swal.fire({" .
             "icon: 'error'," .
             "title: " . json_encode(t('schools.exists_title')) . "," .
             "text: " . json_encode(t('schools.update_error_text')) .
             "});" .
             "</script>";
    }
}
?>

<div class="container mt-4">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <h4 class="fw-bold text-primary mb-3">
        <i class="bi bi-pencil-square"></i> <?= t('schools.edit_title') ?>
      </h4>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label"><?= t('schools.code') ?></label>
          <input type="text" name="matruong" class="form-control" value="<?= htmlspecialchars($row['matruong']) ?>" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('schools.name') ?></label>
          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('schools.university_code') ?></label>
          <input type="text" name="maDH" class="form-control" value="<?= htmlspecialchars($row['maDH']) ?>">
        </div>

        <button type="submit" class="btn btn-primary px-4">
          <i class="bi bi-save"></i> <?= t('common.save_changes') ?>
        </button>
        <a href="index.php" class="btn btn-secondary px-4">
          <i class="bi bi-arrow-left"></i> <?= t('common.back') ?>
        </a>
      </form>
    </div>
  </div>
</div>

<?php include "../../layout/footer.php"; ?>
