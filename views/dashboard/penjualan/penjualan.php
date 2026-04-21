<?php
require_once __DIR__ . '/../../../core/auth_guard.php';
require_once __DIR__ . '/../../../models/Penjualan.php';
require_once __DIR__ . '/../../../models/Produk.php';
$model = new Penjualan();
$model2 = new Produk();
$dataPenjualan = $model->getAllPenjualan();
$dataproduk = $model2->getAllProduk();
?>
<main class="main-content">
    <nav class="navbar navbar-light bg-white border-bottom px-4 sticky-top shadow-sm">
        <h5 class="mb-0 fw-bold">Data Penjualan</h5>
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

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Jadwal Penjualan Daster</h6>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-lg"></i> Tambah Data
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th>Varian</th>
                                <th>Terjual</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody">
                            <?php if (empty($dataPenjualan)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data penjualan.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;
                                foreach ($dataPenjualan as $row): ?>
                                    <tr>
                                        <td class="ps-4"><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['bulan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tahun']); ?></td>
                                        <td><?php echo htmlspecialchars($row['varian']); ?></td>
                                        <td><span
                                                class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2"><?php echo htmlspecialchars($row['terjual']); ?>
                                                pcs</span></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary me-1 text-decoration-none"
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                onclick="populateEdit(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="process_penjualan.php?action=delete&id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
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
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="process_penjualan.php?action=add" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Tambah Data Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal Penjualan</label>
                    <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Varian Produk</label>
                    <select name="produk_id" id="add_produk_id" class="form-select" required onchange="updateStokInfo(this, 'add_stok_info', 'terjual_add')">
                        <option value="">Pilih Varian Produk</option>
                        <?php if (!empty($dataproduk)): ?>
                            <?php foreach ($dataproduk as $row): ?>
                                <option value="<?php echo htmlspecialchars($row['id']); ?>"
                                    data-stok="<?php echo (int)$row['stok']; ?>"
                                    data-nama="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                                    <?php echo htmlspecialchars($row['nama_produk']); ?>
                                    (Stok: <?php echo (int)$row['stok']; ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Belum ada produk terdaftar</option>
                        <?php endif; ?>
                    </select>
                    <!-- Indikator stok dinamis -->
                    <div id="add_stok_info" class="mt-2 d-none"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah Terjual (Pcs)</label>
                    <input type="number" name="terjual" id="terjual_add" class="form-control" required min="1">
                    <div class="form-text text-muted">Maksimal sesuai stok yang tersedia.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="process_penjualan.php?action=update" method="POST" class="modal-content">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" id="edit_stok_awal" value="0"> <!-- stok saat ini + jumlah lama -->
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Data Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal Penjualan</label>
                    <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Varian Produk</label>
                    <select name="produk_id" id="edit_produk_id" class="form-select" required onchange="updateStokInfo(this, 'edit_stok_info', 'terjual_edit')">
                        <option value="">Pilih Varian Produk</option>
                        <?php if (!empty($dataproduk)): ?>
                            <?php foreach ($dataproduk as $row): ?>
                                <option value="<?php echo htmlspecialchars($row['id']); ?>"
                                    data-stok="<?php echo (int)$row['stok']; ?>"
                                    data-nama="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                                    <?php echo htmlspecialchars($row['nama_produk']); ?>
                                    (Stok: <?php echo (int)$row['stok']; ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Belum ada produk terdaftar</option>
                        <?php endif; ?>
                    </select>
                    <div id="edit_stok_info" class="mt-2 d-none"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah Terjual (Pcs)</label>
                    <input type="number" name="terjual" id="terjual_edit" class="form-control" required min="1">
                    <div class="form-text text-muted">Maksimal sesuai stok yang tersedia.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    /**
     * Update indikator stok saat user memilih produk di dropdown
     * @param {HTMLSelectElement} selectEl - elemen select yang berubah
     * @param {string} infoId             - id div info stok
     * @param {string} inputId            - id input jumlah terjual
     */
    function updateStokInfo(selectEl, infoId, inputId) {
        const selected = selectEl.options[selectEl.selectedIndex];
        const infoDiv  = document.getElementById(infoId);
        const inputEl  = document.getElementById(inputId);

        if (!selected || !selected.value) {
            infoDiv.classList.add('d-none');
            if (inputEl) inputEl.removeAttribute('max');
            return;
        }

        const stok  = parseInt(selected.dataset.stok ?? 0);
        const nama  = selected.dataset.nama ?? '';

        // Set max jumlah sesuai stok
        if (inputEl) inputEl.setAttribute('max', stok);

        infoDiv.classList.remove('d-none');

        if (stok === 0) {
            infoDiv.innerHTML = `<div class="alert alert-danger py-2 mb-0">
                <i class="bi bi-x-circle-fill me-1"></i>
                <strong>Stok ${nama} habis!</strong> Tidak dapat memasukkan data penjualan.
            </div>`;
            if (inputEl) inputEl.setAttribute('max', 0);
        } else if (stok <= 5) {
            infoDiv.innerHTML = `<div class="alert alert-warning py-2 mb-0">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Stok tersisa <strong>${stok} pcs</strong> — hampir habis! Segera restok.
            </div>`;
        } else {
            infoDiv.innerHTML = `<div class="alert alert-success py-2 mb-0">
                <i class="bi bi-check-circle-fill me-1"></i>
                Stok tersedia: <strong>${stok} pcs</strong>
            </div>`;
        }
    }

    function populateEdit(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_tanggal').value = data.tanggal;
        document.getElementById('terjual_edit').value = data.terjual;

        const sel = document.getElementById('edit_produk_id');
        sel.value = data.produk_id;

        // Trigger stok info update
        updateStokInfo(sel, 'edit_stok_info', 'terjual_edit');

        // Untuk edit: stok yang tersedia = stok sekarang + jumlah yang sudah tercatat (karena akan dikembalikan dulu)
        const selectedOpt = sel.options[sel.selectedIndex];
        if (selectedOpt) {
            const stokCurrent = parseInt(selectedOpt.dataset.stok ?? 0);
            const maxEdit = stokCurrent + parseInt(data.terjual);
            document.getElementById('terjual_edit').setAttribute('max', maxEdit);
            document.getElementById('edit_stok_awal').value = maxEdit;
        }
    }

    // Reset indikator stok saat modal ditutup
    document.getElementById('addModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('add_stok_info').classList.add('d-none');
        document.getElementById('add_stok_info').innerHTML = '';
        document.getElementById('add_produk_id').value = '';
        document.getElementById('terjual_add').value = '';
    });
</script>