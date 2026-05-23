<?php
require_once __DIR__ . '/../../../core/auth_guard.php';
require_once __DIR__ . '/../../../models/Kategori.php';
$model = new Kategori();
$dataKategori = $model->getAllKategori();
?>
<main class="main-content">
    <nav class="navbar navbar-light bg-white border-bottom px-4 sticky-top shadow-sm">
        <h5 class="mb-0 fw-bold">Kategori Produk Daster</h5>
        <a href="process_logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </nav>

    <div class="container-fluid p-4">

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show shadow-sm"
                role="alert">
                <?php if ($_SESSION['flash_type'] === 'success'): ?>
                    <i class="bi bi-check-circle-fill me-2"></i>
                <?php else: ?>
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php endif; ?>
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
                <h6 class="mb-0 fw-semibold"><i class="bi bi-tags me-2 text-primary"></i>Daftar Kategori Daster</h6>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addKategoriModal">
                    <i class="bi bi-plus-lg"></i> Tambah Kategori
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 80px">No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Jumlah Produk</th>
                                <th>Tanggal Dibuat</th>
                                <th class="text-center" style="width: 150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($dataKategori)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada kategori terdaftar.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;
                                foreach ($dataKategori as $row): ?>
                                    <tr>
                                        <td class="ps-4"><?php echo $no++; ?></td>
                                        <td class="fw-semibold text-dark">
                                            <span class="badge bg-purple bg-opacity-10 text-purple px-2 py-1.5" style="color: #7c3aed; background-color: rgba(124, 58, 237, 0.1)">
                                                <?php echo htmlspecialchars($row['nama_kategori']); ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small"><?php echo htmlspecialchars($row['deskripsi'] ?? 'Tidak ada deskripsi'); ?></td>
                                        <td class="text-center">
                                            <?php if ($row['jumlah_produk'] > 0): ?>
                                                <span class="badge bg-primary px-3 py-1.5 rounded-pill">
                                                    <?php echo $row['jumlah_produk']; ?> item
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary px-3 py-1.5 rounded-pill bg-opacity-50">
                                                    Kosong
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-secondary"><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                                                data-bs-target="#editKategoriModal"
                                                onclick="populateEditKategori(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <?php if ($row['jumlah_produk'] > 0): ?>
                                                <button class="btn btn-sm btn-outline-secondary" disabled 
                                                        title="Kategori ini tidak dapat dihapus karena masih memiliki produk">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <a href="process_kategori.php?action=delete&id=<?php echo $row['id']; ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus kategori \'<?php echo htmlspecialchars($row['nama_kategori']); ?>\' ini?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
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
<div class="modal fade" id="addKategoriModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="/public/process_kategori.php?action=add" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-tags me-2 text-primary"></i>Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kategori" class="form-control" required placeholder="Cth: Daster Ruffle, Daster Batik">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat mengenai jenis/kategori daster ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editKategoriModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="process_kategori.php?action=update" method="POST" class="modal-content">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Ubah Data Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kategori" id="edit_nama_kategori" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function populateEditKategori(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_nama_kategori').value = data.nama_kategori;
        document.getElementById('edit_deskripsi').value = data.deskripsi || '';
    }
</script>
