<?php
include_once "../konek.php";
header('Content-Type: application/json');

$id_resep = $_GET['id_resep'] ?? 0;

$query = "SELECT k.*, u.username 
          FROM komentar k 
          JOIN users u ON k.id_user = u.id_user 
          WHERE k.id_resep = ? 
          ORDER BY k.created_at ASC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_resep);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$listKomentar = [];
while ($row = mysqli_fetch_assoc($result)) {
    $listKomentar[] = $row;
}
echo json_encode($listKomentar);
?>