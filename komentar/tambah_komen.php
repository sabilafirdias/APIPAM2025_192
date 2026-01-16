<?php
include_once "../konek.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$id_resep = $data['id_resep'];
$id_user = $data['id_user'];
$isi_komentar = $data['isi_komentar'];

$query = "INSERT INTO komentar (id_resep, id_user, isi_komentar) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iis", $id_resep, $id_user, $isi_komentar);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["status" => "success", "message" => "Berhasil"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>