<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "resepapp";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Koneksi gagal: ' . $conn->connect_error]));
}

// Set charset UTF-8
$conn->set_charset("utf8");
?>