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
                    <p class="panel-subtitle">Arahkan QR Code dosen ke depan kamera. Pastikan browser mengizinkan akses kamera.</p>
                </div>
                <span class="badge text-bg-primary">Live</span>
            </div>
            <div id="reader" class="scanner-box"></div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button class="btn btn-primary btn-lg flex-grow-1" id="startBtn">
                    <i class="fas fa-play me-2"></i>Mulai Scan
                </button>
                <button class="btn btn-outline-secondary btn-lg flex-grow-1" id="stopBtn" disabled>
                    <i class="fas fa-stop me-2"></i>Stop
                </button>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="panel h-100">
            <div class="panel-header">
                <div>
                    <h2>Status Absensi</h2>
                    <p class="panel-subtitle">Hasil scan akan muncul di sini secara real-time.</p>
                </div>
            </div>
            <div id="scanResult" class="scan-result">
                <i class="fas fa-qrcode"></i>
                <p>Arahkan QR Code dosen ke kamera untuk mencatat kehadiran.</p>
                <small class="text-muted mt-2">Setiap dosen hanya bisa absen 1x per hari</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h2>Tips Scanner</h2>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <i class="fas fa-lightbulb text-warning" style="font-size: 20px;"></i>
                        <div>
                            <strong>Pencahayaan</strong>
                            <p class="small text-muted mb-0">Pastikan area cukup terang untuk hasil scan optimal</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <i class="fas fa-maximize text-info" style="font-size: 20px;"></i>
                        <div>
                            <strong>Jarak</strong>
                            <p class="small text-muted mb-0">Tempatkan QR Code 15-30 cm dari kamera</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <i class="fas fa-phone text-success" style="font-size: 20px;"></i>
                        <div>
                            <strong>Positioning</strong>
                            <p class="small text-muted mb-0">Letakkan QR Code di tengah frame kamera</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jsQR/1.4.0/jsQR.min.js"></script>
<script src="assets/js/scan.js"></script>
<?php include 'includes/footer.php'; ?>
