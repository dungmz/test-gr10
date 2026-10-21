<?php
include "../../config/db.php";
include "../../layout/header_sv.php";
include "../../layout/sidebar_sv.php";
load_lang('employees');
?>

<style>


</style>

<div class="content">
  <h4 class="fw-semibold mb-3 text-primary">
    <i class="bi bi-people"></i> <?= t('employees.list_title') ?>
  </h4>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

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
          <a href="index_sv.php" class="btn btn-secondary w-50"><i class="bi bi-arrow-clockwise"></i> <?= t('common.reset') ?></a>
        </div>
      </form>

      <!-- Bảng hiển thị -->
      <table class="table table-bordered align-middle text-center table-hover">
        <thead class="table-primary">
          <tr>
            <th><?= t('employees.name') ?></th>
            <th><?= t('employees.position') ?></th>
            <th><?= t('employees.email') ?></th>
            <th><?= t('employees.phone') ?></th>
            <th><?= t('employees.school') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $search = isset($_GET['search']) ? trim($_GET['search']) : '';
          $filter_school = isset($_GET['filter_school']) ? trim($_GET['filter_school']) : '';

            $sql = "SELECT e.stt, e.tenNV, e.chucVu, e.email, e.soDienThoai, s.maTruong AS maTruong, s.name AS tenTruong
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
                          <td>{$staffName}</td>
                          <td>{$positionName}</td>
                          <td>{$row['email']}</td>
                          <td>{$row['soDienThoai']}</td>
                          <td>{$schoolName}</td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='5' class='text-muted'>" . t('employees.no_match') . "</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../../layout/footer_sv.php"; ?>
