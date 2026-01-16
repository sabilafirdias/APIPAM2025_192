<?php
include_once "../konek.php";

header('Access-Control-Allow-Origin: *');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$username = $data['username'];
$email    = $data['email'];
$password = $data['password'];

class emp{}

if (empty($username) || empty($email) || empty($password)) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'Data tidak boleh kosong']);
    die();
} else {
    // Hash password sebelum disimpan
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $query = mysqli_query($conn, "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')");

    if (mysqli_affected_rows($conn) > 0) {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Data berhasil ditambahkan']);
    } else {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Data tidak ditemukan']);
    }
}
?>