<?php
session_start();
require_once 'db_connection.php';

// Proteksi halaman: hanya admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login-form.php');
    exit;
}

$pdo = getKoneksiPDO();

// Ambil data kado berdasarkan ID
if (!isset($_GET['id'])) {
    header('Location: admin_list_gift.php');
    exit;
}

$id_kado = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM virtual_gifts WHERE id_kado = ?");
$stmt->execute([$id_kado]);
$gift = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$gift) {
    echo "Kado tidak ditemukan.";
    exit;
}

// Tangani form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_penerima = $_POST['nama_penerima'] ?? '';
    $email_penerima = $_POST['email_penerima'] ?? '';
    $status_kado = $_POST['status_kado'] ?? 'pending';

    $stmt = $pdo->prepare("UPDATE virtual_gifts SET nama_penerima=?, email_penerima=?, status_kado=? WHERE id_kado=?");
    $stmt->execute([$nama_penerima, $email_penerima, $status_kado, $id_kado]);

    header("Location: admin_list_gift.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 min-h-screen">
    <nav class="bg-pink-600 text-white p-4 flex justify-between">
        <div class="text-xl font-bold">Edit Kado</div>
        <div class="space-x-4">
            <a href="admin_dashboard.php" class="bg-white text-pink-600 px-3 py-1 rounded">Dashboard</a>
            <a href="admin_list_gift.php" class="hover:underline">Daftar Kado</a>
            <a href="logout.php" class="bg-red-500 px-3 py-1 rounded">Logout</a>
        </div>
    </nav>

    <main class="max-w-xl mx-auto py-8 px-4 bg-white mt-6 rounded shadow">
        <h1 class="text-xl font-semibold text-pink-600 mb-4">Edit Data Kado</h1>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm">Nama Penerima</label>
                <input type="text" name="nama_penerima" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($gift['nama_penerima']) ?>">
            </div>
            <div>
                <label class="block text-sm">Email Penerima</label>
                <input type="email" name="email_penerima" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($gift['email_penerima']) ?>">
            </div>
            <div>
                <label class="block text-sm">Status Kado</label>
                <select name="status_kado" class="w-full border rounded px-3 py-2">
                    <option value="pending" <?= $gift['status_kado'] === 'pending' ? 'selected' : '' ?>>Belum Dibayar</option>
                    <option value="paid" <?= $gift['status_kado'] === 'paid' ? 'selected' : '' ?>>Sudah Dibayar</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </main>
</body>
</html>
