<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'giangvien') {
    header("Location:login.php");
    exit;
}
include "layout/header_gv.php";
include "layout/sidebar_gv.php";
load_lang('dashboard');
?>

<div class="content">
  <h4 class="fw-semibold text-primary">
    <i class="bi bi-speedometer2"></i> <?= t('dashboard.lecturer_title') ?>
  </h4>

  <div class="alert alert-info mt-3 border-0 shadow-sm rounded-3">
    <i class="bi bi-person-video3"></i>
    <?= t('common.hello') ?>
  </div>

  <div class="row mt-4">
    <div class="col-md-4 mb-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <i class="bi bi-building text-primary" style="font-size:2.5rem;"></i>
          <h5 class="mt-2"><?= t('common.schools') ?></h5>
          <p class="text-muted small"><?= t('dashboard.schools_desc') ?></p>
          <a href="modules/schools/index_gv.php" class="btn btn-outline-primary btn-sm mt-2">
            <?= t('common.view_details') ?>
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <i class="bi bi-diagram-3 text-success" style="font-size:2.5rem;"></i>
          <h5 class="mt-2"><?= t('common.faculties') ?></h5>
          <p class="text-muted small"><?= t('dashboard.faculties_desc') ?></p>
          <a href="modules/faculties/index_gv.php" class="btn btn-outline-success btn-sm mt-2">
            <?= t('common.view_details') ?>
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <i class="bi bi-journal-bookmark text-warning" style="font-size:2.5rem;"></i>
          <h5 class="mt-2"><?= t('common.majors') ?></h5>
          <p class="text-muted small"><?= t('dashboard.majors_desc') ?></p>
          <a href="modules/majors/index_gv.php" class="btn btn-outline-warning btn-sm mt-2">
            <?= t('common.view_details') ?>
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <i class="bi bi-mortarboard text-info" style="font-size:2.5rem;"></i>
          <h5 class="mt-2"><?= t('common.training_programs') ?></h5>
          <p class="text-muted small"><?= t('dashboard.programs_desc') ?></p>
          <a href="modules/programs/index_gv.php" class="btn btn-outline-info btn-sm mt-2">
            <?= t('common.view_details') ?>
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <i class="bi bi-people text-danger" style="font-size:2.5rem;"></i>
          <h5 class="mt-2"><?= t('common.org_structure') ?></h5>
          <p class="text-muted small"><?= t('dashboard.org_desc') ?></p>
          <a href="modules/cocau/index_gv.php" class="btn btn-outline-danger btn-sm mt-2">
            <?= t('common.view_details') ?>
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <i class="bi bi-lightbulb text-primary" style="font-size:2.5rem;"></i>
          <h5 class="mt-2"><?= t('common.my_proposals') ?></h5>
          <p class="text-muted small"><?= t('dashboard.my_proposals_desc') ?></p>
          <a href="modules/dexuat_nhanvien/my_proposals.php" class="btn btn-outline-primary btn-sm mt-2">
            <?= t('common.view_details') ?>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "layout/footer_gv.php"; ?>
