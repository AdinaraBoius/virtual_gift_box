<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['loggedin']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: login-form.php');
    exit;
}

// --- 1. Ambil Data ---
$id_pengirim = $_SESSION['user_id'];
$nama_penerima = trim($_POST['recipientName']);
$email_penerima = trim($_POST['recipientEmail']);
$hubungan_penerima = $_POST['relationship'];
$gift_type = $_POST['giftType'];
$gift_category = $_POST['giftCategory'];
$gift_theme = $_POST['giftTheme'];
$message_personal = trim($_POST['messageText']);
$id_animasi = isset($_POST['id_animasi']) && $_POST['id_animasi'] !== '' ? intval($_POST['id_animasi']) : null;

// Validasi wajib
if (empty($nama_penerima) || empty($email_penerima) || empty($gift_type) || empty($gift_category)) {
    header('Location: form_kado.php?error=Data wajib tidak boleh kosong');
    exit;
}

// --- 2. Kode Kado Unik ---
$kode_kado = "GIFT-" . strtoupper(substr(uniqid(), 7, 13));

// --- 3. Simpan ke Database ---
$pdo = getKoneksiPDO();
if ($pdo) {
    try {
        $sql = "INSERT INTO virtual_gifts (
                    id_pengirim, nama_penerima, email_penerima, hubungan_penerima,
                    gift_type, gift_category, gift_theme, features, message_personal,
                    kode_kado, id_animasi, status_kado, tanggal_dibuat
                ) VALUES (
                    :id_pengirim, :nama_penerima, :email_penerima, :hubungan_penerima,
                    :gift_type, :gift_category, :gift_theme, :features, :message_personal,
                    :kode_kado, :id_animasi, :status_kado, :tanggal_dibuat
                )";

        $stmt = $pdo->prepare($sql);

        $features = '-';
        $status_kado = 'pending';
        $tanggal_dibuat = date('Y-m-d H:i:s');

        $stmt->bindParam(':id_pengirim', $id_pengirim, PDO::PARAM_INT);
        $stmt->bindParam(':nama_penerima', $nama_penerima);
        $stmt->bindParam(':email_penerima', $email_penerima);
        $stmt->bindParam(':hubungan_penerima', $hubungan_penerima);
        $stmt->bindParam(':gift_type', $gift_type);
        $stmt->bindParam(':gift_category', $gift_category);
        $stmt->bindParam(':gift_theme', $gift_theme);
        $stmt->bindParam(':features', $features);
        $stmt->bindParam(':message_personal', $message_personal);
        $stmt->bindParam(':kode_kado', $kode_kado);
        $stmt->bindParam(':id_animasi', $id_animasi, PDO::PARAM_INT);
        $stmt->bindParam(':status_kado', $status_kado);
        $stmt->bindParam(':tanggal_dibuat', $tanggal_dibuat);

        $stmt->execute();

        header('Location: gift_success.php?code=' . urlencode($kode_kado));
        exit;

    } catch (PDOException $e) {
        error_log("Gagal menyimpan kado: " . $e->getMessage());
        header('Location: form_kado.php?error=Gagal menyimpan kado.');
        exit;
    }
} else {
    header('Location: form_kado.php?error=Koneksi database gagal.');
    exit;
}
?>
