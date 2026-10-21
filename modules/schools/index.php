<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('schools');
?>

<div class="container-fluid mt-4">
  <h4 class="fw-semibold mb-3 text-primary">
    <i class="bi bi-building"></i> <?= t('schools.manage_title') ?>
  </h4>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

      <form class="d-flex mb-3" method="GET">
         <input type="text" name="search" class="form-control me-2" style="max-width: 400px;"
           placeholder="<?= t('schools.search_placeholder') ?>"
               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
         <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> <?= t('common.search') ?></button>
         <a href="index.php" class="btn btn-secondary ms-2"><i class="bi bi-arrow-clockwise"></i> <?= t('common.reset') ?></a>
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
              <th><?= t('schools.code') ?></th>
              <th><?= t('schools.name') ?></th>
              <th><?= t('schools.university_code') ?></th>
              <th><?= t('common.actions') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';

            if ($search !== '') {
                $sql = "SELECT matruong, name, maDH FROM schools 
                        WHERE matruong LIKE ? OR name LIKE ?
                        ORDER BY name ASC";
                $term = "%{$search}%";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $term, $term);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $conn->query("SELECT matruong, name, maDH FROM schools ORDER BY name ASC");
            }

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                $schoolName = t_data('schools', (string)$row['matruong'], 'name', (string)$row['name']);
                    echo "<tr>
                            <td><input type='checkbox' name='matruong[]' value='{$row['matruong']}' class='selectItem'></td>
                            <td>{$row['matruong']}</td>
                    <td>{$schoolName}</td>
                            <td>{$row['maDH']}</td>
                            <td>
                              <a href='edit.php?matruong=" . urlencode($row['matruong']) . "' class='btn btn-warning btn-sm me-1'>
                                <i class='bi bi-pencil-square'></i>
                              </a>
                              <button type='button' onclick=\"confirmDelete('delete.php?matruong=" . urlencode($row['matruong']) . "')\" class='btn btn-danger btn-sm'>
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
    text: <?= json_encode(t('schools.delete_selected_text', ['count' => '{count}'])) ?>.replace('{count}', selectedCount),
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
