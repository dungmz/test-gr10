<?php
include "config/db.php";
include "layout/header.php";
include "layout/sidebar.php";
load_lang('dashboard');

$schools    = $conn->query("SELECT COUNT(*) AS total FROM schools")->fetch_assoc()['total'] ?? 0;
$faculties  = $conn->query("SELECT COUNT(*) AS total FROM faculties")->fetch_assoc()['total'] ?? 0;
$majors     = $conn->query("SELECT COUNT(*) AS total FROM majors")->fetch_assoc()['total'] ?? 0;
$employees  = $conn->query("SELECT COUNT(*) AS total FROM employees")->fetch_assoc()['total'] ?? 0;
$programs   = $conn->query("SELECT COUNT(*) AS total FROM programs")->fetch_assoc()['total'] ?? 0;
$departments = $conn->query("SELECT COUNT(*) AS total FROM phongban")->fetch_assoc()['total'] ?? 0;
$pendingDX   = $conn->query("SELECT COUNT(*) AS total FROM de_xuat WHERE trangThai='Chờ duyệt'")->fetch_assoc()['total'] ?? 0;
?>

<div class="container-fluid mt-4">
  <h4 class="fw-semibold mb-4"><i class="bi bi-speedometer2 text-primary"></i> <?= t('dashboard.admin_title') ?></h4>


  <div class="row g-4">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-4 bg-primary text-white">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-building"></i> <?= t('common.schools') ?></h5>
          <p class="display-6 fw-bold"><?= (int)$schools ?></p>
          <span></span>
        </div>
      </div>  
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-4 bg-info text-white">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-diagram-3"></i> <?= t('common.faculties') ?></h5>
          <p class="display-6 fw-bold"><?= (int)$faculties ?></p>
          <span></span>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-4 bg-success text-white">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-journal-text"></i> <?= t('common.majors') ?></h5>
          <p class="display-6 fw-bold"><?= (int)$majors ?></p>
          <span></span>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-4 bg-warning text-dark">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-people"></i> <?= t('common.staff') ?></h5>
          <p class="display-6 fw-bold"><?= (int)$employees ?></p>
          <span></span>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mt-3">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-4 bg-secondary text-white">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-book"></i> <?= t('common.training_programs') ?></h5>
          <p class="display-6 fw-bold"><?= (int)$programs ?></p>
          <span></span>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-4 bg-danger text-white">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-briefcase"></i> <?= t('common.departments') ?></h5>
          <p class="display-6 fw-bold"><?= (int)$departments ?></p>
          <span> </span>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-4 bg-dark text-white">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-check2-square"></i> <?= t('common.pending_proposals') ?></h5>
          <p class="display-6 fw-bold"><?= (int)$pendingDX ?></p>
          <span></span>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-4 p-4 bg-white rounded-4 shadow-sm">
    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-lightning-charge"></i> <?= t('dashboard.quick_shortcuts') ?></h5>
    <div class="d-flex flex-wrap gap-2">
      <a class="btn btn-primary" href="modules/phongban/index.php"><i class="bi bi-briefcase"></i> <?= t('dashboard.manage_departments') ?></a>
      <a class="btn btn-outline-primary" href="modules/dexuat/index.php"><i class="bi bi-check2-square"></i> <?= t('dashboard.review_proposals') ?></a>
      <a class="btn btn-outline-secondary" href="modules/cocau/index.php"><i class="bi bi-diagram-3"></i> <?= t('common.org_structure') ?></a>
    </div>
  </div>

  <div class="mt-4 p-4 bg-white rounded-4 shadow-sm">
    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle"></i> <?= t('dashboard.system_info') ?></h5>
    <p><?= t('dashboard.hello_name', ['name' => '<b>' . htmlspecialchars($_SESSION['username'] ?? 'Admin') . '</b>']) ?> 👋</p>
    <p><?= t('dashboard.welcome_system', ['system' => '<b>' . t('common.system_name') . '</b>']) ?></p>
    <p><?= t('dashboard.left_nav_hint') ?></p>
  </div>
</div>

<?php include "layout/footer.php"; ?>
