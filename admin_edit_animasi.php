<?php
require_once 'db_connection.php';

$pdo = getKoneksiPDO(); // Pastikan fungsi ini benar-benar ada di db_connection.php

if (!isset($_GET['id'])) {
    die("ID animasi tidak ditemukan.");
}

$id = $_GET['id'];

// Ambil data animasi dari database
$stmt = $pdo->prepare("SELECT * FROM animasi WHERE id_animasi = :id");
$stmt->execute(['id' => $id]);
$animasi = $stmt->fetch();

if (!$animasi) {
    die("Data animasi tidak ditemukan.");
}

// Ekstensi file lama
$ext = pathinfo($animasi['nama_file'], PATHINFO_EXTENSION);

// Proses update jika form disubmit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $deskripsi = $_POST['deskripsi'];
    $nama_baru = pathinfo($_POST['nama_file'], PATHINFO_FILENAME); // hanya nama tanpa ekstensi
    $nama_file_final = $nama_baru . '.' . $ext;

    $update = $pdo->prepare("UPDATE animasi SET nama_file = :nama_file, deskripsi = :deskripsi WHERE id_animasi = :id");
    $update->execute([
        'nama_file' => $nama_file_final,
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.9.6/lottie.min.js"></script>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white p-6 rounded shadow-md max-w-xl mx-auto">
            <h1 class="text-2xl font-bold text-pink-600 mb-4">Edit Animasi</h1>

            <!-- Preview Animasi -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Preview Animasi</label>
                <?php
                    $file = $animasi['nama_file'];
                    $tipe = strtolower($animasi['tipe_file']);
                    $path = "animasi/" . $file;

                    if (!file_exists($path)) {
                        echo "<p class='text-red-600'>File tidak ditemukan: $file</p>";
                    } else {
                        if ($tipe === "gif") {
                            echo "<img src='$path' alt='Preview GIF' class='w-full h-auto rounded border'>";
                        } elseif ($tipe === "mp4") {
                            echo "<video src='$path' controls class='w-full h-auto rounded border'></video>";
                        } elseif ($tipe === "json") {
                            echo "<div id='lottie-preview' class='w-full h-64 border rounded'></div>";
                            echo "<script>
                                lottie.loadAnimation({
                                    container: document.getElementById('lottie-preview'),
                                    renderer: 'svg',
                                    loop: true,
                                    autoplay: true,
                                    path: '$path'
                                });
                            </script>";
                        } else {
                            echo "<p class='text-gray-600'>Tipe file tidak dikenali untuk preview.</p>";
                        }
                    }
                ?>
            </div>

            <!-- Form Edit -->
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Nama File (tanpa ekstensi)</label>
                    <input type="text" name="nama_file"
                        value="<?= htmlspecialchars(pathinfo($animasi['nama_file'], PATHINFO_FILENAME)) ?>"
                        class="w-full px-4 py-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" class="w-full px-4 py-2 border rounded" required><?= htmlspecialchars($animasi['deskripsi']) ?></textarea>
                </div>
                <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Simpan Perubahan</button>
                <a href="admin_list_animasi.php" class="ml-4 text-gray-600 hover:underline">Batal</a>
            </form>
        </div>
    </div>

</body>
</html>
