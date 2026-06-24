<?php
require_once 'config.php';
require_once 'auth_check.php';

$pageTitle = 'Scan Absensi';
$activePage = 'scan';

include 'includes/header.php';
?>
<div class="feature-banner mb-4">
    <div>
        <span class="badge text-bg-light text-primary mb-2">Realtime Scanner</span>
        <h2>Scan QR Code dan catat absensi otomatis.</h2>
        <p>Gunakan kamera laptop atau webcam. Data akan masuk ke database tanpa reload halaman.</p>
    </div>
    <i class="fas fa-camera"></i>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h2>Scanner Kamera</h2>
                    <p class="panel-subtitle">Pastikan browser mengizinkan akses kamera.</p>
                </div>
                <span class="badge text-bg-primary">Realtime</span>
            </div>
            <div id="reader" class="scanner-box"></div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button class="btn btn-primary" id="startBtn"><i class="fas fa-camera me-2"></i>Mulai Scan</button>
                <button class="btn btn-outline-secondary" id="stopBtn" disabled><i class="fas fa-stop me-2"></i>Stop</button>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="panel h-100">
            <div class="panel-header">
                <div>
                    <h2>Status Absensi</h2>
                    <p class="panel-subtitle">Hasil scan terakhir akan muncul di sini.</p>
                </div>
            </div>
            <div id="scanResult" class="scan-result">
                <i class="fas fa-qrcode"></i>
                <p>Arahkan QR Code dosen ke kamera untuk mencatat kehadiran.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jsQR/1.4.0/jsQR.min.js"></script>
<script src="assets/js/scan.js"></script>
<?php include 'includes/footer.php'; ?>
