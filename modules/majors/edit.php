<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('majors');

if (!isset($_GET['maNganh'])) {
    echo "<script>
      Swal.fire({
        icon: 'warning',
        title: " . json_encode(t('majors.missing_info_title')) . ",
        text: " . json_encode(t('majors.missing_edit_text')) . ",
      }).then(() => { window.location.href = 'index.php'; });
    </script>";
    include "../../layout/footer.php";
    exit;
}

$maNganh = $_GET['maNganh'];
$stmt = $conn->prepare("SELECT * FROM majors WHERE maNganh = ?");
$stmt->bind_param("s", $maNganh);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>
      Swal.fire({
        icon: 'error',
        title: " . json_encode(t('majors.not_found_title')) . ",
        text: " . json_encode(t('majors.not_found_text')) . ",
      }).then(() => { window.location.href = 'index.php'; });
    </script>";
    include "../../layout/footer.php";
    exit;
}

$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tenNganh = trim($_POST["tenNganh"]);
    $maKhoa = trim($_POST["maKhoa"]);

    $update = $conn->prepare("UPDATE majors SET tenNganh=?, maKhoa=? WHERE maNganh=?");
    $update->bind_param("sss", $tenNganh, $maKhoa, $maNganh);

    if ($update->execute()) {
        echo "<script>
          Swal.fire({
            icon: 'success',
            title: " . json_encode(t('majors.success_title')) . ",
            text: " . json_encode(t('majors.update_success_text')) . ",
            showConfirmButton: false,
            timer: 2000
          }).then(() => { window.location.href = 'index.php'; });
        </script>";
    } else {
        echo "<script>
          Swal.fire({
            icon: 'error',
            title: " . json_encode(t('majors.error_title')) . ",
            text: " . json_encode(t('majors.update_error_text')) . ",
          });
        </script>";
    }
}
?>

<div class="container mt-4">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <h4 class="fw-bold text-primary mb-3"><i class="bi bi-pencil-square"></i> <?= t('majors.edit_title') ?></h4>

     <form method="POST" action="save.php">
        <div class="mb-3">
          <label class="form-label"><?= t('majors.code_label') ?></label>
          <input type="text" name="maNganh" class="form-control" value="<?= htmlspecialchars($row['maNganh']) ?>" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('majors.name_label') ?></label>
          <input type="text" name="tenNganh" class="form-control" value="<?= htmlspecialchars($row['tenNganh']) ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label"><?= t('majors.faculty_label') ?></label>
          <select name="maKhoa" class="form-select" required>
            <?php
            $faculties = $conn->query("SELECT maKhoa, tenKhoa FROM faculties ORDER BY tenKhoa ASC");
            while ($f = $faculties->fetch_assoc()) {
              $selected = ($f['maKhoa'] == $row['maKhoa']) ? "selected" : "";
              $facultyName = t_data('faculties', (string)$f['maKhoa'], 'tenKhoa', (string)$f['tenKhoa']);
              echo "<option value='{$f['maKhoa']}' $selected>{$facultyName}</option>";
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
