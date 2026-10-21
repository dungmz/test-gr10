<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('majors');
?>

<div class="container-fluid mt-4">
  <h4 class="fw-semibold mb-3 text-primary">
    <i class="bi bi-mortarboard"></i> <?= t('majors.manage_title') ?>
  </h4>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

      <form class="row g-2 mb-3" method="GET">
        <div class="col-md-6">
          <input type="text" name="search" class="form-control"
               placeholder="<?= t('majors.search_placeholder') ?>"
                 value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </div>

        <div class="col-md-4">
          <select name="filter_faculty" class="form-select">
            <option value=""><?= t('majors.filter_faculty') ?></option>
            <?php
            $faculties = $conn->query("SELECT maKhoa, tenKhoa FROM faculties ORDER BY tenKhoa ASC");
            while ($f = $faculties->fetch_assoc()) {
                $selected = (isset($_GET['filter_faculty']) && $_GET['filter_faculty'] == $f['maKhoa']) ? "selected" : "";
              $facultyName = t_data('faculties', (string)$f['maKhoa'], 'tenKhoa', (string)$f['tenKhoa']);
              echo "<option value='{$f['maKhoa']}' $selected>{$facultyName}</option>";
            }
            ?>
          </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-50"><i class="bi bi-search"></i> <?= t('common.filter') ?></button>
          <a href="index.php" class="btn btn-secondary w-50"><i class="bi bi-arrow-clockwise"></i> <?= t('common.reset') ?></a>
        </div>
      </form>

      <div class="d-flex justify-content-between align-items-center mb-2">
        <a href="create.php" class="btn btn-success">
          <i class="bi bi-plus-circle"></i> <?= t('common.add_new') ?>
        </a>
        <button type="button" id="deleteSelected" class="btn btn-danger" disabled>
          <i class="bi bi-trash"></i> <?= t('common.delete_selected') ?>
        </button>
      </div>

      <form id="multiDeleteForm" method="POST" action="delete.php">
        <table class="table table-bordered align-middle text-center table-hover">
          <thead class="table-primary">
            <tr>
              <th><input type="checkbox" id="selectAll"></th>
              <th><?= t('majors.code') ?></th>
              <th><?= t('majors.name') ?></th>
              <th><?= t('majors.faculty') ?></th>
              <th><?= t('common.actions') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $filter_faculty = isset($_GET['filter_faculty']) ? trim($_GET['filter_faculty']) : '';

                $sql = "SELECT n.maNganh, n.tenNganh, k.maKhoa AS maKhoa, k.tenKhoa 
                    FROM majors n
                    LEFT JOIN faculties k ON n.maKhoa = k.maKhoa
                    WHERE 1";
            $params = [];
            $types = "";

            if ($search !== "") {
                $sql .= " AND (n.maNganh LIKE ? OR n.tenNganh LIKE ?)";
                $term = "%{$search}%";
                $params = array_merge($params, [$term, $term]);
                $types .= "ss";
            }

            if ($filter_faculty !== "") {
                $sql .= " AND n.maKhoa = ?";
                $params[] = $filter_faculty;
                $types .= "s";
            }

            $sql .= " ORDER BY n.tenNganh ASC";
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                $majorName = t_data('majors', (string)$row['maNganh'], 'tenNganh', (string)$row['tenNganh']);
                $facultyName = t_data('faculties', (string)$row['maKhoa'], 'tenKhoa', (string)$row['tenKhoa']);
                    echo "<tr>
                            <td><input type='checkbox' name='maNganh[]' value='{$row['maNganh']}' class='selectItem'></td>
                            <td>{$row['maNganh']}</td>
                    <td>{$majorName}</td>
                    <td>{$facultyName}</td>
                            <td>
                              <a href='edit.php?maNganh=" . urlencode($row['maNganh']) . "' class='btn btn-warning btn-sm me-1'>
                                <i class='bi bi-pencil-square'></i>
                              </a>
                              <button type='button' onclick=\"confirmDelete('delete.php?maNganh=" . urlencode($row['maNganh']) . "')\" class='btn btn-danger btn-sm'>
                                <i class='bi bi-trash'></i>
                              </button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-muted'>" . t('majors.no_match') . "</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </form>
    </div>
  </div>
</div>

<script>
const selectAll = document.getElementById('selectAll');
const deleteSelectedBtn = document.getElementById('deleteSelected');
const form = document.getElementById('multiDeleteForm');
const confirmDeleteTitle = <?= json_encode(t('common.confirm_delete_title')) ?>;
const confirmDeleteMultiText = <?= json_encode(t('majors.confirm_delete_multi_text')) ?>;
const confirmDeleteSingleTitle = <?= json_encode(t('majors.confirm_delete_single_title')) ?>;
const confirmDeleteText = <?= json_encode(t('common.confirm_delete_text')) ?>;
const confirmDeleteButton = <?= json_encode(t('common.confirm_delete_button')) ?>;
const cancelText = <?= json_encode(t('common.cancel')) ?>;

function toggleDeleteButton() {
  const selectedCount = document.querySelectorAll('.selectItem:checked').length;
  deleteSelectedBtn.disabled = selectedCount === 0;
}

if (selectAll) {
  selectAll.addEventListener('click', () => {
    document.querySelectorAll('.selectItem').forEach(cb => cb.checked = selectAll.checked);
    toggleDeleteButton();
  });
}

document.querySelectorAll('.selectItem').forEach(cb => cb.addEventListener('change', toggleDeleteButton));

deleteSelectedBtn.addEventListener('click', () => {
  const selectedCount = document.querySelectorAll('.selectItem:checked').length;
  if (selectedCount === 0) return;

  Swal.fire({
    title: confirmDeleteTitle,
    text: confirmDeleteMultiText.replace('{count}', selectedCount),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: confirmDeleteButton,
    cancelButtonText: cancelText
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit();
    }
  });
});

function confirmDelete(url) {
  Swal.fire({
    title: confirmDeleteSingleTitle,
    text: confirmDeleteText,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: confirmDeleteButton,
    cancelButtonText: cancelText
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = url;
    }
  });
}
</script>

<?php include "../../layout/footer.php"; ?>
