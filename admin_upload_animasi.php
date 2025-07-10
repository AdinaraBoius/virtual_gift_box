<?php
session_start();
require_once 'db_connection.php';

$pdo = getKoneksiPDO();
$message = '';

// Cek apakah admin sudah login
$uploaded_by = $_SESSION['admin_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deskripsi = $_POST['deskripsi'] ?? '';

    if (!$uploaded_by) {
        $message = "Anda belum login sebagai admin.";
    } elseif (isset($_FILES['file_animasi']) && $_FILES['file_animasi']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['file_animasi']['tmp_name'];
        $fileName = basename($_FILES['file_animasi']['name']);
        $fileType = mime_content_type($fileTmp);

        // Validasi jenis file yang diizinkan
        $allowedTypes = [
            'application/json' => 'json',  // Lottie
            'image/gif' => 'gif',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm'
        ];

        if (array_key_exists($fileType, $allowedTypes)) {
            $tipe_file = $allowedTypes[$fileType];

            // Hindari nama file duplikat
            $fileNameSafe = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
            $targetPath = 'animasi/' . $fileNameSafe;

            if (move_uploaded_file($fileTmp, $targetPath)) {
                // Simpan ke database
                $stmt = $pdo->prepare("INSERT INTO animasi (nama_file, deskripsi, tipe_file, uploaded_by, upload_date) 
                                       VALUES (:nama_file, :deskripsi, :tipe_file, :uploaded_by, NOW())");
                $stmt->execute([
                    'nama_file' => $fileNameSafe,
                    'deskripsi' => $deskripsi,
                    'tipe_file' => $tipe_file,
                    'uploaded_by' => $uploaded_by
                ]);
                $message = "Animasi berhasil diunggah!";
            } else {
                $message = "Gagal memindahkan file.";
            }
        } else {
            $message = "Jenis file tidak didukung. Hanya file JSON (Lottie), GIF, MP4, dan WebM yang diperbolehkan.";
        }
    } else {
        $message = "Tidak ada file yang dipilih atau terjadi kesalahan saat upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Animasi Kado (Admin)</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fde6f0;
            color: #444;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            border: 2px solid #ec4899;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 10px rgba(240, 174, 123, 0.2);
            position: relative;
        }
        h2 {
            text-align: center;
            color: #ec4899;
        }
        label {
            font-weight: bold;
        }
        input[type="file"], textarea, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #ec4899;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #db2777;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            background-color: #fde6f0;
            border-left: 5px solid #ec4899;
        }
        .lihat-list-btn {
            position: absolute;
            right: 25px;
            top: 25px;
            background-color: #f9a8d4;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
        }
        .lihat-list-btn:hover {
            background-color: #ec4899;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_list_animasi.php" class="lihat-list-btn">Lihat List</a>

    <h2>Upload Animasi Kado</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="file_animasi">File Animasi (MP4 / WebM / GIF / JSON)</label>
        <input type="file" name="file_animasi" id="file_animasi" required>

        <label for="deskripsi">Deskripsi Animasi</label>
        <textarea name="deskripsi" id="deskripsi" rows="3" placeholder="Contoh: Animasi konfeti meriah" required></textarea>

        <button type="submit">Unggah Animasi</button>
    </form>
</div>

</body>
</html>
