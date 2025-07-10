<?php
session_start();
require_once 'db_connection.php';

// Pastikan koneksi database tersedia
$pdo = getKoneksiPDO();

// Ambil semua animasi
$stmt = $pdo->query("SELECT * FROM animasi ORDER BY upload_date DESC");
$animasiList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>List Animasi</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fffafc;
            color: #444;
            margin: 0;
            padding: 0;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f9a8d4;
            padding: 15px 30px;
            color: white;
        }

        .top-bar h2 {
            margin: 0;
        }

        .logout-btn {
            background-color: #f87171;
            color: white;
            font-weight: bold;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
        }

        .logout-btn:hover {
            background-color: #ef4444;
        }

        .container {
            width: 95%;
            max-width: 960px;
            margin: 30px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #f3d3e3;
            text-align: center;
        }

        th {
            background-color: #f9a8d4;
            color: white;
        }

        .action-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }

        .edit {
            background-color: #34d399;
            color: white;
        }

        .delete {
            background-color: #f87171;
            color: white;
        }

        .preview-container {
            max-width: 150px;
            max-height: 100px;
            margin: auto;
        }

        .preview-container lottie-player, 
        .preview-container img,
        .preview-container video {
            max-width: 100%;
            max-height: 100px;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 14px;
            background-color: #f9a8d4;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #ec4899;
        }
    </style>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body>

<div class="top-bar">
    <h2>Daftar Animasi Kado</h2>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="container">

    <a href="admin_upload_animasi.php" class="back-btn">Upload Animasi Baru</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Preview</th>
                <th>Deskripsi</th>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($animasiList as $animasi): ?>
            <tr>
                <td><?= htmlspecialchars($animasi['id_animasi']) ?></td>
                <td>
                    <div class="preview-container">
                        <?php
                        $ext = strtolower(pathinfo($animasi['nama_file'], PATHINFO_EXTENSION));
                        $path = 'animasi/' . $animasi['nama_file'];

                        if ($ext === 'json') {
                            echo "<lottie-player src='$path' background='transparent' speed='1' loop autoplay></lottie-player>";
                        } elseif ($ext === 'gif') {
                            echo "<img src='$path' alt='GIF preview'>";
                        } elseif ($ext === 'mp4' || $ext === 'webm') {
                            echo "<video src='$path' autoplay loop muted playsinline></video>";
                        } else {
                            echo "Tidak didukung";
                        }
                        ?>
                    </div>
                </td>
                <td><?= htmlspecialchars($animasi['deskripsi']) ?></td>
                <td><?= htmlspecialchars($animasi['tipe_file']) ?></td>
                <td><?= htmlspecialchars($animasi['upload_date']) ?></td>
                <td>
                    <a href="admin_edit_animasi.php?id=<?= $animasi['id_animasi'] ?>" class="action-btn edit">Edit</a>
                    <a href="admin_delete_animasi.php?id=<?= $animasi['id_animasi'] ?>" class="action-btn delete" onclick="return confirm('Yakin ingin hapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
