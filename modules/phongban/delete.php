<?php
include "../../config/db.php";
include "../../config/i18n.php";
load_lang('phongban');

$maPB = trim($_POST['maPB'] ?? '');

if ($maPB === '') {
  echo json_encode(['status' => 'error', 'message' => t('phongban.missing_required')]);
  exit;
}

$stmt = $conn->prepare("DELETE FROM phongban WHERE maPB = ?");
$stmt->bind_param("s", $maPB);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => t('phongban.delete_error')]);
}
?>
