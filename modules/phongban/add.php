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

$stmt = $conn->prepare("SELECT * FROM phongban WHERE maPB = ?");
$stmt->bind_param("s", $maPB);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  echo json_encode(['status' => 'error', 'message' => t('phongban.code_exists')]);
  exit;
}

$stmt = $conn->prepare("INSERT INTO phongban (maPB, tenPB, diaChi, email, soDienThoai) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $maPB, $tenPB, $diaChi, $email, $soDienThoai);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => t('phongban.add_error')]);
}
?>
