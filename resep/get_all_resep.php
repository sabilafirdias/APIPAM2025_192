<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$id_user_login = $_GET['id_user'] ?? 0;

$query = "SELECT r.*, u.username, 
          IF(b.id_user IS NULL, 0, 1) as is_bookmarked
          FROM resep r 
          JOIN users u ON r.id_user = u.id_user 
          LEFT JOIN bookmark b ON r.id_resep = b.id_resep AND b.id_user = ?
          ORDER BY r.created_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_user_login);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$resepList = [];
while ($row = mysqli_fetch_assoc($result)) {
    $id_resep = $row['id_resep'];

    $queryBahan = "SELECT nama_bahan, takaran FROM bahan WHERE id_resep = $id_resep";
    $resultBahan = mysqli_query($conn, $queryBahan);

    $bahanList = [];
    if ($resultBahan) {
        while ($b = mysqli_fetch_assoc($resultBahan)) {
            $bahanList[] = [
                "nama_bahan" => (string)$b['nama_bahan'],
                "takaran" => (string)$b['takaran']
            ];
        }
    }
    $row['bahan'] = $bahanList;
    $row['id_resep'] = (int)$row['id_resep'];
    $row['id_user'] = (int)$row['id_user'];
    $row['is_bookmarked'] = (int)$row['is_bookmarked'];
    $resepList[] = $row;
}

echo json_encode($resepList);
?>