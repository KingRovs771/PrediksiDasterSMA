<?php

require_once __DIR__ . '/../core/Database.php';

class PrediksiLog
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
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
        $this->db->bind(':periode_n', (int)$data['periode_n']);
        $this->db->bind(':prediksi_bulan', $data['prediksi_bulan']);
        $this->db->bind(':nilai_prediksi', (float)$data['nilai_prediksi']);
        $this->db->bind(':mape', (float)$data['mape']);
        $this->db->bind(':smape', (float)$data['smape']);
        $this->db->bind(':user_id', (int)$data['user_id']);
        
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
