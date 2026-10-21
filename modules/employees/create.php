<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('employees');
?>

<div class="container mt-4">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <h4 class="fw-bold text-primary mb-3">
        <i class="bi bi-person-add"></i> <?= t('employees.create_title') ?>
      </h4>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label"><?= t('employees.name_label') ?></label>
          <input type="text" name="tenNV" class="form-control" placeholder="<?= t('employees.name_placeholder') ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('employees.position_label') ?></label>
          <input type="text" name="chucVu" class="form-control" placeholder="<?= t('employees.position_placeholder') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('employees.email_label') ?></label>
          <input type="email" name="email" class="form-control" placeholder="<?= t('employees.email_placeholder') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('employees.phone_label') ?></label>
          <input type="text" name="soDienThoai" class="form-control" placeholder="<?= t('employees.phone_placeholder') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('employees.school') ?></label>
          <select name="maTruong" class="form-select" required>
            <option value=""><?= t('employees.select_school') ?></option>
            <?php
            $schools = $conn->query("SELECT matruong, name FROM schools ORDER BY name ASC");
            while ($row = $schools->fetch_assoc()) {
              $schoolName = t_data('schools', (string)$row['matruong'], 'name', (string)$row['name']);
              echo "<option value='{$row['matruong']}'>{$schoolName}</option>";
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
    $tenNV = trim($_POST["tenNV"]);
    $chucVu = trim($_POST["chucVu"]);
    $email = trim($_POST["email"]);
    $soDienThoai = trim($_POST["soDienThoai"]);
    $maTruong = trim($_POST["maTruong"]);

    $check = $conn->prepare("SELECT * FROM employees WHERE tenNV = ?");
    $check->bind_param("s", $tenNV);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
          Swal.fire({
            icon: 'error',
            title: " . json_encode(t('employees.error_title')) . ",
            text: " . json_encode(t('employees.create_duplicate_text')) . ",
          });
        </script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO employees (tenNV, chucVu, email, soDienThoai, maTruong)
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $tenNV, $chucVu, $email, $soDienThoai, $maTruong);

        if ($stmt->execute()) {
            echo "<script>
              Swal.fire({
                icon: 'success',
                title: " . json_encode(t('employees.success_title')) . ",
                text: " . json_encode(t('employees.create_success_text')) . ",
                showConfirmButton: false,
                timer: 2000
              }).then(() => { window.location.href = 'index.php'; });
            </script>";
        } else {
            echo "<script>
              Swal.fire({
                icon: 'error',
                title: " . json_encode(t('employees.error_title')) . ",
                text: " . json_encode(t('employees.create_error_text')) . ",
              });
            </script>";
        }
    }
}
include "../../layout/footer.php";
?>
