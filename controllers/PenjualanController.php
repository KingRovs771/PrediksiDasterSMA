<?php

require_once __DIR__ . '/../models/Penjualan.php';
require_once __DIR__ . '/../models/Produk.php';

class PenjualanController
{
    private $model;
    private $produkModel;

    public function __construct()
    {
        $this->model      = new Penjualan();
        $this->produkModel = new Produk();
    }

    public function handleRequest()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $action = $_GET['action'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($action === 'add') {
                $this->add($_POST);
            } elseif ($action === 'update') {
                $this->update($_POST);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($action === 'delete') {
                $this->delete($_GET['id'] ?? 0);
            } elseif ($action === 'get') {
                $id   = $_GET['id'] ?? 0;
                $data = $this->model->getPenjualanById($id);
                echo json_encode($data);
                exit;
            }
        }
    }

    private function add($data)
    {
        $produkId = (int)($data['produk_id'] ?? 0);
        $jumlah   = (int)($data['terjual'] ?? 0);

        // ── Cek stok sebelum menyimpan ──────────────────────
        $stokSekarang = $this->produkModel->getStokById($produkId);
        $produk       = $this->produkModel->getProdukById($produkId);
        $namaProduk   = $produk['nama_produk'] ?? 'produk';

        if ($stokSekarang <= 0) {
            $_SESSION['flash_message'] = "Gagal: Stok {$namaProduk} sudah habis (stok: 0).";
            $_SESSION['flash_type']    = "danger";
            header("Location: /public/index.php?page=penjualan");
            exit;
        }

        if ($jumlah > $stokSekarang) {
            $_SESSION['flash_message'] = "Stok tidak mencukupi! Stok {$namaProduk} tersisa {$stokSekarang} pcs, Anda memasukkan {$jumlah} pcs.";
            $_SESSION['flash_type']    = "warning";
            header("Location: /public/index.php?page=penjualan");
            exit;
        }

        // ── Simpan data penjualan ───────────────────────────
        if ($this->model->tambahPenjualan($data)) {
            // ── Kurangi stok produk ─────────────────────────
            $this->produkModel->kurangiStok($produkId, $jumlah);

            $stokSisa = $stokSekarang - $jumlah;
            if ($stokSisa == 0) {
                $_SESSION['flash_message'] = "Data penjualan berhasil ditambahkan! ⚠️ Stok {$namaProduk} kini habis (0 pcs). Segera restok!";
                $_SESSION['flash_type']    = "warning";
            } elseif ($stokSisa <= 5) {
                $_SESSION['flash_message'] = "Data penjualan berhasil ditambahkan! ⚠️ Stok {$namaProduk} hampir habis ({$stokSisa} pcs tersisa).";
                $_SESSION['flash_type']    = "warning";
            } else {
                $_SESSION['flash_message'] = "Data penjualan berhasil ditambahkan! Stok {$namaProduk} tersisa {$stokSisa} pcs.";
                $_SESSION['flash_type']    = "success";
            }
        } else {
            $_SESSION['flash_message'] = "Gagal menambahkan data penjualan.";
            $_SESSION['flash_type']    = "danger";
        }

        header("Location: /public/index.php?page=penjualan");
        exit;
    }

    private function update($data)
    {
        $produkId    = (int)($data['produk_id'] ?? 0);
        $jumlahBaru  = (int)($data['terjual'] ?? 0);
        $id          = (int)($data['id'] ?? 0);

        // Ambil data lama untuk hitung selisih stok
        $dataLama    = $this->model->getPenjualanById($id);
        $jumlahLama  = $dataLama ? (int)$dataLama['terjual'] : 0;
        $produkIdLama = $dataLama ? (int)$dataLama['produk_id'] : $produkId;

        // Kembalikan stok dari data lama dulu
        $this->produkModel->tambahStok($produkIdLama, $jumlahLama);

        // Cek apakah stok cukup untuk jumlah baru
        $stokSekarang = $this->produkModel->getStokById($produkId);
        $produk       = $this->produkModel->getProdukById($produkId);
        $namaProduk   = $produk['nama_produk'] ?? 'produk';

        if ($jumlahBaru > $stokSekarang) {
            // Kembalikan stok ke kondisi semula (batalkan tambah stok tadi)
            $this->produkModel->kurangiStok($produkIdLama, $jumlahLama);
            $_SESSION['flash_message'] = "Stok tidak mencukupi! Stok {$namaProduk} tersisa {$stokSekarang} pcs, Anda memasukkan {$jumlahBaru} pcs.";
            $_SESSION['flash_type']    = "warning";
            header("Location: /public/index.php?page=penjualan");
            exit;
        }

        if ($this->model->updatePenjualan($data)) {
            // Kurangi stok sesuai jumlah baru
            $this->produkModel->kurangiStok($produkId, $jumlahBaru);

            $stokSisa = $stokSekarang - $jumlahBaru;
            if ($stokSisa <= 5) {
                $_SESSION['flash_message'] = "Data penjualan diperbarui! ⚠️ Stok {$namaProduk} tersisa {$stokSisa} pcs.";
                $_SESSION['flash_type']    = "warning";
            } else {
                $_SESSION['flash_message'] = "Data penjualan berhasil diperbarui! Stok {$namaProduk} tersisa {$stokSisa} pcs.";
                $_SESSION['flash_type']    = "success";
            }
        } else {
            // Batalkan tambah stok tadi karena update gagal
            $this->produkModel->kurangiStok($produkIdLama, $jumlahLama);
            $_SESSION['flash_message'] = "Gagal memperbarui data penjualan.";
            $_SESSION['flash_type']    = "danger";
        }

        header("Location: /public/index.php?page=penjualan");
        exit;
    }

    private function delete($id)
    {
        // Ambil data penjualan sebelum dihapus untuk kembalikan stok
        $dataLama = $this->model->getPenjualanById($id);

        if ($this->model->hapusPenjualan($id)) {
            // Kembalikan stok produk
            if ($dataLama) {
                $this->produkModel->tambahStok($dataLama['produk_id'], $dataLama['terjual']);
            }
            $_SESSION['flash_message'] = "Data penjualan berhasil dihapus dan stok produk dikembalikan.";
            $_SESSION['flash_type']    = "success";
        } else {
            $_SESSION['flash_message'] = "Gagal menghapus data penjualan.";
            $_SESSION['flash_type']    = "danger";
        }

        header("Location: /public/index.php?page=penjualan");
        exit;
    }
}
