<?php
include "../../config/db.php";
include "../../config/i18n.php";
load_lang('dangkyhocphan');
include "../../layout/header.php";
include "../../layout/sidebar.php";

$hocPhiTin = 0;
$res = $conn->query("SELECT value FROM settings WHERE name='hocphi' LIMIT 1");
if ($res && $res->num_rows > 0) {
  $hocPhiTin = (float)$res->fetch_assoc()['value'];
} else {
  $hocPhiTin = 500000;
}

$query = "
  SELECT s.hoTen, s.maSV, c.maHP, c.tenHP, c.soTinChi, r.trangThai, r.ngayDK, r.ngayThanhToan
  FROM registrations r
  JOIN sinhvien s ON r.maSV = s.maSV
  JOIN courses c ON r.maHP = c.maHP
  ORDER BY s.maSV, c.maHP";
$result = $conn->query($query);
?>

<div class="container-fluid mt-4">
  <h4 class="fw-semibold text-primary mb-3">
    <i class="bi bi-clipboard-check"></i> <?php echo t('dangkyhocphan.admin_title'); ?>
  </h4>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <?php if ($result && $result->num_rows > 0) { ?>
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary text-center">
              <tr>
                <th><?php echo t('dangkyhocphan.stt'); ?></th>
                <th><?php echo t('dangkyhocphan.student_code'); ?></th>
                <th><?php echo t('dangkyhocphan.student_name'); ?></th>
                <th><?php echo t('dangkyhocphan.course_code'); ?></th>
                <th><?php echo t('dangkyhocphan.course_name'); ?></th>
                <th><?php echo t('dangkyhocphan.credits'); ?></th>
                <th><?php echo t('dangkyhocphan.tuition'); ?></th>
                <th><?php echo t('dangkyhocphan.status'); ?></th>
                <th><?php echo t('dangkyhocphan.reg_date'); ?></th>
                <th><?php echo t('dangkyhocphan.pay_date'); ?></th>
                <th><?php echo t('dangkyhocphan.actions'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i = 1;
              while ($row = $result->fetch_assoc()) {
                $courseName = t_data('courses', (string)$row['maHP'], 'tenHP', (string)$row['tenHP']);
                $hocPhi = $row['soTinChi'] * $hocPhiTin;

                $statusKey = match($row['trangThai']) {
                  'Đã thanh toán' => 'status_paid',
                  'Chờ xác nhận' => 'status_pending',
                  default => 'status_unpaid'
                };
                $statusLabel = t('dangkyhocphan.' . $statusKey);
                $badgeClass = match($statusKey) {
                  'status_paid' => 'success',
                  'status_pending' => 'warning text-dark',
                  default => 'danger'
                };
                $badge = "<span class='badge bg-{$badgeClass}'>" . $statusLabel . "</span>";

                $actionBtn = $row['trangThai'] === 'Chờ xác nhận'
                  ? "<button class='btn btn-success btn-sm' onclick=\"confirmPayment('{$row['maSV']}', '{$row['maHP']}')\">
                      <i class='bi bi-check-circle'></i> " . t('dangkyhocphan.action_confirm') . "
                    </button>"
                  : '';

                $ngayTT = $row['ngayThanhToan'] ? date('d/m/Y H:i', strtotime($row['ngayThanhToan'])) : '-';

                echo "<tr>
                  <td class='text-center'>$i</td>
                  <td>{$row['maSV']}</td>
                  <td>{$row['hoTen']}</td>
                  <td>{$row['maHP']}</td>
                  <td>{$courseName}</td>
                  <td class='text-center'>{$row['soTinChi']}</td>
                  <td class='text-end'>" . number_format($hocPhi, 0, ',', '.') . " VND</td>
                  <td class='text-center'>$badge</td>
                  <td class='text-center'>" . date('d/m/Y H:i', strtotime($row['ngayDK'])) . "</td>
                  <td class='text-center'>$ngayTT</td>
                  <td class='text-center'>$actionBtn</td>
                </tr>";
                $i++;
              }
              ?>
            </tbody>
          </table>
        </div>
      <?php } else { ?>
        <p class="text-muted"><?php echo t('dangkyhocphan.no_data'); ?></p>
      <?php } ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmPayment(maSV, maHP) {
  const confirmTitle = <?php echo json_encode(t('dangkyhocphan.confirm_title')); ?>;
  const confirmHtmlTemplate = <?php echo json_encode(t('dangkyhocphan.confirm_html')); ?>;
  const confirmButton = <?php echo json_encode(t('dangkyhocphan.confirm_button')); ?>;
  const cancelButton = <?php echo json_encode(t('common.cancel')); ?>;
  const doneTitle = <?php echo json_encode(t('dangkyhocphan.done_title')); ?>;
  const errorTitle = <?php echo json_encode(t('common.error_title')); ?>;
  const updateFailed = <?php echo json_encode(t('dangkyhocphan.update_failed')); ?>;

  const confirmHtml = confirmHtmlTemplate
    .replace('{course}', maHP)
    .replace('{student}', maSV);

  Swal.fire({
    title: confirmTitle,
    html: confirmHtml,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: confirmButton,
    cancelButtonText: cancelButton,
    confirmButtonColor: "#28a745"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("update_payment.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "maSV=" + maSV + "&maHP=" + maHP
      })
      .then(res => res.json())
      .then(data => {
        Swal.fire(doneTitle, data.message, "success").then(() => location.reload());
      })
      .catch(() => Swal.fire(errorTitle, updateFailed, "error"));
    }
  });
}
</script>

<?php include "../../layout/footer.php"; ?>
