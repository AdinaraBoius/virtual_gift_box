<?php
session_start();
require_once 'db_connection.php';

if (!isset($_GET['code'])) {
    echo "Kode kado tidak ditemukan.";
    exit;
}

$kode_kado = $_GET['code'];
$pdo = getKoneksiPDO();

$sql = "
    SELECT vg.*, u.username AS nama_pengirim, a.nama_file, a.tipe_file
    FROM virtual_gifts vg
    JOIN users u ON vg.id_pengirim = u.id
    LEFT JOIN animasi a ON vg.id_animasi = a.id_animasi
    WHERE vg.kode_kado = :kode
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['kode' => $kode_kado]);
$gift = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$gift) {
    echo "Kado tidak ditemukan.";
    exit;
}

$animasiUrl  = $gift['nama_file']  ? "animasi/{$gift['nama_file']}" : null;
$tipeAnimasi = $gift['tipe_file'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kejutan Untukmu! - Virtual Gift Box</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lottie-web@5.10.0/build/player/lottie.min.js"></script>
  <style>
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeUp {
      animation: fadeUp 0.6s ease-out forwards;
    }
  </style>
</head>
<body class="bg-pink-50 flex items-center justify-center min-h-screen p-4">
  <div class="bg-white rounded-2xl shadow-lg overflow-hidden max-w-md w-full animate-fadeUp">
    <div class="bg-pink-500 text-white text-center py-6">
      <h1 class="text-2xl font-bold">KEJUTAN SPESIAL BUATMU!</h1>
      <p class="mt-2 text-lg"><?= htmlspecialchars($gift['nama_penerima']) ?></p>
    </div>
    <div class="p-6 space-y-6">
      <div class="text-center">
        <p class="text-sm text-pink-600">Dari</p>
        <p class="text-xl font-semibold"><?= htmlspecialchars($gift['nama_pengirim']) ?></p>
      </div>

      <?php if ($animasiUrl): ?>
      <div class="flex justify-center">
        <?php if ($tipeAnimasi === 'json'): ?>
        <div id="animasiContainer" class="w-50 h-50"></div>
        <script>
          lottie.loadAnimation({
            container: document.getElementById('animasiContainer'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: "<?= $animasiUrl ?>"
          });
        </script>
        <?php elseif ($tipeAnimasi === 'gif'): ?>
        <img src="<?= htmlspecialchars($animasiUrl) ?>" alt="Animasi GIF" class="w-50 h-50 object-contain rounded">
        <?php elseif ($tipeAnimasi === 'mp4'): ?>
        <video src="<?= htmlspecialchars($animasiUrl) ?>" autoplay loop muted class="w-50 h-50 object-contain rounded"></video>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($gift['message_personal'])): ?>
      <div class="bg-pink-100 border border-pink-200 p-4 rounded">
        <p class="text-gray-800"><?= nl2br(htmlspecialchars($gift['message_personal'])) ?></p>
      </div>
      <?php endif; ?>

      <div class="text-center">
        <a href="index.php" class="text-pink-500 font-medium hover:underline">&larr; Kembali ke Beranda</a>
      </div>
    </div>
  </div>
</body>
</html>
