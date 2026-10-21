<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('cocau');
?>

<div class="content">
  <h4 class="fw-semibold text-primary mb-4">
    <i class="bi bi-diagram-3-fill"></i> <?= t('cocau.title') ?>
  </h4>

  <div class="mb-3 d-flex justify-content-between align-items-center">
    <input type="text" id="searchInput" class="form-control w-50" placeholder="🔍 <?= t('cocau.search_placeholder') ?>">
    <div class="d-flex gap-2">
      <button class="btn btn-outline-primary btn-sm" id="zoomIn"><i class="bi bi-zoom-in"></i></button>
      <button class="btn btn-outline-primary btn-sm" id="zoomOut"><i class="bi bi-zoom-out"></i></button>
      <button class="btn btn-outline-secondary btn-sm" id="resetZoom"><i class="bi bi-arrow-repeat"></i></button>
    </div>
  </div>

  <div id="orgChartContainer" class="border rounded p-4 bg-white shadow-sm" 
       style="min-height:500px; overflow:auto; transform-origin: top left;">
    <?php
    try {
      $schools = $conn->query("SELECT * FROM schools");
      if ($schools->num_rows == 0) {
        echo "<p class='text-center text-danger'>" . t('cocau.no_org') . "</p>";
      } else {
        while ($school = $schools->fetch_assoc()) {
          $schoolName = t_data('schools', (string)$school['matruong'], 'name', (string)$school['name']);
          echo "<div class='org-root mb-4'>
                  <div class='org-unit bg-primary text-white fw-semibold p-2 rounded text-center mb-3'>
                    <i class='bi bi-bank'></i> {$schoolName}
                  </div>";

          $faculties = $conn->query("SELECT * FROM faculties WHERE matruong='{$school['matruong']}'");
          if ($faculties->num_rows > 0) {
            echo "<div class='d-flex flex-wrap justify-content-center gap-3'>";
            while ($faculty = $faculties->fetch_assoc()) {
              $facultyName = t_data('faculties', (string)$faculty['maKhoa'], 'tenKhoa', (string)$faculty['tenKhoa']);

              $lead = $conn->query("
                SELECT stt, tenNV, chucVu, email, soDienThoai
                FROM employees
                WHERE matruong='{$school['matruong']}' 
                AND chucVu LIKE '%Trưởng khoa%'
                LIMIT 1
              ")->fetch_assoc();

              $leadName = $lead['tenNV'] ?? t('cocau.no_head');
              $leadRoleRaw = $lead['chucVu'] ?? t('cocau.no_info');
              $leadRole = isset($lead['stt'])
                ? t_data('employees', (string)$lead['stt'], 'chucVu', (string)$leadRoleRaw)
                : $leadRoleRaw;

              echo "<div class='org-unit border p-3 rounded shadow-sm' style='min-width:260px'>
                      <div class='fw-bold text-primary mb-2 cursor-pointer faculty-item'
                           data-ten='".htmlspecialchars($facultyName, ENT_QUOTES)."'
                           data-lead='".htmlspecialchars($leadName, ENT_QUOTES)."'
                           data-chucvu='".htmlspecialchars($leadRole, ENT_QUOTES)."'
                           data-email='".htmlspecialchars($lead['email'] ?? "-", ENT_QUOTES)."'
                           data-sdt='".htmlspecialchars($lead['soDienThoai'] ?? "-", ENT_QUOTES)."'>
                        <i class='bi bi-diagram-3'></i> {$facultyName}
                      </div>";

              $majors = $conn->query("SELECT * FROM majors WHERE maKhoa='{$faculty['maKhoa']}'");
              if ($majors->num_rows > 0) {
                echo "<ul class='list-unstyled mb-0'>";
                while ($major = $majors->fetch_assoc()) {
                  $majorName = t_data('majors', (string)$major['maNganh'], 'tenNganh', (string)$major['tenNganh']);
                  echo "<li><i class='bi bi-journal-text text-success'></i> {$majorName}</li>";
                }
                echo "</ul>";
              } else {
                echo "<p class='text-muted small mb-0'>" . t('cocau.major_empty') . "</p>";
              }

              echo "</div>";
            }
            echo "</div>";
          } else {
            echo "<p class='text-muted text-center'>" . t('cocau.faculty_empty') . "</p>";
          }

          echo "</div>";
        }
      }
    } catch (Exception $e) {
      echo "<p class='text-center text-danger'>" . t('cocau.no_error') . "</p>";
    }
    ?>
  </div>
</div>

<script>
let zoom = 1;
const container = document.getElementById("orgChartContainer");
document.getElementById("zoomIn").onclick = () => { zoom += 0.1; container.style.transform = `scale(${zoom})`; };
document.getElementById("zoomOut").onclick = () => { if (zoom > 0.3) zoom -= 0.1; container.style.transform = `scale(${zoom})`; };
document.getElementById("resetZoom").onclick = () => { zoom = 1; container.style.transform = "scale(1)"; };

document.getElementById("searchInput").addEventListener("input", function() {
  const term = this.value.toLowerCase();
  document.querySelectorAll(".org-unit, li").forEach(el => {
    el.style.display = el.innerText.toLowerCase().includes(term) ? "block" : "none";
  });
});

document.addEventListener("click", function(e) {
  if (e.target.classList.contains("faculty-item")) {
    const ten = e.target.dataset.ten;
    const lead = e.target.dataset.lead;
    const chucvu = e.target.dataset.chucvu;
    const email = e.target.dataset.email;
    const sdt = e.target.dataset.sdt;

    Swal.fire({
      title: ten,
      html: `
        <p><strong><?= t('cocau.faculty_lead') ?>:</strong> ${lead}</p>
        <p><strong><?= t('cocau.position') ?>:</strong> ${chucvu}</p>
        <p><strong><?= t('cocau.department_email') ?>:</strong> ${email}</p>
        <p><strong><?= t('cocau.department_phone') ?>:</strong> ${sdt}</p>
      `,
      icon: 'info',
      confirmButtonText: <?= json_encode(t('cocau.close')) ?>
    });
  }
});
</script>

<?php
include "../../layout/footer.php";
?>
