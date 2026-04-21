<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /views/auth/login.php");
    exit;
}

// Set page variable for routing
$page = $_GET['page'] ?? 'dashboard';

// Load view dashboard
require_once __DIR__ . '/../views/dashboard/index.php';
