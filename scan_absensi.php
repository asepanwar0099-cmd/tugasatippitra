<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth_check.php';

header('Content-Type: application/json');

$payload = json_decode(file_get_contents('php://input'), true);
$nidn = sanitize_input($payload['nidn'] ?? '');

if ($nidn === '') {
    echo json_encode(['success' => false, 'title' => 'QR tidak valid', 'message' => 'Kode QR tidak berisi NIDN dosen.']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM dosen WHERE nidn = :nidn LIMIT 1");
$stmt->execute(['nidn' => $nidn]);
$dosen = $stmt->fetch();

if (!$dosen) {
    echo json_encode(['success' => false, 'title' => 'Dosen tidak ditemukan', 'message' => 'NIDN pada QR Code belum terdaftar.']);
    exit;
}

$tanggal = date('Y-m-d');
$jamMasuk = date('H:i:s');
$status = $jamMasuk <= BATAS_JAM_MASUK ? 'Hadir' : 'Terlambat';

$stmt = $pdo->prepare("SELECT * FROM absensi WHERE dosen_id = :dosen_id AND tanggal = :tanggal LIMIT 1");
$stmt->execute(['dosen_id' => $dosen['id'], 'tanggal' => $tanggal]);
$existing = $stmt->fetch();

if ($existing) {
    echo json_encode([
        'success' => false,
        'title' => 'Sudah Absen',
        'message' => $dosen['nama'] . ' sudah tercatat pada pukul ' . substr($existing['jam_masuk'], 0, 5) . '.'
    ]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO absensi (dosen_id, tanggal, jam_masuk, status) VALUES (:dosen_id, :tanggal, :jam_masuk, :status)");
$stmt->execute([
    'dosen_id' => $dosen['id'],
    'tanggal' => $tanggal,
    'jam_masuk' => $jamMasuk,
    'status' => $status
]);

echo json_encode([
    'success' => true,
    'title' => 'Absensi Berhasil',
    'message' => $dosen['nama'] . ' tercatat ' . $status . ' pada pukul ' . substr($jamMasuk, 0, 5) . '.'
]);
