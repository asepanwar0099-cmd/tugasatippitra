<?php
require_once 'config.php';
require_once 'auth_check.php';

$pageTitle = 'Beranda';
$activePage = 'dashboard';

$totalDosen = (int) $pdo->query("SELECT COUNT(*) FROM dosen")->fetchColumn();
$today = date('Y-m-d');

$stmt = $pdo->prepare("SELECT COUNT(*) FROM absensi WHERE tanggal = :tanggal AND status = 'Hadir'");
$stmt->execute(['tanggal' => $today]);
$totalHadir = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM absensi WHERE tanggal = :tanggal AND status = 'Terlambat'");
$stmt->execute(['tanggal' => $today]);
$totalTerlambat = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT a.*, d.nama, d.nidn
    FROM absensi a
    JOIN dosen d ON d.id = a.dosen_id
    WHERE a.tanggal = :tanggal
    ORDER BY a.jam_masuk DESC
    LIMIT 6
");
$stmt->execute(['tanggal' => $today]);
$absensiTerbaru = $stmt->fetchAll();

include 'includes/header.php';
?>
<div class="dashboard-hero">
    <span class="badge text-bg-light text-primary mb-3">Dashboard Absensi QR</span>
    <h2>Monitoring kehadiran dosen dalam satu tempat.</h2>
    <p>Kelola data dosen, cetak QR Code, scan absensi melalui webcam, dan pantau laporan kehadiran harian secara cepat.</p>
    <div class="hero-actions">
        <a href="scan.php" class="btn btn-light"><i class="fas fa-camera me-2"></i>Mulai Scan</a>
        <a href="qrcode.php" class="btn btn-outline-light"><i class="fas fa-qrcode me-2"></i>Buat QR Code</a>
        <a href="laporan.php" class="btn btn-outline-light"><i class="fas fa-file-lines me-2"></i>Lihat Laporan</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-blue"><i class="fas fa-user-tie"></i></div>
            <div>
                <span>Total Dosen</span>
                <strong><?= $totalDosen ?></strong>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-green"><i class="fas fa-circle-check"></i></div>
            <div>
                <span>Hadir Hari Ini</span>
                <strong><?= $totalHadir ?></strong>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-amber"><i class="fas fa-clock"></i></div>
            <div>
                <span>Terlambat</span>
                <strong><?= $totalTerlambat ?></strong>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-slate"><i class="fas fa-stopwatch"></i></div>
            <div>
                <span>Jam Digital</span>
                <strong id="digitalClock">--:--:--</strong>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-header">
                <h2>Absensi Terbaru Hari Ini</h2>
                <a href="laporan.php" class="btn btn-sm btn-outline-primary">Lihat laporan</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>NIDN</th>
                            <th>Nama Dosen</th>
                            <th>Jam Masuk</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$absensiTerbaru): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada absensi hari ini.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($absensiTerbaru as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nidn']) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars(substr($row['jam_masuk'], 0, 5)) ?></td>
                                <td><span class="badge status-<?= strtolower($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="panel h-100">
            <div class="panel-header">
                <h2>Ringkasan Sistem</h2>
            </div>
            <div class="quick-info">
                <div><i class="fas fa-calendar-day"></i><span><?= date('d M Y') ?></span></div>
                <div><i class="fas fa-business-time"></i><span>Batas hadir <?= substr(BATAS_JAM_MASUK, 0, 5) ?></span></div>
                <div><i class="fas fa-shield-halved"></i><span>Login aktif sebagai <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span></div>
            </div>
        </div>
    </div>
</div>

<script>
    function tickClock() {
        document.getElementById('digitalClock').textContent = new Date().toLocaleTimeString('id-ID', { hour12: false });
    }
    tickClock();
    setInterval(tickClock, 1000);
</script>
<?php include 'includes/footer.php'; ?>
