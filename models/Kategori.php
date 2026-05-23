<?php

require_once __DIR__ . '/../core/Database.php';

class Kategori
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Mengambil semua kategori beserta jumlah produk yang menggunakan kategori tersebut
     */
    public function getAllKategori()
    {
        $this->db->query("
            SELECT k.*, COUNT(p.id) as jumlah_produk 
            FROM kategori k 
            LEFT JOIN data_produk p ON k.id = p.kategori_id 
            GROUP BY k.id
            ORDER BY k.id DESC
        ");
        return $this->db->resultSet();
    }

    public function getKategoriById($id)
    {
        $this->db->query("SELECT * FROM kategori WHERE id = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->resultSet();
        return !empty($result) ? $result[0] : null;
    }

    public function checkKategoriExist($nama, $excludeId = null)
    {
        if ($excludeId) {
            $this->db->query("SELECT id FROM kategori WHERE nama_kategori = :nama AND id != :exclude_id");
            $this->db->bind(':exclude_id', $excludeId);
        } else {
            $this->db->query("SELECT id FROM kategori WHERE nama_kategori = :nama");
        }
        $this->db->bind(':nama', $nama);
        $result = $this->db->resultSet();
        return !empty($result);
    }

    public function tambahKategori($data)
    {
        $this->db->query("INSERT INTO kategori (nama_kategori, deskripsi) VALUES (:nama, :desc)");
        $this->db->bind(':nama', $data['nama_kategori']);
        $this->db->bind(':desc', $data['deskripsi']);
        return $this->db->execute();
    }

    public function updateKategori($data)
    {
        $this->db->query("UPDATE kategori SET nama_kategori = :nama, deskripsi = :desc WHERE id = :id");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nama', $data['nama_kategori']);
        $this->db->bind(':desc', $data['deskripsi']);
        return $this->db->execute();
    }

    /**
     * Mengecek apakah kategori sedang digunakan oleh produk
     */
    public function isKategoriDipakai($id)
    {
        $this->db->query("SELECT COUNT(*) as total FROM data_produk WHERE kategori_id = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->resultSet();
        return !empty($result) ? ((int)$result[0]['total'] > 0) : false;
    }

    public function hapusKategori($id)
    {
        // Cegah penghapusan jika kategori sedang digunakan
        if ($this->isKategoriDipakai($id)) {
            return false;
        }

        $this->db->query("DELETE FROM kategori WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
