<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Ambil data dari Query Parameter (URL)
$id_resep = $_GET['id_resep'] ?? null;
$id_user  = $_GET['id_user'] ?? null;

if (empty($id_resep) || empty($id_user)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID resep dan ID user wajib diisi']);
    exit;
}

// 1. Verifikasi Kepemilikan (Penting!)
$stmt = mysqli_prepare($conn, "SELECT id_user FROM resep WHERE id_resep = ?");
mysqli_stmt_bind_param($stmt, "i", $id_resep);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row || $row['id_user'] != $id_user) {
    http_response_code(403);
    echo json_encode(['error' => 'Anda tidak memiliki hak untuk menghapus resep ini']);
    exit;
}

// 2. Hapus Resep
// Jika database menggunakan ON DELETE CASCADE, bahan/komentar akan ikut terhapus otomatis.
$stmt_del = mysqli_prepare($conn, "DELETE FROM resep WHERE id_resep = ?");
mysqli_stmt_bind_param($stmt_del, "i", $id_resep);

if (mysqli_stmt_execute($stmt_del)) {
    echo json_encode(['message' => 'Resep berhasil dihapus']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menghapus data di database']);
}
?>