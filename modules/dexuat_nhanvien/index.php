<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$script = str_replace('\\','/', $_SERVER['SCRIPT_NAME'] ?? '/');
if (($p = strpos($script, '/modules/')) !== false) {
  $basePath = substr($script, 0, $p + 1); 
} else {
  $basePath = rtrim(dirname($script), '/') . '/';
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'giangvien') {
  header("Location: " . $basePath . "login.php");
  exit;
}

include "../../config/db.php";
include "../../config/i18n.php";
load_lang('dexuat');
include "../../layout/header_gv.php";
include "../../layout/sidebar_gv.php";

$user = $_SESSION['username'] ?? '';
?>

<div class="content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-semibold text-primary m-0">
      <i class="bi bi-plus-circle"></i> <?php echo t('dexuat_emp.title'); ?>
    </h4>

    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#lichSuModal">
      <i class="bi bi-clock-history"></i> <?php echo t('dexuat_emp.history_button'); ?>
    </button>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form id="proposalForm" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label fw-semibold"><?php echo t('dexuat_emp.type_label'); ?></label>
          <select name="loaiThayDoi" id="loaiThayDoi" class="form-select" required>
            <option value=""><?php echo t('dexuat_emp.type_placeholder'); ?></option>
            <option value="Thêm mới đơn vị"><?php echo t('dexuat_emp.type_add'); ?></option>
            <option value="Chỉnh sửa đơn vị"><?php echo t('dexuat_emp.type_edit'); ?></option>
            <option value="Xóa đơn vị"><?php echo t('dexuat_emp.type_delete'); ?></option>
          </select>
        </div>

        <div id="formDynamic"></div>

        <div class="mb-3">
          <label class="form-label fw-semibold"><?php echo t('dexuat_emp.reason_label'); ?></label>
          <textarea name="noiDung" class="form-control" rows="4" required placeholder="<?php echo t('dexuat_emp.reason_placeholder'); ?>"></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold"><?php echo t('dexuat_emp.attach_label'); ?></label>
          <input type="file" name="tepDinhKem" class="form-control">
        </div>

        <div class="text-end">
          <button type="button" class="btn btn-secondary me-2" onclick="saveProposal('draft')">
            <i class="bi bi-save"></i> <?php echo t('dexuat_emp.save_draft'); ?>
          </button>
          <button type="button" class="btn btn-primary" onclick="saveProposal('submit')">
            <i class="bi bi-send"></i> <?php echo t('dexuat_emp.submit'); ?>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="lichSuModal" tabindex="-1" aria-labelledby="lichSuLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="lichSuLabel" class="modal-title"><i class="bi bi-clock-history"></i> <?php echo t('dexuat_emp.history_title'); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo t('dexuat_emp.close'); ?>"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle text-center">
            <thead class="table-primary">
              <tr>
                <th style="width:80px"><?php echo t('dexuat_emp.table_code'); ?></th>
                <th class="text-start"><?php echo t('dexuat_emp.table_title'); ?></th>
                <th style="width:180px"><?php echo t('dexuat_emp.table_type'); ?></th>
                <th style="width:170px"><?php echo t('dexuat_emp.table_date'); ?></th>
                <th style="width:150px"><?php echo t('dexuat_emp.table_status'); ?></th>
                <th style="width:180px"><?php echo t('dexuat_emp.table_actions'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT maDX, tieuDe, loaiDeXuat, ngayGui, trangThai 
                      FROM de_xuat 
                      WHERE nguoiGui='".$conn->real_escape_string($user)."' 
                      ORDER BY (ngayGui IS NULL), ngayGui DESC, maDX DESC
                      LIMIT 50";
              $res = $conn->query($sql);

              if (!$res || $res->num_rows == 0) {
                echo "<tr><td colspan='6' class='text-muted'>" . t('dexuat_emp.none') . "</td></tr>";
              } else {
                while ($r = $res->fetch_assoc()) {
                  $statusKey = match($r['trangThai']) {
                    'Đã duyệt' => 'status_approved',
                    'Từ chối' => 'status_rejected',
                    'Yêu cầu chỉnh sửa' => 'status_edit',
                    'Nháp' => 'status_draft',
                    default => 'status_pending'
                  };
                  $badge = match($statusKey) {
                    'status_approved' => 'success',
                    'status_rejected' => 'danger',
                    'status_edit' => 'secondary',
                    'status_draft' => 'info',
                    default => 'warning'
                  };
                  $ngay = $r['ngayGui'] ?: '-';
                  $titleDisplay = t_data('dexuat', (string)$r['maDX'], 'tieuDe', (string)$r['tieuDe']);
                  $typeDisplay = t_data('dexuat_types', (string)$r['maDX'], 'loaiDeXuat', (string)$r['loaiDeXuat']);
                  $statusLabel = t('dexuat.' . $statusKey);
                  echo "
                  <tr>
                    <td>".(int)$r['maDX']."</td>
                    <td class='text-start'>".htmlspecialchars($titleDisplay)."</td>
                    <td>".htmlspecialchars($typeDisplay)."</td>
                    <td>".htmlspecialchars($ngay)."</td>
                    <td><span class='badge bg-$badge'>".htmlspecialchars($statusLabel)."</span></td>
                    <td class='d-flex gap-1 justify-content-center flex-wrap'>";
                      if ($r['trangThai'] === 'Nháp') {
                        echo "
                        <button class='btn btn-sm btn-outline-primary' onclick='editProposal(".$r['maDX'].")'>
                          <i class=\"bi bi-pencil\"></i> " . t('dexuat_emp.action_edit') . "
                        </button>
                        <button class='btn btn-sm btn-outline-success' onclick='sendProposal(".$r['maDX'].")'>
                          <i class=\"bi bi-send\"></i> " . t('dexuat_emp.action_send') . "
                        </button>
                        <button class='btn btn-sm btn-outline-danger' onclick='deleteProposal(".$r['maDX'].")'>
                          <i class=\"bi bi-trash\"></i> " . t('dexuat_emp.action_delete') . "
                        </button>";
                      } else {
                        echo "
                        <button class='btn btn-sm btn-outline-secondary' onclick='viewDetail(".$r['maDX'].")'>
                          <i class=\"bi bi-eye\"></i> " . t('dexuat_emp.action_view') . "
                        </button>";
                      }
                  echo "</td>
                  </tr>";
                }
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-outline-primary" href="<?= $basePath ?>modules/dexuat_nhanvien/my_proposals.php">
          <i class="bi bi-box-arrow-up-right"></i> <?php echo t('dexuat_emp.open_my_page'); ?>
        </a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo t('dexuat_emp.close'); ?></button>
      </div>
    </div>
  </div>
</div>

<script>
const labels = {
  unitName: <?php echo json_encode(t('dexuat_emp.unit_name')); ?>,
  unitCode: <?php echo json_encode(t('dexuat_emp.unit_code')); ?>,
  parentUnit: <?php echo json_encode(t('dexuat_emp.parent_unit')); ?>,
  parentPlaceholder: <?php echo json_encode(t('dexuat_emp.parent_placeholder')); ?>,
  description: <?php echo json_encode(t('dexuat_emp.description')); ?>,
  selectUnit: <?php echo json_encode(t('dexuat_emp.select_unit')); ?>,
  selectUnitPlaceholder: <?php echo json_encode(t('dexuat_emp.select_unit_placeholder')); ?>,
  newInfo: <?php echo json_encode(t('dexuat_emp.new_info')); ?>,
  newInfoPlaceholder: <?php echo json_encode(t('dexuat_emp.new_info_placeholder')); ?>,
  detailType: <?php echo json_encode(t('dexuat_emp.detail_type')); ?>,
  detailStatus: <?php echo json_encode(t('dexuat_emp.detail_status')); ?>,
  detailContent: <?php echo json_encode(t('dexuat_emp.detail_content')); ?>,
  detailFeedback: <?php echo json_encode(t('dexuat_emp.detail_feedback')); ?>,
  errLoad: <?php echo json_encode(t('dexuat_emp.err_load')); ?>,
  editTitle: <?php echo json_encode(t('dexuat_emp.edit_title')); ?>,
  editPlaceholder: <?php echo json_encode(t('dexuat_emp.edit_placeholder')); ?>,
  editSave: <?php echo json_encode(t('dexuat_emp.edit_save')); ?>,
  errSave: <?php echo json_encode(t('dexuat_emp.err_save')); ?>,
  errSend: <?php echo json_encode(t('dexuat_emp.err_send')); ?>,
  errDelete: <?php echo json_encode(t('dexuat_emp.err_delete')); ?>,
  deleteConfirmTitle: <?php echo json_encode(t('dexuat_emp.delete_confirm_title')); ?>,
  deleteConfirmText: <?php echo json_encode(t('dexuat_emp.delete_confirm_text')); ?>,
  deleteConfirmButton: <?php echo json_encode(t('dexuat_emp.delete_confirm_button')); ?>,
  close: <?php echo json_encode(t('dexuat_emp.close')); ?>
};

document.getElementById('loaiThayDoi').addEventListener('change', function() {
  const type = this.value;
  const formDynamic = document.getElementById('formDynamic');
  let html = '';

  if (type === 'Thêm mới đơn vị') {
    html = `
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">${labels.unitName}</label>
          <input type="text" name="tenDonVi" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-semibold">${labels.unitCode}</label>
          <input type="text" name="maDonVi" class="form-control" required>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">${labels.parentUnit}</label>
        <select name="donViCha" class="form-select">
          <option value="">${labels.parentPlaceholder}</option>
          <?php
          $dv = $conn->query("SELECT name FROM schools ORDER BY name ASC");
          while ($r = $dv->fetch_assoc()) {
            $schoolName = t_data('schools', (string)$r['name'], 'name', (string)$r['name']);
            echo "<option value='{$r['name']}'>".htmlspecialchars($schoolName)."</option>";
          }
          ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">${labels.description}</label>
        <textarea name="moTa" class="form-control" rows="2"></textarea>
      </div>`;
  }

  if (type === 'Chỉnh sửa đơn vị' || type === 'Xóa đơn vị') {
    html = `
      <div class="mb-3">
        <label class="form-label fw-semibold">${labels.selectUnit}</label>
        <select name="donViChon" class="form-select" required>
          <option value="">${labels.selectUnitPlaceholder}</option>
          <?php
          $dv2 = $conn->query("SELECT name FROM schools ORDER BY name ASC");
          while ($r = $dv2->fetch_assoc()) {
            $schoolName = t_data('schools', (string)$r['name'], 'name', (string)$r['name']);
            echo "<option value='{$r['name']}'>".htmlspecialchars($schoolName)."</option>";
          }
          ?>
        </select>
      </div>
      ${type === 'Chỉnh sửa đơn vị' ? `
        <div class="mb-3">
          <label class="form-label fw-semibold">${labels.newInfo}</label>
          <input type="text" name="thongTinMoi" class="form-control" placeholder="${labels.newInfoPlaceholder}">
        </div>` : ''}
    `;
  }

  formDynamic.innerHTML = html;
});

function saveProposal(mode) {
  const form = document.getElementById('proposalForm');
  const formData = new FormData(form);
  formData.append('mode', mode);

  fetch('save_proposal.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    Swal.fire(data.message, '', data.status);
    if (data.status === 'success') {
      form.reset();
      document.getElementById('formDynamic').innerHTML = '';

      refreshHistoryInModal();
    }
  })
  .catch(()=> Swal.fire('Lỗi', 'Không thể gửi yêu cầu.', 'error'));
}


function refreshHistoryInModal() {
  setTimeout(() => {
    if (document.getElementById('lichSuModal').classList.contains('show')) {
      location.reload();
    }
  }, 600);
}

function viewDetail(id) {
  fetch('../dexuat/get_detail.php?id=' + id)
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        Swal.fire("", labels.errLoad, "error");
        return;
      }
      const dx = data.data;
      Swal.fire({
        title: dx.tieuDeDisplay || dx.tieuDe,
        html: `
          <p><strong>${labels.detailType}:</strong> ${dx.loaiDeXuatDisplay || dx.loaiDeXuat}</p>
          <p><strong>${labels.detailStatus}:</strong> ${dx.trangThaiLabel || dx.trangThai}</p>
          <p><strong>${labels.detailContent}:</strong></p>
          <div class='border p-2 text-start bg-light'>${(dx.noiDungDisplay||dx.noiDung||'').replace(/\n/g, "<br>")}</div>
          ${dx.lyDoTuChoi ? `<p class='mt-3 text-danger'><strong>${labels.detailFeedback}:</strong> ${dx.lyDoTuChoi}</p>` : ''}
        `,
        icon: 'info'
      });
    })
    .catch(()=> Swal.fire("", labels.errLoad, "error"));
}

function editProposal(id) {
  Swal.fire({
    title: labels.editTitle,
    input: "textarea",
    inputPlaceholder: labels.editPlaceholder,
    showCancelButton: true,
    confirmButtonText: labels.editSave,
    cancelButtonText: labels.close
  }).then(res => {
    if (res.isConfirmed) {
      fetch('save_proposal.php', {
        method: 'POST',
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: `id=${id}&noiDung=${encodeURIComponent(res.value || '')}&mode=update`
      })
      .then(r => r.json())
      .then(data => Swal.fire(data.message, '', data.status))
      .then(() => refreshHistoryInModal())
      .catch(()=> Swal.fire("", labels.errSave, "error"));
    }
  });
}

function sendProposal(id) {
  fetch('save_proposal.php', {
    method: 'POST',
    headers: {"Content-Type":"application/x-www-form-urlencoded"},
    body: `id=${id}&mode=submit_existing`
  })
  .then(res => res.json())
  .then(data => Swal.fire(data.message, '', data.status))
  .then(() => refreshHistoryInModal())
  .catch(()=> Swal.fire("", labels.errSend, "error"));
}

function deleteProposal(id) {
  Swal.fire({
    title: labels.deleteConfirmTitle,
    text: labels.deleteConfirmText,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: labels.deleteConfirmButton,
    cancelButtonText: labels.close
  }).then(result => {
    if (result.isConfirmed) {
      fetch('delete_proposal.php', {
        method: 'POST',
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: `id=${id}`
      })
      .then(res => res.json())
      .then(data => Swal.fire(data.message, '', data.status))
      .then(() => refreshHistoryInModal())
      .catch(()=> Swal.fire("", labels.errDelete, "error"));
    }
  });
}
</script>

<?php include "../../layout/footer.php"; ?>
