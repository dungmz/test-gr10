<?php
if (session_status() === PHP_SESSION_NONE) session_start();
@include_once __DIR__ . '/../config/i18n.php';
$theme = $_SESSION['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="<?php echo current_lang(); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo t('common.title_student_system'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f8f9fc;
      margin: 0;
      overflow-x: hidden;
    }

    .navbar-custom {
      background-color: white;
      border-bottom: 1px solid #dee2e6;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 60px;
      z-index: 100;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 1rem;
    }

    .navbar-brand {
      color: #0d6efd;
      font-weight: 600;
      font-size: 1.2rem;
      text-decoration: none;
    }

    .user-info {
      font-weight: 500;
      color: #0d6efd;
    }

    .logout-btn {
      text-decoration: none;
      color: #dc3545;
      font-weight: 500;
      transition: 0.2s;
    }

    .logout-btn:hover {
      color: #b02a37;
      text-decoration: underline;
    }

    .main-content {
      margin-left: 80px;
      margin-top: 70px;
      padding: 20px;
      transition: all 0.3s ease;
    }

    .main-content.expanded {
      margin-left: 280px;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0 !important;
      }
      .content {
        margin-left: 0 !important;
      }
      .sidebar {
        position: absolute;
        width: 280px;
        height: 100%;
        transform: translateX(-100%);
      }
      .sidebar.active {
        transform: translateX(0);
      }
    }
    .sidebar {
  width: 80px;
  background: linear-gradient(180deg, #0d6efd 0%, #4dabf7 100%);
  color: white;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  padding-top: 70px;
  box-shadow: 2px 0 10px rgba(0,0,0,0.1);
  transition: width 0.3s ease;
  overflow: hidden;
  z-index: 99;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.sidebar.expanded {
  width: 280px;
}

.sidebar a {
  color: white;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 20px;
  border-radius: 8px;
  margin: 4px 8px;
  transition: background 0.3s, padding 0.3s;
  white-space: nowrap;
}

.sidebar a:hover,
.sidebar a.active {
  background: rgba(255, 255, 255, 0.25);
}

.sidebar:not(.expanded) a span {
  display: none;
}

.sidebar i {
  font-size: 1.2rem;
  min-width: 24px;
  text-align: center;
}

.sidebar-bottom {
  border-top: 1px solid rgba(255,255,255,0.3);
  padding-top: 10px;
  margin-bottom: 10px;
}
.content {
  margin-left: 80px;
  padding: 20px;
  transition: margin-left 0.3s ease;
}

.content.expanded {
  margin-left: 280px;
}

.toggle-btn{
  border:none;
  background:none;
  color:#fff;
  font-size:20px;
  margin:0 12px 12px;
  cursor:pointer;
}

.theme-dark{background-color:#0f172a;color:#e2e8f0}
.theme-dark .navbar-custom{background:#0b1220;border-color:#1f2937;color:#e2e8f0}
.theme-dark .card{background:#111827;color:#e2e8f0}
.theme-dark .table{color:#e2e8f0}
.theme-dark .table-primary{--bs-table-bg:#1f2937;--bs-table-color:#e2e8f0}
.theme-dark .text-muted{color:#94a3b8 !important}

.table {
  border-radius: 8px;
  overflow: hidden;
}
.card {
  border-radius: 12px;
}
  </style>
  </head>
  <body class="theme-<?php echo htmlspecialchars($theme); ?>">
    
