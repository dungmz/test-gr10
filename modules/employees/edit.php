<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('employees');

// --- Lấy tên nhân viên từ URL ---
if (!isset($_GET['tenNV'])) {
    echo "<script>
      Swal.fire({
        icon: 'warning',
        title: " . json_encode(t('employees.warning_title')) . ",
        text: " . json_encode(t('employees.missing_edit_text')) . ",
      }).then(() => {
        window.location.href = 'index.php';
      });
    </script>";
    include "../../layout/footer.php";
    exit;
}

$tenNV = $_GET['tenNV'];

// --- Lấy dữ liệu hiện tại ---
$stmt = $conn->prepare("SELECT * FROM employees WHERE tenNV = ?");
$stmt->bind_param("s", $tenNV);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>
      Swal.fire({
        icon: 'error',
        title: " . json_encode(t('employees.not_found_title')) . ",
        text: " . json_encode(t('employees.not_found_text')) . ",
      }).then(() => {
        window.location.href = 'index.php';
      });
    </script>";
    include "../../layout/footer.php";
    exit;
}

$row = $result->fetch_assoc();

// --- Cập nhật dữ liệu ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $chucVu = trim($_POST["chucVu"]);
    $email = trim($_POST["email"]);
    $soDienThoai = trim($_POST["soDienThoai"]);
    $maTruong = trim($_POST["maTruong"]);

    $update = $conn->prepare("UPDATE employees SET chucVu=?, email=?, soDienThoai=?, maTruong=? WHERE tenNV=?");
    $update->bind_param("sssss", $chucVu, $email, $soDienThoai, $maTruong, $tenNV);

    if ($update->execute()) {
        echo "<script>
          Swal.fire({
            icon: 'success',
            title: " . json_encode(t('employees.success_title')) . ",
            text: " . json_encode(t('employees.update_success_text')) . ",
            showConfirmButton: false,
            timer: 2000
          }).then(() => { window.location.href = 'index.php'; });
        </script>";
    } else {
        echo "<script>
          Swal.fire({
            icon: 'error',
            title: " . json_encode(t('employees.error_title')) . ",
            text: " . json_encode(t('employees.update_error_text')) . ",
          });
        </script>";
    }
}
?>

<div class="container mt-4">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <h4 class="fw-bold text-primary mb-3"><i class="bi bi-pencil-square"></i> <?= t('employees.edit_title') ?></h4>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label"><?= t('employees.name_label') ?></label>
          <input type="text" name="tenNV" class="form-control" value="<?= htmlspecialchars($row['tenNV']) ?>" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('employees.position_label') ?></label>
          <input type="text" name="chucVu" class="form-control" value="<?= htmlspecialchars($row['chucVu']) ?>">
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('employees.email_label') ?></label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>">
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('employees.phone_label') ?></label>
          <input type="text" name="soDienThoai" class="form-control" value="<?= htmlspecialchars($row['soDienThoai']) ?>">
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('employees.school') ?></label>
          <select name="maTruong" class="form-select" required>
            <option value=""><?= t('employees.select_school') ?></option>
            <?php
            $schools = $conn->query("SELECT matruong, name FROM schools ORDER BY name ASC");
            while ($s = $schools->fetch_assoc()) {
                $selected = ($s['matruong'] == $row['maTruong']) ? "selected" : "";
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
