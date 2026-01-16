<?php
// Mencegah pesan error mentah merusak format JSON
error_reporting(0);
ini_set('display_errors', 0);

// Path ke konek.php (berdasarkan struktur folder yang Anda kirim)
include_once '../konek.php'; 

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

// Pastikan variabel $conn sesuai dengan yang ada di konek.php
if (!isset($conn)) {
    http_response_code(500);
    echo json_encode(array("message" => "Variabel koneksi database tidak ditemukan."));
    exit;
}

if (isset($_GET['id_user'])) {
    $id_user = $_GET['id_user'];
    
    try {
        // PERBAIKAN: Menggunakan tabel 'users' bukan 'user'
        $query = "SELECT r.*, u.username 
                  FROM resep r 
                  JOIN users u ON r.id_user = u.id_user 
                  WHERE r.id_user = ? 
                  ORDER BY r.created_at DESC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();

        $resep_arr = array();

        while ($row = $result->fetch_assoc()) {
            $resep_arr[] = array(
                "id_resep" => (int)$row['id_resep'],
                "id_user" => (int)$row['id_user'],
                "judul" => $row['judul'],
                "langkah" => $row['langkah'],
                "catatan" => $row['catatan'],
                "kategori" => $row['kategori'],
                "created_at" => $row['created_at'],
                "updated_at" => $row['updated_at'],
                "username" => $row['username'],
                "bahan" => array()
            );
        }

        // Jika berhasil, kirim data (meskipun array kosong jika resep tidak ada)
        http_response_code(200);
        echo json_encode($resep_arr);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Query Error: " . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Parameter id_user diperlukan."));
}
?>