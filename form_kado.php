<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login-form.php');
    exit;
}

// Ambil daftar animasi dari database
$pdo = getKoneksiPDO();
$stmta = $pdo->query("SELECT id_animasi, deskripsi, tipe_file FROM animasi ORDER BY upload_date DESC");
$animasiList = $stmta->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Buat Kado Virtual - Virtual Gift Box</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f8f9fa;
        }
        .radio-group label,
        .checkbox-group label {
            margin-right: 15px;
            display: inline-flex;
            align-items: center;
        }
        .radio-group input[type="radio"],
        .checkbox-group input[type="checkbox"] {
            margin-right: 5px;
            accent-color: #ec4899;
        }
    </style>
</head>
<body class="py-8 px-4">
    <div class="container mx-auto max-w-2xl bg-white p-8 md:p-10 rounded-xl shadow-2xl">
        <div class="text-center mb-10">
            <a href="index.php" class="flex items-center justify-center mb-4">
                <!-- logo -->
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Formulir Kado Virtual</h1>
            <p class="text-gray-600 mt-2">
                Anda login sebagai: <span class="font-semibold text-pink-500"><?= htmlspecialchars($_SESSION['username']) ?></span>
            </p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="mb-4 p-3 rounded-md text-sm bg-red-100 text-red-700 text-center">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form action="transaction.php" method="post" class="space-y-8">
            <!-- Informasi Penerima -->
            <fieldset class="border border-gray-300 p-6 rounded-lg">
                <legend class="text-lg font-semibold text-pink-600 px-2">Informasi Penerima</legend>
                <div class="space-y-4 mt-4">
                    <div>
                        <label for="recipientName" class="block text-sm font-medium text-gray-700">Nama Penerima:</label>
                        <input type="text" id="recipientName" name="recipient_name" required
                               class="mt-1 block w-full px-3 py-2 border rounded-md focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                               placeholder="Nama Penerima Kado" />
                    </div>
                    <div>
                        <label for="recipientEmail" class="block text-sm font-medium text-gray-700">Email Penerima:</label>
                        <input type="email" id="recipientEmail" name="recipient_email" required
                               class="mt-1 block w-full px-3 py-2 border rounded-md focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                               placeholder="email@penerima.com" />
                    </div>
                    <div>
                        <label for="relationship" class="block text-sm font-medium text-gray-700">Hubungan:</label>
                        <select id="relationship" name="relationship" required
                                class="mt-1 block w-full px-3 py-2 border bg-white rounded-md focus:ring-pink-500 focus:border-pink-500 sm:text-sm">
                            <option value="">Pilih Hubungan</option>
                            <option value="teman">Teman</option>
                            <option value="keluarga">Keluarga</option>
                            <option value="pasangan">Pasangan</option>
                            <option value="rekan">Rekan Kerja</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <!-- Informasi Kado -->
            <fieldset class="border border-gray-300 p-6 rounded-lg">
                <legend class="text-lg font-semibold text-pink-600 px-2">Informasi Kado</legend>
                <div class="space-y-4 mt-4">

                    <!-- Jenis Kado -->
                    <div>
                        <label for="giftType" class="block text-sm font-medium text-gray-700">Jenis Kado:</label>
                        <select id="giftType" name="gift_type" required
                                class="mt-1 block w-full px-3 py-2 border bg-white rounded-md focus:ring-pink-500 focus:border-pink-500 sm:text-sm">
                            <option value="">Pilih Jenis</option>
                            <option value="ucapan">Kartu Ucapan Digital</option>
                            <option value="voucher">Voucher Digital</option>
                            <option value="musik">Playlist Musik</option>
                            <option value="album">Album Foto Digital</option>
                            <option value="video">Video Ucapan</option>
                        </select>
                    </div>

                    <!-- Kategori Kado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori:</label>
                        <div class="mt-2 flex space-x-4 radio-group">
                            <label><input type="radio" name="gift_category" value="ulangTahun" required/> Ulang Tahun</label>
                            <label><input type="radio" name="gift_category" value="anniversary"/> Anniversary</label>
                            <label><input type="radio" name="gift_category" value="wisuda"/> Wisuda</label>
                            <label><input type="radio" name="gift_category" value="lainnya"/> Lainnya</label>
                        </div>
                    </div>

                    <!-- Tema Kado -->
                    <div>
                        <label for="giftTheme" class="block text-sm font-medium text-gray-700">Tema (opsional):</label>
                        <select id="giftTheme" name="gift_theme"
                                class="mt-1 block w-full px-3 py-2 border bg-white rounded-md focus:ring-pink-500 focus:border-pink-500 sm:text-sm">
                            <option value="">Pilih Tema</option>
                            <option value="elegant">Elegant</option>
                            <option value="cute">Cute</option>
                            <option value="formal">Formal</option>
                            <option value="vintage">Vintage</option>
                            <option value="minimalis">Minimalis</option>
                        </select>
                    </div>

                    <!-- Pilih Animasi -->
                    <div>
                        <label for="animation" class="block text-sm font-medium text-gray-700">Pilih Animasi:</label>
                        <select id="animation" name="animation_id"
                                class="mt-1 block w-full px-3 py-2 border bg-white rounded-md focus:ring-pink-500 focus:border-pink-500 sm:text-sm">
                            <option value="">Tanpa Animasi</option>
                            <?php foreach($animasiList as $a): ?>
                                <option value="<?= $a['id_animasi'] ?>">
                                    <?= htmlspecialchars($a['deskripsi']) ?> (<?= strtoupper($a['tipe_file']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Pesan Personal -->
                    <div>
                        <label for="messageText" class="block text-sm font-medium text-gray-700">Pesan Personal:</label>
                        <textarea id="messageText" name="message_text" rows="4"
                                  class="mt-1 block w-full px-3 py-2 border rounded-md focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                                  placeholder="Tulis pesan..."></textarea>
                    </div>
                </div>
            </fieldset>

            <!-- Tombol -->
            <div class="flex justify-end space-x-4 pt-4">
                <a href="index.php"
                   class="px-6 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit"
                        class="px-6 py-2 bg-pink-500 text-white rounded-md hover:bg-pink-600">Lanjut ke Pembayaran</button>
            </div>
        </form>
    </div>
</body>
</html>
