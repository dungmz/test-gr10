<?php
include "../../config/db.php";
include "../../config/i18n.php";
load_lang('phongban');

$maPB = trim($_POST['maPB'] ?? '');
$tenPB = trim($_POST['tenPB'] ?? '');
$diaChi = trim($_POST['diaChi'] ?? '');
$email = trim($_POST['email'] ?? '');
$soDienThoai = trim($_POST['soDienThoai'] ?? '');

if ($maPB === '' || $tenPB === '') {
  echo json_encode(['status' => 'error', 'message' => t('phongban.missing_required')]);
  exit;
}

$stmt = $conn->prepare("UPDATE phongban SET tenPB = ?, diaChi = ?, email = ?, soDienThoai = ? WHERE maPB = ?");
$stmt->bind_param("sssss", $tenPB, $diaChi, $email, $soDienThoai, $maPB);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => t('phongban.update_error')]);
}
?>
