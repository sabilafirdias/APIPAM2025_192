<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);
$id_resep = $data['id_resep'] ?? null;

if (empty($id_resep)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID resep wajib diisi']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT nama_bahan, takaran FROM bahan WHERE id_resep = ?");
mysqli_stmt_bind_param($stmt, "i", $id_resep);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$bahanList = [];
while ($row = mysqli_fetch_assoc($result)) {
    $bahanList[] = $row;
}

echo json_encode($bahanList);
?>