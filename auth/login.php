<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$email    = $data['email'] ?? null;
$password = $data['password'] ?? null;

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email dan password wajib diisi']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id_user, username, email, password FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);
    if (password_verify($password, $user['password'])) {
        echo json_encode([
            'message' => 'Login berhasil',
            'user' => [
                'id_user' => (int)$user['id_user'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Password salah']);
    }
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Email tidak terdaftar']);
}
?>