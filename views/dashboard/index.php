<?php
require_once __DIR__ . '/../../core/auth_guard.php';
?>
<?php $page = $page ?? $_GET['page'] ?? 'dashboard'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tivayo Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #0f172a;
            color: #f8fafc;
            z-index: 1050;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            border-radius: 0.5rem;
            margin: 0.2rem 0.5rem;
            padding: 0.65rem 1rem;
            text-decoration: none;
            display: block;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: linear-gradient(90deg, #7c3aed, #6366f1);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <aside class="sidebar p-3 d-flex flex-column">
        <div class="d-flex align-items-center mb-4 px-2 pt-2">
            <div class="bg-primary bg-opacity-25 rounded-2 p-2 me-3"><i
                    class="bi bi-bag-heart-fill fs-4 text-primary"></i></div>
            <div>
                <h6 class="mb-0 fw-bold">Tivayo Collection</h6><small class="text-secondary">Prediksi Penjualan</small>
            </div>
        </div>
        <nav class="nav flex-column flex-grow-1">
            <a href="index.php?page=dashboard"
                class="nav-link <?php echo ($page === 'dashboard') ? 'active' : ''; ?>"><i
                    class="bi bi-grid-1x2-fill me-2"></i>Dashboard</a>
            <a href="index.php?page=kategori" class="nav-link <?php echo ($page === 'kategori') ? 'active' : ''; ?>"><i
                    class="bi bi-tags me-2"></i>Kategori Daster</a>
            <a href="index.php?page=produk" class="nav-link <?php echo ($page === 'produk') ? 'active' : ''; ?>"><i
                    class="bi bi-box-seam me-2"></i>Data Produk</a>
            <a href="index.php?page=penjualan"
                class="nav-link <?php echo ($page === 'penjualan') ? 'active' : ''; ?>"><i
                    class="bi bi-cart3 me-2"></i>Data Penjualan</a>
            <a href="index.php?page=grafik" class="nav-link <?php echo ($page === 'grafik') ? 'active' : ''; ?>"><i
                    class="bi bi-graph-up me-2"></i>Grafik Penjualan</a>
            <a href="index.php?page=prediksi" class="nav-link <?php echo ($page === 'prediksi') ? 'active' : ''; ?>"><i
                    class="bi bi-calculator me-2"></i>Hitung Prediksi</a>
            <a href="index.php?page=riwayat_prediksi" class="nav-link <?php echo ($page === 'riwayat_prediksi') ? 'active' : ''; ?>"><i
                    class="bi bi-clock-history me-2"></i>Riwayat Prediksi</a>
        </nav>
    </aside>

    <?php if ($page === 'penjualan'): ?>
        <?php require_once __DIR__ . '/penjualan/penjualan.php'; ?>
    <?php elseif ($page === 'produk'): ?>
        <?php require_once __DIR__ . '/produk/produk.php'; ?>
    <?php elseif ($page === 'kategori'): ?>
        <?php require_once __DIR__ . '/kategori/kategori.php'; ?>
    <?php elseif ($page === 'grafik'): ?>
        <?php require_once __DIR__ . '/laporan/laporan.php'; ?>
    <?php elseif ($page === 'prediksi'): ?>
        <?php require_once __DIR__ . '/prediksi/prediksi.php'; ?>
    <?php elseif ($page === 'riwayat_prediksi'): ?>
        <?php require_once __DIR__ . '/prediksi/riwayat.php'; ?>
    <?php else: ?>
        <main class="main-content">
            <nav class="navbar navbar-light bg-white border-bottom px-4 sticky-top shadow-sm">
                <h5 class="mb-0 fw-bold">Dashboard</h5>
                <a href="/public/process_logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </nav>

            <div class="container-fluid p-4">
                <div class="alert alert-primary d-flex align-items-center mb-4 shadow-sm">
                    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                    <div>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong>,
                        di <strong>Sistem Prediksi Penjualan Daster</strong>.</div>
                </div>

                <?php
                // Calculate dynamic dashboard stats
                require_once __DIR__ . '/../../models/Penjualan.php';
                $model = new Penjualan();
                $dataPenjualan = $model->getAllPenjualan();

                $totalSales = 0;
                $varianUnik = [];
                foreach ($dataPenjualan as $row) {
                    $totalSales += (int) $row['terjual'];
                    $varianUnik[] = $row['varian'];
                }
                $totalVarian = count(array_unique($varianUnik));
                $pendapatanEstimasi = $totalSales * 85000; // Asumsi harga Rp.85.000 per daster
                ?>

                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-2 text-primary">
                                        <i class="bi bi-bag-fill fs-4"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-1"><?php echo number_format($totalSales, 0, ',', '.'); ?> pcs</h4>
                                <p class="text-muted mb-0 small">Total Penjualan Daster</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="bg-success bg-opacity-10 p-2 rounded-2 text-success">
                                        <i class="bi bi-cash-stack fs-4"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-1">Rp
                                    <?php echo number_format($pendapatanEstimasi / 1000000, 1, ',', '.'); ?> Jt
                                </h4>
                                <p class="text-muted mb-0 small">Estimasi Pendapatan</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="bg-warning bg-opacity-10 p-2 rounded-2 text-warning">
                                        <i class="bi bi-box-seam-fill fs-4"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-1"><?php echo $totalVarian; ?></h4>
                                <p class="text-muted mb-0 small">Varian Produk Dinilai</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="bg-info bg-opacity-10 p-2 rounded-2 text-info">
                                        <i class="bi bi-graph-up-arrow fs-4"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold mb-1">SMA</h4>
                                <p class="text-muted mb-0 small">Metode Analisis Cerdas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-semibold">Penjualan Terbaru</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">No</th>
                                        <th>Bulan</th>
                                        <th>Varian</th>
                                        <th>Terjual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $months = [
                                        'January' => 'Januari',
                                        'February' => 'Februari',
                                        'March' => 'Maret',
                                        'April' => 'April',
                                        'May' => 'Mei',
                                        'June' => 'Juni',
                                        'July' => 'Juli',
                                        'August' => 'Agustus',
                                        'September' => 'September',
                                        'October' => 'Oktober',
                                        'November' => 'November',
                                        'December' => 'Desember'
                                    ];
                                    $latestSales = array_slice($dataPenjualan, 0, 5);
                                    if (count($latestSales) > 0) {
                                        $no = 1;
                                        foreach ($latestSales as $sale) {
                                            $bulanInggris = date('F', strtotime($sale['tanggal']));
                                            $bulanIndo = $months[$bulanInggris] ?? $bulanInggris;
                                            $tahun = date('Y', strtotime($sale['tanggal']));
                                            $waktu = $bulanIndo . ' ' . $tahun;
                                            echo "<tr>
                                                    <td class=\"ps-4\">{$no}</td>
                                                    <td>{$waktu}</td>
                                                    <td>" . htmlspecialchars($sale['varian'] ?? '') . "</td>
                                                    <td>" . htmlspecialchars($sale['terjual'] ?? '0') . "</td>
                                                  </tr>";
                                            $no++;
                                        }
                                    } else {
                                        echo "<tr><td colspan=\"4\" class=\"text-center text-muted py-3\">Belum ada data penjualan</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>