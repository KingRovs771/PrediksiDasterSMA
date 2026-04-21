<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Users.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $db = new Database();
        $this->userModel = new Users($db);
    }

    public function login()
    {
        header('Content-Type: application/json');

        // Mulai session di awal agar selalu tersedia
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            exit;
        }

        // Ambil data JSON jika dikirim via payload fetch, atau POST biasa
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($username) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Username dan password harus diisi!']);
            exit;
        }

        $user = $this->userModel->getUserByUsername($username);

        // Verifikasi password menggunakan password_verify untuk keamanan hash bcrypt
        if ($user && password_verify($password, $user['password'])) {
            // Regenerasi ID untuk menghindari Session Fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;

            echo json_encode(['status' => 'success', 'message' => 'Login berhasil! Mengalihkan ke dashboard...']);
        } else {
            // Pesan error umum agar tidak membocorkan apakah username ada atau password salah
            echo json_encode(['status' => 'error', 'message' => 'Username atau password salah!']);
        }
        exit;
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header("Location: /views/auth/login.php");
        exit;
    }
}
