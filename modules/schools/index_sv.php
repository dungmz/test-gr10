<?php
include "../../config/db.php";
include "../../layout/header_sv.php";
include "../../layout/sidebar_sv.php";
load_lang('schools');
?>

<div class="content">
  <h4 class="fw-semibold text-primary mb-3">
    <i class="bi bi-building"></i> <?= t('schools.list_title') ?>
  </h4>

  <form class="d-flex mb-3" method="GET">
        <input type="text" name="search" class="form-control me-2" style="max-width:400px"
          placeholder="<?= t('schools.search_placeholder') ?>"
           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> <?= t('common.search') ?></button>
        <a href="index_sv.php" class="btn btn-secondary ms-2"><i class="bi bi-arrow-clockwise"></i> <?= t('common.reset') ?></a>
  </form>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <table class="table table-bordered align-middle text-center table-hover">
        <thead class="table-primary">
          <tr>
            <th><?= t('schools.code') ?></th>
            <th><?= t('schools.name') ?></th>
            <th><?= t('schools.university_code') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $search = isset($_GET['search']) ? trim($_GET['search']) : '';
          if ($search !== '') {
              $sql = "SELECT maTruong, name, maDH FROM schools WHERE maTruong LIKE ? OR name LIKE ? ORDER BY name ASC";
              $term = "%{$search}%";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("ss", $term, $term);
              $stmt->execute();
              $result = $stmt->get_result();
          } else {
              $result = $conn->query("SELECT maTruong, name, maDH FROM schools ORDER BY name ASC");
          }

          if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  $schoolName = t_data('schools', (string)$row['maTruong'], 'name', (string)$row['name']);
                  echo "<tr>
                          <td>{$row['maTruong']}</td>
                          <td>{$schoolName}</td>
                          <td>{$row['maDH']}</td>
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
