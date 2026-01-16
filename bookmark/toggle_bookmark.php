<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Membaca input JSON mentah dari Android
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Cadangan: Jika JSON kosong, coba ambil dari $_POST biasa
$id_user = $data['id_user'] ?? $_POST['id_user'] ?? null;
$id_resep = $data['id_resep'] ?? $_POST['id_resep'] ?? null;

if (!$id_user || !$id_resep) {
    http_response_code(400);
    echo json_encode(['error' => 'ID user atau resep kosong']);
    exit;
}

// Cek apakah sudah ada di database
$cek = mysqli_prepare($conn, "SELECT 1 FROM bookmark WHERE id_user = ? AND id_resep = ?");
mysqli_stmt_bind_param($cek, "ii", $id_user, $id_resep);
mysqli_stmt_execute($cek);
$res = mysqli_stmt_get_result($cek);

if (mysqli_num_rows($res) > 0) {
    // Jika ada, maka HAPUS
    $query = mysqli_prepare($conn, "DELETE FROM bookmark WHERE id_user = ? AND id_resep = ?");
    $status = "dihapus";
} else {
    // Jika tidak ada, maka TAMBAH
    $query = mysqli_prepare($conn, "INSERT INTO bookmark (id_user, id_resep) VALUES (?, ?)");
    $status = "ditambahkan";
}

mysqli_stmt_bind_param($query, "ii", $id_user, $id_resep);

if (mysqli_stmt_execute($query)) {
    echo json_encode(['message' => "Bookmark $status"]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal eksekusi database']);
}
?>