<?php
require_once __DIR__ . '/../../../core/auth_guard.php';
require_once __DIR__ . '/../../../models/Penjualan.php';
require_once __DIR__ . '/../../../models/Kategori.php';
require_once __DIR__ . '/../../../models/Produk.php';

$model = new Penjualan();
$katModel = new Kategori();
$prodModel = new Produk();

$dataBulanan = $model->getGrafikBulanan();
$rawBulananKategori = $model->getPenjualanBulananPerKategori();
$rawBulananVarian = $model->getPenjualanBulananPerVarian();

$categories = $katModel->getAllKategori();
$products = $prodModel->getAllProduk();

// Siapkan data untuk line chart (penjualan per bulan)
$labelsBulanan = array_column($dataBulanan, 'bulan_label');
$keysBulanan = array_column($dataBulanan, 'bulan_sort'); // format YYYY-MM
$totalsBulanan = array_map('intval', array_column($dataBulanan, 'total'));

$tahunAktif = !empty($labelsBulanan) ? date('Y') : date('Y');
?>
<main class="main-content">
    <nav class="navbar navbar-light bg-white border-bottom px-4 sticky-top shadow-sm">
        <h5 class="mb-0 fw-bold">Grafik Analisis Penjualan</h5>
    </nav>

    <div class="container-fluid p-4">

        <?php if (empty($dataBulanan)): ?>
            <div class="alert alert-warning d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>Belum ada data penjualan. Tambahkan data penjualan terlebih dahulu untuk melihat grafik.</div>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Line Chart: Total Penjualan per Bulan -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-graph-up me-2 text-primary"></i>Total Penjualan per Bulan
                        </h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            <?php echo count($dataBulanan); ?> periode
                        </span>
                    </div>
                    <div class="card-body">
                        <div style="height: 350px;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Line Chart: Per Kategori Daster -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-graph-up me-2 text-success"></i>Tren Penjualan per Kategori
                        </h6>
                        <select id="filterChartKategori" class="form-select form-select-sm" style="width: 160px;">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['nama_kategori']); ?>">
                                    <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="card-body">
                        <?php if (empty($rawBulananKategori)): ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-graph-up fs-1 opacity-25 d-block mb-2"></i>
                                Belum ada data kategori
                            </div>
                        <?php else: ?>
                            <div style="height: 320px;">
                                <canvas id="kategoriChart"></canvas>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Line Chart: Per Varian -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-graph-up me-2 text-warning"></i>Tren Penjualan per Varian Produk
                        </h6>
                        <select id="filterChartVarian" class="form-select form-select-sm" style="width: 160px;">
                            <option value="">Semua Produk</option>
                            <?php foreach ($products as $prod): ?>
                                <option value="<?php echo htmlspecialchars($prod['nama_produk']); ?>">
                                    <?php echo htmlspecialchars($prod['nama_produk']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="card-body">
                        <?php if (empty($rawBulananVarian)): ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-graph-up fs-1 opacity-25 d-block mb-2"></i>
                                Belum ada data varian produk
                            </div>
                        <?php else: ?>
                            <div style="height: 320px;">
                                <canvas id="variantChart"></canvas>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Data dari PHP
    const labelsBulanan = <?php echo json_encode($labelsBulanan); ?>;
    const keysBulanan = <?php echo json_encode($keysBulanan); ?>;
    const totalsBulanan = <?php echo json_encode($totalsBulanan); ?>;
    
    const rawBulananKategori = <?php echo json_encode($rawBulananKategori); ?>;
    const rawBulananVarian = <?php echo json_encode($rawBulananVarian); ?>;

    Chart.defaults.font.family = "'Segoe UI', sans-serif";

    // 1. Line Chart – Total penjualan per bulan
    if (document.getElementById('salesChart') && labelsBulanan.length > 0) {
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: labelsBulanan,
                datasets: [{
                    label: 'Total Terjual (pcs)',
                    data: totalsBulanan,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6366f1',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    // 2. Line Chart – Per Kategori (Dinamis dengan Filter)
    let kategoriChartInstance = null;
    function renderKategoriChart(selectedKat) {
        const monthlyData = {};
        keysBulanan.forEach(key => {
            monthlyData[key] = 0;
        });

        rawBulananKategori.forEach(item => {
            if (selectedKat === "" || item.kategori === selectedKat) {
                const key = item.bulan;
                if (monthlyData[key] !== undefined) {
                    monthlyData[key] += parseInt(item.total);
                }
            }
        });

        const dataPoints = keysBulanan.map(key => monthlyData[key]);
        const ctx = document.getElementById('kategoriChart');
        if (!ctx) return;

        if (kategoriChartInstance) {
            kategoriChartInstance.destroy();
        }

        kategoriChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelsBulanan,
                datasets: [{
                    label: selectedKat === "" ? 'Total Terjual (Semua Kategori)' : `Terjual (Kategori ${selectedKat})`,
                    data: dataPoints,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#22c55e',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    if (document.getElementById('kategoriChart') && labelsBulanan.length > 0) {
        renderKategoriChart("");
        document.getElementById('filterChartKategori').addEventListener('change', function() {
            renderKategoriChart(this.value);
        });
    }

    // 3. Line Chart – Per Varian (Dinamis dengan Filter)
    let varianChartInstance = null;
    function renderVarianChart(selectedVarian) {
        const monthlyData = {};
        keysBulanan.forEach(key => {
            monthlyData[key] = 0;
        });

        rawBulananVarian.forEach(item => {
            if (selectedVarian === "" || item.varian === selectedVarian) {
                const key = item.bulan;
                if (monthlyData[key] !== undefined) {
                    monthlyData[key] += parseInt(item.total);
                }
            }
        });

        const dataPoints = keysBulanan.map(key => monthlyData[key]);
        const ctx = document.getElementById('variantChart');
        if (!ctx) return;

        if (varianChartInstance) {
            varianChartInstance.destroy();
        }

        varianChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelsBulanan,
                datasets: [{
                    label: selectedVarian === "" ? 'Total Terjual (Semua Produk)' : `Terjual (Varian ${selectedVarian})`,
                    data: dataPoints,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#f59e0b',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    if (document.getElementById('variantChart') && labelsBulanan.length > 0) {
        renderVarianChart("");
        document.getElementById('filterChartVarian').addEventListener('change', function() {
            renderVarianChart(this.value);
        });
    }
</script>