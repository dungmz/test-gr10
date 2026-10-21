<?php
include "../../config/db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $hocphi = intval($_POST['hocphi'] ?? 0);
  if ($hocphi > 0) {
    $check = $conn->query("SELECT * FROM settings WHERE name='hocphi'");
    if ($check->num_rows > 0) {
      $conn->query("UPDATE settings SET value='$hocphi' WHERE name='hocphi'");
    } else {
      $conn->query("INSERT INTO settings (name, value) VALUES ('hocphi', '$hocphi')");
    }
  }
}

header("Location: index.php");
exit;
?>
