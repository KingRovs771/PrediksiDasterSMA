-- Dihapus pemaksaan database agar mengikuti Docker env

-- Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- Tabel Data Produk
CREATE TABLE data_produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(50) UNIQUE NOT NULL,
    nama_produk VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) DEFAULT 'Daster',
    harga DECIMAL(10,2) NOT NULL,
    stok INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_kode (kode_produk)
) ENGINE=InnoDB;

-- Tabel Data Penjualan
CREATE TABLE data_penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produk_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jumlah_terjual INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produk_id) REFERENCES data_produk(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_tanggal (tanggal)
) ENGINE=InnoDB;

-- Tabel Log Prediksi
CREATE TABLE prediksi_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    periode_n INT NOT NULL,
    prediksi_bulan VARCHAR(7) NOT NULL,
    nilai_prediksi DECIMAL(10,2) NOT NULL,
    mape DECIMAL(5,2) NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Insert Admin Default (Password: admin123)
-- 💡 Untuk generate hash baru: <?php echo password_hash('password_baru', PASSWORD_DEFAULT); ?>
INSERT INTO users (username, email, password, role) VALUES
('admin_tivayo', 'admin@tivayo.com', '$2y$10$cdJYLKsNkLqBa38qpfhonufvCXPtl0hetZWJj.3/LUHsByZulz.SW', 'admin');