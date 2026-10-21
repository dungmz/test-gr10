<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";

if (isset($_GET["maKhoa"])) {
    $maKhoa = $_GET["maKhoa"];
    $stmt = $conn->prepare("DELETE FROM faculties WHERE maKhoa = ?");
    $stmt->bind_param("s", $maKhoa);
    $stmt->execute();
    echo "<script>
      Swal.fire({icon:'success',title:'Đã xóa!',text:'Xóa thành công!',showConfirmButton:false,timer:2000})
      .then(()=>{window.location.href='index.php';});
    </script>";
}
elseif (isset($_POST["maKhoa"]) && is_array($_POST["maKhoa"])) {
    $maKhoas = $_POST["maKhoa"];
    $placeholders = implode(',', array_fill(0, count($maKhoas), '?'));
    $types = str_repeat('s', count($maKhoas));

    $stmt = $conn->prepare("DELETE FROM faculties WHERE maKhoa IN ($placeholders)");
    $stmt->bind_param($types, ...$maKhoas);
    $stmt->execute();
    echo "<script>
      Swal.fire({icon:'success',title:'Đã xóa!',text:'Đã xóa các mục đã chọn!',showConfirmButton:false,timer:2000})
      .then(()=>{window.location.href='index.php';});
    </script>";
}

include "../../layout/footer.php";
?>
