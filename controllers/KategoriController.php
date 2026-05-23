<?php

require_once __DIR__ . '/../models/Kategori.php';

class KategoriController
{
    private $model;

    public function __construct()
    {
        $this->model = new Kategori();
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
            }
        }
    }

    private function add($data)
    {
        $nama = trim($data['nama_kategori'] ?? '');
        $desc = trim($data['deskripsi'] ?? '');

        if (empty($nama)) {
            $_SESSION['flash_message'] = "Nama Kategori tidak boleh kosong!";
            $_SESSION['flash_type'] = "danger";
            header("Location: /public/index.php?page=kategori");
            exit;
        }

        // Cek duplikasi nama kategori
        if ($this->model->checkKategoriExist($nama)) {
            $_SESSION['flash_message'] = "Gagal: Kategori '{$nama}' sudah ada di database!";
            $_SESSION['flash_type'] = "danger";
            header("Location: /public/index.php?page=kategori");
            exit;
        }

        if ($this->model->tambahKategori([
            'nama_kategori' => $nama,
            'deskripsi' => $desc
        ])) {
            $_SESSION['flash_message'] = "Kategori '{$nama}' berhasil ditambahkan!";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Gagal menambahkan kategori baru.";
            $_SESSION['flash_type'] = "danger";
        }
        header("Location: /public/index.php?page=kategori");
        exit;
    }

    private function update($data)
    {
        $id = (int)($data['id'] ?? 0);
        $nama = trim($data['nama_kategori'] ?? '');
        $desc = trim($data['deskripsi'] ?? '');

        if (empty($nama)) {
            $_SESSION['flash_message'] = "Nama Kategori tidak boleh kosong!";
            $_SESSION['flash_type'] = "danger";
            header("Location: /public/index.php?page=kategori");
            exit;
        }

        // Cek duplikasi nama kategori dengan id yang berbeda
        if ($this->model->checkKategoriExist($nama, $id)) {
            $_SESSION['flash_message'] = "Gagal memperbarui: Kategori dengan nama '{$nama}' sudah terdaftar!";
            $_SESSION['flash_type'] = "danger";
            header("Location: /public/index.php?page=kategori");
            exit;
        }

        if ($this->model->updateKategori([
            'id' => $id,
            'nama_kategori' => $nama,
            'deskripsi' => $desc
        ])) {
            $_SESSION['flash_message'] = "Kategori berhasil diperbarui menjadi '{$nama}'!";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Gagal memperbarui kategori.";
            $_SESSION['flash_type'] = "danger";
        }
        header("Location: /public/index.php?page=kategori");
        exit;
    }

    private function delete($id)
    {
        $id = (int)$id;
        $kategori = $this->model->getKategoriById($id);
        $nama = $kategori['nama_kategori'] ?? 'kategori';

        // Cek apakah kategori masih dipakai oleh produk
        if ($this->model->isKategoriDipakai($id)) {
            $_SESSION['flash_message'] = "Gagal menghapus! Kategori '{$nama}' masih digunakan oleh produk terdaftar. Hapus atau pindahkan produk terlebih dahulu.";
            $_SESSION['flash_type'] = "warning";
            header("Location: /public/index.php?page=kategori");
            exit;
        }

        if ($this->model->hapusKategori($id)) {
            $_SESSION['flash_message'] = "Kategori '{$nama}' berhasil dihapus dari sistem!";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Gagal menghapus kategori.";
            $_SESSION['flash_type'] = "danger";
        }
        header("Location: /public/index.php?page=kategori");
        exit;
    }
}
