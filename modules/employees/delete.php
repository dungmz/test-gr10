<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";
load_lang('employees');

/**
 * Trường hợp 1️⃣: XÓA 1 NHÂN VIÊN (GET)
 */
if (isset($_GET["tenNV"])) {
    $tenNV = $_GET["tenNV"];

    $stmt = $conn->prepare("DELETE FROM employees WHERE tenNV = ?");
    $stmt->bind_param("s", $tenNV);

    if ($stmt->execute()) {
        echo "<script>
          Swal.fire({
            icon: 'success',
            title: " . json_encode(t('employees.delete_success_title')) . ",
            text: " . json_encode(t('employees.delete_single_success_text')) . ",
            showConfirmButton: false,
            timer: 2000
          }).then(()=>{window.location.href='index.php';});
        </script>";
    } else {
        echo "<script>
          Swal.fire({
            icon: 'error',
            title: " . json_encode(t('employees.error_title')) . ",
            text: " . json_encode(t('employees.delete_error_text')) . ",
          }).then(()=>{window.location.href='index.php';});
        </script>";
    }
}

/**
 * Trường hợp 2️⃣: XÓA NHIỀU NHÂN VIÊN (POST)
 */
elseif (isset($_POST['tenNV']) && is_array($_POST['tenNV'])) {
    $tenNVs = $_POST['tenNV'];

    if (count($tenNVs) > 0) {
        $placeholders = implode(',', array_fill(0, count($tenNVs), '?'));
        $types = str_repeat('s', count($tenNVs));

        $sql = "DELETE FROM employees WHERE tenNV IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$tenNVs);

        if ($stmt->execute()) {
            echo "<script>
              Swal.fire({
                icon: 'success',
                title: " . json_encode(t('employees.delete_success_title')) . ",
                text: " . json_encode(t('employees.delete_multi_success_text')) . ",
                showConfirmButton: false,
                timer: 2000
              }).then(()=>{window.location.href='index.php';});
            </script>";
        } else {
            echo "<script>
              Swal.fire({
                icon: 'error',
                title: " . json_encode(t('employees.error_title')) . ",
                text: " . json_encode(t('employees.delete_multi_error_text')) . ",
              }).then(()=>{window.location.href='index.php';});
            </script>";
        }
    } else {
        echo "<script>
          Swal.fire({
            icon: 'info',
            title: " . json_encode(t('employees.delete_none_title')) . ",
            text: " . json_encode(t('employees.delete_none_text')) . ",
          }).then(()=>{window.location.href='index.php';});
        </script>";
    }
}

include "../../layout/footer.php";
?>
