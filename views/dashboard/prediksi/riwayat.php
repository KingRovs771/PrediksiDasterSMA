<?php
require_once __DIR__ . '/../../../core/auth_guard.php';
require_once __DIR__ . '/../../../models/PrediksiLog.php';

$logModel = new PrediksiLog();

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        if ($logModel->hapusLog($id)) {
            $_SESSION['flash_message'] = "Catatan riwayat peramalan berhasil dihapus!";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Gagal menghapus catatan riwayat peramalan.";
            $_SESSION['flash_type'] = "danger";
        }
    }
    header("Location: index.php?page=riwayat_prediksi");
    exit;
}

$riwayatData = $logModel->getAllLog();

// Helper untuk format YYYY-MM ke Bulan Indo
$bulanIndo = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

function formatBulanIndo($ym) {
    global $bulanIndo;
    if (empty($ym) || strlen($ym) < 7) return $ym;
    $parts = explode('-', $ym);
    if (count($parts) < 2) return $ym;
    return ($bulanIndo[$parts[1]] ?? $parts[1]) . ' ' . $parts[0];
}
?>
<main class="main-content">
    <nav class="navbar navbar-light bg-white border-bottom px-4 sticky-top shadow-sm">
        <h5 class="mb-0 fw-bold">Riwayat Hasil Peramalan</h5>
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
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-primary"></i>Daftar Riwayat Peramalan (SMA)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Kategori</th>
                                <th>Rentang Data Aktual</th>
                                <th class="text-center">n (SMA)</th>
                                <th>Bulan Diramal</th>
                                <th class="text-center">Hasil Prediksi</th>
                                <th class="text-center">MAPE</th>
                                <th class="text-center">sMAPE</th>
                                <th>Dihitung Oleh</th>
                                <th>Waktu Perhitungan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($riwayatData)): ?>
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <i class="bi bi-folder-x fs-1 d-block mb-3 text-secondary"></i>
                                        Belum ada riwayat peramalan yang disimpan di database.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;
                                foreach ($riwayatData as $row): ?>
                                    <tr>
                                        <td class="ps-4"><?php echo $no++; ?></td>
                                        <td class="fw-semibold text-dark"><?php echo htmlspecialchars($row['kategori']); ?></td>
                                        <td class="small text-muted">
                                            <?php echo formatBulanIndo($row['bulan_awal']); ?> s/d <br>
                                            <?php echo formatBulanIndo($row['bulan_akhir']); ?>
                                        </td>
                                        <td class="text-center"><span class="badge bg-secondary"><?php echo $row['periode_n']; ?></span></td>
                                        <td class="fw-semibold text-success"><?php echo formatBulanIndo($row['prediksi_bulan']); ?></td>
                                        <td class="text-center fw-bold text-primary"><?php echo number_format($row['nilai_prediksi'], 2, ',', '.'); ?> pcs</td>
                                        <td class="text-center"><span class="badge bg-warning text-dark"><?php echo number_format($row['mape'], 2, ',', '.'); ?> %</span></td>
                                        <td class="text-center"><span class="badge bg-info text-dark"><?php echo number_format($row['smape'], 2, ',', '.'); ?> %</span></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['username'] ?? 'System'); ?></span></td>
                                        <td class="small text-muted"><?php echo date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
                                        <td class="text-center">
                                            <a href="index.php?page=riwayat_prediksi&action=delete&id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus catatan riwayat peramalan ini?');">
                                                <i class="bi bi-trash"></i> Hapus
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
