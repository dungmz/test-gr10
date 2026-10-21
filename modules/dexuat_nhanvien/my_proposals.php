<?php
if (session_status()===PHP_SESSION_NONE) session_start();
$basePath = '/KTPM2/KTPM/';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'giangvien') {
  header('Location: '.$basePath.'login.php');
  exit;
}
include "../../config/db.php";
include "../../config/i18n.php";
load_lang('dexuat');
include "../../layout/header_gv.php";
include "../../layout/sidebar_gv.php";

$userName = $_SESSION['username'] ?? ($_SESSION['tenGV'] ?? '');
$userEmail = $_SESSION['email'] ?? ($_SESSION['gv_email'] ?? '');
$filter = $userEmail !== '' 
  ? "emailNguoiGui='".$conn->real_escape_string($userEmail)."'"
  : "nguoiGui='".$conn->real_escape_string($userName)."'";
?>
<div class="content">
  <h4 class="fw-semibold text-primary mb-4"><i class="bi bi-journal-text"></i> <?php echo t('dexuat_emp.my_title'); ?></h4>
  <div class="card shadow-sm">
    <div class="card-body">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th><?php echo t('dexuat_emp.table_code'); ?></th>
            <th><?php echo t('dexuat_emp.table_title'); ?></th>
            <th><?php echo t('dexuat_emp.table_type'); ?></th>
            <th><?php echo t('dexuat_emp.table_date'); ?></th>
            <th><?php echo t('dexuat_emp.table_status'); ?></th>
            <th><?php echo t('dexuat_emp.table_actions'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
            $sql = "SELECT * FROM de_xuat WHERE $filter ORDER BY ngayGui DESC, maDX DESC";
            $res = $conn->query($sql);
            if (!$res || $res->num_rows==0) {
              echo "<tr><td colspan='6' class='text-muted'>" . t('dexuat_emp.none') . "</td></tr>";
            } else {
              while($r=$res->fetch_assoc()){
                $statusKey = match($r['trangThai']) {
                  'Đã duyệt' => 'status_approved',
                  'Từ chối' => 'status_rejected',
                  'Yêu cầu chỉnh sửa' => 'status_edit',
                  'Nháp' => 'status_draft',
                  default => 'status_pending'
                };
                $badge = match($statusKey) {
                  'status_approved' => 'success',
                  'status_rejected' => 'danger',
                  'status_edit' => 'secondary',
                  'status_draft' => 'info',
                  default => 'warning'
                };
                $titleDisplay = t_data('dexuat', (string)$r['maDX'], 'tieuDe', (string)$r['tieuDe']);
                $typeDisplay = t_data('dexuat_types', (string)$r['maDX'], 'loaiDeXuat', (string)$r['loaiDeXuat']);
                $statusLabel = t('dexuat.' . $statusKey);
                echo "<tr>";
                echo "<td>{$r['maDX']}</td>";
                echo "<td class='text-start'>".htmlspecialchars($titleDisplay)."</td>";
                echo "<td>".htmlspecialchars($typeDisplay)."</td>";
                echo "<td>".htmlspecialchars($r['ngayGui'])."</td>";
                echo "<td><span class='badge bg-$badge'>".htmlspecialchars($statusLabel)."</span></td>";
                echo "<td>";
                if ($r['trangThai']=='Nháp') {
                  echo "<button class='btn btn-sm btn-outline-primary' onclick='editProposal({$r['maDX']})'><i class=\"bi bi-pencil\"></i> " . t('dexuat_emp.action_edit') . "</button> ";
                  echo "<button class='btn btn-sm btn-outline-success' onclick='sendProposal({$r['maDX']})'><i class=\"bi bi-send\"></i> " . t('dexuat_emp.action_send') . "</button> ";
                  echo "<button class='btn btn-sm btn-outline-danger' onclick='deleteProposal({$r['maDX']})'><i class=\"bi bi-trash\"></i> " . t('dexuat_emp.action_delete') . "</button>";
                } else {
                  echo "<button class='btn btn-sm btn-outline-secondary' onclick='viewDetail({$r['maDX']})'><i class=\"bi bi-eye\"></i> " . t('dexuat_emp.action_view') . "</button>";
                }
                echo "</td>";
                echo "</tr>";
              }
            }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script>
const labels = {
  detailType: <?php echo json_encode(t('dexuat_emp.detail_type')); ?>,
  detailStatus: <?php echo json_encode(t('dexuat_emp.detail_status')); ?>,
  detailContent: <?php echo json_encode(t('dexuat_emp.detail_content')); ?>,
  detailFeedback: <?php echo json_encode(t('dexuat_emp.detail_feedback')); ?>,
  errLoad: <?php echo json_encode(t('dexuat_emp.err_load')); ?>,
  editTitle: <?php echo json_encode(t('dexuat_emp.edit_title')); ?>,
  editPlaceholder: <?php echo json_encode(t('dexuat_emp.edit_placeholder')); ?>,
  editSave: <?php echo json_encode(t('dexuat_emp.edit_save')); ?>,
  errSave: <?php echo json_encode(t('dexuat_emp.err_save')); ?>,
  errSend: <?php echo json_encode(t('dexuat_emp.err_send')); ?>,
  errDelete: <?php echo json_encode(t('dexuat_emp.err_delete')); ?>,
  deleteConfirmTitle: <?php echo json_encode(t('dexuat_emp.delete_confirm_title')); ?>,
  deleteConfirmText: <?php echo json_encode(t('dexuat_emp.delete_confirm_text')); ?>,
  deleteConfirmButton: <?php echo json_encode(t('dexuat_emp.delete_confirm_button')); ?>,
  close: <?php echo json_encode(t('dexuat_emp.close')); ?>
};

function viewDetail(id){
  fetch('get_detail.php?id='+id)
    .then(r=>r.json())
    .then(d=>{
      if(!d.success){Swal.fire('', labels.errLoad, 'error');return;}
      const dx=d.data;
      Swal.fire({
        title: dx.tieuDeDisplay || dx.tieuDe,
        html: `
          <p><b>${labels.detailType}:</b> ${dx.loaiDeXuatDisplay || dx.loaiDeXuat}</p>
          <p><b>${labels.detailStatus}:</b> ${dx.trangThaiLabel || dx.trangThai}</p>
          <div class='border p-2 text-start bg-light' style='white-space:pre-wrap'>${dx.noiDungDisplay||dx.noiDung||''}</div>
          ${dx.lyDoTuChoi?`<p class='mt-2 text-danger'><b>${labels.detailFeedback}:</b> ${dx.lyDoTuChoi}</p>`:''}
        `,
        icon:'info'
      });
    })
    .catch(()=>Swal.fire('', labels.errLoad, 'error'));
}
function editProposal(id){
  Swal.fire({title:labels.editTitle,input:'textarea',inputPlaceholder:labels.editPlaceholder,showCancelButton:true,confirmButtonText:labels.editSave,cancelButtonText:labels.close})
    .then(res=>{
      if(res.isConfirmed){
        fetch('save_proposal.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`mode=update&id=${id}&noiDung=${encodeURIComponent(res.value||'')}`})
          .then(r=>r.json()).then(d=>{Swal.fire(d.message,'',d.status);location.reload();})
          .catch(()=>Swal.fire('',labels.errSave,'error'));
      }
    });
}
function sendProposal(id){
  fetch('save_proposal.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`mode=submit_existing&id=${id}`})
    .then(r=>r.json()).then(d=>{Swal.fire(d.message,'',d.status);location.reload();})
    .catch(()=>Swal.fire('',labels.errSend,'error'));
}
function deleteProposal(id){
  Swal.fire({title:labels.deleteConfirmTitle,text:labels.deleteConfirmText,icon:'warning',showCancelButton:true,confirmButtonText:labels.deleteConfirmButton,cancelButtonText:labels.close})
    .then(rs=>{
      if(rs.isConfirmed){
        fetch('delete_proposal.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`id=${id}`})
          .then(r=>r.json()).then(d=>{Swal.fire(d.message,'',d.status);location.reload();})
          .catch(()=>Swal.fire('',labels.errDelete,'error'));
      }
    });
}
</script>
<?php include "../../layout/footer.php"; ?>
