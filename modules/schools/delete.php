<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";

if (isset($_GET["matruong"])) {
    $matruong = $_GET["matruong"];
    $stmt = $conn->prepare("DELETE FROM schools WHERE matruong = ?");
    $stmt->bind_param("s", $matruong);
    $stmt->execute();
    echo "<script>
      Swal.fire({icon:'success',title:'Đã xóa!',text:'Xóa thành công!',showConfirmButton:false,timer:2000})
      .then(()=>{window.location.href='index.php';});
    </script>";
}

elseif (isset($_POST["matruong"]) && is_array($_POST["matruong"])) {
    $matruongs = $_POST["matruong"];
    $placeholders = implode(',', array_fill(0, count($matruongs), '?'));
    $types = str_repeat('s', count($matruongs));

    $stmt = $conn->prepare("DELETE FROM schools WHERE matruong IN ($placeholders)");
    $stmt->bind_param($types, ...$matruongs);
    $stmt->execute();
    echo "<script>
      Swal.fire({icon:'success',title:'Đã xóa!',text:'Đã xóa các mục đã chọn!',showConfirmButton:false,timer:2000})
      .then(()=>{window.location.href='index.php';});
    </script>";
}

include "../../layout/footer.php";
?>
