<?php
// welcome.php - Menerima dan menampilkan data dari form_kado.html

// -----------------------------------------------------------------------------
// FASE 1: PENGAMBILAN & PEMROSESAN DATA
// -----------------------------------------------------------------------------

// Keamanan dasar: Pastikan request adalah POST. Jika tidak, redirect ke form.
// Ganti 'form_kado.html' jika nama file form Anda berbeda.
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: form_kado.php");
    exit;
}

// Fungsi htmlspecialchars adalah wajib untuk keamanan (mencegah serangan XSS).
// Operator '??' (null coalescing) menyediakan nilai default jika data tidak ada.

// --- Data Pengirim & Penerima ---
$senderName = htmlspecialchars($_POST['senderName'] ?? 'Tidak diisi');
$senderEmail = htmlspecialchars($_POST['senderEmail'] ?? 'Tidak diisi');
$senderPhone = htmlspecialchars($_POST['senderPhone'] ?? 'Tidak diisi');
$recipientName = htmlspecialchars($_POST['recipientName'] ?? 'Tidak diisi');
$recipientEmail = htmlspecialchars($_POST['recipientEmail'] ?? 'Tidak diisi');

// --- Pemetaan Nilai Form ke Teks yang Mudah Dibaca ---
// Ini adalah praktik yang baik untuk memisahkan logika dari presentasi.

$relationshipOptions = [
    'teman' => 'Teman', 'keluarga' => 'Keluarga', 'pasangan' => 'Pasangan',
    'rekan' => 'Rekan Kerja', 'lainnya' => 'Lainnya'
];
$giftTypeOptions = [
    'ucapan' => 'Kartu Ucapan Digital', 'voucher' => 'Voucher Digital', 'musik' => 'Playlist Musik',
    'album' => 'Album Foto Digital', 'video' => 'Video Ucapan'
];
$giftCategoryOptions = [
    'ulangTahun' => 'Ulang Tahun', 'anniversary' => 'Anniversary',
    'wisuda' => 'Wisuda', 'lainnya' => 'Lainnya'
];
$giftThemeOptions = [
    'elegant' => 'Elegant', 'cute' => 'Cute', 'formal' => 'Formal',
    'vintage' => 'Vintage', 'minimalis' => 'Minimalis'
];
$featureOptions = [
    'musik' => 'Musik Latar', 'animasi' => 'Animasi Pembuka',
    'foto' => 'Upload Foto Personal', 'suara' => 'Rekaman Suara'
];

// --- Memproses Data Pilihan (Select, Radio) ---
$relationship = $relationshipOptions[$_POST['relationship']] ?? 'Tidak dipilih';
$giftType = $giftTypeOptions[$_POST['giftType']] ?? 'Tidak dipilih';
$giftCategory = $giftCategoryOptions[$_POST['giftCategory']] ?? 'Tidak dipilih';
$giftTheme = $giftThemeOptions[$_POST['giftTheme']] ?? 'Tidak dipilih';

// --- Memproses Fitur Tambahan (Checkbox) ---
// Checkbox dengan `name="features[]"` akan diterima sebagai array di PHP.
$selectedFeatures = [];
if (isset($_POST['features']) && is_array($_POST['features'])) {
    foreach ($_POST['features'] as $featureKey) {
        // Ambil teks dari mapping, dan pastikan nilainya aman
        if (isset($featureOptions[$featureKey])) {
            $selectedFeatures[] = htmlspecialchars($featureOptions[$featureKey]);
        }
    }
}
$featuresText = !empty($selectedFeatures) ? implode(', ', $selectedFeatures) : 'Tidak ada fitur tambahan';

// --- Memproses Pesan Personal (Textarea) ---
// Fungsi nl2br() mengubah baris baru (\n) menjadi tag HTML <br>
$message = nl2br(htmlspecialchars($_POST['messageText'] ?? 'Tidak ada pesan.'));

// -----------------------------------------------------------------------------
// FASE 2: PRESENTASI DATA (HTML & TAILWIND CSS)
// -----------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rangkuman Kado Virtual Anda - Virtual Gift Box</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="py-8 px-4">
    <div class="container mx-auto max-w-3xl">
        <div class="bg-white p-8 md:p-12 rounded-xl shadow-2xl text-center">
            
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h1 class="text-3xl font-bold text-gray-800">Kado Virtual Berhasil Dibuat!</h1>
            <p class="text-gray-600 mt-2 mb-8">Berikut adalah rangkuman dari kado yang telah Anda siapkan.</p>
            
            <div class="text-left bg-gray-50 p-6 rounded-lg border border-gray-200 space-y-6">
                
                <div>
                    <h2 class="text-xl font-semibold text-pink-600 mb-4 border-b pb-2">Detail Pengirim & Penerima</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <p><strong>Nama Pengirim:</strong><br><?php echo $senderName; ?></p>
                        <p><strong>Nama Penerima:</strong><br><?php echo $recipientName; ?></p>
                        <p><strong>Email Pengirim:</strong><br><?php echo $senderEmail; ?></p>
                        <p><strong>Email Penerima:</strong><br><?php echo $recipientEmail; ?></p>
                        <p><strong>No. Telepon:</strong><br><?php echo $senderPhone; ?></p>
                        <p><strong>Hubungan:</strong><br><?php echo $relationship; ?></p>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-pink-600 mb-4 border-b pb-2">Spesifikasi Kado</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        <p><strong>Jenis Kado:</strong><br><?php echo $giftType; ?></p>
                        <p><strong>Kategori Kado:</strong><br><?php echo $giftCategory; ?></p>
                        <p><strong>Tema Desain:</strong><br><?php echo $giftTheme; ?></p>
                        <p><strong>Fitur Tambahan:</strong><br><?php echo $featuresText; ?></p>
                    </div>
                </div>
                
                <div>
                     <h2 class="text-xl font-semibold text-pink-600 mb-3 border-b pb-2">Pesan Personal</h2>
                     <blockquote class="border-l-4 border-pink-500 pl-4 text-gray-700 italic">
                        <?php echo $message; ?>
                    </blockquote>
                </div>
            </div>
            
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="form_kado.php" class="w-full sm:w-auto px-6 py-2 border border-pink-500 text-pink-500 rounded-md shadow-sm font-medium hover:bg-pink-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition duration-300">
                    Buat Kado Lain
                </a>
                <a href="index.php" class="w-full sm:w-auto px-6 py-2 border border-transparent rounded-md shadow-sm font-medium text-white bg-pink-500 hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition duration-300">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</body>
</html>