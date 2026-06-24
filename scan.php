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

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const html5QrCode = new Html5Qrcode('reader');
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const resultBox = document.getElementById('scanResult');
    let isProcessing = false;

    async function startScanner() {
        try {
            await html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 260, height: 260 } },
                onScanSuccess
            );
            startBtn.disabled = true;
            stopBtn.disabled = false;
        } catch (error) {
            Swal.fire('Kamera gagal dibuka', 'Pastikan browser mengizinkan akses kamera.', 'error');
        }
    }

    async function stopScanner() {
        if (html5QrCode.isScanning) {
            await html5QrCode.stop();
        }
        startBtn.disabled = false;
        stopBtn.disabled = true;
    }

    async function onScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;

        try {
            const response = await fetch('actions/scan_absensi.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nidn: decodedText })
            });
            const data = await response.json();
            const alertType = data.success ? 'success' : 'warning';

            resultBox.innerHTML = `
                <div class="result-card ${data.success ? 'success' : 'warning'}">
                    <i class="fas ${data.success ? 'fa-circle-check' : 'fa-triangle-exclamation'}"></i>
                    <h3>${data.title}</h3>
                    <p>${data.message}</p>
                </div>`;

            Swal.fire({
                icon: alertType,
                title: data.title,
                text: data.message,
                timer: 2200,
                showConfirmButton: false
            });
        } catch (error) {
            Swal.fire('Gagal', 'Server tidak merespons dengan benar.', 'error');
        }

        setTimeout(() => { isProcessing = false; }, 2500);
    }

    startBtn.addEventListener('click', startScanner);
    stopBtn.addEventListener('click', stopScanner);
</script>
<?php include 'includes/footer.php'; ?>
