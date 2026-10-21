<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('majors');

if (isset($_GET["maNganh"])) {
    $maNganh = $_GET["maNganh"];
    $stmt = $conn->prepare("DELETE FROM majors WHERE maNganh = ?");
    $stmt->bind_param("s", $maNganh);
    $stmt->execute();
    echo "<script>
      Swal.fire({icon:'success',title:" . json_encode(t('majors.delete_success_title')) . ",text:" . json_encode(t('majors.delete_success_single_text')) . ",showConfirmButton:false,timer:2000})
      .then(()=>{window.location.href='index.php';});
    </script>";
}
elseif (isset($_POST["maNganh"]) && is_array($_POST["maNganh"])) {
    $maNganhs = $_POST["maNganh"];
    $placeholders = implode(',', array_fill(0, count($maNganhs), '?'));
    $types = str_repeat('s', count($maNganhs));

    $stmt = $conn->prepare("DELETE FROM majors WHERE maNganh IN ($placeholders)");
    $stmt->bind_param($types, ...$maNganhs);
    $stmt->execute();
    echo "<script>
      Swal.fire({icon:'success',title:" . json_encode(t('majors.delete_success_title')) . ",text:" . json_encode(t('majors.delete_success_multi_text')) . ",showConfirmButton:false,timer:2000})
      .then(()=>{window.location.href='index.php';});
    </script>";
}

include "../../layout/footer.php";
?>
