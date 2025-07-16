<?php
session_start(); // Mulai sesi untuk mengaksesnya

// Hapus semua variabel sesi
$_SESSION = array();

// Hancurkan sesi
session_destroy();

// Arahkan kembali ke halaman login
header("Location: login-form.php");
exit;
?>