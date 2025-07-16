<?php
session_start();
require_once 'db_connection.php';

// Proteksi halaman: hanya admin yang bisa mengakses
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login-form.php');
    exit;
}

$pdo = getKoneksiPDO();
$message = '';
$message_type = ''; // Untuk membedakan notifikasi sukses atau error

// Pastikan admin_id ada di sesi saat login
$uploaded_by = $_SESSION['admin_id'] ?? null; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deskripsi = $_POST['deskripsi'] ?? '';

    if (!$uploaded_by) {
        $message = "Error: Sesi admin tidak ditemukan. Silakan login kembali.";
        $message_type = 'error';
    } elseif (isset($_FILES['file_animasi']) && $_FILES['file_animasi']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['file_animasi']['tmp_name'];
        $fileName = basename($_FILES['file_animasi']['name']);
        
        // Dapatkan tipe MIME file yang sebenarnya
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $fileTmp);
        finfo_close($finfo);

        // Validasi jenis file yang diizinkan (JSON, GIF, MP4, WebM)
        $allowedTypes = [
            'application/json' => 'json',
            'image/gif' => 'gif',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm'
        ];

        if (array_key_exists($fileType, $allowedTypes)) {
            $tipe_file = $allowedTypes[$fileType];

            // Buat nama file yang unik untuk menghindari konflik
            $fileNameSafe = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
            $targetPath = 'animasi/' . $fileNameSafe;

            // Pastikan direktori 'animasi' ada dan bisa ditulis
            if (!is_dir('animasi')) {
                mkdir('animasi', 0755, true);
            }

            if (move_uploaded_file($fileTmp, $targetPath)) {
                // Simpan informasi file ke database
                $stmt = $pdo->prepare("INSERT INTO animasi (nama_file, deskripsi, tipe_file, uploaded_by, upload_date) 
                                        VALUES (:nama_file, :deskripsi, :tipe_file, :uploaded_by, NOW())");
                $stmt->execute([
                    'nama_file' => $fileNameSafe,
                    'deskripsi' => $deskripsi,
                    'tipe_file' => $tipe_file,
                    'uploaded_by' => $uploaded_by
                ]);
                $message = "Animasi berhasil diunggah!";
                $message_type = 'success';
            } else {
                $message = "Gagal memindahkan file yang diunggah. Periksa izin folder.";
                $message_type = 'error';
            }
        } else {
            $message = "Jenis file tidak didukung. Hanya file JSON (Lottie), GIF, MP4, dan WebM yang diperbolehkan.";
            $message_type = 'error';
        }
    } else {
        $message = "Tidak ada file yang dipilih atau terjadi kesalahan saat proses unggah.";
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Upload Animasi</title>
    <!-- Memuat Tailwind CSS dari CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 min-h-screen font-sans">
    <!-- Navigasi Bar yang Konsisten -->
    <nav class="bg-pink-600 text-white p-4 flex justify-between items-center shadow-md">
        <div class="text-xl font-bold">Upload Animasi</div>
        <div class="flex items-center space-x-4">
            <a href="admin_dashboard.php" class="hover:underline">Dashboard</a>
            <a href="admin_list_gift.php" class="hover:underline">Kado</a>
            <a href="admin_list_animasi.php" class="hover:underline">Animasi</a>
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded-md font-semibold">Logout</a>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="container mx-auto py-8 px-4">
        <!-- Kartu Formulir -->
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-pink-700">Formulir Upload Animasi</h1>
                <a href="admin_list_animasi.php" class="text-pink-600 hover:text-pink-800 font-semibold">
                    &larr; Kembali ke Daftar
                </a>
            </div>

            <!-- Tampilkan notifikasi jika ada -->
            <?php if ($message): ?>
                <div class="p-4 mb-4 rounded-md text-sm <?php echo $message_type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Formulir Upload -->
            <form method="post" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="file_animasi" class="block text-sm font-medium text-gray-700 mb-1">
                        File Animasi (JSON, GIF, MP4, WebM)
                    </label>
                    <input type="file" name="file_animasi" id="file_animasi" required
                            class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-pink-100 file:text-pink-700
                                    hover:file:bg-pink-200">
                    <p class="mt-1 text-xs text-gray-500">Ukuran file maksimal: 2MB.</p>
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi Animasi
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" required
                                placeholder="Contoh: Animasi konfeti meriah untuk ulang tahun"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500"></textarea>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent
                                    rounded-md shadow-sm text-sm font-medium text-white bg-pink-600
                                    hover:bg-pink-700 focus:outline-none focus:ring-2
                                    focus:ring-offset-2 focus:ring-pink-500 transition-colors">
                        Unggah Animasi
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>