<?php
include "../../config/db.php";
include "../../config/i18n.php";
load_lang('dexuat');
include "../../layout/header.php";
include "../../layout/sidebar.php";
?>

<div class="content">
  <h4 class="fw-semibold text-primary mb-4">
    <i class="bi bi-check2-square"></i> <?php echo t('dexuat.title'); ?>
  </h4>

  <div class="card shadow-sm">
    <div class="card-body">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th><?php echo t('dexuat.code'); ?></th>
            <th><?php echo t('dexuat.subject'); ?></th>
            <th><?php echo t('dexuat.sender'); ?></th>
            <th><?php echo t('dexuat.date'); ?></th>
            <th><?php echo t('dexuat.type'); ?></th>
            <th><?php echo t('dexuat.status'); ?></th>
            <th><?php echo t('dexuat.actions'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT * FROM de_xuat ORDER BY ngayGui DESC";
          $result = $conn->query($sql);

            if ($result->num_rows == 0) {
              echo "<tr><td colspan='7' class='text-muted'>" . t('dexuat.none') . "</td></tr>";
          } else {
              while ($row = $result->fetch_assoc()) {
                $statusKey = match($row['trangThai']) {
                  'Đã duyệt' => 'status_approved',
                  'Từ chối' => 'status_rejected',
                  'Yêu cầu chỉnh sửa' => 'status_edit',
                  'Nháp' => 'status_draft',
                  default => 'status_pending'
                };
                $badgeClass = match($statusKey) {
                  'status_approved' => 'success',
                  'status_rejected' => 'danger',
                  'status_edit' => 'secondary',
                  'status_draft' => 'info',
                  default => 'warning'
                };
                $titleDisplay = t_data('dexuat', (string)$row['maDX'], 'tieuDe', (string)$row['tieuDe']);
                $typeDisplay = t_data('dexuat_types', (string)$row['maDX'], 'loaiDeXuat', (string)$row['loaiDeXuat']);
                $statusLabel = t('dexuat.' . $statusKey);
                  echo "
                  <tr>
                      <td>{$row['maDX']}</td>
                  <td class='text-start'>{$titleDisplay}</td>
                      <td>{$row['nguoiGui']}</td>
                      <td>{$row['ngayGui']}</td>
                  <td>{$typeDisplay}</td>
                  <td><span class='badge bg-$badgeClass'>{$statusLabel}</span></td>
                      <td>
                        <button class='btn btn-sm btn-outline-primary view-btn' data-id='{$row['maDX']}'>
                    <i class='bi bi-eye'></i> " . t('dexuat.view') . "
                        </button>
                      </td>
                  </tr>";
              }
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal xem chi tiết -->
<div class="modal fade" id="viewModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-file-earmark-text"></i> <?php echo t('dexuat.detail_title'); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="modal-content-body">
          <p class="text-center text-muted"><?php echo t('dexuat.loading'); ?></p>
        </div>
      </div>
      <div class="modal-footer" id="modal-actions"></div>
    </div>
  </div>
</div>

<script>
const labels = {
  loadFailed: <?php echo json_encode(t('dexuat.load_failed')); ?>,
  title: <?php echo json_encode(t('dexuat.title_label')); ?>,
  sender: <?php echo json_encode(t('dexuat.sender_label')); ?>,
  email: <?php echo json_encode(t('dexuat.email_label')); ?>,
  date: <?php echo json_encode(t('dexuat.date_label')); ?>,
  type: <?php echo json_encode(t('dexuat.type_label')); ?>,
  content: <?php echo json_encode(t('dexuat.content_label')); ?>,
  feedback: <?php echo json_encode(t('dexuat.feedback_label')); ?>,
  accept: <?php echo json_encode(t('dexuat.accept')); ?>,
  reject: <?php echo json_encode(t('dexuat.reject')); ?>,
  requestEdit: <?php echo json_encode(t('dexuat.request_edit')); ?>,
  processed: <?php echo json_encode(t('dexuat.processed')); ?>,
  confirmAccept: <?php echo json_encode(t('dexuat.confirm_accept')); ?>,
  reasonReject: <?php echo json_encode(t('dexuat.reason_reject')); ?>,
  reasonEdit: <?php echo json_encode(t('dexuat.reason_edit')); ?>,
  inputPlaceholder: <?php echo json_encode(t('dexuat.input_placeholder')); ?>,
  confirm: <?php echo json_encode(t('dexuat.confirm')); ?>,
  cancel: <?php echo json_encode(t('dexuat.cancel')); ?>,
  invalidAction: <?php echo json_encode(t('dexuat.invalid_action')); ?>
};

document.querySelectorAll(".view-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    const id = btn.dataset.id;
    fetch("get_detail.php?id=" + id)
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          Swal.fire("", labels.loadFailed, "error");
          return;
        }
        const dx = data.data;
        document.getElementById("modal-content-body").innerHTML = `
          <p><strong>${labels.title}:</strong> ${dx.tieuDeDisplay}</p>
          <p><strong>${labels.sender}:</strong> ${dx.nguoiGui}</p>
          <p><strong>${labels.email}:</strong> ${dx.emailNguoiGui ?? ""}</p>
          <p><strong>${labels.date}:</strong> ${dx.ngayGui}</p>
          <p><strong>${labels.type}:</strong> ${dx.loaiDeXuatDisplay}</p>
          <p><strong>${labels.content}:</strong></p>
          <div class='border rounded p-3 bg-light'>${(dx.noiDungDisplay||'').replace(/\n/g, "<br>")}</div>
          ${dx.lyDoTuChoi ? `<p class='mt-3 text-danger'><strong>${labels.feedback}:</strong> ${dx.lyDoTuChoi}</p>` : ""}
        `;
        const footer = document.getElementById("modal-actions");
        if (dx.trangThaiRaw === "Chờ duyệt") {
          footer.innerHTML = `
            <button class='btn btn-success' onclick="handleAction('${dx.maDX}','accept')">
              <i class='bi bi-check-circle'></i> ${labels.accept}
            </button>
            <button class='btn btn-danger' onclick="handleAction('${dx.maDX}','reject')">
              <i class='bi bi-x-circle'></i> ${labels.reject}
            </button>
            <button class='btn btn-warning text-white' onclick="handleAction('${dx.maDX}','edit')">
              <i class='bi bi-pencil-square'></i> ${labels.requestEdit}
            </button>
          `;
        } else {
          footer.innerHTML = `<span class='text-muted'>${labels.processed.replace('{status}', dx.trangThaiLabel)}</span>`;
        }
        new bootstrap.Modal(document.getElementById("viewModal")).show();
      });
  });
});

function handleAction(id, action) {
  let title = "", input = false;
  if (action === 'accept') title = labels.confirmAccept;
  if (action === 'reject') { title = labels.reasonReject; input = "textarea"; }
  if (action === 'edit') { title = labels.reasonEdit; input = "textarea"; }

  Swal.fire({
    title,
    input: input ? input : undefined,
    inputPlaceholder: input ? labels.inputPlaceholder : "",
    showCancelButton: true,
    confirmButtonText: labels.confirm,
    cancelButtonText: labels.cancel
  }).then(result => {
    if (result.isConfirmed) {
      const lyDo = result.value || "";
      fetch("update_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}&action=${action}&lydo=${encodeURIComponent(lyDo)}`
      })
      .then(res => res.json())
      .then(data => {
        Swal.fire(data.message, '', data.status);
        if (data.status === 'success') setTimeout(() => location.reload(), 1200);
      });
    }
  });
}
</script>

<?php include "../../layout/footer.php"; ?>
