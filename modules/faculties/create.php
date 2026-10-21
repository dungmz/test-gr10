<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('faculties');
?>

<div class="container mt-4">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <h4 class="fw-bold text-primary mb-3">
        <i class="bi bi-plus-circle"></i> <?= t('faculties.add_title') ?>
      </h4>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label"><?= t('faculties.code') ?></label>
          <input type="text" name="maKhoa" class="form-control" required placeholder="<?= t('faculties.code_placeholder') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('faculties.name') ?></label>
          <input type="text" name="tenKhoa" class="form-control" required placeholder="<?= t('faculties.name_placeholder') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('faculties.school') ?></label>
          <select name="matruong" class="form-select" required>
            <option value="">-- <?= t('common.schools') ?> --</option>
            <?php
            $schools = $conn->query("SELECT matruong, name FROM schools ORDER BY name ASC");
            while ($row = $schools->fetch_assoc()) {
              $schoolName = t_data('schools', (string)$row['maTruong'], 'name', (string)$row['name']);
              echo "<option value='{$row['maTruong']}'>{$schoolName}</option>";
            }
            ?>
          </select>
        </div>

        <button type="submit" class="btn btn-primary px-4">
          <i class="bi bi-save"></i> <?= t('common.save') ?>
        </button>
        <a href="index.php" class="btn btn-secondary px-4">
          <i class="bi bi-arrow-left"></i> <?= t('common.back') ?>
        </a>
      </form>
    </div>
  </div>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $maKhoa = trim($_POST["maKhoa"]);
    $tenKhoa = trim($_POST["tenKhoa"]);
    $matruong = trim($_POST["matruong"]);

    $check = $conn->prepare("SELECT * FROM faculties WHERE maKhoa = ?");
    $check->bind_param("s", $maKhoa);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>" .
             "Swal.fire({" .
             "icon: 'error'," .
             "title: " . json_encode(t('faculties.exists_title')) . "," .
             "text: " . json_encode(t('faculties.exists_text')) .
             "});" .
             "</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO faculties (maKhoa, tenKhoa, matruong) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $maKhoa, $tenKhoa, $matruong);

        if ($stmt->execute()) {
            echo "<script>" .
                 "Swal.fire({" .
                 "icon: 'success'," .
                 "title: " . json_encode(t('faculties.create_success_title')) . "," .
                 "text: " . json_encode(t('faculties.create_success_text')) . "," .
                 "showConfirmButton: false," .
                 "timer: 2000" .
                 "}).then(() => { window.location.href = 'index.php'; });" .
                 "</script>";
        } else {
            echo "<script>" .
                 "Swal.fire({" .
                 "icon: 'error'," .
                 "title: " . json_encode(t('faculties.exists_title')) . "," .
                 "text: " . json_encode(t('faculties.create_error_text')) .
                 "});" .
                 "</script>";
        }
    }
}
include "../../layout/footer.php";
?>
