<?php
session_start();
require_once 'db_connection.php'; // Memanggil file koneksi database Anda

// Jika pengguna sudah login, alihkan ke dashboard
if (isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

// Hanya proses jika request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // --- Validasi Sederhana ---
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: login-form.php?error=Email tidak valid');
        exit;
    }
    if (empty($username) || empty($email) || empty($password)) {
        header('Location: register-form.php?error=Data tidak lengkap');
        exit;
    }
    if ($password !== $confirmPassword) {
        header('Location: register-form.php?error=Password tidak cocok');
        exit;
    }
    if (strlen($password) < 8) {
        header('Location: register-form.php?error=Password minimal 8 karakter');
        exit;
    }

    // --- Cek apakah username atau email sudah ada ---
    $pdo = getKoneksiPDO();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmt->execute(['username' => $username, 'email' => $email]);
    if ($stmt->fetch()) {
        header('Location: register-form.php?error=Username atau email sudah terdaftar');
        exit;
    }

    // --- Hash Password (SANGAT PENTING UNTUK KEAMANAN) ---
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // --- Simpan ke Database ---
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);
        // Registrasi berhasil, arahkan ke halaman login dengan pesan sukses
        header('Location: login-form.php?success=Registrasi berhasil! Silakan masuk.');
        exit;
    } catch (PDOException $e) {
        // Jika ada error database
        header('Location: register-form.php?error=Terjadi kesalahan pada server');
        exit;
    }
} else {
    // Jika diakses langsung, redirect ke form
    header('Location: register-form.php');
    exit;
}
?>