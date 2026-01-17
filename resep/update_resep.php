<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$id_resep  = $data['id_resep'] ?? null;
$id_user   = $data['id_user'] ?? null;
$judul     = $data['judul'] ?? null;
$langkah   = $data['langkah'] ?? null;
$catatan   = $data['catatan'] ?? null;
$kategori  = $data['kategori'] ?? null;
$bahanList = $data['bahan'] ?? [];

if (empty($id_resep) || empty($id_user) || empty($judul) || empty($langkah) || empty($kategori)) {
    http_response_code(400);
    echo json_encode(['error' => 'Field wajib tidak boleh kosong']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id_user FROM resep WHERE id_resep = ?");
mysqli_stmt_bind_param($stmt, "i", $id_resep);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Resep tidak ditemukan']);
    exit;
}

$row = mysqli_fetch_assoc($result);
if ((int)$row['id_user'] !== (int)$id_user) {
    http_response_code(403);
    echo json_encode(['error' => 'Anda tidak berhak mengedit resep ini']);
    exit;
}

mysqli_begin_transaction($conn);

try {
    $stmtUpdate = mysqli_prepare($conn, "UPDATE resep SET judul = ?, langkah = ?, catatan = ?, kategori = ? WHERE id_resep = ?");
    mysqli_stmt_bind_param($stmtUpdate, "ssssi", $judul, $langkah, $catatan, $kategori, $id_resep);
    
    if (!mysqli_stmt_execute($stmtUpdate)) {
        throw new Exception("Gagal update resep utama");
    }

    $stmtDel = mysqli_prepare($conn, "DELETE FROM bahan WHERE id_resep = ?");
    mysqli_stmt_bind_param($stmtDel, "i", $id_resep);
    mysqli_stmt_execute($stmtDel);

    if (!empty($bahanList) && is_array($bahanList)) {
        $stmtIns = mysqli_prepare($conn, "INSERT INTO bahan (id_resep, nama_bahan, takaran) VALUES (?, ?, ?)");
        
        foreach ($bahanList as $b) {
            $nama = $b['nama_bahan'] ?? '';
            $takaran = $b['takaran'] ?? '';
            if (!empty($nama)) {
                mysqli_stmt_bind_param($stmtIns, "iss", $id_resep, $nama, $takaran);
                mysqli_stmt_execute($stmtIns);
            }
        }
    }

    mysqli_commit($conn);
    echo json_encode(['message' => 'Resep berhasil diperbarui']);

} catch (Exception $e) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>