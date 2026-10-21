<?php
if (session_status() === PHP_SESSION_NONE) session_start();
@include_once __DIR__ . '/../config/i18n.php';
@include_once __DIR__ . '/../config/db.php';

$user = $_SESSION['username'] ?? '';
$newCount = 0;
$latestNoti = null;

if (isset($conn) && $user) {
  $r = $conn->query("SELECT COUNT(*) AS c FROM notifications WHERE nguoiNhan='$user' AND daXem=0");
  $newCount = $r ? (int)($r->fetch_assoc()['c'] ?? 0) : 0;
  $latestNoti = $conn->query("SELECT * FROM notifications WHERE nguoiNhan='$user' ORDER BY thoiGian DESC LIMIT 5");
}

$theme = $_SESSION['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="<?php echo current_lang(); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo t('common.title_admin'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    body{font-family:"Poppins",sans-serif;background-color:#f8f9fc;margin:0;overflow-x:hidden;display:flex}
    .sidebar{width:80px;background:linear-gradient(180deg,#0d6efd 0%,#4dabf7 100%);color:#fff;height:100vh;position:fixed;padding-top:20px;box-shadow:2px 0 10px rgba(0,0,0,.1);transition:width .3s ease;z-index:100;overflow:hidden}
    .sidebar.expanded{width:280px}
    .sidebar h4{text-align:center;margin-bottom:30px;font-weight:600;opacity:0;transition:opacity .3s}
    .sidebar.expanded h4{opacity:1}
    .sidebar a{color:#fff;text-decoration:none;display:flex;align-items:center;gap:10px;padding:10px 20px;border-radius:8px;margin:4px 8px;transition:background .3s,padding .3s;white-space:nowrap}
    .sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,.25)}
    .sidebar a span{display:none;transition:opacity .2s}
    .sidebar.expanded a span{display:inline;opacity:1}
    .content{margin-left:80px;padding:20px;width:100%;transition:margin-left .3s ease}
    .content.expanded{margin-left:280px}
    .navbar-custom{background:#fff;border-bottom:1px solid #dee2e6;box-shadow:0 2px 4px rgba(0,0,0,.05)}
    .toggle-btn{border:none;background:none;font-size:22px;color:#0d6efd;cursor:pointer}
    @media (max-width:768px){
      .sidebar{position:absolute;width:280px;height:100%;transform:translateX(-100%)}
      .sidebar.active{transform:translateX(0)}
      .content{margin-left:0!important}
    }
    .card-title{color:#ff6600;font-weight:700;font-size:1.2rem;text-transform:uppercase;margin-bottom:10px}
    .content{margin-left:80px;margin-right:80px;padding:20px;transition:margin-left .3s ease,margin-right .3s ease}
    @media (max-width:768px){.content{margin:0 15px;padding:15px}}
    .org-unit{transition:all .2s ease-in-out}
    .org-unit:hover{transform:scale(1.03);box-shadow:0 4px 10px rgba(0,0,0,.15)}
    .org-root{border-bottom:1px dashed #ccc;padding-bottom:10px}

    .dropdown-menu .dropdown-item.fw-semibold:hover{background-color:#eef4ff}
    .nav-user{display:flex;align-items:center;gap:.5rem}
    .theme-dark{background-color:#0f172a;color:#e2e8f0}
    .theme-dark .navbar-custom{background:#0b1220;border-color:#1f2937;color:#e2e8f0}
    .theme-dark .card{background:#111827;color:#e2e8f0}
    .theme-dark .table{color:#e2e8f0}
    .theme-dark .table-primary{--bs-table-bg:#1f2937;--bs-table-color:#e2e8f0}
    .theme-dark .text-muted{color:#94a3b8 !important}
    
  </style>
</head>
<body class="theme-<?php echo htmlspecialchars($theme); ?>">

<!-- <nav class="navbar navbar-custom w-100 px-3 d-flex justify-content-between align-items-center" style="position:fixed; left:80px; right:0; top:0; z-index:90;">
  <div class="d-flex align-items-center gap-2">
    <h5 class="m-0 fw-semibold text-primary">Hệ thống quản lý </h5>
  </div>

  <div class="d-flex align-items-center">
    <div class="dropdown me-3">
      <button class="btn btn-light position-relative" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-bell fs-5"></i>
        <?php if ($newCount > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $newCount ?></span>
        <?php endif; ?>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-lg p-2" aria-labelledby="notifDropdown" style="min-width:340px;max-height:380px;overflow-y:auto;">
        <li class="dropdown-header fw-semibold text-primary">🔔 Thông báo gần đây</li>
        <li><hr class="dropdown-divider"></li>
        <?php if ($latestNoti && $latestNoti->num_rows>0): ?>
          <?php while ($n = $latestNoti->fetch_assoc()): ?>
            <li class="mb-2">
              <a href="<?= htmlspecialchars($n['lienKet'] ?? '#') ?>" class="dropdown-item small <?= $n['daXem'] ? 'text-muted' : 'fw-semibold' ?>">
                <div class="d-flex flex-column">
                  <span><?= htmlspecialchars($n['tieuDe']) ?></span>
                  <span class="text-secondary small"><?= htmlspecialchars($n['noiDung']) ?></span>
                  <span class="text-secondary small fst-italic"><?= date('d/m/Y H:i', strtotime($n['thoiGian'])) ?></span>
                </div>
              </a>
            </li>
          <?php endwhile; ?>
        <?php else: ?>
          <li><p class="text-center text-muted small mb-0">Không có thông báo nào.</p></li>
        <?php endif; ?>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a href="/KTPM2/KTPM/modules/notifications/index.php" class="dropdown-item text-center text-primary fw-semibold">Xem tất cả thông báo</a>
        </li>
      </ul>
    </div>

    <?php if ($user): ?>
      <div class="nav-user">
        <i class="bi bi-person-circle text-primary fs-5"></i>
        <span class="fw-semibold text-dark"><?= htmlspecialchars($user) ?></span>
      </div>
    <?php endif; ?>
  </div>
</nav> -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('shown.bs.dropdown', function(e){
  if(e.target && e.target.id === 'notifDropdown'){
    fetch('/KTPM2/KTPM/modules/notifications/mark_read.php')
      .then(r=>r.json())
      .then(_=>{
        const badge = document.querySelector('#notifDropdown .badge');
        if(badge) badge.remove();
      });
  }
});
setInterval(()=>{
  fetch('/KTPM2/KTPM/modules/notifications/count_new.php')
    .then(res=>res.json())
    .then(data=>{
      let badge = document.querySelector('#notifDropdown .badge');
      if((data.count||0)>0){
        if(!badge){
          badge = document.createElement('span');
          badge.className='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
          badge.textContent=data.count;
          document.getElementById('notifDropdown').appendChild(badge);
        }else{
          badge.textContent=data.count;
        }
      }else{
        if(badge) badge.remove();
      }
    });
},60000);
</script>

