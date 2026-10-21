<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('majors');
?>

<div class="container mt-4">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <h4 class="fw-bold text-primary mb-3">
        <i class="bi bi-plus-circle"></i> <?= t('majors.create_title') ?>
      </h4>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label"><?= t('majors.code_label') ?></label>
          <input type="text" name="maNganh" class="form-control" required placeholder="<?= t('majors.code_placeholder') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('majors.name_label') ?></label>
          <input type="text" name="tenNganh" class="form-control" required placeholder="<?= t('majors.name_placeholder') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('majors.faculty_label') ?></label>
          <select name="maKhoa" class="form-select" required>
            <option value=""><?= t('majors.select_faculty') ?></option>
            <?php
            $faculties = $conn->query("SELECT maKhoa, tenKhoa FROM faculties ORDER BY tenKhoa ASC");
            while ($row = $faculties->fetch_assoc()) {
              $facultyName = t_data('faculties', (string)$row['maKhoa'], 'tenKhoa', (string)$row['tenKhoa']);
              echo "<option value='{$row['maKhoa']}'>{$facultyName}</option>";
            }
            ?>
          </select>
        </div>

        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save"></i> <?= t('common.save') ?></button>
        <a href="index.php" class="btn btn-secondary px-4"><i class="bi bi-arrow-left"></i> <?= t('common.back') ?></a>
      </form>
    </div>
  </div>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $maNganh = trim($_POST["maNganh"]);
    $tenNganh = trim($_POST["tenNganh"]);
    $maKhoa = trim($_POST["maKhoa"]);

    $check = $conn->prepare("SELECT * FROM majors WHERE maNganh = ?");
    $check->bind_param("s", $maNganh);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
          Swal.fire({
            icon: 'error',
            title: " . json_encode(t('majors.error_title')) . ",
            text: " . json_encode(t('majors.create_duplicate_text')) . ",
          });
        </script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO majors (maNganh, tenNganh, maKhoa) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $maNganh, $tenNganh, $maKhoa);

        if ($stmt->execute()) {
            echo "<script>
              Swal.fire({
                icon: 'success',
                title: " . json_encode(t('majors.success_title')) . ",
                text: " . json_encode(t('majors.create_success_text')) . ",
                showConfirmButton: false,
                timer: 2000
              }).then(() => { window.location.href = 'index.php'; });
            </script>";
        } else {
            echo "<script>
              Swal.fire({
                icon: 'error',
                title: " . json_encode(t('majors.error_title')) . ",
                text: " . json_encode(t('majors.create_error_text')) . ",
              });
            </script>";
        }
    }
}
include "../../layout/footer.php";
?>
