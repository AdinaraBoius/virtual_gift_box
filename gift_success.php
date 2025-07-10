<?php
session_start();

// Proteksi halaman
if (!isset($_SESSION['loggedin'])) {
    header('Location: login-form.php');
    exit;
}

// Ambil data kado
$kode_kado = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : 'KODE_ERROR';
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Pengguna';

// URL untuk dibagikan
$base_url = "http://localhost/virtual_gift_box/view_gift_page.php?code=" . urlencode($kode_kado);  // Ganti ini jika online
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kado Berhasil Dibuat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f9fafb; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-2xl text-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Kado Siap Dikirim!</h1>
        <p class="text-gray-600 mb-4">Selamat, <?php echo $username; ?>! Bagikan link berikut ke penerima:</p>

        <div class="bg-blue-50 border border-blue-200 p-4 rounded-md shadow mb-4">
            <input id="giftUrl" type="text" readonly value="<?php echo $base_url; ?>" 
                class="w-full p-2 text-sm border border-blue-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button onclick="salinURL()" class="mt-3 w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Salin Link Kado 📋
            </button>
            <p id="copyStatus" class="mt-2 text-green-600 text-sm hidden">Link berhasil disalin!</p>
        </div>

        <div class="flex justify-center gap-4 mt-6">
            <a href="form_kado.php" class="px-6 py-3 border border-pink-500 text-pink-500 rounded-full font-semibold hover:bg-pink-50">+ Buat Kado Baru</a>
            <a href="dashboard.php" class="px-6 py-3 bg-pink-500 text-white rounded-full font-semibold hover:bg-pink-600">Kembali ke Dashboard</a>
        </div>
    </div>

    <script>
        function salinURL() {
            const input = document.getElementById("giftUrl");
            input.select();
            input.setSelectionRange(0, 99999); // Untuk iOS

            navigator.clipboard.writeText(input.value)
                .then(() => {
                    document.getElementById("copyStatus").classList.remove("hidden");
                    setTimeout(() => {
                        document.getElementById("copyStatus").classList.add("hidden");
                    }, 2000);
                })
                .catch(() => {
                    alert("Gagal menyalin URL.");
                });
        }
    </script>
</body>
</html>