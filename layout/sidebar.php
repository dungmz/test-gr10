<?php
$basePath = '/KTPM2/KTPM/';
?>

<div class="sidebar" id="sidebar">
  <h4><i class="bi bi-mortarboard-fill"></i><br><span><?= t('common.group_name') ?></span></h4>

  <a href="<?= $basePath ?>dashboard_admin.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard_admin.php'?'active':'' ?>">
    <i class="bi bi-house-door"></i> <span><?= t('common.home') ?></span>
  </a>

  <a href="<?= $basePath ?>modules/schools/index.php" class="<?= strpos($_SERVER['PHP_SELF'],'schools')!==false?'active':'' ?>">
    <i class="bi bi-building"></i> <span><?= t('common.schools') ?></span>
  </a>

  <a href="<?= $basePath ?>modules/faculties/index.php" class="<?= strpos($_SERVER['PHP_SELF'],'faculties')!==false?'active':'' ?>">
    <i class="bi bi-diagram-3"></i> <span><?= t('common.faculties') ?></span>
  </a>

  <a href="<?= $basePath ?>modules/majors/index.php" class="<?= strpos($_SERVER['PHP_SELF'],'majors')!==false?'active':'' ?>">
    <i class="bi bi-journal-text"></i> <span><?= t('common.majors') ?></span>
  </a>

  <a href="<?= $basePath ?>modules/phongban/index.php" class="<?= strpos($_SERVER['PHP_SELF'],'phongban')!==false?'active':'' ?>">
    <i class="bi bi-briefcase"></i> <span><?= t('common.departments') ?></span>
  </a>

  <a href="<?= $basePath ?>modules/dexuat/index.php" class="<?= strpos($_SERVER['PHP_SELF'],'dexuat')!==false?'active':'' ?>">
    <i class="bi bi-check2-square"></i> <span><?= t('common.approve_reject_proposals') ?></span>
  </a>

  <a href="<?= $basePath ?>modules/cocau/index.php" class="<?= strpos($_SERVER['PHP_SELF'],'cocau')!==false?'active':'' ?>">
    <i class="bi bi-diagram-3-fill"></i> <span><?= t('common.org_structure') ?></span>
  </a>

  <a href="<?= $basePath ?>modules/programs/index.php" class="<?= strpos($_SERVER['PHP_SELF'],'programs')!==false?'active':'' ?>">
    <i class="bi bi-book-half"></i> <span><?= t('common.programs_abbrev') ?></span>
  </a>

  <a href="<?= $basePath ?>modules/employees/index.php" class="<?= strpos($_SERVER['PHP_SELF'],'employees')!==false?'active':'' ?>">
    <i class="bi bi-person-badge"></i> <span><?= t('common.staff') ?></span>
  </a>
    <a href="/KTPM2/KTPM/modules/dangkyhocphan/admin_view.php">
    <i class="bi bi-clipboard-check"></i> <span><?= t('common.manage_course_regs') ?></span>
  </a>

  <a href="<?= $basePath ?>modules/settings/index.php" class="<?= strpos($_SERVER['PHP_SELF'],'/settings/')!==false?'active':'' ?>">
    <i class="bi bi-gear"></i> <span><?= t('common.settings') ?></span>
  </a>

  <hr style="border-color: rgba(255,255,255,0.3);">

  <a href="<?= $basePath ?>logout.php">
    <i class="bi bi-box-arrow-right"></i> <span><?= t('common.sign_out') ?></span>
  </a>
</div>

<div class="content" id="main-content">
  <nav class="navbar navbar-custom p-3 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <button class="toggle-btn" id="toggleSidebar"><i class="bi bi-list"></i></button>
      <h5 class="m-0 fw-semibold text-primary"><?= t('common.system_name') ?></h5>
    </div>

    <div class="d-flex align-items-center gap-2">
      <?php if(isset($_SESSION['username'])): ?>
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-person-circle text-primary fs-5"></i>
          <span class="fw-semibold text-dark"><?= htmlspecialchars($_SESSION['username']) ?></span>
        </div>
      <?php endif; ?>
    </div>
  </nav>

  <script>
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('main-content');
    toggleBtn.addEventListener('click', () => {
      if (window.innerWidth <= 768) {
        sidebar.classList.toggle('active');
        return;
      }
      sidebar.classList.toggle('expanded');
      content.classList.toggle('expanded');
    });
  </script>
