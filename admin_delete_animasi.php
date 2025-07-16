<?php
session_start();
require_once 'db_connection.php';

// Cek proteksi admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login-form.php');
    exit;
}

$pdo = getKoneksiPDO();

if (!isset($_GET['id'])) {
    die("ID animasi tidak ditemukan.");
}

$id = (int) $_GET['id'];

// Ambil nama file untuk dihapus dari folder
$stmt = $pdo->prepare("SELECT nama_file FROM animasi WHERE id_animasi = :id");
$stmt->execute(['id' => $id]);
$animasi = $stmt->fetch(PDO::FETCH_ASSOC);

if ($animasi) {
    $filePath = 'animasi/' . $animasi['nama_file'];

    // Hapus file dari folder jika ada
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Hapus dari database
    $delete = $pdo->prepare("DELETE FROM animasi WHERE id_animasi = :id");
    $delete->execute(['id' => $id]);
}

header("Location: admin_list_animasi.php");
exit();
?>