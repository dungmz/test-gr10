<?php
include "../../config/db.php";
include "../../layout/header_sv.php";
include "../../layout/sidebar_sv.php";
load_lang('faculties');
?>

<style>
/* ====== TỰ ĐỘNG CĂN CHỈNH THEO SIDEBAR ====== */
.content {
  margin-left: 80px;
  padding: 20px;
  transition: margin-left 0.3s ease;
}

/* Khi sidebar mở rộng */
.sidebar:hover ~ .content {
  margin-left: 240px;
}

/* Bảng hiển thị gọn gàng */
.table {
  border-radius: 8px;
  overflow: hidden;
}
.card {
  border-radius: 12px;
}
</style>

<div class="content">
  <h4 class="fw-semibold mb-3 text-primary">
    <i class="bi bi-diagram-3"></i> <?= t('faculties.list_title') ?>
  </h4>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      
      <!-- Thanh tìm kiếm + bộ lọc -->
      <form class="row g-2 mb-3" method="GET">
        <div class="col-md-5">
             <input type="text" name="search" class="form-control"
               placeholder="<?= t('faculties.search_placeholder') ?>"
                 value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </div>

        <div class="col-md-4">
          <select name="filter_school" class="form-select">
            <option value=""><?= t('faculties.filter_school') ?></option>
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
          <button type="submit" class="btn btn-primary w-50">
            <i class="bi bi-search"></i> <?= t('common.filter') ?>
          </button>
          <a href="index_sv.php" class="btn btn-secondary w-50">
            <i class="bi bi-arrow-clockwise"></i> <?= t('common.reset') ?>
          </a>
        </div>
      </form>

      <!-- Bảng hiển thị -->
      <table class="table table-bordered align-middle text-center table-hover">
        <thead class="table-primary">
          <tr>
            <th><?= t('faculties.code') ?></th>
            <th><?= t('faculties.name') ?></th>
            <th><?= t('faculties.school') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $search = isset($_GET['search']) ? trim($_GET['search']) : '';
          $filter_school = isset($_GET['filter_school']) ? trim($_GET['filter_school']) : '';

            $sql = "SELECT k.maKhoa, k.tenKhoa, k.maTruong AS maTruong, s.name AS tenTruong
                  FROM faculties k
                  LEFT JOIN schools s ON k.maTruong = s.maTruong
                  WHERE 1";
          $params = [];
          $types = "";

          if ($search !== '') {
            $sql .= " AND (k.maKhoa LIKE ? OR k.tenKhoa LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term]);
            $types .= "ss";
          }

          if ($filter_school !== '') {
            $sql .= " AND k.maTruong = ?";
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
                      <td>{$row['maKhoa']}</td>
                      <td>{$facultyName}</td>
                      <td>{$schoolName}</td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='3' class='text-muted'>" . t('common.no_data') . "</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../../layout/footer_sv.php"; ?>
