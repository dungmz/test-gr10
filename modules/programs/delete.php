<?php
include "../../config/db.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Xóa học phần</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background-color:#f8f9fc; font-family: Arial, sans-serif;">

<?php
if (isset($_GET['maHP'])) {
    $maHP = trim($_GET['maHP']);

    // Kiểm tra học phần tồn tại
    $check = $conn->prepare("SELECT maHP FROM courses WHERE maHP = ?");
    $check->bind_param("s", $maHP);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        echo "
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Không tìm thấy học phần!',
            text: 'Học phần này không tồn tại trong cơ sở dữ liệu.',
            confirmButtonText: 'Quay lại'
        }).then(() => window.location='index.php');
        </script>";
        exit;
    }

    // Thực hiện xóa
    $stmt = $conn->prepare("DELETE FROM courses WHERE maHP = ?");
    $stmt->bind_param("s", $maHP);
    $success = $stmt->execute();

    if ($success) {
        echo "
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Đã xóa học phần thành công!',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        }).then(() => window.location='index.php');
        </script>";
    } else {
        echo "
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Không thể xóa học phần!',
            text: 'Vui lòng kiểm tra lại dữ liệu hoặc thử lại sau.',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Đóng'
        }).then(() => window.location='index.php');
        </script>";
    }
} else {
    echo "
    <script>
    Swal.fire({
        icon: 'warning',
        title: 'Yêu cầu không hợp lệ!',
        text: 'Thiếu mã học phần để xóa.',
        confirmButtonText: 'Quay lại'
    }).then(() => window.location='index.php');
    </script>";
}
?>

</body>
</html>
