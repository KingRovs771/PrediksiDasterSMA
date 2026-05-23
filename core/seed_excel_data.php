<?php
/**
 * Script Seeder untuk memasukkan data aktual penjualan Daster Lengan dan Tanpa Lengan
 * Sesuai data dari lembar Excel Pengguna untuk tahun 2026.
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

    // 1. Pastikan Kategori ada
    $kategoriData = [
        'Daster Tanpa Lengan' => 'Daster sub kategori tanpa lengan',
        'Daster Lengan'       => 'Daster sub kategori memiliki lengan'
    ];

    $kategoriIds = [];
    foreach ($kategoriData as $nama => $desc) {
        $stmt = $db->prepare("INSERT IGNORE INTO kategori (nama_kategori, deskripsi) VALUES (:nama, :desc)");
        $stmt->execute([':nama' => $nama, ':desc' => $desc]);

        $stmtGet = $db->prepare("SELECT id FROM kategori WHERE nama_kategori = :nama");
        $stmtGet->execute([':nama' => $nama]);
        $kategoriIds[$nama] = $stmtGet->fetchColumn();
        echo "Kategori '{$nama}' dipastikan memiliki ID: {$kategoriIds[$nama]}.\n";
    }

    // 2. Pastikan Produk Standard ada untuk masing-masing Kategori
    $produkData = [
        [
            'kode' => 'DST-TL-01',
            'nama' => 'Daster Tanpa Lengan Standard',
            'kat_id' => $kategoriIds['Daster Tanpa Lengan'],
            'harga' => 35000.00,
            'stok' => 1000
        ],
        [
            'kode' => 'DST-L-01',
            'nama' => 'Daster Lengan Standard',
            'kat_id' => $kategoriIds['Daster Lengan'],
            'harga' => 45000.00,
            'stok' => 1000
        ]
    ];

    $produkIds = [];
    foreach ($produkData as $prod) {
        $stmt = $db->prepare("INSERT IGNORE INTO data_produk (kode_produk, nama_produk, kategori_id, harga, stok) 
                              VALUES (:kode, :nama, :kat_id, :harga, :stok)");
        $stmt->execute([
            ':kode' => $prod['kode'],
            ':nama' => $prod['nama'],
            ':kat_id' => $prod['kat_id'],
            ':harga' => $prod['harga'],
            ':stok' => $prod['stok']
        ]);

        $stmtGet = $db->prepare("SELECT id FROM data_produk WHERE kode_produk = :kode");
        $stmtGet->execute([':kode' => $prod['kode']]);
        $produkIds[$prod['nama']] = $stmtGet->fetchColumn();
        echo "Produk '{$prod['nama']}' dipastikan memiliki ID: {$produkIds[$prod['nama']]}.\n";
    }

    // 3. Bersihkan data penjualan lama untuk kedua produk ini agar tidak duplikat saat dijalankan ulang
    $db->exec("DELETE FROM data_penjualan WHERE produk_id IN (" . implode(',', $produkIds) . ")");
    echo "Pembersihan data penjualan lama untuk produk seeder selesai.\n";

    // 4. Data Penjualan Sesuai Excel
    // DASTER SUB KATEGORI (TANPA LENGAN)
    $salesTanpaLengan = [
        '2026-01-01' => 58,
        '2026-02-01' => 57,
        '2026-03-01' => 59,
        '2026-04-01' => 58,
        '2026-05-01' => 60,
        '2026-06-01' => 59,
        '2026-07-01' => 50,
        '2026-08-01' => 44,
        '2026-09-01' => 48,
        '2026-10-01' => 50,
        '2026-11-01' => 44,
        '2026-12-01' => 41
    ];

    // DASTER SUB KATEGORI (LENGAN)
    $salesLengan = [
        '2026-01-01' => 105,
        '2026-02-01' => 110,
        '2026-03-01' => 112,
        '2026-04-01' => 120,
        '2026-05-01' => 108,
        '2026-06-01' => 115,
        '2026-07-01' => 95,
        '2026-08-01' => 80,
        '2026-09-01' => 92,
        '2026-10-01' => 100,
        '2026-11-01' => 85,
        '2026-12-01' => 75
    ];

    // Simpan Data Tanpa Lengan
    $stmtInsert = $db->prepare("INSERT INTO data_penjualan (produk_id, tanggal, jumlah_terjual, user_id) VALUES (:prod_id, :tanggal, :terjual, 1)");
    foreach ($salesTanpaLengan as $tgl => $jml) {
        $stmtInsert->execute([
            ':prod_id' => $produkIds['Daster Tanpa Lengan Standard'],
            ':tanggal' => $tgl,
            ':terjual' => $jml
        ]);
    }
    echo "Berhasil memasukkan 12 data penjualan untuk Daster Tanpa Lengan.\n";

    // Simpan Data Lengan
    foreach ($salesLengan as $tgl => $jml) {
        $stmtInsert->execute([
            ':prod_id' => $produkIds['Daster Lengan Standard'],
            ':tanggal' => $tgl,
            ':terjual' => $jml
        ]);
    }
    echo "Berhasil memasukkan 12 data penjualan untuk Daster Lengan.\n";

    echo "=== SEEDING EXCEL DATA SELESAI DENGAN SUKSES ===\n";
} catch (Exception $e) {
    echo "ERROR SEEDING: " . $e->getMessage() . "\n";
    exit(1);
}
