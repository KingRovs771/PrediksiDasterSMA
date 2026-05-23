<?php

require_once __DIR__ . '/../models/Produk.php';

class ProdukController
{
    private $model;

    public function __construct()
    {
        $this->model = new Produk();
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
        if (empty($data['kategori_id'])) {
            $data['kategori_id'] = 1;
        } else {
            $data['kategori_id'] = (int)$data['kategori_id'];
        }

        if ($this->model->tambahProduk($data)) {
            $_SESSION['flash_message'] = "Katalog Produk berhasil ditambahkan!";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Gagal menambahkan produk. Cek duplikasi kode produk.";
            $_SESSION['flash_type'] = "danger";
        }
        header("Location: /public/index.php?page=produk");
        exit;
    }

    private function update($data)
    {
        if (empty($data['kategori_id'])) {
            $data['kategori_id'] = 1;
        } else {
            $data['kategori_id'] = (int)$data['kategori_id'];
        }

        if ($this->model->updateProduk($data)) {
            $_SESSION['flash_message'] = "Data produk berhasil diperbarui!";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Gagal memperbarui data produk.";
            $_SESSION['flash_type'] = "danger";
        }
        header("Location: /public/index.php?page=produk");
        exit;
    }

    private function delete($id)
    {
        if ($this->model->hapusProduk($id)) {
            $_SESSION['flash_message'] = "Katalog Produk berhasil dihapus dari sistem!";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Gagal menghapus data produk.";
            $_SESSION['flash_type'] = "danger";
        }
        header("Location: /public/index.php?page=produk");
        exit;
    }
}
