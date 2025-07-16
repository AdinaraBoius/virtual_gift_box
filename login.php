<?php
session_start();
require_once 'db_connection.php';

// 1. Jika sudah login, langsung kirim ke halaman sesuai role
if (isset($_SESSION['loggedin'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

// 2. Proses hanya jika POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // 2a. Validasi email & password
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: login-form.php?error=Email tidak valid');
        exit;
    }
    if (empty($password)) {
        header('Location: login-form.php?error=Password wajib diisi');
        exit;
    }

    $pdo = getKoneksiPDO();

    // 3. Cek di tabel admin
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        // Login admin
        session_regenerate_id(true);
        $_SESSION['loggedin'] = true;
        $_SESSION['role']     = 'admin';
        $_SESSION['admin_id'] = $admin['id_admin'];
        $_SESSION['username'] = $admin['username'];
        header('Location: admin_dashboard.php');
        exit;
    }

    // 4. Kalau bukan admin, cek di tabel users
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Login user
        session_regenerate_id(true);
        $_SESSION['loggedin'] = true;
        $_SESSION['role']     = 'user';
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    }

    // 5. Gagal login
    header('Location: login-form.php?error=Email atau password salah');
    exit;
}

// 6. Jika akses langsung (GET), kirim ke form
header('Location: login-form.php');
exit;
