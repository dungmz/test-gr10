<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];

switch ($role) {
    case 'admin':
        header("Location: dashboard_admin.php");
        break;
    case 'teacher':
        header("Location: dashboard_teacher.php");
        break;
    case 'student':
        header("Location: dashboard_student.php");
        break;
    default:
        echo "❌ Vai trò người dùng không hợp lệ!";
        session_destroy();
        break;
}
exit;
?>
