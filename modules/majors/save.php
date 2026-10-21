<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('majors');

$maNganh  = isset($_POST['maNganh']) ? trim($_POST['maNganh']) : '';
$tenNganh = isset($_POST['tenNganh']) ? trim($_POST['tenNganh']) : '';
$maKhoa   = isset($_POST['maKhoa']) ? trim($_POST['maKhoa']) : '';

if ($maNganh == '' || $tenNganh == '' || $maKhoa == '') {
    echo "<script>
        Swal.fire({
          icon: 'warning',
                    title: " . json_encode(t('majors.missing_info_title')) . ",
                    text: " . json_encode(t('majors.missing_info_text')) . ",
        }).then(() => { window.history.back(); });
    </script>";
    include "../../layout/footer.php";
    exit;
}

$check = $conn->prepare("SELECT * FROM majors WHERE maNganh = ?");
$check->bind_param("s", $maNganh);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE majors SET tenNganh=?, maKhoa=? WHERE maNganh=?");
    $stmt->bind_param("sss", $tenNganh, $maKhoa, $maNganh);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
              icon: 'success',
              title: " . json_encode(t('majors.success_title')) . ",
              text: " . json_encode(t('majors.update_success_text')) . ",
              showConfirmButton: false,
              timer: 2000
            }).then(() => { window.location.href = 'index.php'; });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
              icon: 'error',
              title: " . json_encode(t('majors.error_title')) . ",
              text: " . json_encode(t('majors.update_error_text')) . ",
            }).then(() => { window.history.back(); });
        </script>";
    }
} else {
    $insert = $conn->prepare("INSERT INTO majors (maNganh, tenNganh, maKhoa) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $maNganh, $tenNganh, $maKhoa);

    if ($insert->execute()) {
        echo "<script>
            Swal.fire({
              icon: 'success',
              title: " . json_encode(t('majors.success_title')) . ",
              text: " . json_encode(t('majors.create_success_text')) . ",
              showConfirmButton: false,
              timer: 2000
            }).then(() => { window.location.href = 'index.php'; });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
              icon: 'error',
              title: " . json_encode(t('majors.error_title')) . ",
              text: " . json_encode(t('majors.create_error_text')) . ",
            }).then(() => { window.history.back(); });
        </script>";
    }
}

include "../../layout/footer.php";
?>
