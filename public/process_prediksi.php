<?php
require_once __DIR__ . '/../core/auth_guard.php';
require_once __DIR__ . '/../models/PrediksiLog.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input.']);
    exit;
}

$kategori = trim($data['kategori'] ?? '');
$bulan_awal = trim($data['bulan_awal'] ?? '');
$bulan_akhir = trim($data['bulan_akhir'] ?? '');
$periode_n = (int)($data['periode_n'] ?? 0);
$mape = (float)($data['mape'] ?? 0);
$smape = (float)($data['smape'] ?? 0);
$predictions = $data['predictions'] ?? []; // Array of { ym, Ft }

if (empty($kategori) || empty($bulan_awal) || empty($bulan_akhir) || $periode_n <= 0 || empty($predictions)) {
    echo json_encode(['status' => 'error', 'message' => 'Parameter tidak lengkap.']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? 1;

$model = new PrediksiLog();
$successCount = 0;

foreach ($predictions as $pred) {
    $logData = [
        'kategori' => $kategori,
        'bulan_awal' => $bulan_awal,
        'bulan_akhir' => $bulan_akhir,
        'periode_n' => $periode_n,
        'prediksi_bulan' => $pred['ym'],
        'nilai_prediksi' => $pred['Ft'],
        'mape' => $mape,
        'smape' => $smape,
        'user_id' => $user_id
    ];
    if ($model->tambahLog($logData)) {
        $successCount++;
    }
}

if ($successCount > 0) {
    echo json_encode(['status' => 'success', 'message' => "$successCount riwayat prediksi berhasil disimpan!"]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan riwayat prediksi ke database.']);
}
exit;
