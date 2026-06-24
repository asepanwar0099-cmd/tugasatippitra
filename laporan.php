<?php
require_once 'config.php';
require_once 'auth_check.php';

$pageTitle = 'Laporan Absensi';
$activePage = 'laporan';

$startDate = sanitize_input($_GET['start_date'] ?? date('Y-m-01'));
$endDate = sanitize_input($_GET['end_date'] ?? date('Y-m-d'));

$stmt = $pdo->prepare("
    SELECT a.*, d.nama, d.nidn
    FROM absensi a
    JOIN dosen d ON d.id = a.dosen_id
    WHERE a.tanggal BETWEEN :start_date AND :end_date
    ORDER BY a.tanggal DESC, a.jam_masuk DESC
");
$stmt->execute(['start_date' => $startDate, 'end_date' => $endDate]);
$rows = $stmt->fetchAll();
$totalData = count($rows);
$totalHadir = count(array_filter($rows, fn($row) => $row['status'] === 'Hadir'));
$totalTerlambat = count(array_filter($rows, fn($row) => $row['status'] === 'Terlambat'));

if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=laporan-absensi-' . date('Ymd-His') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['NIDN', 'Nama Dosen', 'Tanggal', 'Jam Masuk', 'Status']);
    foreach ($rows as $row) {
        fputcsv($output, [$row['nidn'], $row['nama'], $row['tanggal'], $row['jam_masuk'], $row['status']]);
    }
    fclose($output);
    exit;
}

include 'includes/header.php';
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="mini-metric">
            <i class="fas fa-file-lines"></i>
            <div><span>Total Data</span><strong><?= $totalData ?></strong></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mini-metric">
            <i class="fas fa-circle-check"></i>
            <div><span>Hadir</span><strong><?= $totalHadir ?></strong></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mini-metric">
            <i class="fas fa-clock"></i>
            <div><span>Terlambat</span><strong><?= $totalTerlambat ?></strong></div>
        </div>
    </div>
</div>

<div class="panel mb-4 no-print">
    <form class="row g-3 align-items-end" method="GET">
        <div class="col-md-4">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-primary flex-fill" type="submit"><i class="fas fa-filter me-2"></i>Filter</button>
            <a class="btn btn-outline-success" href="?start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>&export=excel">
                <i class="fas fa-file-excel"></i>
            </a>
            <button class="btn btn-outline-secondary" type="button" onclick="window.print()"><i class="fas fa-print"></i></button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header">
        <div>
            <h2>Riwayat Kehadiran</h2>
            <p class="panel-subtitle">Data absensi berdasarkan rentang tanggal terpilih.</p>
        </div>
        <span class="text-muted small"><?= count($rows) ?> data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>NIDN</th>
                    <th>Nama Dosen</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$rows): ?>
                    <tr>
                        <td colspan="5">
                            <div class="table-empty">
                                <i class="fas fa-calendar-xmark"></i>
                                <strong>Data tidak ditemukan</strong>
                                <span>Ubah rentang tanggal atau lakukan scan absensi terlebih dahulu.</span>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nidn']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars(date('d M Y', strtotime($row['tanggal']))) ?></td>
                        <td><?= htmlspecialchars(substr($row['jam_masuk'], 0, 5)) ?></td>
                        <td><span class="badge status-<?= strtolower($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
