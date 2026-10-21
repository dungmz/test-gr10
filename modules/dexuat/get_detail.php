<?php
include "../../config/db.php";
include "../../config/i18n.php";
load_lang('dexuat');
header("Content-Type: application/json");

$id = $_GET['id'] ?? 0;
$res = $conn->query("SELECT * FROM de_xuat WHERE maDX='$id' LIMIT 1");

if ($res && $res->num_rows > 0) {
  $row = $res->fetch_assoc();
  $statusKey = match($row['trangThai']) {
    'Đã duyệt' => 'status_approved',
    'Từ chối' => 'status_rejected',
    'Yêu cầu chỉnh sửa' => 'status_edit',
    'Nháp' => 'status_draft',
    default => 'status_pending'
  };
  $row['trangThaiRaw'] = $row['trangThai'];
  $row['trangThaiLabel'] = t('dexuat.' . $statusKey);
  $row['tieuDeDisplay'] = t_data('dexuat', (string)$row['maDX'], 'tieuDe', (string)$row['tieuDe']);
  $row['loaiDeXuatDisplay'] = t_data('dexuat_types', (string)$row['maDX'], 'loaiDeXuat', (string)$row['loaiDeXuat']);
  $row['noiDungDisplay'] = t_data('dexuat_content', (string)$row['maDX'], 'noiDung', (string)$row['noiDung']);
  echo json_encode(["success" => true, "data" => $row]);
} else {
  echo json_encode(["success" => false]);
}
?>
