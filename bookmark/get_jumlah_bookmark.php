<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// jumlah bookmark suatu postan
$id_resep = $_GET['id_resep'] ?? null;

if (empty($id_resep)) {
    http_response_code(400);
    echo json_encode(0); 
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM bookmark WHERE id_resep = ?");
mysqli_stmt_bind_param($stmt, "i", $id_resep);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

echo json_encode((int)$data['total']);
?>