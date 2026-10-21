<?php
include "../../config/db.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Lưu học phần</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background-color:#f8f9fc; font-family: Arial, sans-serif;">

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    $maHP = trim($_POST['maHP'] ?? '');
    $tenHP = trim($_POST['tenHP'] ?? '');
    $soTinChi = $_POST['soTinChi'] ?? 0;
    $lyThuyet = $_POST['lyThuyet'] ?? 0;
    $thucHanh = $_POST['thucHanh'] ?? 0;
    $hpTienQuyet = trim($_POST['hpTienQuyet'] ?? '');
    $hpHocTruoc = trim($_POST['hpHocTruoc'] ?? '');
    $khoaQuanLy = trim($_POST['khoaQuanLy'] ?? '');
    $ghiChu = trim($_POST['ghiChu'] ?? '');
    $KhoiKienThuc = trim($_POST['KhoiKienThuc'] ?? '');

    // Kiểm tra dữ liệu rỗng
    if ($maHP === '' || $tenHP === '') {
        echo "
        <script>
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin!',
            text: 'Vui lòng điền đầy đủ mã và tên học phần.',
            confirmButtonText: 'Quay lại'
        }).then(() => window.history.back());
        </script>";
        exit;
    }

    if ($action === 'create') {
        // Kiểm tra trùng mã học phần
        $check = $conn->prepare("SELECT maHP FROM courses WHERE maHP = ?");
        $check->bind_param("s", $maHP);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            echo "
            <script>
            Swal.fire({
                icon: 'error',
                title: 'Mã học phần đã tồn tại!',
                text: 'Vui lòng sử dụng mã học phần khác.',
                confirmButtonText: 'Quay lại'
            }).then(() => window.history.back());
            </script>";
            exit;
        }

        // Thêm mới
        $stmt = $conn->prepare("INSERT INTO courses 
            (maHP, tenHP, soTinChi, lyThuyet, thucHanh, hpTienQuyet, hpHocTruoc, khoaQuanLy, ghiChu, KhoiKienThuc)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiddsssss", $maHP, $tenHP, $soTinChi, $lyThuyet, $thucHanh, $hpTienQuyet, $hpHocTruoc, $khoaQuanLy, $ghiChu, $KhoiKienThuc);

        if ($stmt->execute()) {
            echo "
            <script>
            Swal.fire({
                icon: 'success',
                title: 'Thêm học phần thành công!',
                confirmButtonText: 'OK'
            }).then(() => window.location='index.php');
            </script>";
        } else {
            echo "
            <script>
            Swal.fire({
                icon: 'error',
                title: 'Không thể thêm học phần!',
                text: 'Đã xảy ra lỗi trong quá trình lưu dữ liệu.',
                confirmButtonText: 'Quay lại'
            }).then(() => window.history.back());
            </script>";
        }
        exit;
    }

    if ($action === 'edit') {
        $maHP_old = trim($_POST['maHP_old'] ?? '');

        $stmt = $conn->prepare("UPDATE courses 
            SET maHP=?, tenHP=?, soTinChi=?, lyThuyet=?, thucHanh=?, hpTienQuyet=?, hpHocTruoc=?, khoaQuanLy=?, ghiChu=?, KhoiKienThuc=? 
            WHERE maHP=?");
        $stmt->bind_param("ssiddssssss", $maHP, $tenHP, $soTinChi, $lyThuyet, $thucHanh, 
                                            $hpTienQuyet, $hpHocTruoc, $khoaQuanLy, $ghiChu, $KhoiKienThuc, $maHP_old);

                if ($stmt->execute()) {
            echo "
            <script>
            Swal.fire({
                icon: 'success',
                title: 'Cập nhật học phần thành công!',
                confirmButtonText: 'OK'
            }).then(() => window.location='index.php');
            </script>";
        } else {
            echo "
            <script>
            Swal.fire({
                icon: 'error',
                title: 'Không thể cập nhật học phần!',
                text: 'Vui lòng thử lại sau.',
                confirmButtonText: 'Quay lại'
            }).then(() => window.history.back());
            </script>";
        }
        exit;
    }
} else {
    echo "
    <script>
    Swal.fire({
        icon: 'warning',
        title: 'Yêu cầu không hợp lệ!',
        confirmButtonText: 'Quay lại'
    }).then(() => window.location='index.php');
    </script>";
}
?>

</body>
</html>
