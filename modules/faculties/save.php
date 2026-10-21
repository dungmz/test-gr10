<?php
include "../../config/db.php";
include "../../layout/header.php";
include "../../layout/sidebar.php";

// Lấy dữ liệu từ form
$maKhoa   = isset($_POST['maKhoa']) ? trim($_POST['maKhoa']) : '';
$tenKhoa  = isset($_POST['tenKhoa']) ? trim($_POST['tenKhoa']) : '';
$matruong = isset($_POST['matruong']) ? trim($_POST['matruong']) : '';

if ($maKhoa == '' || $tenKhoa == '' || $matruong == '') {
    echo "<script>
        Swal.fire({
          icon: 'warning',
          title: 'Thiếu thông tin!',
          text: 'Vui lòng điền đầy đủ thông tin trước khi lưu.',
        }).then(() => { window.history.back(); });
    </script>";
    include "../../layout/footer.php";
    exit;
}

$check = $conn->prepare("SELECT * FROM faculties WHERE maKhoa = ?");
$check->bind_param("s", $maKhoa);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE faculties SET tenKhoa=?, matruong=? WHERE maKhoa=?");
    $stmt->bind_param("sss", $tenKhoa, $matruong, $maKhoa);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
              icon: 'success',
              title: 'Thành công!',
              text: 'Đã cập nhật thông tin Khoa!',
              showConfirmButton: false,
              timer: 2000
            }).then(() => { window.location.href = 'index.php'; });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
              icon: 'error',
              title: 'Lỗi!',
              text: 'Không thể cập nhật dữ liệu. Vui lòng thử lại!',
            }).then(() => { window.history.back(); });
        </script>";
    }
} else {
    $insert = $conn->prepare("INSERT INTO faculties (maKhoa, tenKhoa, matruong) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $maKhoa, $tenKhoa, $matruong);

    if ($insert->execute()) {
        echo "<script>
            Swal.fire({
              icon: 'success',
              title: 'Thành công!',
              text: 'Đã thêm Khoa mới!',
              showConfirmButton: false,
              timer: 2000
            }).then(() => { window.location.href = 'index.php'; });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
              icon: 'error',
              title: 'Lỗi!',
              text: 'Không thể thêm dữ liệu. Vui lòng thử lại!',
            }).then(() => { window.history.back(); });
        </script>";
    }
}

include "../../layout/footer.php";
?>
