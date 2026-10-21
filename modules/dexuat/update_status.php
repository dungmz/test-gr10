<?php
include "../../config/db.php";
include "../../config/i18n.php";
load_lang('dexuat');
header("Content-Type: application/json; charset=utf-8");
session_start();

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$action = trim($_POST['action'] ?? '');
$lydo = trim($_POST['lydo'] ?? '');
$nguoiDuyet = $_SESSION['username'] ?? 'admin';

if (!$id || !$action) {
  echo json_encode(["status" => "error", "message" => t('dexuat.missing_params')]);
  exit;
}

$lydo_safe = mysqli_real_escape_string($conn, $lydo);
$nguoiDuyet_safe = mysqli_real_escape_string($conn, $nguoiDuyet);

switch ($action) {
  case "accept":
    $trangThai = "Đã duyệt";
    $hanhDong = "Chấp nhận";
    $query = "UPDATE de_xuat SET trangThai='$trangThai', ngayDuyet=NOW(), nguoiDuyet='$nguoiDuyet_safe', lyDoTuChoi=NULL WHERE maDX='$id'";
    $msg = t('dexuat.accepted_msg');
    break;
  case "reject":
    if ($lydo_safe == "") {
      echo json_encode(["status" => "warning", "message" => t('dexuat.missing_input')]);
      exit;
    }
    $trangThai = "Từ chối";
    $hanhDong = "Từ chối";
    $query = "UPDATE de_xuat SET trangThai='$trangThai', lyDoTuChoi='$lydo_safe', ngayDuyet=NOW(), nguoiDuyet='$nguoiDuyet_safe' WHERE maDX='$id'";
    $msg = t('dexuat.rejected_msg');
    break;
  case "edit":
    if ($lydo_safe == "") {
      echo json_encode(["status" => "warning", "message" => t('dexuat.missing_input')]);
      exit;
    }
    $trangThai = "Yêu cầu chỉnh sửa";
    $hanhDong = "Yêu cầu chỉnh sửa";
    $query = "UPDATE de_xuat SET trangThai='$trangThai', lyDoTuChoi='$lydo_safe', ngayDuyet=NOW(), nguoiDuyet='$nguoiDuyet_safe' WHERE maDX='$id'";
    $msg = t('dexuat.edit_msg');
    break;
  default:
    echo json_encode(["status" => "error", "message" => t('dexuat.invalid_action')]);
    exit;
}

if ($conn->query($query)) {
  $conn->query("INSERT INTO de_xuat_log (maDX, hanhDong, nguoiThucHien, noiDung) VALUES ('$id', '$hanhDong', '$nguoiDuyet_safe', '$lydo_safe')");
  $info = $conn->query("SELECT nguoiGui, tieuDe FROM de_xuat WHERE maDX='$id'")->fetch_assoc();
  $nguoiGui = $info['nguoiGui'];
  $tieuDe = $info['tieuDe'];
  $title = t_data('dexuat', (string)$id, 'tieuDe', (string)$tieuDe);
  $template = match($action) {
    'accept' => t('dexuat.notify_accept'),
    'reject' => t('dexuat.notify_reject'),
    default => t('dexuat.notify_edit')
  };
  $noiDungThongBao = str_replace('{title}', $title, $template);
  $thongBaoTitle = t('dexuat.notify_title');
  $conn->query("INSERT INTO notifications (nguoiNhan, tieuDe, noiDung, lienKet) VALUES ('$nguoiGui', '$thongBaoTitle', '$noiDungThongBao', '../dexuat_nhanvien/my_proposals.php')");
  echo json_encode(["status" => "success", "message" => $msg]);
} else {
  echo json_encode(["status" => "error", "message" => t('dexuat.update_failed')]);
}
