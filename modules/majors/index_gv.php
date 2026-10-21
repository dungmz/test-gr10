<?php
include "../../config/db.php";
include "../../layout/header_gv.php";
include "../../layout/sidebar_gv.php";
load_lang('majors');
?>

<div class="content">
  <h4 class="fw-semibold text-primary mb-3">
    <i class="bi bi-layers"></i> <?= t('majors.list_title') ?>
  </h4>

  <form class="d-flex mb-3" method="GET">
    <input type="text" name="search" class="form-control me-2" style="max-width:400px"
          placeholder="<?= t('majors.search_placeholder') ?>"
           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> <?= t('common.search') ?></button>
        <a href="index_gv.php" class="btn btn-secondary ms-2"><i class="bi bi-arrow-clockwise"></i> <?= t('common.reset') ?></a>
  </form>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <table class="table table-bordered align-middle text-center table-hover">
        <thead class="table-primary">
          <tr>
            <th><?= t('majors.code') ?></th>
            <th><?= t('majors.name') ?></th>
            <th><?= t('majors.faculty') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $search = isset($_GET['search']) ? trim($_GET['search']) : '';
          if ($search !== '') {
                    $sql = "SELECT n.maNganh, n.tenNganh, k.maKhoa AS maKhoa, k.tenKhoa 
                      FROM majors n 
                      LEFT JOIN faculties k ON n.maKhoa = k.maKhoa
                      WHERE n.maNganh LIKE ? OR n.tenNganh LIKE ?
                      ORDER BY n.tenNganh ASC";
              $term = "%{$search}%";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("ss", $term, $term);
              $stmt->execute();
              $result = $stmt->get_result();
          } else {
                $result = $conn->query("SELECT n.maNganh, n.tenNganh, k.maKhoa AS maKhoa, k.tenKhoa 
                                      FROM majors n 
                                      LEFT JOIN faculties k ON n.maKhoa = k.maKhoa 
                                      ORDER BY n.tenNganh ASC");
          }

          if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  $majorName = t_data('majors', (string)$row['maNganh'], 'tenNganh', (string)$row['tenNganh']);
                  $facultyName = t_data('faculties', (string)$row['maKhoa'], 'tenKhoa', (string)$row['tenKhoa']);
                  echo "<tr>
                          <td>{$row['maNganh']}</td>
                      <td>{$majorName}</td>
                      <td>{$facultyName}</td>
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

<?php include "../../layout/footer_gv.php"; ?>
