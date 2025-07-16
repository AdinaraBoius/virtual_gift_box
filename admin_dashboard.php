<?php
session_start();
require_once 'db_connection.php';

// Proteksi: hanya admin yang boleh akses
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login-form.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VirtualGift</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 min-h-screen">
    <nav class="bg-pink-600 text-white p-4 flex justify-between">
        <div class="text-xl font-bold">Admin Dashboard</div>
        <div class="space-x-4">
            <a href="admin_list_gift.php" class="hover:underline">Kelola Kado</a>
            <a href="admin_list_animasi.php" class="hover:underline">Kelola Animasi</a>
            <a href="logout.php" class="bg-white text-pink-600 px-3 py-1 rounded">Logout</a>
        </div>
    </nav>

    <main class="container mx-auto py-10 px-4">
        <h1 class="text-3xl font-semibold text-pink-600 mb-6">Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-2">CRUD Kado</h2>
                <p>Kelola semua kado virtual: lihat, ubah status, hapus.</p>
                <a href="admin_list_gift.php" class="mt-4 inline-block bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Buka Halaman</a>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-2">CRUD Animasi</h2>
                <p>Kelola animasi GIF, MP4, dan Lottie yang tersedia.</p>
                <a href="admin_list_animasi.php" class="mt-4 inline-block bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Buka Halaman</a>
            </div>
        </div>
    </main>
</body>
</html>
