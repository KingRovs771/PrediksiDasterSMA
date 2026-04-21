<?php

require_once __DIR__ . '/../core/Database.php';

class Produk
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllProduk()
    {
        $this->db->query("SELECT * FROM data_produk ORDER BY id DESC");
        return $this->db->resultSet();
    }

    public function getProdukById($id)
    {
        $this->db->query("SELECT * FROM data_produk WHERE id = :id");
        $this->db->bind(':id', $id);

        $result = $this->db->resultSet();
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Ambil stok terkini produk berdasarkan ID
     */
    public function getStokById($id)
    {
        $this->db->query("SELECT stok FROM data_produk WHERE id = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->resultSet();
        return !empty($result) ? (int) $result[0]['stok'] : 0;
    }

    /**
     * Kurangi stok produk sebanyak $jumlah
     * HANYA kurangi jika stok mencukupi, kembalikan false jika tidak
     */
    public function kurangiStok($produk_id, $jumlah)
    {
        $stokSekarang = $this->getStokById($produk_id);
        if ($stokSekarang < $jumlah) {
            return false; // stok tidak cukup
        }
        $this->db->query("UPDATE data_produk SET stok = stok - :jumlah WHERE id = :id");
        $this->db->bind(':jumlah', $jumlah);
        $this->db->bind(':id', $produk_id);
        return $this->db->execute();
    }

    /**
     * Kembalikan stok produk (dipakai saat data penjualan dihapus)
     */
    public function tambahStok($produk_id, $jumlah)
    {
        $this->db->query("UPDATE data_produk SET stok = stok + :jumlah WHERE id = :id");
        $this->db->bind(':jumlah', $jumlah);
        $this->db->bind(':id', $produk_id);
        return $this->db->execute();
    }

    public function tambahProduk($data)
    {
        $this->db->query("INSERT INTO data_produk (kode_produk, nama_produk, kategori, harga, stok) VALUES (:kode, :nama, :kategori, :harga, :stok)");
        $this->db->bind(':kode', $data['kode_produk']);
        $this->db->bind(':nama', $data['nama_produk']);
        $this->db->bind(':kategori', $data['kategori']);
        $this->db->bind(':harga', $data['harga']);
        $this->db->bind(':stok', $data['stok']);
        return $this->db->execute();
    }

    public function updateProduk($data)
    {
        $this->db->query("UPDATE data_produk SET kode_produk = :kode, nama_produk = :nama, kategori = :kategori, harga = :harga, stok = :stok WHERE id = :id");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':kode', $data['kode_produk']);
        $this->db->bind(':nama', $data['nama_produk']);
        $this->db->bind(':kategori', $data['kategori']);
        $this->db->bind(':harga', $data['harga']);
        $this->db->bind(':stok', $data['stok']);
        return $this->db->execute();
    }

    public function hapusProduk($id)
    {
        $this->db->query("DELETE FROM data_produk WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
