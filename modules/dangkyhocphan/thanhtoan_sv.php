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

if (isset($_POST['xacnhan'])) {
  $conn->query("UPDATE registrations 
                SET trangThai='Chờ xác nhận' 
                WHERE maSV='$student_id' AND trangThai='Chưa thanh toán'");
  echo "<script>window.location='thanhtoan_sv.php';</script>";
  exit;
}

$query = "SELECT c.maHP, c.tenHP, c.soTinChi, r.trangThai, r.ngayThanhToan
          FROM registrations r
          JOIN courses c ON r.maHP = c.maHP
          WHERE r.maSV = '$student_id'
          ORDER BY c.maHP ASC";
$result = $conn->query($query);
?>

<div class="container mt-4">
  <h4 class="fw-semibold text-primary mb-3">
    <i class="bi bi-cash-coin"></i> <?php echo t('dangkyhocphan.tab_payment'); ?>
  </h4>

  <ul class="nav nav-tabs mb-4">
    <li class="nav-item">
      <a class="nav-link" href="/KTPM2/KTPM/modules/dangkyhocphan/index_sv.php">
        <i class="bi bi-pencil-square"></i> <?php echo t('dangkyhocphan.tab_register'); ?>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/KTPM2/KTPM/modules/dangkyhocphan/ketqua_sv.php">
        <i class="bi bi-search"></i> <?php echo t('dangkyhocphan.tab_results'); ?>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="#">
        <i class="bi bi-cash-coin"></i> <?php echo t('dangkyhocphan.tab_payment'); ?>
      </a>
    </li>
  </ul>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <?php
      if ($result && $result->num_rows > 0) {
        echo "<form id='paymentForm' method='POST'>
              <input type='hidden' name='xacnhan' value='1'>
              <table class='table table-bordered align-middle'>
                <thead class='table-warning text-center'>
                  <tr>
                    <th><?php echo t('dangkyhocphan.course_code'); ?></th>
                    <th><?php echo t('dangkyhocphan.course_name'); ?></th>
                    <th><?php echo t('dangkyhocphan.credits'); ?></th>
                    <th><?php echo t('dangkyhocphan.tuition'); ?></th>
                    <th><?php echo t('dangkyhocphan.status'); ?></th>
                    <th><?php echo t('dangkyhocphan.pay_date'); ?></th>
                  </tr>
                </thead>
                <tbody>";
        $tong_chua_tt = 0;
        while ($row = $result->fetch_assoc()) {
          $courseName = t_data('courses', (string)$row['maHP'], 'tenHP', (string)$row['tenHP']);
          $hocPhi = $row['soTinChi'] * $hocPhiTin;
          if ($row['trangThai'] == 'Chưa thanh toán') $tong_chua_tt += $hocPhi;

          $badge = match($row['trangThai']) {
            'Đã thanh toán' => "<span class='badge bg-success'>" . t('dangkyhocphan.status_paid') . "</span>",
            'Chờ xác nhận' => "<span class='badge bg-warning text-dark'>" . t('dangkyhocphan.status_pending') . "</span>",
            default => "<span class='badge bg-danger'>" . t('dangkyhocphan.status_unpaid') . "</span>"
          };

          $ngayTT = $row['ngayThanhToan'] ? date('d/m/Y H:i', strtotime($row['ngayThanhToan'])) : '-';

          echo "<tr>
                  <td>{$row['maHP']}</td>
              <td>{$courseName}</td>
                  <td class='text-center'>{$row['soTinChi']}</td>
                  <td class='text-end'>".number_format($hocPhi, 0, ',', '.')." VND</td>
                  <td class='text-center'>$badge</td>
                  <td class='text-center'>$ngayTT</td>
                </tr>";
        }

        echo "</tbody></table>";

        if ($tong_chua_tt > 0) {
          echo "<div class='text-center mt-3'>
                  <button type='button' class='btn btn-success px-4' id='payNow'>
                    <i class='bi bi-wallet2'></i> " . t('dangkyhocphan.pay_now') . "
                  </button>
                </div>";
        } else {
          echo "<p class='text-center text-success mt-3'>
                  <i class='bi bi-check-circle'></i> " . t('dangkyhocphan.paid_all') . "
                </p>";
        }

        echo "</form>";
      } else {
        echo "<p class='text-muted'>" . t('dangkyhocphan.no_registered') . "</p>";
      }
      ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById("payNow")?.addEventListener("click", function() {
  const tong = <?= (int)$tong_chua_tt ?>;
  const soTien = tong.toLocaleString("vi-VN");

  const payTitle = <?php echo json_encode(t('dangkyhocphan.qr_title')); ?>;
  const totalLabel = <?php echo json_encode(t('dangkyhocphan.qr_total')); ?>;
  const accountOwner = <?php echo json_encode(t('dangkyhocphan.qr_owner')); ?>;
  const accountNumber = <?php echo json_encode(t('dangkyhocphan.qr_number')); ?>;
  const bankLabel = <?php echo json_encode(t('dangkyhocphan.qr_bank')); ?>;
  const contentLabel = <?php echo json_encode(t('dangkyhocphan.qr_content')); ?>;
  const confirmText = <?php echo json_encode(t('dangkyhocphan.qr_confirm')); ?>;
  const closeText = <?php echo json_encode(t('dangkyhocphan.close_button')); ?>;
  const transferNote = <?php echo json_encode(t('dangkyhocphan.qr_note')); ?>;

  Swal.fire({
    title: payTitle,
    html: `
      <div style="text-align:center">
        <p><b>${totalLabel}:</b> ${soTien} VND</p>
        <img src="/KTPM2/KTPM/assets/qr_do_hai_lam.png" 
             style="width:250px;height:250px;border-radius:10px;border:1px solid #ccc;margin-top:10px;">
        <div class="mt-3">
          <p><b>${accountOwner}:</b> DO HAI LAM</p>
          <p><b>${accountNumber}:</b> 2686824112005</p>
          <p><b>${bankLabel}:</b> MSB (Maritime Bank)</p>
          <p class="text-muted small">${contentLabel}: <b>${transferNote} <?= $student_id ?></b></p>
        </div>
      </div>`,
    showCancelButton: true,
    confirmButtonText: confirmText,
    cancelButtonText: closeText,
    confirmButtonColor: "#28a745"
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById("paymentForm").submit();
    }
  });
});
</script>

<?php include "../../layout/footer_sv.php"; ?>
