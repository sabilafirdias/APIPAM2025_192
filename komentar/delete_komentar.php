<?php
include_once "../konek.php";
header('Content-Type: application/json');

$id_komentar = $_GET['id_komentar'] ?? 0;
$id_user = $_GET['id_user'] ?? 0; // Untuk validasi kepemilikan

$query = "DELETE FROM komentar WHERE id_komentar = ? AND id_user = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id_komentar, $id_user);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error"]);
}
?>