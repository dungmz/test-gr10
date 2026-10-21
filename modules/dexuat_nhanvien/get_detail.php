<?php
if (session_status()===PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

if(!isset($_GET['id'])){echo json_encode(['success'=>false]);exit;}
require_once '../../config/db.php';
require_once '../../config/i18n.php';
load_lang('dexuat');

$id=(int)$_GET['id'];
$q=$conn->prepare("SELECT maDX,tieuDe,noiDung,loaiDeXuat,nguoiGui,emailNguoiGui,ngayGui,trangThai,lyDoTuChoi,nguoiDuyet,ngayDuyet FROM de_xuat WHERE maDX=?");
$q->bind_param('i',$id);
$q->execute();
$res=$q->get_result();
if($res&&$res->num_rows===1){
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
  echo json_encode(['success'=>true,'data'=>$row]);
}else{
  echo json_encode(['success'=>false]);
}
