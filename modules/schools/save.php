<?php
include "../../config/db.php";

$action = $_POST['action'] ?? '';
$matruong = trim($_POST["matruong"]);
$name = trim($_POST["name"]);
$maDH = trim($_POST["maDH"]);

if ($action === "create") {
    $check = $conn->prepare("SELECT * FROM schools WHERE matruong = ?");
    $check->bind_param("s", $matruong);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Mã trường đã tồn tại!"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO schools (matruong, name, maDH) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $matruong, $name, $maDH);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Thêm trường mới thành công!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi khi thêm dữ liệu!"]);
    }

} elseif ($action === "edit") {
    $stmt = $conn->prepare("UPDATE schools SET name=?, maDH=? WHERE matruong=?");
    $stmt->bind_param("sss", $name, $maDH, $matruong);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Cập nhật thành công!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Không thể cập nhật dữ liệu!"]);
    }
}
?>
