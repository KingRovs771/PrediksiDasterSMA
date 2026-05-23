<?php
/**
 * Script Migrasi Database - Fitur Kategori
 * Menambahkan tabel kategori, memigrasikan data lama, dan memperbarui tabel data_produk.
 */

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

    // 1. Buat Tabel Kategori jika belum ada
    $sqlKategori = "CREATE TABLE IF NOT EXISTS kategori (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_kategori VARCHAR(50) UNIQUE NOT NULL,
        deskripsi VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;";
    $db->exec($sqlKategori);
    echo "Tabel 'kategori' berhasil dipastikan/dibuat.\n";

    // Pastikan kategori default 'Daster' ada
    $stmt = $db->prepare("INSERT IGNORE INTO kategori (nama_kategori, deskripsi) VALUES ('Daster', 'Kategori daster standar')");
    $stmt->execute();

    // 2. Cek apakah kolom 'kategori_id' sudah ada di 'data_produk'
    $stmt = $db->query("SHOW COLUMNS FROM data_produk LIKE 'kategori_id'");
    $hasKategoriId = $stmt->fetch();

    if (!$hasKategoriId) {
        // Tambahkan kolom kategori_id
        $db->exec("ALTER TABLE data_produk ADD COLUMN kategori_id INT DEFAULT NULL AFTER nama_produk");
        echo "Kolomb 'kategori_id' berhasil ditambahkan ke tabel 'data_produk'.\n";

        // Tambah constraint foreign key
        $db->exec("ALTER TABLE data_produk ADD CONSTRAINT fk_produk_kategori 
                   FOREIGN KEY (kategori_id) REFERENCES kategori(id) 
                   ON DELETE SET NULL ON UPDATE CASCADE");
        echo "Foreign Key Constraint 'fk_produk_kategori' berhasil ditambahkan.\n";
    }

    // 3. Migrasikan Kategori Lama
    // Cek apakah kolom 'kategori' lama masih ada
    $stmt = $db->query("SHOW COLUMNS FROM data_produk LIKE 'kategori'");
    $hasKategoriLama = $stmt->fetch();

    if ($hasKategoriLama) {
        // Ambil semua kategori unik dari data_produk
        $stmt = $db->query("SELECT DISTINCT kategori FROM data_produk WHERE kategori IS NOT NULL AND kategori != ''");
        $kategoriUnik = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($kategoriUnik as $katName) {
            // Masukkan ke tabel kategori
            $stmtInsert = $db->prepare("INSERT IGNORE INTO kategori (nama_kategori, deskripsi) VALUES (:nama, :desc)");
            $stmtInsert->execute([
                ':nama' => $katName,
                ':desc' => "Kategori " . $katName . " hasil migrasi sistem"
            ]);

            // Dapatkan ID kategori yang baru dimasukkan atau yang sudah ada
            $stmtGetId = $db->prepare("SELECT id FROM kategori WHERE nama_kategori = :nama");
            $stmtGetId->execute([':nama' => $katName]);
            $katId = $stmtGetId->fetchColumn();

            // Hubungkan produk dengan kategori_id
            $stmtUpdate = $db->prepare("UPDATE data_produk SET kategori_id = :kat_id WHERE kategori = :kat_nama");
            $stmtUpdate->execute([
                ':kat_id' => $katId,
                ':kat_nama' => $katName
            ]);
            echo "Migrasi produk kategori '{$katName}' ke kategori_id = {$katId} selesai.\n";
        }

        // Set produk sisa yang kategori_id-nya masih null ke kategori default 'Daster'
        $stmtGetDefault = $db->prepare("SELECT id FROM kategori WHERE nama_kategori = 'Daster'");
        $stmtGetDefault->execute();
        $defaultId = $stmtGetDefault->fetchColumn();
        if ($defaultId) {
            $db->exec("UPDATE data_produk SET kategori_id = {$defaultId} WHERE kategori_id IS NULL");
        }

        // Hapus kolom kategori lama agar bersih
        $db->exec("ALTER TABLE data_produk DROP COLUMN kategori");
        echo "Kolom lama 'kategori' berhasil dihapus dari tabel 'data_produk'.\n";
    } else {
        echo "Kolom lama 'kategori' sudah tidak ada. Tidak perlu migrasi data.\n";
    }

    echo "=== MIGRASI SELESAI DENGAN SUKSES ===\n";
} catch (Exception $e) {
    echo "ERROR MIGRASI: " . $e->getMessage() . "\n";
    exit(1);
}
