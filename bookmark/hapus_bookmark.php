<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$id_user  = $data['id_user'] ?? null;
$id_resep = $data['id_resep'] ?? null;

if (empty($id_user) || empty($id_resep)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID user dan resep wajib diisi']);
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM bookmark WHERE id_user = ? AND id_resep = ?");
mysqli_stmt_bind_param($stmt, "ii", $id_user, $id_resep);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['message' => 'Bookmark berhasil dihapus']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menghapus bookmark']);
}
?>