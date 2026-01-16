<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$keyword = $_GET['q'] ?? '';

if (empty($keyword)) {
    echo json_encode([]);
    exit;
}

$search = "%$keyword%";

// Query mencari resep berdasarkan judul r.judul ATAU nama bahan b.nama_bahan
$query = "SELECT DISTINCT r.*, u.username 
          FROM resep r 
          JOIN users u ON r.id_user = u.id_user 
          LEFT JOIN bahan b ON r.id_resep = b.id_resep 
          WHERE r.judul LIKE ? OR b.nama_bahan LIKE ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $search, $search);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$listResep = [];
while ($row = mysqli_fetch_assoc($result)) {
    $id_resep = $row['id_resep'];
    
    // Ambil detail bahan lengkap agar model ResepResponse (List<Bahan>) terpenuhi
    $resBahan = mysqli_query($conn, "SELECT nama_bahan, takaran FROM bahan WHERE id_resep = $id_resep");
    
    $bahanArray = [];
    while($b = mysqli_fetch_assoc($resBahan)) {
        $bahanArray[] = [
            "nama_bahan" => $b['nama_bahan'],
            "takaran" => $b['takaran']
        ];
    }
    
    // Sesuaikan field ini dengan nama di data class ResepResponse (val bahan: List<Bahan>)
    $row['bahan'] = $bahanArray;
    
    // Pastikan tipe data sesuai (is_bookmarked di model Anda mengharapkan Int)
    $row['is_bookmarked'] = 0; 
    
    $listResep[] = $row;
}

echo json_encode($listResep);
?>