<?php

require_once __DIR__ . '/../core/Database.php';

class PrediksiLog
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->ensureSchema();
    }

    /**
     * Memastikan tabel prediksi_log memiliki kolom-kolom terbaru (kategori & smape)
     */
    private function ensureSchema()
    {
        try {
            // Cek apakah tabel ada
            $this->db->query("SHOW TABLES LIKE 'prediksi_log'");
            $tableExists = $this->db->resultSet();

            if (empty($tableExists)) {
                // Jika tabel belum ada, buat baru
                $this->db->query("
                    CREATE TABLE prediksi_log (
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
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");
                $this->db->execute();
                return;
            }

            // Ambil info kolom yang ada di database saat ini
            $this->db->query("DESCRIBE prediksi_log");
            $columns = $this->db->resultSet();
            $columnNames = array_column($columns, 'Field');

            // Daftar semua kolom yang wajib ada beserta definisinya
            $requiredColumns = [
                'kategori' => "VARCHAR(100) NOT NULL AFTER id",
                'bulan_awal' => "VARCHAR(7) NOT NULL AFTER kategori",
                'bulan_akhir' => "VARCHAR(7) NOT NULL AFTER bulan_awal",
                'periode_n' => "INT NOT NULL AFTER bulan_akhir",
                'prediksi_bulan' => "VARCHAR(7) NOT NULL AFTER periode_n",
                'nilai_prediksi' => "DECIMAL(10,2) NOT NULL AFTER prediksi_bulan",
                'mape' => "DECIMAL(5,2) NOT NULL AFTER nilai_prediksi",
                'smape' => "DECIMAL(5,2) NOT NULL AFTER mape",
                'user_id' => "INT NOT NULL AFTER smape"
            ];

            // Tambahkan kolom yang hilang satu per satu
            foreach ($requiredColumns as $col => $definition) {
                if (!in_array($col, $columnNames)) {
                    $this->db->query("ALTER TABLE prediksi_log ADD COLUMN {$col} {$definition}");
                    $this->db->execute();
                }
            }
        } catch (Exception $e) {
            error_log("Gagal melakukan sinkronisasi skema database pada PrediksiLog: " . $e->getMessage());
        }
    }


    /**
     * Tambah log peramalan baru
     */
    public function tambahLog($data)
    {
        $this->db->query("
            INSERT INTO prediksi_log (
                kategori, bulan_awal, bulan_akhir, periode_n, 
                prediksi_bulan, nilai_prediksi, mape, smape, user_id
            ) VALUES (
                :kategori, :bulan_awal, :bulan_akhir, :periode_n, 
                :prediksi_bulan, :nilai_prediksi, :mape, :smape, :user_id
            )
        ");
        $this->db->bind(':kategori', $data['kategori']);
        $this->db->bind(':bulan_awal', $data['bulan_awal']);
        $this->db->bind(':bulan_akhir', $data['bulan_akhir']);
        $this->db->bind(':periode_n', (int) $data['periode_n']);
        $this->db->bind(':prediksi_bulan', $data['prediksi_bulan']);
        $this->db->bind(':nilai_prediksi', (float) $data['nilai_prediksi']);
        $this->db->bind(':mape', (float) $data['mape']);
        $this->db->bind(':smape', (float) $data['smape']);
        $this->db->bind(':user_id', (int) $data['user_id']);

        return $this->db->execute();
    }

    /**
     * Ambil semua log peramalan
     */
    public function getAllLog()
    {
        $this->db->query("
            SELECT pl.*, u.username 
            FROM prediksi_log pl
            LEFT JOIN users u ON pl.user_id = u.id
            ORDER BY pl.created_at DESC, pl.id DESC
        ");
        return $this->db->resultSet();
    }

    /**
     * Hapus entri log peramalan berdasarkan ID
     */
    public function hapusLog($id)
    {
        $this->db->query("DELETE FROM prediksi_log WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
