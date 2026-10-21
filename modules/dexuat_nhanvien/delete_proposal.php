<?php
if (session_status()===PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../config/i18n.php';
load_lang('dexuat');

if(!isset($_SESSION['role'])||$_SESSION['role']!=='giangvien'){echo json_encode(['status'=>'error','message'=>t('dexuat_emp.no_permission')]);exit;}
require_once '../../config/db.php';

$user=$_SESSION['username']??($_SESSION['tenGV']??'');
$id=(int)($_POST['id']??0);

if($id<=0){echo json_encode(['status'=>'error','message'=>t('dexuat_emp.missing_data')]);exit;}

$stmt=$conn->prepare("DELETE FROM de_xuat WHERE maDX=? AND nguoiGui=? AND trangThai='Nháp'");
$stmt->bind_param('is',$id,$user);
if(!$stmt->execute()||$stmt->affected_rows<=0){echo json_encode(['status'=>'error','message'=>t('dexuat_emp.delete_failed')]);exit;}
echo json_encode(['status'=>'success','message'=>t('dexuat_emp.deleted_draft')]);
