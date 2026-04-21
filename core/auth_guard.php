<?php

/**
 * Auth Guard - Middleware Autentikasi
 * 
 * Sertakan file ini di awal setiap halaman yang dilindungi.
 * Jika user belum login, akan langsung diredirect ke halaman login.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /views/auth/login.php");
    exit;
}
