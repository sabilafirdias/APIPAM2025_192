<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$id_user = $_GET['id_user'] ?? null;

if (empty($id_user)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID pengguna wajib diisi']);
    exit;
}

if (!is_numeric($id_user)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID pengguna tidak valid']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id_user, username, email FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt, "i", $id_user);
$success = mysqli_stmt_execute($stmt);

if (!$success) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Pengguna tidak ditemukan']);
    exit;
}

$user = mysqli_fetch_assoc($result);
echo json_encode($user);
?>