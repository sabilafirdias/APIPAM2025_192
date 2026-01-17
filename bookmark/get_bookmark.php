<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// bookmark satu user
$id_user = $_GET['id_user'] ?? null;

if (empty($id_user)) {
    http_response_code(400);
    echo json_encode([]); 
    exit;
}

try {
    $query = "SELECT r.*, u.username 
              FROM resep r 
              JOIN bookmark b ON r.id_resep = b.id_resep 
              JOIN users u ON r.id_user = u.id_user 
              WHERE b.id_user = ? 
              ORDER BY r.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    $resep_arr = array();
    while ($row = $result->fetch_assoc()) {
        $id_resep = $row['id_resep'];
        
        $queryBahan = "SELECT nama_bahan, takaran FROM bahan WHERE id_resep = ?";
        $stmtBahan = $conn->prepare($queryBahan);
        $stmtBahan->bind_param("i", $id_resep);
        $stmtBahan->execute();
        $resBahan = $stmtBahan->get_result();
        
        $bahanList = [];
        while ($b = $resBahan->fetch_assoc()) {
            $bahanList[] = [
                "nama_bahan" => (string)$b['nama_bahan'],
                "takaran" => (string)$b['takaran']
            ];
        }
        $row['bahan'] = $bahanList; 
        
        $row['id_resep'] = (int)$row['id_resep'];
        $row['id_user'] = (int)$row['id_user'];
        $row['is_bookmarked'] = 1;
        
        $resep_arr[] = $row;
    }

    echo json_encode($resep_arr);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>