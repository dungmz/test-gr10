<?php
include "../../config/db.php";
include "../../config/i18n.php";
load_lang('dangkyhocphan');
include "../../layout/header_sv.php";
include "../../layout/sidebar_sv.php";

$hocPhiTin = 0;
$res = $conn->query("SELECT value FROM settings WHERE name='hocphi' LIMIT 1");
if ($res && $res->num_rows > 0) {
  $hocPhiTin = (float)$res->fetch_assoc()['value'];
} else {
  $hocPhiTin = 500000;
}

$student_id = $_SESSION['username'] ?? 'SV001';
$selectedCourses = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['hocphan'])) {
  foreach ($_POST['hocphan'] as $maHP) {
    $maHP = $conn->real_escape_string($maHP);
    $exists = $conn->query("SELECT * FROM registrations WHERE maSV='$student_id' AND maHP='$maHP'");
    if ($exists->num_rows == 0) {
      $conn->query("INSERT INTO registrations (maSV, maHP, ngayDK) VALUES ('$student_id', '$maHP', NOW())");
    }
    $selectedCourses[] = $maHP;
  }
}
?>

<div class="container mt-4">
  <h4 class="fw-semibold text-primary mb-3">
    <i class="bi bi-journal-check"></i> <?php echo t('dangkyhocphan.student_title'); ?>
  </h4>

  <ul class="nav nav-tabs mb-4">
    <li class="nav-item">
      <a class="nav-link active" href="/KTPM2/KTPM/modules/dangkyhocphan/index_sv.php">
        <i class="bi bi-pencil-square"></i> <?php echo t('dangkyhocphan.tab_register'); ?>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/KTPM2/KTPM/modules/dangkyhocphan/ketqua_sv.php">
        <i class="bi bi-search"></i> <?php echo t('dangkyhocphan.tab_results'); ?>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/KTPM2/KTPM/modules/dangkyhocphan/thanhtoan_sv.php">
        <i class="bi bi-cash-coin"></i> <?php echo t('dangkyhocphan.tab_payment'); ?>
      </a>
    </li>
  </ul>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="alert alert-info mb-4 d-flex justify-content-between align-items-center">
        <div><b><?php echo t('dangkyhocphan.current_tuition'); ?>:</b> <?php echo number_format($hocPhiTin, 0, ',', '.'); ?> <?php echo t('dangkyhocphan.tuition_unit'); ?></div>
        <div class="d-flex align-items-center gap-2">
          <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="<?php echo t('dangkyhocphan.search_placeholder'); ?>" style="width: 250px;">
        </div>
      </div>

      <?php if (empty($selectedCourses)): ?>
      <form id="registerForm" method="POST" action="">
        <div class="table-responsive">
          <table id="hocphanTable" class="table table-bordered table-hover align-middle">
            <thead class="table-primary text-center">
              <tr>
                <th width="50"><input type="checkbox" id="checkAll"></th>
                <th><?php echo t('dangkyhocphan.course_code'); ?></th>
                <th><?php echo t('dangkyhocphan.course_name'); ?></th>
                <th><?php echo t('dangkyhocphan.credits'); ?></th>
                <th><?php echo t('dangkyhocphan.tuition'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $query = "SELECT maHP, tenHP, soTinChi FROM courses ORDER BY maHP ASC";
              $result = $conn->query($query);
              if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $courseName = t_data('courses', (string)$row['maHP'], 'tenHP', (string)$row['tenHP']);
                  $hocPhi = $row['soTinChi'] * $hocPhiTin;
                  echo "
                    <tr>
                      <td class='text-center'>
                        <input type='checkbox' name='hocphan[]' value='{$row['maHP']}' class='chkHP'>
                      </td>
                      <td>{$row['maHP']}</td>
                      <td>{$courseName}</td>
                      <td class='text-center'>{$row['soTinChi']}</td>
                      <td class='text-end'>".number_format($hocPhi, 0, ',', '.')." VND</td>
                    </tr>";
                }
              } else {
                echo "<tr><td colspan='5' class='text-center text-muted'>" . t('dangkyhocphan.no_courses') . "</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
          <button type="submit" class="btn btn-success px-4">
            <i class="bi bi-check2-circle"></i> <?php echo t('dangkyhocphan.confirm_register'); ?>
          </button>
        </div>
      </form>
      <?php else: ?>
        <h5 class="text-success mb-3"><i class="bi bi-check2-circle"></i> <?php echo t('dangkyhocphan.registered_list'); ?></h5>
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-success text-center">
            <tr>
              <th><?php echo t('dangkyhocphan.course_code'); ?></th>
              <th><?php echo t('dangkyhocphan.course_name'); ?></th>
              <th><?php echo t('dangkyhocphan.credits'); ?></th>
              <th><?php echo t('dangkyhocphan.tuition'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $tong = 0;
            foreach ($selectedCourses as $maHP) {
              $data = $conn->query("SELECT * FROM courses WHERE maHP='$maHP'")->fetch_assoc();
              if ($data) {
                $courseName = t_data('courses', (string)$data['maHP'], 'tenHP', (string)$data['tenHP']);
                $hocPhi = $data['soTinChi'] * $hocPhiTin;
                $tong += $hocPhi;
                echo "
                  <tr>
                    <td>{$data['maHP']}</td>
                    <td>{$courseName}</td>
                    <td class='text-center'>{$data['soTinChi']}</td>
                    <td class='text-end'>".number_format($hocPhi, 0, ',', '.')." VND</td>
                  </tr>";
              }
            }
            echo "
              <tr class='fw-bold table-light'>
                <td colspan='3' class='text-end'>" . t('dangkyhocphan.total_estimated') . ":</td>
                <td class='text-end text-danger'>".number_format($tong, 0, ',', '.')." VND</td>
              </tr>";
            ?>
          </tbody>
        </table>

        <div class="text-center mt-4">
          <a href="/KTPM2/KTPM/modules/dangkyhocphan/ketqua_sv.php" class="btn btn-primary">
            <i class="bi bi-search"></i> <?php echo t('dangkyhocphan.view_results'); ?>
          </a>
          <a href="/KTPM2/KTPM/modules/dangkyhocphan/index_sv.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle"></i> <?php echo t('dangkyhocphan.back'); ?>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById("checkAll")?.addEventListener("click", function() {
  const checks = document.querySelectorAll(".chkHP");
  checks.forEach(c => c.checked = this.checked);
});
document.getElementById("registerForm")?.addEventListener("submit", function(e) {
  const checked = document.querySelectorAll(".chkHP:checked");
  if (checked.length === 0) {
    e.preventDefault();
    Swal.fire(
      <?php echo json_encode(t('dangkyhocphan.notice_title')); ?>,
      <?php echo json_encode(t('dangkyhocphan.select_warning')); ?>,
      "warning"
    );
  }
});
document.getElementById("searchInput")?.addEventListener("keyup", function() {
  const value = this.value.toLowerCase();
  const rows = document.querySelectorAll("#hocphanTable tbody tr");
  rows.forEach(row => {
    const maHP = row.children[1].textContent.toLowerCase();
    const tenHP = row.children[2].textContent.toLowerCase();
    row.style.display = (maHP.includes(value) || tenHP.includes(value)) ? "" : "none";
  });
});
</script>

<?php include "../../layout/footer_sv.php"; ?>
