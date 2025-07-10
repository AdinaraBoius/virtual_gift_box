<?php
session_start();

// Cek apakah sudah login dan role adalah user
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'user') {
    header('Location: login-form.php');
    exit;
}

// Cek jika pengguna tidak login, tendang ke halaman login
if (!isset($_SESSION['loggedin'])) {
    header('Location: login-form.php');
    exit;
}

// Ambil informasi pengguna dari sesi
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Pengguna';
$user_id = $_SESSION['user_id']; // Kita butuh ini untuk query database

// Memanggil file koneksi database
require_once 'db_connection.php';

// --- LOGIKA UNTUK MENGAMBIL RIWAYAT KADO ---
$gifts = []; // Inisialisasi array kosong untuk kado
$pdo = getKoneksiPDO();

if ($pdo) {
    try {
        // Query untuk mengambil kado yang dibuat oleh pengguna yang sedang login
        // Diurutkan berdasarkan tanggal dibuat, yang terbaru di atas       
        $sql = "SELECT
                        vg.id_kado, vg.nama_penerima, vg.gift_type AS jenis_kado, vg.gift_category AS kategori_kado, vg.gift_theme AS tema_kado, vg.message_personal AS pesan_personal, vg.kode_kado, vg.status_kado, vg.tanggal_dibuat, a.nama_file, a.tipe_file
                FROM virtual_gifts vg
                LEFT JOIN animasi a
                    ON vg.id_animasi = a.id_animasi
                WHERE vg.id_pengirim = :id_pengirim
                ORDER BY vg.tanggal_dibuat DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_pengirim', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $gifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        // Catat error jika query gagal, jangan tampilkan ke pengguna secara langsung
        error_log("Gagal mengambil riwayat kado: " . $e->getMessage());
        // Anda bisa mengatur pesan error untuk ditampilkan jika perlu
        // $errorMessage = "Terjadi kesalahan saat memuat riwayat kado.";
    }
}

// Array untuk memetakan nilai jenis_kado ke teks yang lebih ramah pengguna
// Sebaiknya ini konsisten dengan yang ada di create_gift.php atau welcome.php
$giftTypeOptions = [
    'ucapan' => 'Kartu Ucapan Digital', 
    'voucher' => 'Voucher Digital', 
    'musik' => 'Playlist Musik',
    'album' => 'Album Foto Digital', 
    'video' => 'Video Ucapan'
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Virtual Gift Box</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
        }
        .tooltip { /* Style untuk tombol salin */
            position: relative;
            display: inline-block;
        }
        .tooltip .tooltiptext {
            visibility: hidden; width: 100px; background-color: #555; color: #fff; text-align: center;
            border-radius: 6px; padding: 5px 0; position: absolute; z-index: 1; bottom: 125%;
            left: 50%; margin-left: -50px; opacity: 0; transition: opacity 0.3s;
        }
        .tooltip:hover .tooltiptext { visibility: visible; opacity: 1;}
    </style>
</head>
<body class="text-gray-800">

    <nav class="bg-white shadow-sm py-4 sticky top-0 z-50">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a href="index.php" class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" /></svg>
                <span class="ml-2 text-xl font-bold text-gray-800">VirtualGift</span>
            </a>
            <div>
                <a href="index.php" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-full font-medium transition duration-300 text-sm sm:text-base">Beranda</a>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full font-medium transition duration-300 text-sm sm:text-base">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 sm:py-12">
        <section class="mb-8 sm:mb-12">
            <div class="bg-gradient-to-r from-pink-500 to-rose-500 p-6 sm:p-10 rounded-xl shadow-xl text-white">
                <h1 class="text-3xl sm:text-4xl font-bold">Selamat Datang Kembali, <?php echo $username; ?>!</h1>
                <p class="mt-2 text-lg sm:text-xl opacity-90">Siap untuk membuat dan berbagi kebahagiaan hari ini?</p>
                <div class="mt-8">
                    <a href="form_kado.php" class="inline-block bg-white text-pink-600 hover:bg-gray-100 px-6 py-3 sm:px-8 sm:py-3 rounded-lg font-semibold text-base sm:text-lg shadow-md hover:shadow-lg transition duration-300 transform hover:scale-105">
                        <span class="mr-2">🎁</span> Buat Kado Baru Sekarang
                    </a>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-700 mb-4 sm:mb-6">Riwayat Kado Anda</h2>
            <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg">
                <?php if (empty($gifts)): ?>
                    <div class="text-center py-10 border-2 border-dashed border-gray-300 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Belum Ada Riwayat Kado</h3>
                        <p class="mt-1 text-sm text-gray-500">Semua kado yang telah Anda buat akan tercatat dan ditampilkan di sini.</p>
                        <div class="mt-6">
                            <a href="form_kado.php" class="text-pink-500 hover:text-pink-600 font-medium hover:underline">Mulai buat kado pertama Anda &rarr;</a>
                        </div>
                    </div>
                <?php else: ?>
                    <table class="min-w-full table-auto mt-6 border border-gray-200">
                        <thead>
                            <tr class="bg-pink-100 text-pink-800 text-sm">
                                <th class="px-4 py-2 border">Preview</th>
                                <th class="px-4 py-2 border">Untuk</th>
                                <th class="px-4 py-2 border">Jenis</th>
                                <th class="px-4 py-2 border">Tanggal</th>
                                <th class="px-4 py-2 border">Kode</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gifts as $gift): 
                                $file = $gift['nama_file'] ?? '';
                                $path = $file ? "animasi/$file" : '';
                                $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            ?>
                                <tr class="text-gray-700">
                                    <td class="border px-4 py-2">
                                        <?php if ($ext==='gif'): ?>
                                            <img src="<?= $path ?>" class="w-16 h-16 object-cover rounded-lg border border-pink-200">
                                        <?php elseif ($ext==='mp4'): ?>
                                            <video src="<?= $path ?>" class="w-16 h-16 object-cover rounded-lg border border-pink-200" muted loop></video>
                                        <?php elseif ($ext==='json'): ?>
                                            <div id="lottie-<?= $gift['id_kado'] ?>" class="w-16 h-16 mx-auto"></div>
                                            <script>
                                                lottie.loadAnimation({
                                                    container: document.getElementById('lottie-<?= $gift['id_kado'] ?>'),
                                                    renderer: 'svg', loop: false, autoplay: true,
                                                    path: "<?= $path ?>"
                                                });
                                            </script>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($gift['nama_penerima']) ?></td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($giftTypeOptions[$gift['jenis_kado']]) ?></td>
                                    <td class="border px-4 py-2"><?= (new DateTime($gift['tanggal_dibuat']))->format('d M Y') ?></td>
                                    <td class="border px-4 py-2 font-mono text-pink-500"><?= htmlspecialchars($gift['kode_kado']) ?></td>
                                    <td class="border px-4 py-2 space-x-2">
                                        <a href="view_gift_page.php?code=<?= urlencode($gift['kode_kado']) ?>" class="text-blue-600 hover:underline">Lihat</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="text-center py-8 mt-auto">
        <p class="text-sm text-gray-500">&copy; <?php echo date("Y"); ?> VirtualGift. Dibuat dengan penuh cinta.</p>
    </footer>

<script>
function copyGiftCode(elementId, buttonElement) {
    const kodeElement = document.getElementById(elementId);
    const kodeText = kodeElement.textContent;
    const tooltip = buttonElement.nextElementSibling; // Asumsi tooltip adalah elemen berikutnya

    navigator.clipboard.writeText(kodeText).then(() => {
        tooltip.textContent = "Tersalin!";
        buttonElement.textContent = "OK";
        setTimeout(() => {
            tooltip.textContent = "Salin kode";
            buttonElement.textContent = "Salin";
        }, 2000);
    }).catch(err => {
        console.error("Gagal menyalin kode: ", err);
        tooltip.textContent = "Gagal!";
         setTimeout(() => {
            tooltip.textContent = "Salin kode";
        }, 2000);
    });
}
</script>
</body>
</html>