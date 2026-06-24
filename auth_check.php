<?php
// auth_check.php - Validasi keamanan session admin
// Memastikan file ini dipanggil setelah config.php yang sudah menjalankan session_start()

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Jika belum login, redirect ke halaman login
    header("Location: login.php");
    exit;
}
?>
