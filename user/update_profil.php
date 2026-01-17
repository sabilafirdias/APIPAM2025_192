<?php
include_once "../konek.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$id_user = $data['id_user'] ?? null;
$username = $data['username'] ?? null;
$email = $data['email'] ?? null;
$old_password = $data['old_password'] ?? null;
$new_password = $data['new_password'] ?? null;

if (empty($id_user) || empty($username) || empty($email)) {
    http_response_code(400);
    echo json_encode(['error' => 'Data wajib diisi']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id_user FROM users WHERE (username = ? OR email = ?) AND id_user != ?");
mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $existing = mysqli_fetch_assoc($result);
    if ($existing['username'] == $username) {
        http_response_code(409);
        echo json_encode(['error' => 'Username sudah dipakai']);
        exit;
    }
    if ($existing['email'] == $email) {
        http_response_code(409);
        echo json_encode(['error' => 'Email sudah terdaftar']);
        exit;
    }
}

if ($old_password !== null || $new_password !== null) {
    $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    if (!password_verify($old_password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Password lama salah']);
        exit;
    }
    
    if (empty($new_password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Password baru wajib diisi']);
        exit;
    }
    
    if (strlen($new_password) < 8) {
        http_response_code(400);
        echo json_encode(['error' => 'Password minimal 8 karakter']);
        exit;
    }
    
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, email = ?, password = ? WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $hashed_password, $id_user);
} else {
    $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, email = ? WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $id_user);
}

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['message' => 'Profil berhasil diperbarui']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal memperbarui profil']);
}
?>