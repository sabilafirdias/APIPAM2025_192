<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$id_user   = $data['id_user'] ?? null;
$judul     = $data['judul'] ?? null;
$langkah   = $data['langkah'] ?? null;
$catatan   = $data['catatan'] ?? null;
$kategori  = $data['kategori'] ?? null;
$bahanList = $data['bahan'] ?? [];

if (empty($id_user) || empty($judul) || empty($langkah) || empty($kategori)) {
    http_response_code(400);
    echo json_encode(['error' => 'Field wajib tidak boleh kosong']);
    exit;
}

mysqli_autocommit($conn, false);
mysqli_begin_transaction($conn);

// Simpan resep
$stmt = mysqli_prepare($conn, "INSERT INTO resep (id_user, judul, langkah, catatan, kategori) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "issss", $id_user, $judul, $langkah, $catatan, $kategori);
$success = mysqli_stmt_execute($stmt);
$id_resep = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

if (!$success || !$id_resep) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menyimpan resep']);
    exit;
}

// Simpan bahan
foreach ($bahanList as $b) {
    $nama = $b['nama_bahan'] ?? '';
    $takaran = $b['takaran'] ?? '';
    $stmt2 = mysqli_prepare($conn, "INSERT INTO bahan (id_resep, nama_bahan, takaran) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt2, "iss", $id_resep, $nama, $takaran);
    if (!mysqli_stmt_execute($stmt2)) {
        mysqli_rollback($conn);
        http_response_code(500);
        echo json_encode(['error' => 'Gagal menyimpan bahan']);
        exit;
    }
    mysqli_stmt_close($stmt2);
}

mysqli_commit($conn);
echo json_encode(['message' => 'Resep berhasil disimpan', 'id_resep' => $id_resep]);
?>