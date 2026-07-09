<?php
/**
 * Script Migrasi Database - Memperbarui tabel prediksi_log
 * Menambahkan kolom smape, kategori, bulan_awal, bulan_akhir
 */

header('Content-Type: text/plain');

try {
    $config = require __DIR__ . '/../config/database.php';
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $db = new PDO($dsn, $config['user'], $config['password'], $options);
    echo "Koneksi ke database berhasil.\n";

    // 1. Hapus tabel lama jika ada
    echo "Menghapus tabel 'prediksi_log' yang lama jika ada...\n";
    $db->exec("DROP TABLE IF EXISTS prediksi_log");

    // 2. Buat tabel baru dengan kolom yang lengkap
    echo "Membuat tabel 'prediksi_log' baru dengan kolom smape...\n";
    $sql = "CREATE TABLE prediksi_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        kategori VARCHAR(100) NOT NULL,
        bulan_awal VARCHAR(7) NOT NULL,
        bulan_akhir VARCHAR(7) NOT NULL,
        periode_n INT NOT NULL,
        prediksi_bulan VARCHAR(7) NOT NULL,
        nilai_prediksi DECIMAL(10,2) NOT NULL,
        mape DECIMAL(5,2) NOT NULL,
        smape DECIMAL(5,2) NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $db->exec($sql);
    echo "Tabel 'prediksi_log' berhasil diperbarui!\n";
    echo "=== MIGRASI SELESAI DENGAN SUKSES ===\n";
} catch (Exception $e) {
    echo "ERROR MIGRASI: " . $e->getMessage() . "\n";
    exit(1);
}
?>
