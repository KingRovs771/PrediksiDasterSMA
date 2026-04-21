<?php
require_once __DIR__ . '/../../../core/auth_guard.php';
require_once __DIR__ . '/../../../models/Penjualan.php';

$model = new Penjualan();
$rawData = $model->getPenjualanBulananPerVarian();

// Konversi nama bulan Inggris → Indonesia dari DB
$bulanMap = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember',
];

// Susun data per varian: { 'Motif Bunga' => [{ bulan:'YYYY-MM', label:'Januari 2025', total:120 }, ...] }
$dataByVarian = [];
foreach ($rawData as $row) {
    $v = $row['varian'];
    if (!isset($dataByVarian[$v])) {
        $dataByVarian[$v] = [];
    }
    // Terjemahkan nama bulan
    $parts = explode(' ', $row['bulan_label'], 2); // "January 2025"
    $labelIndo = ($bulanMap[$parts[0]] ?? $parts[0]) . ' ' . ($parts[1] ?? '');
    $dataByVarian[$v][] = [
        'bulan'  => $row['bulan'],        // YYYY-MM
        'label'  => $labelIndo,           // "Januari 2025"
        'total'  => (int) $row['total'],
    ];
}
$varianList = array_keys($dataByVarian);

// Cari bulan min dan max dari semua data
$allBulan = array_column($rawData, 'bulan');
$bulanMin = !empty($allBulan) ? min($allBulan) : date('Y-m', strtotime('-1 year'));
$bulanMax = !empty($allBulan) ? max($allBulan) : date('Y-m');
?>
<main class="main-content">
    <nav class="navbar navbar-light bg-white border-bottom px-4 sticky-top shadow-sm">
        <h5 class="mb-0 fw-bold">Hitung Prediksi – Single Moving Average (SMA)</h5>
    </nav>

    <div class="container-fluid p-4">

        <?php if (empty($varianList)): ?>
            <div class="alert alert-warning d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>Belum ada data penjualan. Tambahkan data penjualan terlebih dahulu.</div>
            </div>
        <?php endif; ?>

        <!-- Form Pilih Parameter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-sliders me-2 text-primary"></i>Parameter Prediksi</h6>
            </div>
            <div class="card-body">
                <form id="predictionForm">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Jenis Daster <span class="text-danger">*</span></label>
                            <select class="form-select" id="variantSelect" <?php echo empty($varianList) ? 'disabled' : ''; ?>>
                                <?php if (empty($varianList)): ?>
                                    <option value="">Belum ada data varian</option>
                                <?php else: ?>
                                    <?php foreach ($varianList as $v): ?>
                                        <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bulan Awal <span class="text-danger">*</span></label>
                            <input type="month" class="form-control" id="bulanAwal"
                                value="<?php echo $bulanMin; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bulan Akhir <span class="text-danger">*</span></label>
                            <input type="month" class="form-control" id="bulanAkhir"
                                value="<?php echo $bulanMax; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Periode Moving (n) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="periodeN" value="3" min="2" max="12" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jumlah Bulan Diramal <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlahDiramal" value="1" min="1" max="24" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-calculator me-1"></i> Hitung Prediksi
                            </button>
                        </div>
                    </div>
                </form>

                <?php if (!empty($dataByVarian)): ?>
                <div class="mt-3 pt-3 border-top">
                    <p class="text-muted small mb-2"><i class="bi bi-info-circle me-1"></i>Data tersedia per varian:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($dataByVarian as $v => $rows): ?>
                            <span class="badge bg-light text-dark border">
                                <?php echo htmlspecialchars($v); ?>:
                                <strong><?php echo count($rows); ?> bulan</strong>
                                (<?php echo $rows[0]['label']; ?> s/d <?php echo $rows[count($rows)-1]['label']; ?>)
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Hasil (tersembunyi sebelum hitung) -->
        <div id="resultWrapper" class="d-none">

            <!-- Tabel Perhitungan SMA -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-table me-2 text-primary"></i>
                        Tabel Perhitungan SMA – <span id="lblVarian"></span>
                    </h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary" id="lblPeriode"></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0" id="smaTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-center" style="width:50px">NO</th>
                                    <th>TANGGAL</th>
                                    <th class="text-center">Xt</th>
                                    <th class="text-center">Ft</th>
                                    <th class="text-center">Xt-Ft</th>
                                    <th class="text-center">MAPE</th>
                                    <th class="text-center">sMAPE</th>
                                </tr>
                            </thead>
                            <tbody id="smaTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Hasil Prediksi -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-bullseye me-2 text-success"></i>Hasil Prediksi Penjualan Daster Selanjutnya</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">TANGGAL</th>
                                <th class="text-center">PENJUALAN</th>
                            </tr>
                        </thead>
                        <tbody id="hasilPrediksiBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Grafik Aktual vs Prediksi SMA -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-graph-up-arrow me-2 text-primary"></i>
                        Grafik Aktual vs Prediksi SMA – <span id="chartLblVarian"></span>
                    </h6>
                    <span class="badge bg-success bg-opacity-10 text-success" id="chartLblPeriode"></span>
                </div>
                <div class="card-body">
                    <div style="height: 360px;">
                        <canvas id="prediksiChart"></canvas>
                    </div>
                </div>
            </div>

        </div><!-- /#resultWrapper -->
    </div>
</main>

<script>
    // Data dari PHP: { 'NamaProduk': [{ bulan:'YYYY-MM', label:'Januari 2025', total:120 }, ...] }
    const DBData = <?php echo json_encode($dataByVarian); ?>;

    // Daftar nama bulan Indonesia untuk labeling prediksi ke depan
    const BULAN_ID = ['Januari','Februari','Maret','April','Mei','Juni',
                      'Juli','Agustus','September','Oktober','November','Desember'];

    /**
     * Menghasilkan label bulan Indonesia dari string 'YYYY-MM'
     */
    function labelDariBulan(ym) {
        const [y, m] = ym.split('-');
        return BULAN_ID[parseInt(m, 10) - 1] + ' ' + y;
    }

    /**
     * Tambah sejumlah bulan ke string 'YYYY-MM', kembalikan 'YYYY-MM' baru
     */
    function tambahBulan(ym, n) {
        const [y, m] = ym.split('-').map(Number);
        const date = new Date(y, m - 1 + n, 1);
        const ny = date.getFullYear();
        const nm = String(date.getMonth() + 1).padStart(2, '0');
        return `${ny}-${nm}`;
    }

    let prediksiChartInstance = null;

    document.getElementById('predictionForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const variant      = document.getElementById('variantSelect').value;
        const bulanAwal    = document.getElementById('bulanAwal').value;   // YYYY-MM
        const bulanAkhir   = document.getElementById('bulanAkhir').value;  // YYYY-MM
        const n            = parseInt(document.getElementById('periodeN').value);
        const jumlahDiramal = parseInt(document.getElementById('jumlahDiramal').value);

        // ── Validasi ──────────────────────────────────────────
        if (!variant || !DBData[variant]) {
            alert('Pilih varian produk terlebih dahulu.'); return;
        }
        if (!bulanAwal || !bulanAkhir) {
            alert('Bulan Awal dan Akhir harus diisi.'); return;
        }
        if (bulanAwal > bulanAkhir) {
            alert('Bulan Awal tidak boleh lebih besar dari Bulan Akhir.'); return;
        }

        // ── Filter data sesuai rentang bulan ────────────────
        const allRows = DBData[variant];
        const rows = allRows.filter(r => r.bulan >= bulanAwal && r.bulan <= bulanAkhir);

        if (rows.length < n) {
            alert(`Data dalam rentang ${labelDariBulan(bulanAwal)} – ${labelDariBulan(bulanAkhir)} hanya ada ${rows.length} bulan.\nDibutuhkan minimal ${n} bulan untuk SMA-${n}.\n\nGunakan Periode Moving yang lebih kecil atau perluas rentang bulan.`);
            return;
        }

        // ── Hitung SMA untuk data historis ────────────────────
        const tableRows = [];
        let sumMAPE = 0, sumSMAPE = 0, validCount = 0;

        const chartLabels   = [];
        const chartAktual   = [];
        const chartPrediksi = [];

        for (let i = 0; i < rows.length; i++) {
            const Xt    = rows[i].total;
            const label = rows[i].label;  // "Januari 2025"
            let Ft = null, XtFt = null, mapeRow = null, smapeRow = null;

            if (i >= n) {
                const slice = rows.slice(i - n, i).map(r => r.total);
                Ft       = parseFloat((slice.reduce((a, b) => a + b, 0) / n).toFixed(2));
                XtFt     = parseFloat((Xt - Ft).toFixed(2));
                const absErr = Math.abs(XtFt);
                mapeRow  = Xt !== 0 ? parseFloat((absErr / Xt * 100).toFixed(2)) : 0;
                const denom = (Xt + Ft) / 2;
                smapeRow = denom !== 0 ? parseFloat((absErr / denom * 100).toFixed(2)) : 0;

                sumMAPE  += mapeRow;
                sumSMAPE += smapeRow;
                validCount++;
            }
            tableRows.push({ label, Xt, Ft, XtFt, mapeRow, smapeRow, isDiramal: false });
            chartLabels.push(label);
            chartAktual.push(Xt);
            chartPrediksi.push(Ft);
        }

        // ── Hitung prediksi multi-bulan ke depan ─────────────
        const buffer = [...rows.map(r => r.total)];
        const hasilPrediksi = [];
        let lastBulan = rows[rows.length - 1].bulan; // YYYY-MM

        for (let p = 0; p < jumlahDiramal; p++) {
            const slice = buffer.slice(-n);
            const Ft = parseFloat((slice.reduce((a, b) => a + b, 0) / n).toFixed(2));
            buffer.push(Ft);

            lastBulan = tambahBulan(lastBulan, 1);
            const labelBulan = labelDariBulan(lastBulan);

            hasilPrediksi.push({ label: labelBulan, Ft });
            tableRows.push({ label: labelBulan, Xt: null, Ft, XtFt: null, mapeRow: null, smapeRow: null, isDiramal: true });

            chartLabels.push(labelBulan);
            chartAktual.push(null);
            chartPrediksi.push(Ft);
        }

        // ── Rata-rata MAPE & sMAPE ────────────────────────────
        const MAPE  = validCount ? parseFloat((sumMAPE  / validCount).toFixed(2)) : 0;
        const SMAPE = validCount ? parseFloat((sumSMAPE / validCount).toFixed(2)) : 0;

        // ── Render Tabel ──────────────────────────────────────
        const tbody = document.getElementById('smaTableBody');
        tbody.innerHTML = '';
        let no = 1;
        tableRows.forEach(r => {
            const row = document.createElement('tr');
            if (r.isDiramal) row.className = 'table-success';
            const dash = '<span class="text-muted">–</span>';
            row.innerHTML = `
                <td class="text-center small text-muted">${no++}</td>
                <td class="small ${r.isDiramal ? 'fw-semibold text-success' : ''}"
                >${r.label}${r.isDiramal ? ' <span class="badge bg-success ms-1">Diramal</span>' : ''}</td>
                <td class="text-center">${r.Xt !== null ? r.Xt : dash}</td>
                <td class="text-center ${r.isDiramal ? 'fw-bold text-success' : ''}">${r.Ft !== null ? r.Ft.toFixed(2) : dash}</td>
                <td class="text-center ${r.XtFt !== null && r.XtFt < 0 ? 'text-danger' : ''}">${r.XtFt !== null ? r.XtFt.toFixed(2) : dash}</td>
                <td class="text-center">${r.mapeRow !== null ? r.mapeRow.toFixed(2) : dash}</td>
                <td class="text-center">${r.smapeRow !== null ? r.smapeRow.toFixed(2) : dash}</td>
            `;
            tbody.appendChild(row);
        });

        // Baris MAPE & sMAPE di footer tabel
        tbody.innerHTML += `
            <tr class="table-warning fw-semibold">
                <td class="text-center" colspan="5">MAPE</td>
                <td class="text-center" colspan="2">${MAPE.toFixed(2)}</td>
            </tr>
            <tr class="table-info fw-semibold">
                <td class="text-center" colspan="5">sMAPE</td>
                <td class="text-center" colspan="2">${SMAPE.toFixed(2)}</td>
            </tr>
        `;

        // ── Label header ─────────────────────────────────────
        document.getElementById('lblVarian').textContent       = variant;
        document.getElementById('lblPeriode').textContent      = `n = ${n} | ${labelDariBulan(bulanAwal)} – ${labelDariBulan(bulanAkhir)}`;
        document.getElementById('chartLblVarian').textContent  = variant;
        document.getElementById('chartLblPeriode').textContent = `SMA-${n}`;

        // ── Tabel Hasil Prediksi ──────────────────────────────
        const hasilBody = document.getElementById('hasilPrediksiBody');
        hasilBody.innerHTML = '';
        hasilPrediksi.forEach((hp, idx) => {
            hasilBody.innerHTML += `
                <tr>
                    <td class="ps-4 fw-semibold text-success">${hp.label}</td>
                    <td class="text-center fw-bold text-success">${Math.round(hp.Ft)}</td>
                </tr>
            `;
        });
        // Baris "dst" jika ada lebih dari 1 prediksi (atau selalu tampilkan sebagai penanda)
        hasilBody.innerHTML += `
            <tr class="text-muted small">
                <td class="ps-4 fst-italic">dst</td>
                <td class="text-center fst-italic">dst</td>
            </tr>
        `;

        // ── Render Grafik ─────────────────────────────────────
        if (prediksiChartInstance) prediksiChartInstance.destroy();
        Chart.defaults.font.family = "'Segoe UI', sans-serif";

        const pointColors = chartPrediksi.map((v, i) =>
            i >= rows.length ? '#22c55e' : '#f59e0b'
        );
        const pointSizes = chartPrediksi.map((v, i) =>
            i >= rows.length ? 8 : 5
        );

        prediksiChartInstance = new Chart(document.getElementById('prediksiChart'), {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Aktual (Y)',
                        data: chartAktual,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        spanGaps: false
                    },
                    {
                        label: `Prediksi SMA-${n} (Ft)`,
                        data: chartPrediksi,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.05)',
                        borderDash: [6, 4],
                        fill: false,
                        tension: 0.4,
                        spanGaps: true,
                        pointBackgroundColor: pointColors,
                        pointRadius: pointSizes,
                        pointHoverRadius: pointSizes.map(s => s + 2)
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y !== null ? ctx.parsed.y + ' pcs' : 'N/A'}`
                        }
                    }
                },
                scales: {
                    x: { ticks: { maxRotation: 45 } },
                    y: { beginAtZero: false, ticks: { precision: 0 } }
                }
            }
        });

        // Tampilkan & scroll
        document.getElementById('resultWrapper').classList.remove('d-none');
        document.getElementById('resultWrapper').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
</script>