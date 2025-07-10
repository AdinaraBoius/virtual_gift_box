<?php
static $koneksi_pdo_instance = null;

function getKoneksiPDO() {
    global $koneksi_pdo_instance;

    if ($koneksi_pdo_instance === null) {
        $db_host = "localhost";
        $db_user = "root";
        $db_pass = ""; 
        $db_name = "virtual_gift_box"; // Pastikan nama database ini benar
        $db_port = 3306;
        $charset = "utf8mb4";

        $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        ];

        try {
            $koneksi_pdo_instance = new PDO($dsn, $db_user, $db_pass, $options);
        } catch (PDOException $e) {
            error_log("Koneksi Database Gagal (PDO): " . $e->getMessage());
            $koneksi_pdo_instance = null; 
            return null; 
        }
    }
    return $koneksi_pdo_instance;
}

/**
 * Fungsi untuk menutup koneksi database PDO.
 */
function tutupKoneksiPDO() {
    global $koneksi_pdo_instance;
    $koneksi_pdo_instance = null; 
}

// --- BLOK UNTUK TES KONEKSI LANGSUNG ---
if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"])) {
    header('Content-Type: text/plain; charset=utf-8');

    // Mengambil detail koneksi yang sama seperti di dalam fungsi untuk ditampilkan
    $_db_host_test = "localhost";
    $_db_user_test = "root";
    $_db_pass_test = "";
    $_db_name_test = "virtual_gift_box";
    $_db_port_test = 3306;
    $_charset_test = "utf8mb4";

    $_dsn_test = "mysql:host=$_db_host_test;port=$_db_port_test;dbname=$_db_name_test;charset=$_charset_test";
    $_options_test = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ];
    
    $connTest = null;

    try {
        // Mencoba membuat koneksi baru khusus untuk blok tes ini
        // agar tidak mengganggu variabel statis 
        $connTest = new PDO($_dsn_test, $_db_user_test, $_db_pass_test, $_options_test);
        echo "Koneksi Berhasil!";

    } catch (PDOException $e) {
        echo "Koneksi Gagal: " . htmlspecialchars($e->getMessage()); 
    } finally {
        // Menutup koneksi tes
        $connTest = null; 
    }
}
?>
