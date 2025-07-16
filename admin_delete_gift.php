<?php
session_start();
require_once 'db_connection.php';

// Proteksi halaman: hanya admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login-form.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: admin_list_gift.php');
    exit;
}

$id = (int)$_GET['id'];
$pdo = getKoneksiPDO();

// Pastikan kado benar-benar ada
$stmt = $pdo->prepare("SELECT * FROM virtual_gifts WHERE id_kado = ?");
$stmt->execute([$id]);
$gift = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$gift) {
    header('Location: admin_list_gift.php');
    exit;
}

// Lakukan penghapusan
$stmt = $pdo->prepare("DELETE FROM virtual_gifts WHERE id_kado = ?");
$stmt->execute([$id]);

header('Location: admin_list_gift.php');
exit;
?>
