<?php
require_once __DIR__ . '/../../../core/auth_guard.php';
require_once __DIR__ . '/../../../models/Penjualan.php';

$model = new Penjualan();
$rawData = $model->getPenjualanBulananPerKategori();

// Konversi nama bulan Inggris → Indonesia dari DB
$bulanMap = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember',
];

// Susun data per kategori: { 'Daster Arab' => [{ bulan:'YYYY-MM', label:'Januari 2025', total:120 }, ...] }
$dataByKategori = [];
foreach ($rawData as $row) {
    $k = $row['kategori'];
    if (empty($k)) continue;
    if (!isset($dataByKategori[$k])) {
        $dataByKategori[$k] = [];
    }
    // Terjemahkan nama bulan
    $parts = explode(' ', $row['bulan_label'], 2); // "January 2025"
    $labelIndo = ($bulanMap[$parts[0]] ?? $parts[0]) . ' ' . ($parts[1] ?? '');
    $dataByKategori[$k][] = [
        'bulan'  => $row['bulan'],        // YYYY-MM
        'label'  => $labelIndo,           // "Januari 2025"
        'total'  => (int) $row['total'],
    ];
}
$kategoriList = array_keys($dataByKategori);

// Cari bulan min dan max dari semua data
$allBulan = array_column($rawData, 'bulan');
$bulanMin = !empty($allBulan) ? min($allBulan) : date('Y-m', strtotime('-1 year'));
$bulanMax = !empty($allBulan) ? max($allBulan) : date('Y-m');
?>
<main class="main-content">
    <nav class="navbar navbar-light bg-white border-bottom px-4 sticky-top shadow-sm">
        <h5 class="mb-0 fw-bold">Hitung Prediksi Kategori – Single Moving Average (SMA)</h5>
    </nav>

    <div class="container-fluid p-4">

        <?php if (empty($kategoriList)): ?>
            <div class="alert alert-warning d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>Belum ada data penjualan atau kategori terdaftar. Tambahkan produk dengan kategori dan data penjualan terlebih dahulu.</div>
            </div>
        <?php endif; ?>

        <!-- Form Pilih Parameter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-sliders me-2 text-primary"></i>Parameter Prediksi Kategori</h6>
            </div>
            <div class="card-body">
                <form id="predictionForm">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Kategori Daster <span class="text-danger">*</span></label>
                            <select class="form-select" id="kategoriSelect" <?php echo empty($kategoriList) ? 'disabled' : ''; ?>>
                                <?php if (empty($kategoriList)): ?>
                                    <option value="">Belum ada data kategori</option>
                                <?php else: ?>
                                    <?php foreach ($kategoriList as $k): ?>
                                        <option value="<?php echo htmlspecialchars($k); ?>"><?php echo htmlspecialchars($k); ?></option>
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
                            <button type="submit" class="btn btn-primary px-4" <?php echo empty($kategoriList) ? 'disabled' : ''; ?>>
                                <i class="bi bi-calculator me-1"></i> Hitung Prediksi Kategori
                            </button>
                        </div>
                    </div>
                </form>

                <?php if (!empty($dataByKategori)): ?>
                <div class="mt-3 pt-3 border-top">
                    <p class="text-muted small mb-2"><i class="bi bi-info-circle me-1"></i>Data tersedia per Kategori:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($dataByKategori as $k => $rows): ?>
                            <span class="badge bg-light text-dark border">
                                <?php echo htmlspecialchars($k); ?>:
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
                        Tabel Perhitungan SMA Kategori – <span id="lblKategori"></span>
                    </h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary" id="lblPeriode"></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0" id="smaTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-center" style="width:50px">NO</th>
                                    <th>BULAN</th>
                                    <th class="text-center">Xt (AKTUAL)</th>
                                    <th class="text-center">Ft (PREDIKSI)</th>
                                    <th class="text-center">Xt-Ft (ERROR)</th>
                                    <th class="text-center">MAPE (%)</th>
                                    <th class="text-center">sMAPE (%)</th>
                                </tr>
                            </thead>
                            <tbody id="smaTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Hasil Prediksi Proyeksi -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-bullseye me-2 text-success"></i>Hasil Proyeksi Prediksi Kategori Selanjutnya</h6>
                    <button type="button" class="btn btn-success btn-sm" id="btnSaveHistory">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Simpan ke Riwayat
                    </button>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">BULAN PERIODE</th>
                                <th class="text-center">HASIL PREDIKSI (PCS)</th>
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
                        Grafik Penjualan Aktual vs Prediksi – Kategori <span id="chartLblKategori"></span>
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
    // Menyimpan data peramalan aktif untuk fitur simpan riwayat
    let currentPredictionData = null;

    // Data dari PHP: { 'NamaKategori': [{ bulan:'YYYY-MM', label:'Januari 2025', total:120 }, ...] }
    const DBData = <?php echo json_encode($dataByKategori); ?>;

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

        const kategori     = document.getElementById('kategoriSelect').value;
        const bulanAwal    = document.getElementById('bulanAwal').value;   // YYYY-MM
        const bulanAkhir   = document.getElementById('bulanAkhir').value;  // YYYY-MM
        const n            = parseInt(document.getElementById('periodeN').value);
        const jumlahDiramal = parseInt(document.getElementById('jumlahDiramal').value);

        // ── Validasi ──────────────────────────────────────────
        if (!kategori || !DBData[kategori]) {
            alert('Pilih kategori daster terlebih dahulu.'); return;
        }
        if (!bulanAwal || !bulanAkhir) {
            alert('Bulan Awal dan Akhir harus diisi.'); return;
        }
        if (bulanAwal > bulanAkhir) {
            alert('Bulan Awal tidak boleh lebih besar dari Bulan Akhir.'); return;
        }

        // ── Filter data sesuai rentang bulan ────────────────
        const allRows = DBData[kategori];
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
                // Keep Ft in full double-precision!
                const FtFull = slice.reduce((a, b) => a + b, 0) / n;
                Ft = FtFull;
                
                // Keep error in full precision
                const XtFtFull = Xt - FtFull;
                XtFt = XtFtFull;
                
                const absErr = Math.abs(XtFtFull);
                
                // Keep MAPE and sMAPE in full precision
                const mapeRowFull = Xt !== 0 ? (absErr / Xt * 100) : 0;
                mapeRow = mapeRowFull;
                
                const denom = (Xt + FtFull) / 2;
                const smapeRowFull = denom !== 0 ? (absErr / denom * 100) : 0;
                smapeRow = smapeRowFull;

                sumMAPE  += mapeRowFull;
                sumSMAPE += smapeRowFull;
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
            const FtFull = slice.reduce((a, b) => a + b, 0) / n;
            buffer.push(FtFull);

            lastBulan = tambahBulan(lastBulan, 1);
            const labelBulan = labelDariBulan(lastBulan);

            hasilPrediksi.push({ label: labelBulan, Ft: FtFull, ym: lastBulan });
            tableRows.push({ label: labelBulan, Xt: null, Ft: FtFull, XtFt: null, mapeRow: null, smapeRow: null, isDiramal: true });

            chartLabels.push(labelBulan);
            chartAktual.push(null);
            chartPrediksi.push(FtFull);
        }

        // ── Rata-rata MAPE & sMAPE ────────────────────────────
        const MAPE  = validCount ? (sumMAPE  / validCount) : 0;
        const SMAPE = validCount ? (sumSMAPE / validCount) : 0;

        // ── Render Tabel ──────────────────────────────────────
        const tbody = document.getElementById('smaTableBody');
        tbody.innerHTML = '';
        let no = 1;
        tableRows.forEach(r => {
            const row = document.createElement('tr');
            if (r.isDiramal) row.className = 'table-success';
            const dash = '<span class="text-muted">–</span>';
            
            // Format numbers to match Excel's display (exactly 2 decimal places, or dash if null)
            const displayXt = r.Xt !== null ? r.Xt : dash;
            const displayFt = r.Ft !== null ? r.Ft.toFixed(2) : dash;
            const displayXtFt = r.XtFt !== null ? r.XtFt.toFixed(2) : dash;
            const displayMape = r.mapeRow !== null ? r.mapeRow.toFixed(2) : dash;
            const displaySmape = r.smapeRow !== null ? r.smapeRow.toFixed(2) : dash;

            row.innerHTML = `
                <td class="text-center small text-muted">${no++}</td>
                <td class="small ${r.isDiramal ? 'fw-semibold text-success' : ''}"
                >${r.label}${r.isDiramal ? ' <span class="badge bg-success ms-1">Diramal</span>' : ''}</td>
                <td class="text-center">${displayXt}</td>
                <td class="text-center ${r.isDiramal ? 'fw-bold text-success' : ''}">${displayFt}</td>
                <td class="text-center ${r.XtFt !== null && r.XtFt < 0 ? 'text-danger' : ''}">${displayXtFt}</td>
                <td class="text-center">${displayMape}</td>
                <td class="text-center">${displaySmape}</td>
            `;
            tbody.appendChild(row);
        });

        // Baris MAPE & sMAPE di footer tabel
        tbody.innerHTML += `
            <tr class="table-warning fw-semibold">
                <td class="text-center" colspan="5">MAPE (Mean Absolute Percentage Error)</td>
                <td class="text-center" colspan="2">${MAPE.toFixed(2)} %</td>
            </tr>
            <tr class="table-info fw-semibold">
                <td class="text-center" colspan="5">sMAPE (Symmetric Mean Absolute Percentage Error)</td>
                <td class="text-center" colspan="2">${SMAPE.toFixed(2)} %</td>
            </tr>
        `;

        // ── Label header ─────────────────────────────────────
        document.getElementById('lblKategori').textContent       = kategori;
        document.getElementById('lblPeriode').textContent      = `n = ${n} | ${labelDariBulan(bulanAwal)} – ${labelDariBulan(bulanAkhir)}`;
        document.getElementById('chartLblKategori').textContent  = kategori;
        document.getElementById('chartLblPeriode').textContent = `SMA-${n}`;

        // ── Tabel Hasil Prediksi ──────────────────────────────
        const hasilBody = document.getElementById('hasilPrediksiBody');
        hasilBody.innerHTML = '';
        hasilPrediksi.forEach((hp, idx) => {
            hasilBody.innerHTML += `
                <tr>
                    <td class="ps-4 fw-semibold text-success">${hp.label}</td>
                    <td class="text-center fw-bold text-success">${Math.round(hp.Ft)} pcs</td>
                </tr>
            `;
        });
        // Baris "dst" jika ada lebih dari 1 prediksi
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
                        label: 'Aktual (Yt)',
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
                        label: `Prediksi Kategori SMA-${n} (Ft)`,
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

        // Reset tombol simpan riwayat ke keadaan aktif
        const btnSave = document.getElementById('btnSaveHistory');
        btnSave.disabled = false;
        btnSave.className = 'btn btn-success btn-sm';
        btnSave.innerHTML = `<i class="bi bi-cloud-arrow-up-fill me-1"></i> Simpan ke Riwayat`;

        // Simpan data perhitungan saat ini ke variabel global
        currentPredictionData = {
            kategori: kategori,
            bulan_awal: bulanAwal,
            bulan_akhir: bulanAkhir,
            periode_n: n,
            mape: MAPE,
            smape: SMAPE,
            predictions: hasilPrediksi.map(hp => ({ ym: hp.ym, Ft: hp.Ft }))
        };

        // Simpan otomatis ke riwayat
        savePredictionToHistory(currentPredictionData, btnSave, true);
    });

    // Fungsi pembantu untuk menyimpan data ke database
    function savePredictionToHistory(predictionData, btn, isAutomatic = false) {
        if (!predictionData) return;

        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...`;

        fetch('process_prediksi.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(predictionData)
        })
        .then(response => {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error("Respon server bukan JSON: " + text.substring(0, 150));
                });
            }
        })
        .then(data => {
            if (data.status === 'success') {
                if (!isAutomatic) {
                    alert(data.message);
                }
                btn.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${isAutomatic ? 'Tersimpan Otomatis' : 'Tersimpan'}`;
                btn.className = 'btn btn-secondary btn-sm';
                btn.disabled = true;
            } else {
                alert(data.message);
                btn.disabled = false;
                btn.className = 'btn btn-danger btn-sm';
                btn.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> Simpan Gagal`;
            }
        })
        .catch(error => {
            console.error("Save error:", error);
            alert('Gagal menyimpan riwayat: ' + error.message);
            btn.disabled = false;
            btn.className = 'btn btn-danger btn-sm';
            btn.innerHTML = `<i class="bi bi-cloud-arrow-up-fill me-1"></i> Simpan ke Riwayat`;
        });
    }

    // Event handler untuk tombol Simpan ke Riwayat (jika ingin menyimpan ulang secara manual saat gagal)
    document.getElementById('btnSaveHistory').addEventListener('click', function () {
        savePredictionToHistory(currentPredictionData, this, false);
    });
</script>