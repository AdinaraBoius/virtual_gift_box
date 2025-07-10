<?php
session_start();
require_once 'db_connection.php';

// Pastikan user sudah login dan ada data kado di session
if (!isset($_SESSION['loggedin'], $_SESSION['pending_gift'], $_SESSION['user_id'])) {
    header('Location: form_kado.php?error=Data transaksi tidak ditemukan.');
    exit;
}

$gift   = $_SESSION['pending_gift'];
$userId = $_SESSION['user_id'];
$pdo    = getKoneksiPDO();

try {
    // 1) Simpan kado ke virtual_gifts
    $kodeKado      = 'GIFT-' . strtoupper(substr(uniqid(), 7, 13));
    $tanggalDibuat = date('Y-m-d H:i:s');

    $stmtKado = $pdo->prepare("
        INSERT INTO virtual_gifts (
            id_pengirim, nama_penerima, email_penerima, hubungan_penerima,
            gift_type, gift_category, gift_theme, features, message_personal,
            kode_kado, id_animasi, status_kado, tanggal_dibuat
        ) VALUES (
            :id_pengirim, :nama_penerima, :email_penerima, :hubungan_penerima,
            :gift_type, :gift_category, :gift_theme, :features, :message_personal,
            :kode_kado, :id_animasi, 'paid', :tanggal_dibuat
        )
    ");
    $stmtKado->execute([
        'id_pengirim'       => $userId,
        'nama_penerima'     => $gift['recipient_name'],
        'email_penerima'    => $gift['recipient_email'],
        'hubungan_penerima' => $gift['relationship'],
        'gift_type'         => $gift['gift_type'],
        'gift_category'     => $gift['gift_category'],
        'gift_theme'        => $gift['gift_theme'],
        'features'          => '-',   // jika tidak ada fitur lain
        'message_personal'  => $gift['message_text'],
        'kode_kado'         => $kodeKado,
        'id_animasi'        => $gift['animation_id'] ?: null,
        'tanggal_dibuat'    => $tanggalDibuat
    ]);
    $idKado = $pdo->lastInsertId();

    // 2) Simpan transaksi ke tabel transaksi
    $stmtTx = $pdo->prepare("
        INSERT INTO transaksi (
            id_kado, amount, currency, payment_method, status, created_at, completed_at
        ) VALUES (
            :id_kado, :amount, 'IDR', 'manual', 'paid', NOW(), NOW()
        )
    ");
    $stmtTx->execute([
        'id_kado' => $idKado,
        'amount'  => $gift['total_amount']
    ]);

    // 2a) Simpan riwayat status ke gift_history
    $stmtHistory = $pdo->prepare("
        INSERT INTO gift_history (id_kado, status_old, status_new)
        VALUES (:id_kado, :status_old, :status_new)
    ");
    $stmtHistory->execute([
        'id_kado'     => $idKado,
        'status_old'  => 'initial', // Atau 'pending' jika kamu menganggap default-nya begitu
        'status_new'  => 'paid'
    ]);


    // 3) Bersihkan session pending
    unset($_SESSION['pending_gift']);

} catch (PDOException $e) {
    // Jika error, tampilkan pesan
    echo "<p>Terjadi kesalahan: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Pembayaran Berhasil - Virtual Gift Box</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-xl shadow-2xl max-w-md w-full text-center">
        <h1 class="text-2xl font-bold text-pink-600 mb-4">🎉 Pembayaran Berhasil! 🎉</h1>
        <p class="text-gray-700 mb-6">
            Terima kasih, pembayaran sebesar
            <strong>Rp <?= number_format($gift['total_amount'],0,',','.') ?></strong>
            telah kami terima.
        </p>
        <a href="gift_success.php?code=<?= urlencode($kodeKado) ?>"
           class="inline-block bg-pink-500 text-white px-6 py-3 rounded-md font-medium hover:bg-pink-600 transition">
            Lihat Kado Saya
        </a>
        <div class="mt-4">
            <a href="dashboard.php" class="text-gray-600 hover:underline">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>
