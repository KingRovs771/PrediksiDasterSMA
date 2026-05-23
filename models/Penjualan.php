<?php

require_once __DIR__ . '/../core/Database.php';

class Penjualan
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllPenjualan()
    {
        $this->db->query("
            SELECT 
                dp.id, 
                DATE_FORMAT(dp.tanggal, '%M') as bulan, 
                YEAR(dp.tanggal) as tahun, 
                dp.tanggal,
                dp.produk_id,
                pr.nama_produk as varian, 
                dp.jumlah_terjual as terjual
            FROM data_penjualan dp
            JOIN data_produk pr ON dp.produk_id = pr.id
            ORDER BY dp.tanggal DESC, dp.id DESC
        ");
        return $this->db->resultSet();
    }

    public function getPenjualanById($id)
    {
        $this->db->query("
            SELECT 
                dp.id, 
                dp.tanggal,
                dp.produk_id,
                dp.jumlah_terjual as terjual
            FROM data_penjualan dp
            WHERE dp.id = :id
        ");
        $this->db->bind(':id', $id);

        $result = $this->db->resultSet();
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Ambil total penjualan per bulan (untuk line chart)
     * Format: [{ bulan: 'Jan 2026', total: 120 }, ...]
     */
    public function getGrafikBulanan()
    {
        $this->db->query("
            SELECT 
                DATE_FORMAT(dp.tanggal, '%b %Y') as bulan_label,
                DATE_FORMAT(dp.tanggal, '%Y-%m') as bulan_sort,
                SUM(dp.jumlah_terjual) as total
            FROM data_penjualan dp
            GROUP BY bulan_sort, bulan_label
            ORDER BY bulan_sort ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Ambil total penjualan per varian produk (untuk doughnut chart)
     * Format: [{ varian: 'Motif Bunga', total: 551 }, ...]
     */
    public function getGrafikPerVarian()
    {
        $this->db->query("
            SELECT 
                pr.nama_produk as varian,
                SUM(dp.jumlah_terjual) as total
            FROM data_penjualan dp
            JOIN data_produk pr ON dp.produk_id = pr.id
            GROUP BY pr.id, pr.nama_produk
            ORDER BY total DESC
        ");
        return $this->db->resultSet();
    }

    /**
     * Ambil total penjualan per kategori produk (untuk doughnut chart)
     * Format: [{ kategori: 'Daster Arab', total: 551 }, ...]
     */
    public function getGrafikPerKategori()
    {
        $this->db->query("
            SELECT 
                k.nama_kategori as kategori,
                SUM(dp.jumlah_terjual) as total
            FROM data_penjualan dp
            JOIN data_produk pr ON dp.produk_id = pr.id
            JOIN kategori k ON pr.kategori_id = k.id
            GROUP BY k.id, k.nama_kategori
            ORDER BY total DESC
        ");
        return $this->db->resultSet();
    }

    /**
     * Ambil total penjualan per bulan per varian (untuk halaman prediksi)
     * Format: [{ bulan: 'YYYY-MM', bulan_label: 'Januari 2025', varian: 'Motif Bunga', total: 120 }, ...]
     */
    public function getPenjualanBulananPerVarian()
    {
        $this->db->query("
            SELECT 
                DATE_FORMAT(dp.tanggal, '%Y-%m') as bulan,
                DATE_FORMAT(dp.tanggal, '%M %Y') as bulan_label,
                pr.nama_produk as varian,
                SUM(dp.jumlah_terjual) as total
            FROM data_penjualan dp
            JOIN data_produk pr ON dp.produk_id = pr.id
            GROUP BY bulan, bulan_label, pr.id, pr.nama_produk
            ORDER BY bulan ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Ambil total penjualan per bulan per kategori (untuk halaman prediksi by kategori daster)
     * Format: [{ bulan: 'YYYY-MM', bulan_label: 'Januari 2025', kategori: 'Daster Arab', total: 120 }, ...]
     */
    public function getPenjualanBulananPerKategori()
    {
        $this->db->query("
            SELECT 
                DATE_FORMAT(dp.tanggal, '%Y-%m') as bulan,
                DATE_FORMAT(dp.tanggal, '%M %Y') as bulan_label,
                k.nama_kategori as kategori,
                SUM(dp.jumlah_terjual) as total
            FROM data_penjualan dp
            JOIN data_produk pr ON dp.produk_id = pr.id
            JOIN kategori k ON pr.kategori_id = k.id
            GROUP BY bulan, bulan_label, k.id, k.nama_kategori
            ORDER BY bulan ASC
        ");
        return $this->db->resultSet();
    }

    public function tambahPenjualan($data)
    {
        $this->db->query("INSERT INTO data_penjualan (tanggal, produk_id, jumlah_terjual, user_id) VALUES (:tanggal, :produk_id, :terjual, :user_id)");
        $this->db->bind(':tanggal', $data['tanggal']);
        $this->db->bind(':produk_id', $data['produk_id']);
        $this->db->bind(':terjual', $data['terjual']);
        $this->db->bind(':user_id', $_SESSION['user_id'] ?? 1);
        return $this->db->execute();
    }

    public function updatePenjualan($data)
    {
        $this->db->query("UPDATE data_penjualan SET tanggal = :tanggal, produk_id = :produk_id, jumlah_terjual = :terjual WHERE id = :id");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':tanggal', $data['tanggal']);
        $this->db->bind(':produk_id', $data['produk_id']);
        $this->db->bind(':terjual', $data['terjual']);
        return $this->db->execute();
    }

    public function hapusPenjualan($id)
    {
        $this->db->query("DELETE FROM data_penjualan WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
