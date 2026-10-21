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

if (isset($_GET['huy'])) {
  $maHP = $conn->real_escape_string($_GET['huy']);
  $check = $conn->query("SELECT trangThai FROM registrations WHERE maSV='$student_id' AND maHP='$maHP'")->fetch_assoc();
  if ($check && $check['trangThai'] === 'Chưa thanh toán') {
    $conn->query("DELETE FROM registrations WHERE maSV='$student_id' AND maHP='$maHP'");
  }
  echo "<script>window.location='ketqua_sv.php';</script>";
  exit;
}

$query = "SELECT c.maHP, c.tenHP, c.soTinChi, r.ngayDK, r.trangThai, r.ngayThanhToan 
          FROM registrations r 
          JOIN courses c ON r.maHP = c.maHP 
          WHERE r.maSV = '$student_id' 
          ORDER BY r.ngayDK DESC";
$result = $conn->query($query);
?>

<div class="container mt-4">
  <h4 class="fw-semibold text-primary mb-3">
    <i class="bi bi-search"></i> <?php echo t('dangkyhocphan.tab_results'); ?>
  </h4>

  <ul class="nav nav-tabs mb-4">
    <li class="nav-item">
      <a class="nav-link" href="/KTPM2/KTPM/modules/dangkyhocphan/index_sv.php">
        <i class="bi bi-pencil-square"></i> <?php echo t('dangkyhocphan.tab_register'); ?>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="#">
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
      <?php
      if ($result && $result->num_rows > 0) {
        echo "<table class='table table-bordered table-striped align-middle'>
                <thead class='table-primary text-center'>
                  <tr>
                    <th><?php echo t('dangkyhocphan.course_code'); ?></th>
                    <th><?php echo t('dangkyhocphan.course_name'); ?></th>
                    <th><?php echo t('dangkyhocphan.credits'); ?></th>
                    <th><?php echo t('dangkyhocphan.tuition'); ?></th>
                    <th><?php echo t('dangkyhocphan.reg_date'); ?></th>
                    <th><?php echo t('dangkyhocphan.status'); ?></th>
                    <th><?php echo t('dangkyhocphan.actions'); ?></th>
                  </tr>
                </thead>
                <tbody>";
        $tong = 0;
        while ($row = $result->fetch_assoc()) {
          $courseName = t_data('courses', (string)$row['maHP'], 'tenHP', (string)$row['tenHP']);
          $courseNameJs = json_encode($courseName);
          $hocPhi = $row['soTinChi'] * $hocPhiTin;
          $tong += $hocPhi;
          $badge = $row['trangThai'] === 'Đã thanh toán'
            ? "<span class='badge bg-success'>" . t('dangkyhocphan.status_paid') . "</span>"
            : "<span class='badge bg-danger'>" . t('dangkyhocphan.status_unpaid') . "</span>";

          $huyBtn = $row['trangThai'] === 'Chưa thanh toán'
            ? "<button class='btn btn-danger btn-sm' onclick=\"huyDangKy('{$row['maHP']}', {$courseNameJs})\">
                 <i class='bi bi-trash'></i> " . t('dangkyhocphan.cancel_register') . "
               </button>"
            : "<button class='btn btn-secondary btn-sm' disabled><i class='bi bi-lock'></i> " . t('dangkyhocphan.status_paid') . "</button>";

          echo "<tr>
                  <td>{$row['maHP']}</td>
              <td>{$courseName}</td>
                  <td class='text-center'>{$row['soTinChi']}</td>
                  <td class='text-end'>".number_format($hocPhi, 0, ',', '.')." VND</td>
                  <td class='text-center'>".date('d/m/Y H:i', strtotime($row['ngayDK']))."</td>
                  <td class='text-center'>$badge</td>
                  <td class='text-center'>$huyBtn</td>
                </tr>";
        }

        echo "<tr class='fw-bold table-light'>
                <td colspan='3' class='text-end'>" . t('dangkyhocphan.total_registered') . ":</td>
                <td class='text-end text-danger'>".number_format($tong, 0, ',', '.')." VND</td>
                <td colspan='3'></td>
              </tr></tbody></table>";
      } else {
        echo "<p class='text-muted'>" . t('dangkyhocphan.no_registered') . "</p>";
      }
      ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function huyDangKy(maHP, tenHP) {
  Swal.fire({
    title: <?php echo json_encode(t('dangkyhocphan.cancel_confirm_title')); ?>,
    html: <?php echo json_encode(t('dangkyhocphan.cancel_confirm_html')); ?>
      .replace('{course}', tenHP)
      .replace('{code}', maHP),
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: <?php echo json_encode(t('dangkyhocphan.cancel_confirm_button')); ?>,
    cancelButtonText: <?php echo json_encode(t('dangkyhocphan.close_button')); ?>
  }).then((result) => {
    if (result.isConfirmed) {
      window.location = "ketqua_sv.php?huy=" + maHP;
    }
  });
}
</script>

<?php include "../../layout/footer_sv.php"; ?>
