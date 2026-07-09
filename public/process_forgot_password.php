<?php
require_once __DIR__ . '/../core/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    exit;
}

$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$newPassword = trim($data['new_password'] ?? '');

if (empty($username) || empty($email) || empty($newPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'Semua kolom harus diisi!']);
    exit;
}

try {
    $db = new Database();

    // 1. Cek apakah user dengan username dan email tersebut ada
    $db->query("SELECT id FROM users WHERE username = :username AND email = :email LIMIT 1");
    $db->bind(':username', $username);
    $db->bind(':email', $email);
    $result = $db->resultSet();

    if (empty($result)) {
        echo json_encode(['status' => 'error', 'message' => 'Username atau Email salah / tidak cocok!']);
        exit;
    }

    $userId = $result[0]['id'];

    // 2. Hash password baru
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // 3. Update password di database
    $db->query("UPDATE users SET password = :password WHERE id = :id");
    $db->bind(':password', $hashedPassword);
    $db->bind(':id', $userId);

    if ($db->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Password berhasil diubah! Mengalihkan ke login...']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui password di database.']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
}
exit;
