<?php
require_once __DIR__ . '/../../../core/auth_guard.php';
require_once __DIR__ . '/../../../models/Penjualan.php';

$model = new Penjualan();
$dataBulanan = $model->getGrafikBulanan();
$dataPerVarian = $model->getGrafikPerVarian();
$dataPerKategori = $model->getGrafikPerKategori();

// Siapkan data untuk line chart (penjualan per bulan)
$labelsBulanan = array_column($dataBulanan, 'bulan_label');
$totalsBulanan = array_map('intval', array_column($dataBulanan, 'total'));

// Siapkan data untuk doughnut chart (per varian)
$labelsVarian = array_column($dataPerVarian, 'varian');
$totalsVarian = array_map('intval', array_column($dataPerVarian, 'total'));

// Siapkan data untuk doughnut chart (per kategori)
$labelsKategori = array_column($dataPerKategori, 'kategori');
$totalsKategori = array_map('intval', array_column($dataPerKategori, 'total'));

// Palet warna dinamis
$palette = ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#3b82f6', '#a855f7', '#14b8a6', '#f97316', '#ec4899', '#84cc16'];
$warnaDoughnut = array_slice(array_merge($palette, $palette), 0, count($labelsVarian));
$warnaKategori = array_slice(array_merge($palette, $palette), 0, count($labelsKategori));

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

            <!-- Doughnut Chart: Per Kategori Daster -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-pie-chart me-2 text-primary"></i>Penjualan per Kategori Daster
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($dataPerKategori)): ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-pie-chart fs-1 opacity-25 d-block mb-2"></i>
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

            <!-- Doughnut Chart: Per Varian -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-pie-chart-fill me-2 text-primary"></i>Penjualan per Varian Produk
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($dataPerVarian)): ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-pie-chart fs-1 opacity-25 d-block mb-2"></i>
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
    const totalsBulanan = <?php echo json_encode($totalsBulanan); ?>;
    const labelsVarian = <?php echo json_encode($labelsVarian); ?>;
    const totalsVarian = <?php echo json_encode($totalsVarian); ?>;
    const warnaDoughnut = <?php echo json_encode($warnaDoughnut); ?>;
    
    const labelsKategori = <?php echo json_encode($labelsKategori); ?>;
    const totalsKategori = <?php echo json_encode($totalsKategori); ?>;
    const warnaKategori = <?php echo json_encode($warnaKategori); ?>;

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

    // 2. Doughnut Chart – Per Kategori
    if (document.getElementById('kategoriChart') && labelsKategori.length > 0) {
        new Chart(document.getElementById('kategoriChart'), {
            type: 'doughnut',
            data: {
                labels: labelsKategori,
                datasets: [{
                    data: totalsKategori,
                    backgroundColor: warnaKategori,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12 } }
                }
            }
        });
    }

    // 3. Doughnut Chart – Per varian
    if (document.getElementById('variantChart') && labelsVarian.length > 0) {
        new Chart(document.getElementById('variantChart'), {
            type: 'doughnut',
            data: {
                labels: labelsVarian,
                datasets: [{
                    data: totalsVarian,
                    backgroundColor: warnaDoughnut,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12 } }
                }
            }
        });
    }

</script>