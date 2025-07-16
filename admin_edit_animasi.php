<?php
session_start();
require_once 'db_connection.php';

// Proteksi halaman: hanya admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login-form.php');
    exit;
}

$pdo = getKoneksiPDO();

if (!isset($_GET['id'])) {
    die("ID animasi tidak ditemukan.");
}

$id = (int) $_GET['id'];

// Ambil data animasi dari database
$stmt = $pdo->prepare("SELECT * FROM animasi WHERE id_animasi = :id");
$stmt->execute(['id' => $id]);
$animasi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$animasi) {
    die("Data animasi tidak ditemukan.");
}

// Path file animasi
$file = $animasi['nama_file'];
$tipe = strtolower($animasi['tipe_file']);
$path = "animasi/" . $file;

// Proses update jika form disubmit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $deskripsi = trim($_POST['deskripsi']);

    $update = $pdo->prepare("UPDATE animasi SET deskripsi = :deskripsi WHERE id_animasi = :id");
    $update->execute([
        'deskripsi' => $deskripsi,
        'id' => $id
    ]);

    header("Location: admin_list_animasi.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Animasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lottie-web@5.10.0/build/player/lottie.min.js"></script>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white p-6 rounded shadow-md max-w-xl mx-auto">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-pink-600">Edit Animasi</h1>
                <a href="admin_list_animasi.php" class="text-gray-600 hover:underline">&larr; Kembali</a>
            </div>

            <!-- Preview Animasi -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Preview Animasi</label>
                <?php if (!file_exists($path)): ?>
                    <p class="text-red-600">File tidak ditemukan: <?= htmlspecialchars($file) ?></p>
                <?php else: ?>
                    <?php if ($tipe === 'gif'): ?>
                        <img src="<?= htmlspecialchars($path) ?>" alt="GIF preview" class="w-full h-auto rounded border">
                    <?php elseif ($tipe === 'mp4'): ?>
                        <video src="<?= htmlspecialchars($path) ?>" controls class="w-full h-auto rounded border"></video>
                    <?php elseif ($tipe === 'json'): ?>
                        <div id="lottie-preview" class="w-full h-64 border rounded"></div>
                        <script>
                            lottie.loadAnimation({
                                container: document.getElementById('lottie-preview'),
                                renderer: 'svg',
                                loop: true,
                                autoplay: true,
                                path: '<?= htmlspecialchars($path) ?>'
                            });
                        </script>
                    <?php else: ?>
                        <p class="text-gray-600">Tipe file tidak dikenali untuk preview.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Form Edit -->
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="w-full px-4 py-2 border rounded" required><?= htmlspecialchars($animasi['deskripsi']) ?></textarea>
                </div>
                <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Simpan Perubahan</button>
            </form>
        </div>
    </div>

</body>
</html>