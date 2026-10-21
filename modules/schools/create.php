<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('schools');
?>

<div class="container mt-4">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <h4 class="fw-bold text-primary mb-3">
        <i class="bi bi-building"></i> <?= t('schools.add_title') ?>
      </h4>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label"><?= t('schools.code') ?></label>
          <input type="text" name="matruong" class="form-control" placeholder="<?= t('schools.code_placeholder') ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('schools.name') ?></label>
          <input type="text" name="name" class="form-control" placeholder="<?= t('schools.name_placeholder') ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('schools.university_code') ?></label>
          <input type="text" name="maDH" class="form-control" placeholder="<?= t('schools.university_code_placeholder') ?>">
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
    $matruong = trim($_POST["matruong"]);
    $name = trim($_POST["name"]);
    $maDH = trim($_POST["maDH"]);

    // Kiểm tra trùng mã trường
    $check = $conn->prepare("SELECT * FROM schools WHERE matruong = ?");
    $check->bind_param("s", $matruong);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // ❌ Mã trường đã tồn tại
        echo "<script>" .
             "Swal.fire({" .
             "icon: 'error'," .
             "title: " . json_encode(t('schools.exists_title')) . "," .
             "text: " . json_encode(t('schools.exists_text')) . "," .
             "confirmButtonColor: '#3085d6'" .
             "});" .
             "</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO schools (matruong, name, maDH) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $matruong, $name, $maDH);

        if ($stmt->execute()) {
            echo "<script>" .
                 "Swal.fire({" .
                 "icon: 'success'," .
                 "title: " . json_encode(t('schools.create_success_title')) . "," .
                 "text: " . json_encode(t('schools.create_success_text')) . "," .
                 "showConfirmButton: false," .
                 "timer: 2000" .
                 "}).then(() => { window.location.href = 'index.php'; });" .
                 "</script>";
        } else {
            echo "<script>" .
                 "Swal.fire({" .
                 "icon: 'error'," .
                 "title: " . json_encode(t('schools.exists_title')) . "," .
                 "text: " . json_encode(t('schools.create_error_text')) . "," .
                 "confirmButtonColor: '#3085d6'" .
                 "});" .
                 "</script>";
        }
    }
}
include "../../layout/footer.php";
?>
