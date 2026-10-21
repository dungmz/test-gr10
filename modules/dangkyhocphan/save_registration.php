<?php
include "../../config/db.php";
session_start();

$student_id = $_SESSION['username'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['hocphan'])) {
  foreach ($_POST['hocphan'] as $maHP) {
    $maHP = mysqli_real_escape_string($conn, $maHP);
    $exists = $conn->query("SELECT * FROM registrations WHERE maSV='$student_id' AND maHP='$maHP'");
    if ($exists->num_rows === 0) {
      $conn->query("INSERT INTO registrations (maSV, maHP, ngayDK) VALUES ('$student_id', '$maHP', NOW())");
    }
  }
}

header("Location: index_sv.php");
exit;
?>
