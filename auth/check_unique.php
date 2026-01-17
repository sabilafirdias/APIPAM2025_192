<?php
header("Content-Type: application/json");
include "../konek.php";

$username = $_GET['username'] ?? '';
$email = $_GET['email'] ?? '';

$response = [
    "email_exists" => false,
    "username_exists" => false
];

$stmt = $conn->prepare("SELECT id_user FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $response["email_exists"] = true;
}

$stmt = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $response["username_exists"] = true;
}

echo json_encode($response);
