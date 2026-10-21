<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('faculties');
?>

<div class="container-fluid mt-4">
  <h4 class="fw-semibold mb-3 text-primary">
    <i class="bi bi-diagram-3"></i> <?= t('faculties.manage_title') ?>
  </h4>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

      <form class="row g-2 mb-3" method="GET">
        <div class="col-md-6">
             <input type="text" name="search" class="form-control"
               placeholder="<?= t('faculties.search_placeholder') ?>"
                 value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </div>

        <div class="col-md-4">
          <select name="filter_school" class="form-select">
            <option value=""><?= t('faculties.filter_school') ?></option>
            <?php
            $schools = $conn->query("SELECT matruong, name FROM schools ORDER BY name ASC");
            while ($s = $schools->fetch_assoc()) {
                $selected = (isset($_GET['filter_school']) && $_GET['filter_school'] == $s['matruong']) ? "selected" : "";
              $schoolName = t_data('schools', (string)$s['matruong'], 'name', (string)$s['name']);
              echo "<option value='{$s['matruong']}' $selected>{$schoolName}</option>";
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
              <th><?= t('faculties.code') ?></th>
              <th><?= t('faculties.name') ?></th>
              <th><?= t('faculties.school') ?></th>
              <th><?= t('common.actions') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $filter_school = isset($_GET['filter_school']) ? trim($_GET['filter_school']) : '';

                $sql = "SELECT k.maKhoa, k.tenKhoa, k.matruong AS maTruong, s.name AS tenTruong 
                    FROM faculties k
                    LEFT JOIN schools s ON k.matruong = s.matruong
                    WHERE 1";
            $params = [];
            $types = "";

            if ($search !== "") {
                $sql .= " AND (k.maKhoa LIKE ? OR k.tenKhoa LIKE ?)";
                $term = "%{$search}%";
                $params = array_merge($params, [$term, $term]);
                $types .= "ss";
            }

            if ($filter_school !== "") {
                $sql .= " AND k.matruong = ?";
                $params[] = $filter_school;
                $types .= "s";
            }

            $sql .= " ORDER BY k.tenKhoa ASC";
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                $facultyName = t_data('faculties', (string)$row['maKhoa'], 'tenKhoa', (string)$row['tenKhoa']);
                $schoolName = t_data('schools', (string)$row['maTruong'], 'name', (string)$row['tenTruong']);
                    echo "<tr>
                            <td><input type='checkbox' name='maKhoa[]' value='{$row['maKhoa']}' class='selectItem'></td>
                            <td>{$row['maKhoa']}</td>
                    <td>{$facultyName}</td>
                    <td>{$schoolName}</td>
                            <td>
                              <a href='edit.php?maKhoa=" . urlencode($row['maKhoa']) . "' class='btn btn-warning btn-sm me-1'>
                                <i class='bi bi-pencil-square'></i>
                              </a>
                              <button type='button' onclick=\"confirmDelete('delete.php?maKhoa=" . urlencode($row['maKhoa']) . "')\" class='btn btn-danger btn-sm'>
                                <i class='bi bi-trash'></i>
                              </button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-muted'>" . t('common.no_data') . "</td></tr>";
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
    title: <?= json_encode(t('common.confirm_delete_title')) ?>,
    text: <?= json_encode(t('faculties.delete_selected_text', ['count' => '{count}'])) ?>.replace('{count}', selectedCount),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: <?= json_encode(t('common.confirm_delete_button')) ?>,
    cancelButtonText: <?= json_encode(t('common.cancel')) ?>
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit();
    }
  });
});

function confirmDelete(url) {
  Swal.fire({
    title: <?= json_encode(t('common.confirm_delete_title')) ?>,
    text: <?= json_encode(t('common.confirm_delete_text')) ?>,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: <?= json_encode(t('common.confirm_delete_button')) ?>,
    cancelButtonText: <?= json_encode(t('common.cancel')) ?>
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = url;
    }
  });
}
</script>

<?php include "../../layout/footer.php"; ?>
