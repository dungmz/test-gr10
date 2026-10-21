<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('employees');
?>

<div class="container-fluid mt-4">
  <h4 class="fw-semibold mb-3 text-primary">
    <i class="bi bi-people"></i> <?= t('employees.manage_title') ?>
  </h4>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

      <!-- Tìm kiếm + Bộ lọc -->
      <form class="row g-2 mb-3" method="GET">
        <div class="col-md-5">
          <input type="text" name="search" class="form-control"
               placeholder="<?= t('employees.search_placeholder') ?>"
                 value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </div>

        <div class="col-md-4">
          <select name="filter_school" class="form-select">
            <option value=""><?= t('employees.filter_school') ?></option>
            <?php
            $schools = $conn->query("SELECT maTruong, name FROM schools ORDER BY name ASC");
            while ($s = $schools->fetch_assoc()) {
                $selected = (isset($_GET['filter_school']) && $_GET['filter_school'] == $s['maTruong']) ? "selected" : "";
              $schoolName = t_data('schools', (string)$s['maTruong'], 'name', (string)$s['name']);
              echo "<option value='{$s['maTruong']}' $selected>{$schoolName}</option>";
            }
            ?>
          </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-50"><i class="bi bi-search"></i> <?= t('common.filter') ?></button>
          <a href="index.php" class="btn btn-secondary w-50"><i class="bi bi-arrow-clockwise"></i> <?= t('common.reset') ?></a>
        </div>
      </form>

      <!-- Form xóa hàng loạt -->
      <form id="multiDeleteForm" method="POST" action="delete.php">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <a href="create.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> <?= t('common.add_new') ?>
          </a>
          <button type="button" id="deleteSelected" class="btn btn-danger" disabled>
            <i class="bi bi-trash"></i> <?= t('common.delete_selected') ?>
          </button>
        </div>

        <table class="table table-bordered align-middle text-center table-hover">
          <thead class="table-primary">
            <tr>
              <th><input type="checkbox" id="selectAll"></th>
              <th><?= t('employees.name') ?></th>
              <th><?= t('employees.position') ?></th>
              <th><?= t('employees.email') ?></th>
              <th><?= t('employees.phone') ?></th>
              <th><?= t('employees.school') ?></th>
              <th><?= t('common.actions') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $filter_school = isset($_GET['filter_school']) ? trim($_GET['filter_school']) : '';

                $sql = "SELECT e.stt, e.tenNV, e.chucVu, e.email, e.soDienThoai, s.matruong AS maTruong, s.name AS tenTruong
                    FROM employees e
                    LEFT JOIN schools s ON e.maTruong = s.maTruong
                    WHERE 1";
            $params = [];
            $types = "";

            if ($search !== '') {
                $sql .= " AND (e.tenNV LIKE ? OR e.chucVu LIKE ?)";
                $term = "%{$search}%";
                $params = array_merge($params, [$term, $term]);
                $types .= "ss";
            }

            if ($filter_school !== '') {
                $sql .= " AND e.maTruong = ?";
                $params[] = $filter_school;
                $types .= "s";
            }

            $sql .= " ORDER BY e.tenNV ASC";
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                $staffName = t_data('employees', (string)$row['stt'], 'tenNV', (string)$row['tenNV']);
                $positionName = t_data('employees', (string)$row['stt'], 'chucVu', (string)$row['chucVu']);
                $schoolName = t_data('schools', (string)$row['maTruong'], 'name', (string)$row['tenTruong']);
                    echo "<tr>
                            <td><input type='checkbox' name='tenNV[]' value='{$row['tenNV']}' class='selectItem'></td>
                    <td>{$staffName}</td>
                    <td>{$positionName}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['soDienThoai']}</td>
                    <td>{$schoolName}</td>
                            <td>
                              <a href='edit.php?tenNV=" . urlencode($row['tenNV']) . "' class='btn btn-warning btn-sm me-1'>
                                <i class='bi bi-pencil-square'></i>
                              </a>
                              <button type='button' onclick=\"confirmDelete('delete.php?tenNV=" . urlencode($row['tenNV']) . "')\" class='btn btn-danger btn-sm'>
                                <i class='bi bi-trash'></i>
                              </button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-muted'>" . t('employees.no_match') . "</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </form>
    </div>
  </div>
</div>

<script>
// ---------------------- XÓA HÀNG LOẠT ----------------------
const selectAll = document.getElementById('selectAll');
const deleteSelectedBtn = document.getElementById('deleteSelected');
const form = document.getElementById('multiDeleteForm');
const confirmDeleteTitle = <?= json_encode(t('common.confirm_delete_title')) ?>;
const confirmDeleteMultiText = <?= json_encode(t('employees.confirm_delete_multi_text')) ?>;
const confirmDeleteSingleTitle = <?= json_encode(t('employees.confirm_delete_single_title')) ?>;
const confirmDeleteText = <?= json_encode(t('common.confirm_delete_text')) ?>;
const confirmDeleteButton = <?= json_encode(t('common.confirm_delete_button')) ?>;
const cancelText = <?= json_encode(t('common.cancel')) ?>;

function toggleDeleteButton() {
  const selectedCount = document.querySelectorAll('.selectItem:checked').length;
  deleteSelectedBtn.disabled = selectedCount === 0;
}

selectAll.addEventListener('click', () => {
  document.querySelectorAll('.selectItem').forEach(cb => cb.checked = selectAll.checked);
  toggleDeleteButton();
});

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

// ---------------------- XÓA TỪNG NGƯỜI ----------------------
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
