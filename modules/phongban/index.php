<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('phongban');
?>

<div class="content" id="contentArea">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-semibold text-primary">
      <i class="bi bi-briefcase"></i> <?= t('phongban.title') ?>
    </h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
      <i class="bi bi-plus-circle"></i> <?= t('phongban.add_button') ?>
    </button>
  </div>

  <div class="row">
    <?php
    $result = $conn->query("SELECT * FROM phongban ORDER BY tenPB ASC");
    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $deptName = t_data('phongban', (string)$row['maPB'], 'tenPB', (string)$row['tenPB']);
        $deptAddress = t_data('phongban', (string)$row['maPB'], 'diaChi', (string)($row['diaChi'] ?? ''));
        $labelAddress = t('phongban.address_label');
        $labelEmail = t('phongban.email_label');
        $labelPhone = t('phongban.phone_label');
        $editLabel = t('common.edit');
        $deleteLabel = t('common.delete');
        $dataTen = htmlspecialchars($deptName, ENT_QUOTES);
        $dataDiaChi = htmlspecialchars($deptAddress, ENT_QUOTES);
        $dataEmail = htmlspecialchars($row['email'] ?? '', ENT_QUOTES);
        $dataPhone = htmlspecialchars($row['soDienThoai'] ?? '', ENT_QUOTES);
        echo "
        <div class='col-md-4 mb-4'>
          <div class='card shadow-sm border-0 h-100'>
            <div class='card-body'>
              <h6 class='card-title fw-bold fs-5'>{$deptName}</h6>
              <ul class='list-unstyled mb-3'>
                <li><i class='bi bi-geo-alt text-danger'></i> <b>{$labelAddress}:</b> {$deptAddress}</li>
                <li><i class='bi bi-envelope text-success'></i> <b>{$labelEmail}:</b> {$row['email']}</li>
                <li><i class='bi bi-telephone text-secondary'></i> <b>{$labelPhone}:</b> {$row['soDienThoai']}</li>
              </ul>
              <div class='d-flex gap-2'>
                <button class='btn btn-warning btn-sm btnEdit'
                  data-ma='{$row['maPB']}'
                  data-ten='{$dataTen}'
                  data-diachi='{$dataDiaChi}'
                  data-email='{$dataEmail}'
                  data-sdt='{$dataPhone}'>
                  <i class='bi bi-pencil-square'></i> {$editLabel}
                </button>
                <button class='btn btn-danger btn-sm btnDelete' data-ma='{$row['maPB']}'>
                  <i class='bi bi-trash'></i> {$deleteLabel}
                </button>
              </div>
            </div>
          </div>
        </div>";
      }
    } else {
      echo "<p class='text-muted'>" . t('phongban.no_data') . "</p>";
    }
    ?>
  </div>
</div>

<!-- ========== MODAL THÊM ========== -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-plus-circle"></i> <?= t('phongban.add_title') ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addForm">
          <div class="mb-3">
            <label class="form-label"><?= t('phongban.code_label') ?></label>
            <input type="text" class="form-control" name="maPB" required>
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('phongban.name_label') ?></label>
            <input type="text" class="form-control" name="tenPB" required>
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('phongban.address_label') ?></label>
            <input type="text" class="form-control" name="diaChi">
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('phongban.email_label') ?></label>
            <input type="email" class="form-control" name="email">
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('phongban.phone_label') ?></label>
            <input type="text" class="form-control" name="soDienThoai">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('common.cancel') ?></button>
        <button type="button" class="btn btn-success" id="btnAddConfirm"><?= t('phongban.add_confirm') ?></button>
      </div>
    </div>
  </div>
</div>

<!-- ========== MODAL SỬA ========== -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title"><i class="bi bi-pencil-square"></i> <?= t('phongban.edit_title') ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editForm">
          <div class="mb-3">
            <label class="form-label"><?= t('phongban.code_label') ?></label>
            <input type="text" class="form-control" name="maPB" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('phongban.name_label') ?></label>
            <input type="text" class="form-control" name="tenPB" required>
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('phongban.address_label') ?></label>
            <input type="text" class="form-control" name="diaChi">
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('phongban.email_label') ?></label>
            <input type="email" class="form-control" name="email">
          </div>
          <div class="mb-3">
            <label class="form-label"><?= t('phongban.phone_label') ?></label>
            <input type="text" class="form-control" name="soDienThoai">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('common.cancel') ?></button>
        <button type="button" class="btn btn-success" id="btnEditConfirm"><?= t('phongban.update_confirm') ?></button>
      </div>
    </div>
  </div>
</div>

<script>
// ====== Thêm phòng ban ======
document.getElementById("btnAddConfirm").addEventListener("click", function () {
  const form = document.getElementById("addForm");
  const formData = new FormData(form);

  fetch("add.php", { method: "POST", body: formData })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        Swal.fire({ icon: "success", title: <?= json_encode(t('common.success_title')) ?>, text: <?= json_encode(t('phongban.add_success')) ?>, showConfirmButton: false, timer: 1500 })
          .then(() => location.reload());
      } else {
        Swal.fire({ icon: "error", title: <?= json_encode(t('common.error_title')) ?>, text: data.message });
      }
    });
});

// ====== Mở form sửa ======
document.querySelectorAll('.btnEdit').forEach(btn => {
  btn.addEventListener('click', () => {
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    const form = document.getElementById('editForm');
    form.maPB.value = btn.getAttribute('data-ma');
    form.tenPB.value = btn.getAttribute('data-ten');
    form.diaChi.value = btn.getAttribute('data-diachi');
    form.email.value = btn.getAttribute('data-email');
    form.soDienThoai.value = btn.getAttribute('data-sdt');
    modal.show();
  });
});

// ====== Lưu sửa ======
document.getElementById("btnEditConfirm").addEventListener("click", function () {
  const form = document.getElementById("editForm");
  const formData = new FormData(form);

  fetch("edit.php", { method: "POST", body: formData })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        Swal.fire({ icon: "success", title: <?= json_encode(t('common.success_title')) ?>, text: <?= json_encode(t('phongban.update_success')) ?>, showConfirmButton: false, timer: 1500 })
          .then(() => location.reload());
      } else {
        Swal.fire({ icon: "error", title: <?= json_encode(t('common.error_title')) ?>, text: data.message });
      }
    });
});

// ====== Xóa phòng ban ======
document.querySelectorAll('.btnDelete').forEach(btn => {
  btn.addEventListener('click', () => {
    const maPB = btn.getAttribute('data-ma');
    Swal.fire({
      title: <?= json_encode(t('phongban.delete_confirm_title')) ?>,
      text: <?= json_encode(t('phongban.delete_confirm_text')) ?>,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: <?= json_encode(t('common.confirm_delete_button')) ?>,
      cancelButtonText: <?= json_encode(t('common.cancel')) ?>
    }).then(result => {
      if (result.isConfirmed) {
        fetch("delete.php", { method: "POST", body: new URLSearchParams({ maPB }) })
          .then(res => res.json())
          .then(data => {
            if (data.status === "success") {
              Swal.fire({ icon: "success", title: <?= json_encode(t('common.deleted_title')) ?>, text: <?= json_encode(t('phongban.delete_success')) ?>, showConfirmButton: false, timer: 1500 })
                .then(() => location.reload());
            } else {
              Swal.fire({ icon: "error", title: <?= json_encode(t('common.error_title')) ?>, text: data.message });
            }
          });
      }
    });
  });
});
</script>

<?php
include "../../layout/footer.php";
?>
