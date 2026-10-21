<?php
include "../../config/db.php";
include "../../config/i18n.php";
load_lang('programs');
include "../../layout/header_sv.php";
include "../../layout/sidebar_sv.php";
?>

<div class="main-content">
  <div class="container-fluid mt-4">
    <h4 class="fw-semibold mb-3 text-primary">
      <i class="bi bi-mortarboard"></i> <?php echo t('programs.title'); ?>
    </h4>

    <!-- Bộ lọc -->
    <form class="row g-3 mb-4">
      <div class="col-md-4">
        <select class="form-select">
          <option selected><?php echo t('programs.filter_major'); ?></option>
        </select>
      </div>
      <div class="col-md-4">
        <select class="form-select">
          <option selected><?php echo t('programs.filter_cohort'); ?></option>
        </select>
      </div>
      <div class="col-md-4">
        <select class="form-select">
          <option selected><?php echo t('programs.filter_level'); ?></option>
        </select>
      </div>
    </form>

    <!-- Thông tin tổng quan -->
    <div class="card mb-4 border-0 shadow-sm">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p><b><?php echo t('programs.major'); ?>:</b> <?php echo t('programs.sample_major'); ?></p>
            <p><b><?php echo t('programs.major_code'); ?>:</b> 7480103</p>
            <p><b><?php echo t('programs.duration'); ?>:</b> <?php echo t('programs.sample_duration'); ?></p>
          </div>
          <div class="col-md-6">
            <p><b><?php echo t('programs.total_credits'); ?>:</b>
              <?php
                $sum = $conn->query("SELECT SUM(soTinChi) AS total FROM courses");
                $r = $sum->fetch_assoc();
                echo "<b>" . $r['total'] . "</b>";
              ?>
            </p>
            <p><b><?php echo t('programs.level'); ?>:</b> <?php echo t('programs.sample_level'); ?></p>
            <p><b><?php echo t('programs.academic_year'); ?>:</b> <?php echo t('programs.sample_year'); ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Accordion hiển thị CTĐT -->
    <div class="accordion" id="curriculumAccordion">
      <?php
      $khoiQuery = "SELECT DISTINCT KhoiKienThuc FROM courses ORDER BY KhoiKienThuc";
      $khoiList = $conn->query($khoiQuery);
      $i = 1;

      if ($khoiList && $khoiList->num_rows > 0) {
        while ($khoi = $khoiList->fetch_assoc()) {
          $tenKhoi = $khoi['KhoiKienThuc'];
          $tenKhoiDisplay = t_data('course_blocks', (string)$tenKhoi, 'KhoiKienThuc', (string)$tenKhoi);

          // Lấy học phần của từng khối
          $hpQuery = $conn->prepare("SELECT * FROM courses WHERE KhoiKienThuc = ? ORDER BY maHP ASC");
          $hpQuery->bind_param("s", $tenKhoi);
          $hpQuery->execute();
          $hpResult = $hpQuery->get_result();

          // Tính tổng tín chỉ, bắt buộc, tự chọn
          $tongTinChi = $batBuoc = $tuChon = $tongHP = 0;
          $coursesData = [];
          while ($hp = $hpResult->fetch_assoc()) {
            $tongHP++;
            $tongTinChi += $hp['soTinChi'];
            if (trim(mb_strtolower($hp['ghiChu'])) === 'bắt buộc') $batBuoc += $hp['soTinChi'];
            if (trim(mb_strtolower($hp['ghiChu'])) === 'tự chọn') $tuChon += $hp['soTinChi'];
            $coursesData[] = $hp;
          }

          echo "
          <div class='accordion-item mb-2'>
            <h2 class='accordion-header' id='heading$i'>
              <button class='accordion-button collapsed toggle-accordion d-flex justify-content-between align-items-center' 
                type='button' data-target='#collapse$i'>
                <span>$tenKhoiDisplay</span>
                <i class='bi bi-chevron-right rotate-icon'></i>
              </button>
            </h2>
            <div id='collapse$i' class='accordion-collapse collapse'>
              <div class='accordion-body'>
                <div class='alert alert-info py-2 mb-3'>
                  <b>📘 " . t('programs.block_summary') . " $tenKhoiDisplay:</b><br>
                  - " . t('programs.total_courses') . ": <b>$tongHP</b><br>
                  - " . t('programs.total_credits_label') . ": <b>$tongTinChi</b><br>
                  - " . t('programs.required') . ": <b>$batBuoc</b> " . t('programs.credits') . " &nbsp; | &nbsp; " . t('programs.elective') . ": <b>$tuChon</b> " . t('programs.credits') . "
                </div>

                <table class='table table-striped text-center align-middle table-bordered'>
                  <thead class='table-primary'>
                    <tr>
                      <th><?php echo t('programs.course_code'); ?></th>
                      <th><?php echo t('programs.course_name'); ?></th>
                      <th><?php echo t('programs.credits'); ?></th>
                      <th><?php echo t('programs.theory'); ?></th>
                      <th><?php echo t('programs.practice'); ?></th>
                      <th><?php echo t('programs.faculty'); ?></th>
                      <th><?php echo t('programs.prereq'); ?></th>
                      <th><?php echo t('programs.prior'); ?></th>
                      <th><?php echo t('programs.note'); ?></th>
                    </tr>
                  </thead>
                  <tbody>";
          
          if (!empty($coursesData)) {
            foreach ($coursesData as $hp) {
              $courseName = t_data('courses', (string)$hp['maHP'], 'tenHP', (string)$hp['tenHP']);
              $courseNote = t_data('courses', (string)$hp['maHP'], 'ghiChu', (string)$hp['ghiChu']);
              echo "
              <tr>
                <td>{$hp['maHP']}</td>
                <td>{$courseName}</td>
                <td>{$hp['soTinChi']}</td>
                <td>{$hp['lyThuyet']}</td>
                <td>{$hp['thucHanh']}</td>
                <td>" . t_data('faculties', (string)$hp['khoaQuanLy'], 'tenKhoa', (string)$hp['khoaQuanLy']) . "</td>
                <td>" . ($hp['hpTienQuyet'] ?: '-') . "</td>
                <td>" . ($hp['hpHocTruoc'] ?: '-') . "</td>
                <td>{$courseNote}</td>
              </tr>";
            }
          } else {
            echo "<tr><td colspan='9' class='text-muted'>" . t('programs.no_courses') . "</td></tr>";
          }

          echo "</tbody></table></div></div></div>";
          $i++;
        }
      } else {
        echo "<p class='text-muted'>" . t('programs.no_programs') . "</p>";
      }
      ?>
    </div>

    <div class="text-end mt-4">
      <!-- <button class="btn btn-outline-primary"><i class="bi bi-file-earmark-pdf"></i> Xuất PDF</button>
      <button class="btn btn-outline-success"><i class="bi bi-printer"></i> In CTĐT</button> -->
    </div>
  </div>
</div>

<style>
  .rotate-icon {
    transition: transform 0.3s ease;
    font-size: 1rem;
  }
  .rotate-icon.rotated {
    transform: rotate(90deg);
  }
  .accordion-button {
    background-color: #e7f1ff;
    color: #0d6efd;
    font-weight: 500;
  }
  .accordion-button:not(.collapsed) {
    background-color: #cfe2ff;
    color: #0a58ca;
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.toggle-accordion').forEach(btn => {
  btn.addEventListener('click', function () {
    const targetSelector = this.getAttribute('data-target');
    const target = document.querySelector(targetSelector);
    let bsCollapse = bootstrap.Collapse.getInstance(target);

    if (!bsCollapse) {
      bsCollapse = new bootstrap.Collapse(target, { toggle: false });
    }

    const icon = this.querySelector('.rotate-icon');

    if (target.classList.contains('show')) {
      bsCollapse.hide();
      this.classList.add('collapsed');
      if (icon) icon.classList.remove('rotated');
    } else {
      bsCollapse.show();
      this.classList.remove('collapsed');
      if (icon) icon.classList.add('rotated');
    }
  });
});
</script>

<?php include "../../layout/footer_sv.php"; ?>
