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

// Mulai transaksi
mysqli_autocommit($conn, FALSE);

try {
    // Hapus resep terkait
    $stmt = mysqli_prepare($conn, "DELETE FROM resep WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    
    // Hapus bahan terkait (jika ada foreign key)
    $stmt = mysqli_prepare($conn, "DELETE FROM bahan WHERE id_resep NOT IN (SELECT id_resep FROM resep)");
    mysqli_stmt_execute($stmt);
    
    // Hapus user
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    
    if (mysqli_stmt_execute($stmt)) {
        // Commit transaksi
        mysqli_commit($conn);
        echo json_encode(['message' => 'Akun berhasil dihapus']);
    } else {
        throw new Exception("Gagal menghapus akun");
    }
    
} catch (Exception $e) {
    // Rollback transaksi
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menghapus akun: ' . $e->getMessage()]);
}

mysqli_autocommit($conn, TRUE);
?>