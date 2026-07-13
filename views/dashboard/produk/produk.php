<?php
require_once __DIR__ . '/../../../core/auth_guard.php';
require_once __DIR__ . '/../../../models/Produk.php';
require_once __DIR__ . '/../../../models/Kategori.php';
$model = new Produk();
$katModel = new Kategori();
$dataProduk = $model->getAllProduk();
$categories = $katModel->getAllKategori();
?>
<main class="main-content">
    <nav class="navbar navbar-light bg-white border-bottom px-4 sticky-top shadow-sm">
        <h5 class="mb-0 fw-bold">Data Produk Daster</h5>
        <a href="process_logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </nav>

    <div class="container-fluid p-4">

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show shadow-sm"
                role="alert">
                <?php echo $_SESSION['flash_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
            ?>
        <?php endif; ?>

        <!-- Card Ringkasan per Kategori -->
        <div class="row g-3 mb-4">
            <?php 
            $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
            foreach ($categories as $index => $cat): 
                $namaKat = $cat['nama_kategori'];
                $varianCount = 0;
                $totalStok = 0;
                foreach ($dataProduk as $prod) {
                    if ($prod['kategori'] === $namaKat) {
                        $varianCount++;
                        $totalStok += (int)$prod['stok'];
                    }
                }
                $color = $colors[$index % count($colors)];
            ?>
                <div class="col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 card-hover" style="border-left: 4px solid var(--bs-<?php echo $color; ?>) !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-semibold text-uppercase tracking-wider"><?php echo htmlspecialchars($namaKat); ?></span>
                                <div class="bg-<?php echo $color; ?> bg-opacity-10 p-2 rounded-2 text-<?php echo $color; ?>">
                                    <i class="bi bi-tag-fill fs-5"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold mb-1"><?php echo $varianCount; ?> <span class="fs-6 text-muted fw-normal">Varian</span></h3>
                            <p class="text-muted mb-0 small">
                                <i class="bi bi-box-seam me-1"></i> Total Stok: <strong><?php echo $totalStok; ?></strong> Pcs
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-semibold">Katalog Produk</h6>
                <div class="d-flex gap-2">
                    <select id="filterKategori" class="form-select form-select-sm" style="width: 180px;">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['nama_kategori']); ?>"><?php echo htmlspecialchars($cat['nama_kategori']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProdukModal">
                        <i class="bi bi-plus-lg"></i> Tambah Produk
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($dataProduk)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data produk daster
                                        terdaftar.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;
                                foreach ($dataProduk as $row): ?>
                                    <tr class="produk-row" data-kategori="<?php echo htmlspecialchars($row['kategori']); ?>">
                                        <td class="ps-4"><?php echo $no++; ?></td>
                                        <td><span
                                                class="badge bg-secondary"><?php echo htmlspecialchars($row['kode_produk']); ?></span>
                                        </td>
                                        <td class="fw-medium text-dark"><?php echo htmlspecialchars($row['nama_produk']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($row['stok'] > 5): ?>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success px-2 py-1"><?php echo $row['stok']; ?>
                                                    Pcs</span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger px-2 py-1"><?php echo $row['stok']; ?>
                                                    Pcs (Tipis)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                                                data-bs-target="#editProdukModal"
                                                onclick="populateEditProduk(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="process_produk.php?action=delete&id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add Modal -->
<div class="modal fade" id="addProdukModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="/public/process_produk.php?action=add" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Kode Produk</label>
                    <input type="text" name="kode_produk" class="form-control" required placeholder="Cth: DST-001">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Varian Produk</label>
                    <input type="text" name="nama_produk" class="form-control" required
                        placeholder="Cth: Daster Motif Kelelawar">
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori_id" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nama_kategori']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" name="stok" class="form-control" required min="0" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editProdukModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="process_produk.php?action=update" method="POST" class="modal-content">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Kode Produk</label>
                    <input type="text" name="kode_produk" id="edit_kode_produk" class="form-control" required readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Varian Produk</label>
                    <input type="text" name="nama_produk" id="edit_nama_produk" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori_id" id="edit_kategori_id" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nama_kategori']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" id="edit_harga" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stok Item</label>
                        <input type="number" name="stok" id="edit_stok" class="form-control" required min="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Ubah Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    function populateEditProduk(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_kode_produk').value = data.kode_produk;
        document.getElementById('edit_nama_produk').value = data.nama_produk;
        document.getElementById('edit_kategori_id').value = data.kategori_id;
        document.getElementById('edit_harga').value = data.harga;
        document.getElementById('edit_stok').value = data.stok;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const filterKategori = document.getElementById('filterKategori');
        const rows = document.querySelectorAll('.produk-row');
        const tableBody = document.querySelector('table tbody');

        filterKategori.addEventListener('change', function () {
            const selectedKat = this.value;
            let visibleCount = 0;

            const noMatchRow = document.getElementById('noMatchProdukRow');
            if (noMatchRow) {
                noMatchRow.remove();
            }

            rows.forEach(row => {
                const rowKat = row.getAttribute('data-kategori');
                if (selectedKat === '' || rowKat === selectedKat) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleCount === 0 && rows.length > 0) {
                const emptyTr = document.createElement('tr');
                emptyTr.id = 'noMatchProdukRow';
                emptyTr.innerHTML = `
                    <td colspan="7" class="text-center py-4 text-muted">
                        Tidak ada data produk untuk kategori "${selectedKat}".
                    </td>
                `;
                tableBody.appendChild(emptyTr);
            }
        });
    });
</script>