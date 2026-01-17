<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// mengambil satu resep untuk detail
$id_resep = $_GET['id_resep'] ?? null;

if (empty($id_resep)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID resep wajib diisi']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT r.*, u.username FROM resep r JOIN users u ON r.id_user = u.id_user WHERE r.id_resep = ?");
mysqli_stmt_bind_param($stmt, "i", $id_resep);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Resep tidak ditemukan']);
    exit;
}

$resep = mysqli_fetch_assoc($result);

$stmtBahan = mysqli_prepare($conn, "SELECT nama_bahan, takaran FROM bahan WHERE id_resep = ?");
mysqli_stmt_bind_param($stmtBahan, "i", $id_resep);
mysqli_stmt_execute($stmtBahan);
$resultBahan = mysqli_stmt_get_result($stmtBahan);

$bahanList = [];
while ($b = mysqli_fetch_assoc($resultBahan)) {
    $bahanList[] = $b;
}

$resep['bahan'] = $bahanList;

echo json_encode($resep);
?>