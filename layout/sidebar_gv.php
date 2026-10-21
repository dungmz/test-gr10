
<div class="sidebar" id="sidebar">
  <button class="toggle-btn" id="toggleSidebar" type="button">
    <i class="bi bi-list"></i>
  </button>
  <div class="sidebar-top">
    <a href="/KTPM2/KTPM/dashboard_gv.php" class="active">
      <i class="bi bi-speedometer2"></i> <span><?= t('common.dashboard') ?></span>
    </a>

    <a href="/KTPM2/KTPM/modules/schools/index_gv.php">
      <i class="bi bi-building"></i> <span><?= t('common.schools') ?></span>
    </a>

    <a href="/KTPM2/KTPM/modules/faculties/index_gv.php">
      <i class="bi bi-diagram-3"></i> <span><?= t('common.faculties') ?></span>
    </a>

    <a href="/KTPM2/KTPM/modules/majors/index_gv.php">
      <i class="bi bi-layers"></i> <span><?= t('common.majors') ?></span>
    </a>

    <a href="/KTPM2/KTPM/modules/programs/index_gv.php">
      <i class="bi bi-journal-bookmark"></i> <span><?= t('common.programs_abbrev') ?></span>
    </a>
    <a href="/KTPM2/KTPM/modules/cocau/index_gv.php">
  <i class="bi bi-diagram-3-fill"></i> <span><?= t('common.org_structure') ?></span>
</a>

  </div>
  <a href="/KTPM2/KTPM/modules/dexuat_nhanvien/index.php">
  <i class="bi bi-pencil-square"></i> <span><?= t('common.propose_structure_change') ?></span>
</a>
<a href="/KTPM2/KTPM/modules/dexuat_nhanvien/my_proposals.php">
  <i class="bi bi-journal-text"></i> <span><?= t('common.my_proposals') ?></span>
</a>

<a href="/KTPM2/KTPM/modules/settings/index_gv.php">
  <i class="bi bi-gear"></i> <span><?= t('common.settings') ?></span>
</a>

  <div class="sidebar-bottom">
    <a href="/KTPM2/KTPM/logout.php" class="text-danger">
      <i class="bi bi-box-arrow-right"></i> <span><?= t('common.sign_out') ?></span>
    </a>
  </div>
</div>

<script>
  const toggleBtn = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebar');
  const content = document.querySelector('.content');

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => {
      if (window.innerWidth <= 768) {
        sidebar.classList.toggle('active');
        return;
      }
      sidebar.classList.toggle('expanded');
      if (content) content.classList.toggle('expanded');
    });
  }
</script>

