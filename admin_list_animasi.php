<?php
session_start();
require_once 'db_connection.php';

// Proteksi halaman: hanya admin yang bisa mengakses
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login-form.php');
    exit;
}

// Pastikan koneksi database tersedia
$pdo = getKoneksiPDO();

// Ambil semua data animasi, diurutkan dari yang terbaru
$stmt = $pdo->query("SELECT * FROM animasi ORDER BY upload_date DESC");
$animasiList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Kelola Animasi</title>
    <!-- Memuat Tailwind CSS dari CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Memuat Lottie Player untuk animasi JSON -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body class="bg-pink-50 min-h-screen font-sans">
    <!-- Navigasi Bar yang Konsisten -->
    <nav class="bg-pink-600 text-white p-4 flex justify-between items-center shadow-md">
        <div class="text-xl font-bold">Kelola Animasi</div>
        <div class="flex items-center space-x-4">
            <a href="admin_dashboard.php" class="hover:underline">Dashboard</a>
            <a href="admin_list_gift.php" class="hover:underline">Kado</a>
            <!-- Menandai halaman aktif -->
            <a href="admin_list_animasi.php" class="bg-white text-pink-600 px-3 py-1 rounded-md font-semibold">Animasi</a>
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded-md font-semibold">Logout</a>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="container mx-auto py-8 px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-pink-700">Daftar Animasi Kado</h1>
            <a href="admin_upload_animasi.php" class="bg-pink-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-pink-700 transition-colors shadow">
                + Upload Animasi Baru
            </a>
        </div>

        <!-- Tabel Data Animasi -->
        <div class="bg-white rounded-lg shadow-lg overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-pink-100 text-pink-800 uppercase text-sm">
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Preview</th>
                        <th class="px-6 py-3 text-left">Nama Deskripsi</th>
                        <th class="px-6 py-3 text-left">Tipe File</th>
                        <th class="px-6 py-3 text-left">Tanggal Upload</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($animasiList)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">
                                Belum ada animasi yang di-upload.
                            </td>
                        </tr>
                    <?php else: foreach ($animasiList as $animasi): ?>
                    <tr class="hover:bg-pink-50 transition-colors">
                        <td class="px-6 py-4 align-middle font-semibold text-gray-700">
                            <?= htmlspecialchars($animasi['id_animasi']) ?>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <!-- Kontainer untuk preview dengan ukuran seragam -->
                            <div class="w-24 h-24 flex items-center justify-center bg-gray-100 rounded-md overflow-hidden">
                                <?php
                                $ext = strtolower(pathinfo($animasi['nama_file'], PATHINFO_EXTENSION));
                                $path = 'animasi/' . htmlspecialchars($animasi['nama_file']);

                                if ($ext === 'json') {
                                    echo "<lottie-player src='$path' class='max-w-full max-h-full' background='transparent' speed='1' loop autoplay></lottie-player>";
                                } elseif (in_array($ext, ['gif', 'jpg', 'jpeg', 'png', 'webp'])) {
                                    echo "<img src='$path' alt='Preview' class='max-w-full max-h-full object-contain'>";
                                } elseif (in_array($ext, ['mp4', 'webm'])) {
                                    echo "<video src='$path' autoplay loop muted playsinline class='max-w-full max-h-full'></video>";
                                } else {
                                    echo "<span class='text-xs text-gray-500 p-2 text-center'>Preview tidak didukung</span>";
                                }
                                ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle text-gray-800">
                            <?= htmlspecialchars($animasi['deskripsi']) ?>
                        </td>
                        <td class="px-6 py-4 align-middle font-mono text-sm text-gray-600">
                            <?= htmlspecialchars($animasi['tipe_file']) ?>
                        </td>
                        <td class="px-6 py-4 align-middle text-gray-600">
                            <?= (new DateTime($animasi['upload_date']))->format('d M Y, H:i') ?>
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
                            <!-- Tombol aksi yang konsisten -->
                            <div class="flex justify-center items-center space-x-2">
                                <a href="admin_edit_animasi.php?id=<?= $animasi['id_animasi'] ?>" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors text-sm font-semibold shadow-sm">Edit</a>
                                <a href="admin_delete_animasi.php?id=<?= $animasi['id_animasi'] ?>" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors text-sm font-semibold shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus animasi ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>