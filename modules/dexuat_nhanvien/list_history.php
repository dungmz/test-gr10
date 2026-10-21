<?php
if (session_status()===PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['role']) || $_SESSION['role']!=='giangvien'){echo json_encode(['success'=>false,'data'=>[]]);exit;}
require_once '../../config/db.php';
$userName = $_SESSION['username'] ?? ($_SESSION['tenGV'] ?? '');
$userEmail = $_SESSION['email'] ?? ($_SESSION['gv_email'] ?? '');
if ($userEmail!=='') {
  $val = $conn->real_escape_string($userEmail);
  $where = "d.emailNguoiGui='$val'";
} else {
  $val = $conn->real_escape_string($userName);
  $where = "d.nguoiGui='$val'";
}
$sql = "
  SELECT l.maDX, d.tieuDe, l.hanhDong, l.noiDung, l.nguoiThucHien, l.thoiGian
  FROM de_xuat_log l
  JOIN de_xuat d ON d.maDX=l.maDX
  WHERE $where
  ORDER BY l.thoiGian DESC, l.id DESC
";
$res = $conn->query($sql);
$out=[];
if($res){while($row=$res->fetch_assoc()){$out[]=$row;}}
echo json_encode(['success'=>true,'data'=>$out]);
