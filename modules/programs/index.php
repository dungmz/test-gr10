<?php
include "../../config/db.php";
include "../../config/i18n.php";
load_lang('programs');
include "../../layout/header.php";
include "../../layout/sidebar.php";
?>

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

<div class="container-fluid mt-4">
  <h4 class="fw-semibold mb-3 text-primary">
    <i class="bi bi-mortarboard"></i> <?php echo t('programs.title'); ?>
  </h4>

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

  <div class="card mb-4 border-0 shadow-sm position-relative">
    <div class="card-body">
      <div class="d-flex justify-content-end">
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tuitionModal">
          <i class="bi bi-cash-coin"></i> <?php echo t('programs.change_tuition'); ?>
        </button>
      </div>
      <div class="row mt-2">
        <div class="col-md-6">
          <p><b><?php echo t('programs.major'); ?>:</b> <?php echo t('programs.sample_major'); ?></p>
          <p><b><?php echo t('programs.major_code'); ?>:</b> 7480103</p>
          <p><b><?php echo t('programs.duration'); ?>:</b> <?php echo t('programs.sample_duration'); ?></p>
          <p>
            <b><?php echo t('programs.tuition'); ?>:</b>
            <?php
              $hp = $conn->query("SELECT value FROM settings WHERE name='hocphi'")->fetch_assoc();
              $hocphi = $hp['value'] ?? 1200000; // mặc định nếu chưa có
              echo "<b>" . number_format($hocphi) . " " . t('programs.tuition_unit') . "</b>";
            ?>
          </p>
        </div>
        <div class="col-md-6">
          <p><b><?php echo t('programs.total_credits'); ?>:</b>
            <?php
              $sum = $conn->query("SELECT SUM(soTinChi) AS total FROM courses");
              $r = $sum->fetch_assoc();
              echo "<b>" . ($r['total'] ?? 0) . "</b>";
            ?>
          </p>
          <p><b><?php echo t('programs.level'); ?>:</b> <?php echo t('programs.sample_level'); ?></p>
          <p><b><?php echo t('programs.academic_year'); ?>:</b> <?php echo t('programs.sample_year'); ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="tuitionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST" action="update_tuition.php">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title"><i class="bi bi-cash-stack"></i> <?php echo t('programs.tuition_update_title'); ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label class="form-label"><?php echo t('programs.tuition_per_credit'); ?></label>
            <input type="number" class="form-control" name="hocphi" min="0" step="1000" required>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> <?php echo t('programs.save'); ?></button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo t('programs.cancel'); ?></button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

      <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
          <i class="bi bi-plus-circle"></i> <?php echo t('programs.add_course'); ?>
        </button>
      </div>

      <div class="accordion" id="curriculumAccordion">
        <?php
        $khoiQuery = "SELECT DISTINCT KhoiKienThuc FROM courses ORDER BY KhoiKienThuc";
        $khoiList = $conn->query($khoiQuery);
        $i = 1;

        if ($khoiList && $khoiList->num_rows > 0) {
          while ($khoi = $khoiList->fetch_assoc()) {
            $tenKhoi = $khoi['KhoiKienThuc'];
            $tenKhoiDisplay = t_data('course_blocks', (string)$tenKhoi, 'KhoiKienThuc', (string)$tenKhoi);
            $hpQuery = $conn->prepare("SELECT * FROM courses WHERE KhoiKienThuc = ? ORDER BY maHP ASC");
            $hpQuery->bind_param("s", $tenKhoi);
            $hpQuery->execute();
            $hpResult = $hpQuery->get_result();

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
                        <th><?php echo t('programs.actions'); ?></th>
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
                  <td>
                    <button class='btn btn-warning btn-sm me-1' data-bs-toggle='modal' data-bs-target='#editModal{$hp['maHP']}'>
                      <i class='bi bi-pencil-square'></i>
                    </button>
                    <button onclick=\"confirmDelete('delete.php?maHP=" . urlencode($hp['maHP']) . "')\" class='btn btn-danger btn-sm'>
                      <i class='bi bi-trash'></i>
                    </button>
                  </td>
                </tr>";
              }
            } else {
              echo "<tr><td colspan='10' class='text-muted'>" . t('programs.no_courses') . "</td></tr>";
            }
            echo "</tbody></table></div></div></div>";
            $i++;
          }
        } else {
          echo "<p class='text-muted'>" . t('programs.no_programs') . "</p>";
        }
        ?>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

function confirmDelete(url) {
  Swal.fire({
    title: <?php echo json_encode(t('common.confirm_delete_title')); ?>,
    text: <?php echo json_encode(t('common.confirm_delete_text')); ?>,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: <?php echo json_encode(t('common.confirm_delete_button')); ?>,
    cancelButtonText: <?php echo json_encode(t('common.cancel')); ?>
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = url;
    }
  });
}
</script>

<?php include "../../layout/footer.php"; ?>
