<?php
include "../../config/db.php";
header("Content-Type: application/json");

$maSV = $_POST['maSV'] ?? '';
$maHP = $_POST['maHP'] ?? '';

if (!$maSV || !$maHP) {
  echo json_encode(["status" => "error", "message" => "Thiếu thông tin."]);
  exit;
}

$query = "UPDATE registrations 
          SET trangThai='Đã thanh toán', ngayThanhToan=NOW() 
          WHERE maSV='$maSV' AND maHP='$maHP'";

if ($conn->query($query)) {
  echo json_encode(["status" => "success", "message" => "Đã xác nhận thanh toán."]);
} else {
  echo json_encode(["status" => "error", "message" => "Lỗi cơ sở dữ liệu."]);
}
?>
