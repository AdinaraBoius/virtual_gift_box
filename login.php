<?php
session_start();
require_once 'db_connection.php';

if (isset($_SESSION['loggedin'])) {
    // Redirect ke halaman yang sesuai role
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_list_animasi.php');
    } elseif ($_SESSION['role'] === 'user') {
        header('Location: dashboard.php');
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header('Location: login-form.php?error=Email dan password wajib diisi');
        exit;
    }

    $pdo = getKoneksiPDO();

    // Coba cari di tabel admin dulu
    $stmtAdmin = $pdo->prepare("SELECT * FROM admin WHERE email = :email");
    $stmtAdmin->execute(['email' => $email]);
    $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        // Login sebagai admin
        session_regenerate_id(true);
        $_SESSION['loggedin']  = true;
        $_SESSION['role']      = 'admin';
        $_SESSION['admin_id']  = $admin['id_admin'];
        $_SESSION['username']  = $admin['username'];

        header('Location: admin_list_animasi.php');
        exit;
    }

    // Jika bukan admin, cek sebagai user
    $stmtUser = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmtUser->execute(['email' => $email]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Login sebagai user
        session_regenerate_id(true);
        $_SESSION['loggedin']  = true;
        $_SESSION['role']      = 'user';
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];

        header('Location: dashboard.php');
        exit;
    }

    // Jika gagal dua-duanya
    header('Location: login-form.php?error=Email atau password salah');
    exit;
} else {
    // Akses langsung tanpa POST
    header('Location: login-form.php');
    exit;
}