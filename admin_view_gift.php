<?php
session_start();
require_once 'db_connection.php';

// Proteksi halaman: hanya admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login-form.php');
    exit;
}

$pdo = getKoneksiPDO();

// Pastikan ada parameter ID
if (!isset($_GET['id'])) {
    die('ID kado tidak ditemukan.');
}

$id = (int)$_GET['id'];

// Ambil detail kado
$sql = "
    SELECT vg.*, u.username AS pengirim
      FROM virtual_gifts vg
      JOIN users u ON vg.id_pengirim = u.id
    WHERE vg.id_kado = :id
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$gift = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$gift) {
    die('Kado tidak ditemukan.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kado #<?= htmlspecialchars($gift['kode_kado']) ?> - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 min-h-screen">
    <nav class="bg-pink-600 p-4 text-white flex justify-between">
        <div class="font-bold">Detail Kado</div>
        <div class="space-x-4">
            <a href="admin_list_gift.php" class="bg-white text-pink-600 px-3 py-1 rounded">Kembali</a>
            <a href="admin_dashboard.php" class="hover:underline">Dashboard</a>
            <a href="logout.php" class="bg-red-500 px-3 py-1 rounded">Logout</a>
        </div>
    </nav>
    <main class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-semibold text-pink-600 mb-4">Kode: <?= htmlspecialchars($gift['kode_kado']) ?></h1>
            <p><strong>Penerima:</strong> <?= htmlspecialchars($gift['nama_penerima']) ?> (<?= htmlspecialchars($gift['email_penerima']) ?>)</p>
            <p><strong>Pengirim:</strong> <?= htmlspecialchars($gift['pengirim']) ?></p>
            <p><strong>Hubungan:</strong> <?= htmlspecialchars($gift['hubungan_penerima']) ?></p>
            <p><strong>Jenis Kado:</strong> <?= htmlspecialchars($gift['gift_type']) ?></p>
            <p><strong>Kategori:</strong> <?= htmlspecialchars($gift['gift_category']) ?></p>
            <?php if ($gift['gift_theme']): ?>
                <p><strong>Tema:</strong> <?= htmlspecialchars($gift['gift_theme']) ?></p>
            <?php endif; ?>
            <?php if ($gift['message_personal']): ?>
                <div class="mt-4 p-4 bg-pink-50 border-l-4 border-pink-300"><strong>Pesan:</strong><br><?= nl2br(htmlspecialchars($gift['message_personal'])) ?></div>
            <?php endif; ?>
            <p class="mt-4"><strong>Status:</strong> <span class="capitalize"><?= htmlspecialchars($gift['status_kado']) ?></span></p>
            <p><strong>Tanggal Dibuat:</strong> <?= (new DateTime($gift['tanggal_dibuat']))->format('d M Y, H:i') ?></p>
            <?php if ($gift['dibuka_pada']): ?>
                <p><strong>Dibuka pada:</strong> <?= (new DateTime($gift['dibuka_pada']))->format('d M Y, H:i') ?></p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
