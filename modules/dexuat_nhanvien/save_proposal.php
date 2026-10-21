<?php
if (session_status()===PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

$script=str_replace('\\','/',$_SERVER['SCRIPT_NAME']??'/');
if(($p=strpos($script,'/modules/'))!==false){$basePath=substr($script,0,$p+1);}else{$basePath=rtrim(dirname($script),'/').'/';}

require_once '../../config/i18n.php';
load_lang('dexuat');

if(!isset($_SESSION['role'])||$_SESSION['role']!=='giangvien'){echo json_encode(['status'=>'error','message'=>t('dexuat_emp.no_permission')]);exit;}

require_once '../../config/db.php';

$user=$_SESSION['username']??($_SESSION['tenGV']??'');
$email=$_SESSION['email']??($_SESSION['gv_email']??'');

$mode=$_POST['mode']??'';
$loai=$_POST['loaiThayDoi']??'';
$noiDung=trim($_POST['noiDung']??'');

$tenDonVi=trim($_POST['tenDonVi']??'');
$maDonVi=trim($_POST['maDonVi']??'');
$donViCha=trim($_POST['donViCha']??'');
$moTa=trim($_POST['moTa']??'');
$donViChon=trim($_POST['donViChon']??'');
$thongTinMoi=trim($_POST['thongTinMoi']??'');

function mkTitle($loai,$ten,$chon,$ma){
  if($loai==='Thêm mới đơn vị' && $ten!=='') return 'Thêm mới: '.$ten;
  if($loai==='Chỉnh sửa đơn vị' && $chon!=='') return 'Chỉnh sửa: '.$chon;
  if($loai==='Xóa đơn vị' && $chon!=='') return 'Xóa: '.$chon;
  if($ma!=='') return $loai.' - '.$ma;
  return $loai;
}

if($mode==='draft' || $mode==='submit'){
  if($loai===''||$noiDung===''){echo json_encode(['status'=>'error','message'=>t('dexuat_emp.missing_data')]);exit;}
  $tieuDe=mkTitle($loai,$tenDonVi,$donViChon,$maDonVi);
  $trangThai=($mode==='draft')?'Nháp':'Chờ duyệt';

  $stmt=$conn->prepare("INSERT INTO de_xuat(tieuDe,noiDung,loaiDeXuat,nguoiGui,emailNguoiGui,trangThai) VALUES (?,?,?,?,?,?)");
  $stmt->bind_param('ssssss',$tieuDe,$noiDung,$loai,$user,$email,$trangThai);
  if(!$stmt->execute()){echo json_encode(['status'=>'error','message'=>t('dexuat_emp.send_failed')]);exit;}
  echo json_encode(['status'=>'success','message'=>$mode==='draft'?t('dexuat_emp.saved_draft'):t('dexuat_emp.sent')]);
  exit;
}

if($mode==='update'){
  $id=(int)($_POST['id']??0);
  if($id<=0||$noiDung===''){echo json_encode(['status'=>'error','message'=>t('dexuat_emp.missing_data')]);exit;}
  $stmt=$conn->prepare("UPDATE de_xuat SET noiDung=? WHERE maDX=? AND nguoiGui=? AND trangThai='Nháp'");
  $stmt->bind_param('sis',$noiDung,$id,$user);
  if(!$stmt->execute()||$stmt->affected_rows<=0){echo json_encode(['status'=>'error','message'=>t('dexuat_emp.err_save')]);exit;}
  echo json_encode(['status'=>'success','message'=>t('dexuat_emp.saved_changes')]);
  exit;
}

if($mode==='submit_existing'){
  $id=(int)($_POST['id']??0);
  if($id<=0){echo json_encode(['status'=>'error','message'=>t('dexuat_emp.missing_data')]);exit;}
  $stmt=$conn->prepare("UPDATE de_xuat SET trangThai='Chờ duyệt', ngayGui=NOW() WHERE maDX=? AND nguoiGui=? AND trangThai='Nháp'");
  $stmt->bind_param('is',$id,$user);
  if(!$stmt->execute()||$stmt->affected_rows<=0){echo json_encode(['status'=>'error','message'=>t('dexuat_emp.err_send')]);exit;}
  echo json_encode(['status'=>'success','message'=>t('dexuat_emp.send_review')]);
  exit;
}

echo json_encode(['status'=>'error','message'=>t('dexuat_emp.invalid_request')]);
