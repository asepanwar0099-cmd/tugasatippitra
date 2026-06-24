<?php
require_once 'config.php';
require_once 'auth_check.php';

$pageTitle = 'QR Code Generator';
$activePage = 'qrcode';

$dosen = $pdo->query("SELECT id, nidn, nama FROM dosen ORDER BY nama ASC")->fetchAll();
$selectedNidn = sanitize_input($_GET['nidn'] ?? ($dosen[0]['nidn'] ?? ''));
$selectedDosen = null;

foreach ($dosen as $row) {
    if ($row['nidn'] === $selectedNidn) {
        $selectedDosen = $row;
        break;
    }
}

include 'includes/header.php';
?>
<div class="feature-banner mb-4">
    <div>
        <span class="badge text-bg-light text-primary mb-2">QR Generator</span>
        <h2>Buat QR Code absensi per dosen.</h2>
        <p>QR berisi NIDN dosen dan dapat diunduh atau dicetak untuk digunakan saat scan absensi.</p>
    </div>
    <i class="fas fa-qrcode"></i>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h2>Pilih Dosen</h2>
                    <p class="panel-subtitle">Pilih nama dosen dari data yang sudah terdaftar.</p>
                </div>
            </div>
            <form method="GET">
                <label class="form-label">Dosen</label>
                <select name="nidn" class="form-select mb-3" onchange="this.form.submit()" required>
                    <?php foreach ($dosen as $row): ?>
                        <option value="<?= htmlspecialchars($row['nidn']) ?>" <?= $selectedNidn === $row['nidn'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['nidn'] . ' - ' . $row['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary w-100" type="submit"><i class="fas fa-qrcode me-2"></i>Generate QR</button>
            </form>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="panel text-center">
            <div class="panel-header">
                <div class="text-start">
                    <h2>QR Code Dosen</h2>
                    <p class="panel-subtitle">Kartu siap cetak untuk absensi harian.</p>
                </div>
            </div>
            <?php if ($selectedDosen): ?>
                <div id="printArea" class="qr-print-card">
                    <div class="qr-title">Kartu Absensi Dosen</div>
                    <div id="qrcode" class="qr-box mx-auto"></div>
                    <h3><?= htmlspecialchars($selectedDosen['nama']) ?></h3>
                    <p>NIDN: <?= htmlspecialchars($selectedDosen['nidn']) ?></p>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
                    <button class="btn btn-outline-primary" type="button" onclick="downloadQr()"><i class="fas fa-download me-2"></i>Download QR</button>
                    <button class="btn btn-primary" type="button" onclick="printQr()"><i class="fas fa-print me-2"></i>Cetak</button>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user-plus"></i>
                    <p>Tambahkan data dosen terlebih dahulu untuk membuat QR Code.</p>
                    <a href="dosen.php" class="btn btn-primary">Tambah Dosen</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const qrPayload = <?= json_encode($selectedDosen['nidn'] ?? '') ?>;
    if (qrPayload) {
        new QRCode(document.getElementById('qrcode'), {
            text: qrPayload,
            width: 240,
            height: 240,
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    function downloadQr() {
        const canvas = document.querySelector('#qrcode canvas');
        const img = document.querySelector('#qrcode img');
        const link = document.createElement('a');
        link.download = `qr-dosen-${qrPayload}.png`;
        link.href = canvas ? canvas.toDataURL('image/png') : img.src;
        link.click();
    }

    function printQr() {
        window.print();
    }
</script>
<?php include 'includes/footer.php'; ?>
