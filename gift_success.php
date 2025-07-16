<?php
session_start();

// Proteksi halaman: Pengguna harus sudah login
if (!isset($_SESSION['loggedin'])) {
    header('Location: login-form.php');
    exit;
}

// Ambil data kado dan username dari sesi/URL dengan aman
$kode_kado = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : 'KODE_ERROR';
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Pengguna';

// Tentukan URL dasar aplikasi Anda. Ganti jika sudah online.
// Ini penting agar QR code dan link menunjuk ke alamat yang benar.
$app_base_url = "http://" . $_SERVER['HTTP_HOST'] . "/virtual_gift_box"; // Contoh: http://localhost/virtual_gift_box
$gift_view_url = $app_base_url . "/view_gift_page.php?code=" . urlencode($kode_kado);

// URL untuk menghasilkan QR Code dari API eksternal
$qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($gift_view_url);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kado Berhasil Dibuat!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7fafc;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="max-w-lg w-full bg-white p-8 rounded-2xl shadow-xl text-center">
        
        <!-- Header -->
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Kado Telah Siap!</h1>
        <p class="text-gray-600 mb-6">Selamat, <?= $username ?>! Kado virtual Anda siap untuk dibagikan.</p>

        <!-- Kode Kado -->
        <div class="mb-6">
            <p class="text-sm text-gray-500 mb-2">Kode Kado Anda:</p>
            <div class="flex items-center justify-center gap-2 bg-gray-100 p-3 rounded-lg">
                <input id="giftCode" type="text" readonly value="<?= $kode_kado ?>" class="font-mono text-lg tracking-widest text-pink-600 bg-transparent border-none focus:ring-0 w-full text-center">
                <button onclick="copyToClipboard('giftCode', 'copyCodeStatus')" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded-md text-sm font-semibold">Salin</button>
            </div>
            <p id="copyCodeStatus" class="mt-2 text-green-600 text-xs hidden">Kode berhasil disalin!</p>
        </div>

        <!-- Opsi Berbagi: Link dan QR Code -->
        <div class="grid md:grid-cols-2 gap-6 items-center">
            
            <!-- Kolom Kiri: Link -->
            <div class="text-left">
                <label for="giftUrl" class="font-semibold text-gray-700 block mb-2">Bagikan via Tautan:</label>
                <div class="relative">
                    <input id="giftUrl" type="text" readonly value="<?= $gift_view_url ?>" class="w-full p-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-400 pr-20">
                    <button onclick="copyToClipboard('giftUrl', 'copyLinkStatus')" class="absolute inset-y-0 right-0 flex items-center px-4 bg-pink-500 text-white rounded-r-md hover:bg-pink-600 font-semibold text-sm">Salin Link</button>
                </div>
                <p id="copyLinkStatus" class="mt-2 text-green-600 text-xs hidden">Tautan berhasil disalin!</p>
            </div>

            <!-- Kolom Kanan: QR Code -->
            <div class="text-center">
                <p class="font-semibold text-gray-700 mb-2">atau Pindai QR Code:</p>
                <div class="bg-white p-3 border border-dashed border-gray-300 rounded-lg inline-block">
                    <img src="<?= $qr_api_url ?>" alt="QR Code Kado Virtual" class="mx-auto rounded-md w-32 h-32">
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
            <a href="form_kado.php" class="w-full sm:w-auto px-6 py-3 border border-pink-500 text-pink-500 rounded-full font-semibold hover:bg-pink-50 transition-colors">
                + Buat Kado Baru
            </a>
            <a href="dashboard.php" class="w-full sm:w-auto px-6 py-3 bg-pink-500 text-white rounded-full font-semibold hover:bg-pink-600 transition-colors">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <script>
        function copyToClipboard(elementId, statusId) {
            const input = document.getElementById(elementId);
            const status = document.getElementById(statusId);

            // Pilih teks di dalam input
            input.select();
            input.setSelectionRange(0, 99999); // Untuk kompatibilitas mobile

            try {
                // Gunakan Clipboard API modern jika tersedia
                navigator.clipboard.writeText(input.value).then(() => {
                    status.classList.remove('hidden');
                    setTimeout(() => {
                        status.classList.add('hidden');
                    }, 2000); // Sembunyikan pesan setelah 2 detik
                });
            } catch (err) {
                // Fallback untuk browser lama
                document.execCommand('copy');
                status.classList.remove('hidden');
                setTimeout(() => {
                    status.classList.add('hidden');
                }, 2000);
            }
        }
    </script>
</body>
</html>