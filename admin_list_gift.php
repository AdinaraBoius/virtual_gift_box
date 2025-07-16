<?php
session_start();
require_once 'db_connection.php';

// Proteksi halaman: hanya admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login-form.php');
        exit;
}

$pdo = getKoneksiPDO();

// Ambil semua kado bersama username pengirim
$sql = "
    SELECT vg.id_kado, vg.kode_kado, vg.nama_penerima, vg.email_penerima, vg.status_kado, vg.tanggal_dibuat, u.username AS pengirim
        FROM virtual_gifts vg
        JOIN users u ON vg.id_pengirim = u.id
    ORDER BY vg.tanggal_dibuat DESC
";
$stmt = $pdo->query($sql);
$gifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Kelola Kado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 min-h-screen">
    <nav class="bg-pink-600 text-white p-4 flex justify-between items-center shadow-md">
        <div class="text-xl font-bold">Kelola Kado</div>
        <div class="flex items-center space-x-4">
            <a href="admin_dashboard.php" class="hover:underline">Dashboard</a>
            <a href="admin_list_animasi.php" class="hover:underline">Animasi</a>
            <!-- Menandai halaman aktif -->
            <a href="admin_list_gift.php" class="bg-white text-pink-600 px-3 py-1 rounded-md font-semibold">Kado</a>
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded-md font-semibold">Logout</a>
        </div>
    </nav>

    <main class="container mx-auto py-8 px-4">
        <h1 class="text-2xl font-semibold text-pink-600 mb-6">Daftar Kado Virtual</h1>
        <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
            <thead class="bg-pink-100 text-pink-800">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Kode Kado</th>
                    <th class="px-4 py-2">Penerima</th>
                    <th class="px-4 py-2">Pengirim</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($gifts)): ?>
                    <tr><td colspan="7" class="text-center py-6">Belum ada kado.</td></tr>
                <?php else: foreach ($gifts as $g): ?>
                <tr class="border-t">
                    <td class="px-4 py-2"><?= htmlspecialchars($g['id_kado']) ?></td>
                    <td class="px-4 py-2 font-mono text-pink-600"><?= htmlspecialchars($g['kode_kado']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($g['nama_penerima']) ?><br><small><?= htmlspecialchars($g['email_penerima']) ?></small></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($g['pengirim']) ?></td>
                    <td class="px-4 py-2 capitalize"><?= htmlspecialchars($g['status_kado']) ?></td>
                    <td class="px-4 py-2"><?= (new DateTime($g['tanggal_dibuat']))->format('d M Y, H:i') ?></td>
                    <td class="px-4 py-2 space-x-2">
                        <a href="admin_view_gift.php?id=<?= $g['id_kado'] ?>" class="px-2 py-1 bg-blue-400 text-white rounded">View</a>
                        <a href="admin_edit_gift.php?id=<?= $g['id_kado'] ?>" class="px-2 py-1 bg-green-400 text-white rounded">Edit</a>
                        <a href="admin_delete_gift.php?id=<?= $g['id_kado'] ?>" onclick="return confirm('Yakin ingin menghapus kado ini?')" class="px-2 py-1 bg-red-400 text-white rounded">Delete</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
