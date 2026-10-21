<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$script = str_replace('\\','/', $_SERVER['SCRIPT_NAME'] ?? '/');
if (($p = strpos($script, '/modules/')) !== false) { $basePath = substr($script, 0, $p + 1); } else { $basePath = rtrim(dirname($script), '/') . '/'; }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'giangvien') { header("Location: " . $basePath . "login.php"); exit; }
include "../../config/db.php";
include "../../layout/header_gv.php";
include "../../layout/sidebar_gv.php";
load_lang('cocau');
?>
<div class="content">
  <h4 class="fw-semibold text-primary mb-4"><i class="bi bi-diagram-3"></i> <?= t('cocau.title') ?></h4>

  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-truong-khoa-nganh" type="button" role="tab"><?= t('cocau.tab_school') ?></button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-phongban" type="button" role="tab"><?= t('cocau.tab_departments') ?></button>
    </li>
  </ul>

  <div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="tab-truong-khoa-nganh" role="tabpanel">
      <div class="accordion accordion-flush" id="accSchools">
        <?php
        $schools = $conn->query("SELECT name, matruong FROM schools ORDER BY name ASC");
        if ($schools && $schools->num_rows) {
          $i = 0;
          while ($s = $schools->fetch_assoc()) {
            $i++;
            $sid = "sch".$i;
            $schoolName = t_data('schools', (string)$s['matruong'], 'name', (string)$s['name']);
            echo '<div class="accordion-item">';
            echo '<h2 class="accordion-header" id="h'.$sid.'">';
            echo '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c'.$sid.'">';
            echo '<span class="fw-semibold">'.htmlspecialchars($schoolName).'</span> <span class="ms-2 text-muted">('.htmlspecialchars($s['matruong']).')</span>';
            echo '</button></h2>';
            echo '<div id="c'.$sid.'" class="accordion-collapse collapse" data-bs-parent="#accSchools"><div class="accordion-body p-2">';
            $fac = $conn->query("SELECT maKhoa, tenKhoa FROM faculties WHERE matruong='".$conn->real_escape_string($s['matruong'])."' ORDER BY tenKhoa ASC");
            if ($fac && $fac->num_rows) {
              echo '<div class="list-group list-group-flush">';
              while ($f = $fac->fetch_assoc()) {
                $facultyName = t_data('faculties', (string)$f['maKhoa'], 'tenKhoa', (string)$f['tenKhoa']);
                echo '<div class="list-group-item">';
                echo '<div class="d-flex align-items-center justify-content-between">';
                echo '<div class="fw-semibold"><i class="bi bi-diagram-3"></i> '.htmlspecialchars($facultyName).'</div>';
                echo '</div>';
                $maj = $conn->query("SELECT maNganh, tenNganh FROM majors WHERE maKhoa='".$conn->real_escape_string($f['maKhoa'])."' ORDER BY tenNganh ASC");
                if ($maj && $maj->num_rows) {
                  echo '<div class="mt-2 ps-3">';
                  echo '<ul class="list-unstyled mb-0">';
                  while ($m = $maj->fetch_assoc()) {
                    $majorName = t_data('majors', (string)$m['maNganh'], 'tenNganh', (string)$m['tenNganh']);
                    echo '<li class="py-1"><i class="bi bi-dot"></i> '.htmlspecialchars($majorName).' <span class="text-muted">('.htmlspecialchars($m['maNganh']).')</span></li>';
                  }
                  echo '</ul>';
                  echo '</div>';
                } else {
                  echo '<div class="mt-2 ps-3 text-muted">'.t('cocau.major_empty').'</div>';
                }
                echo '</div>';
              }
              echo '</div>';
            } else {
              echo '<div class="text-muted">'.t('cocau.faculty_empty').'</div>';
            }
            echo '</div></div></div>';
          }
        } else {
          echo '<div class="alert alert-light border">'.t('cocau.school_empty').'</div>';
        }
        ?>
      </div>
    </div>

    <div class="tab-pane fade" id="tab-phongban" role="tabpanel">
      <div class="card shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
              <thead class="table-primary">
                <tr>
                  <th style="width:120px"><?= t('cocau.department_code') ?></th>
                  <th><?= t('cocau.department_name') ?></th>
                  <th style="width:220px"><?= t('cocau.department_email') ?></th>
                  <th style="width:160px"><?= t('cocau.department_phone') ?></th>
                  <th><?= t('cocau.department_address') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php
                $pb = $conn->query("SELECT maPB, tenPB, email, soDienThoai, diaChi FROM phongban ORDER BY tenPB ASC");
                if ($pb && $pb->num_rows) {
                  while ($r = $pb->fetch_assoc()) {
                    $deptName = t_data('phongban', (string)$r['maPB'], 'tenPB', (string)$r['tenPB']);
                    $deptAddress = t_data('phongban', (string)$r['maPB'], 'diaChi', (string)($r['diaChi'] ?? ''));
                    echo '<tr>';
                    echo '<td>'.htmlspecialchars($r['maPB']).'</td>';
                    echo '<td>'.htmlspecialchars($deptName).'</td>';
                    echo '<td>'.htmlspecialchars($r['email'] ?: '').'</td>';
                    echo '<td>'.htmlspecialchars($r['soDienThoai'] ?: '').'</td>';
                    echo '<td>'.htmlspecialchars($deptAddress ?: '').'</td>';
                    echo '</tr>';
                  }
                } else {
                  echo '<tr><td colspan="5" class="text-muted">'.t('cocau.department_empty').'</td></tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include "../../layout/footer.php"; ?>
