<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'sinhvien') {
  header("Location: login.php");
  exit;
}

include "layout/header_sv.php";
include "layout/sidebar_sv.php";
load_lang('dashboard');
?>

<div class="main-content">
  <div class="container-fluid mt-4">
    <h4 class="fw-semibold text-success">
      <i class="bi bi-speedometer2"></i> <?= t('dashboard.student_title') ?>
    </h4>

    <div class="alert alert-success mt-3 border-0 shadow-sm rounded-3">
      <i class="bi bi-person-circle"></i>
      <?= t('common.hello') ?>
      <?= t('dashboard.student_intro') ?>
    </div>

    <div class="row mt-4">
      <div class="col-md-4 mb-3">
        <div class="card text-center shadow-sm border-0">
          <div class="card-body">
            <i class="bi bi-building text-primary" style="font-size:2.5rem;"></i>
            <h5 class="mt-2"><?= t('common.schools') ?></h5>
            <a href="modules/schools/index_sv.php" class="btn btn-outline-primary btn-sm mt-2"><?= t('common.view') ?></a>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="card text-center shadow-sm border-0">
          <div class="card-body">
            <i class="bi bi-diagram-3 text-success" style="font-size:2.5rem;"></i>
            <h5 class="mt-2"><?= t('common.faculties') ?></h5>
            <a href="modules/faculties/index_sv.php" class="btn btn-outline-success btn-sm mt-2"><?= t('common.view') ?></a>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="card text-center shadow-sm border-0">
          <div class="card-body">
            <i class="bi bi-layers text-warning" style="font-size:2.5rem;"></i>
            <h5 class="mt-2"><?= t('common.majors') ?></h5>
            <a href="modules/majors/index_sv.php" class="btn btn-outline-warning btn-sm mt-2"><?= t('common.view') ?></a>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="card text-center shadow-sm border-0">
          <div class="card-body">
            <i class="bi bi-journal-bookmark text-info" style="font-size:2.5rem;"></i>
            <h5 class="mt-2"><?= t('common.training_programs') ?></h5>
            <a href="modules/programs/index_sv.php" class="btn btn-outline-info btn-sm mt-2"><?= t('common.view') ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "layout/footer_sv.php"; ?>
