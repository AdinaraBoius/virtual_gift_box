<?php
session_start();
require_once 'db_connection.php';

// Jika belum login, arahkan ke login
if (!isset($_SESSION['loggedin'])) {
    header('Location: login-form.php');
    exit;
}

// Ambil data dari form_kado
$recipientName   = trim($_POST['recipient_name']   ?? '');
$recipientEmail  = trim($_POST['recipient_email']  ?? '');
$relationship    = $_POST['relationship']          ?? '';
$giftType        = $_POST['gift_type']             ?? '';
$giftCategory    = $_POST['gift_category']         ?? '';
$giftTheme       = $_POST['gift_theme']            ?? '';
$animationId     = $_POST['animation_id']          ?? '';
$messageText     = trim($_POST['message_text']     ?? '');

// Validasi sederhana
if (!$recipientName || !$recipientEmail || !$relationship || !$giftType || !$giftCategory) {
    header('Location: form_kado.php?error=Semua kolom wajib diisi kecuali tema');
    exit;
}

// Hitung biaya
$animationFee = $animationId ? 5000 : 0;
$messageFee   = $messageText ? 2000 : 0;
$totalAmount  = $animationFee + $messageFee;

// Simpan data kado & transaksi sementara di session
$_SESSION['pending_gift'] = [
    'recipient_name'  => $recipientName,
    'recipient_email' => $recipientEmail,
    'relationship'    => $relationship,
    'gift_type'       => $giftType,
    'gift_category'   => $giftCategory,
    'gift_theme'      => $giftTheme,
    'animation_id'    => $animationId,
    'message_text'    => $messageText,
    'animation_fee'   => $animationFee,
    'message_fee'     => $messageFee,
    'total_amount'    => $totalAmount,
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Ringkasan Pembayaran - Virtual Gift Box</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-xl shadow-2xl max-w-md w-full">
        <h1 class="text-2xl font-bold text-pink-600 mb-6 text-center">Ringkasan Pembayaran</h1>
        <div class="space-y-4">
            <div class="flex justify-between">
                <span>Biaya Animasi</span>
                <span>Rp <?= number_format($animationFee, 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between">
                <span>Biaya Pesan Personal</span>
                <span>Rp <?= number_format($messageFee, 0, ',', '.') ?></span>
            </div>
            <hr class="border-pink-200">
            <div class="flex justify-between font-semibold text-lg">
                <span>Total Bayar</span>
                <span>Rp <?= number_format($totalAmount, 0, ',', '.') ?></span>
            </div>
        </div>
        <form action="process_payment.php" method="post" class="mt-6 text-center">
            <button type="submit"
                    class="bg-pink-500 text-white px-6 py-2 rounded-lg hover:bg-pink-600 transition">
                Bayar Sekarang
            </button>
        </form>
        <div class="mt-4 text-center">
            <a href="form_kado.php" class="text-gray-600 hover:underline">&larr; Kembali ke Form Kado</a>
        </div>
    </div>
</body>
</html>
