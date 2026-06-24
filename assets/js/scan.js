// scan.js - QR Code Scanner dan Attendance Handler
class AttendanceScanner {
    constructor() {
        this.scanner = null;
        this.video = null;
        this.isScanning = false;
        this.lastScannedId = null;
        this.scanDelay = 1000; // Jangan scan ID yang sama dalam 1 detik
        this.canvasElement = null;
        this.startBtn = null;
        this.stopBtn = null;
        this.resultDiv = null;
    }

    init() {
        this.startBtn = document.getElementById('startBtn');
        this.stopBtn = document.getElementById('stopBtn');
        this.resultDiv = document.getElementById('scanResult');

        if (!this.startBtn || !this.stopBtn || !this.resultDiv) {
            console.error('Required elements not found');
            return;
        }

        this.startBtn.addEventListener('click', () => this.start());
        this.stopBtn.addEventListener('click', () => this.stop());
    }

    async start() {
        if (this.isScanning) return;

        this.isScanning = true;
        this.startBtn.disabled = true;
        this.stopBtn.disabled = false;
        this.resultDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengaktifkan kamera...';

        try {
            // Minta akses kamera
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }
            });

            // Buat video element
            this.video = document.createElement('video');
            this.video.srcObject = stream;
            this.video.play();
            this.video.style.width = '100%';
            this.video.style.height = 'auto';

            // Tambahkan video ke reader div
            const reader = document.getElementById('reader');
            reader.innerHTML = '';
            reader.appendChild(this.video);

            // Buat canvas untuk processing
            this.canvasElement = document.createElement('canvas');

            // Mulai scanning
            this.scanQRCode();
        } catch (error) {
            this.resultDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Tidak dapat mengakses kamera. Pastikan browser memiliki izin akses kamera.</div>`;
            this.isScanning = false;
            this.startBtn.disabled = false;
            this.stopBtn.disabled = true;
        }
    }

    scanQRCode() {
        if (!this.isScanning || !this.video || !this.video.srcObject) return;

        const canvas = this.canvasElement;
        const ctx = canvas.getContext('2d');

        // Set canvas size sesuai video
        canvas.width = this.video.videoWidth || 640;
        canvas.height = this.video.videoHeight || 480;

        if (canvas.width === 0 || canvas.height === 0) {
            // Video belum siap, coba lagi
            setTimeout(() => this.scanQRCode(), 100);
            return;
        }

        // Draw video frame ke canvas
        ctx.drawImage(this.video, 0, 0, canvas.width, canvas.height);

        // Get image data
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

        // Scan QR code
        const code = jsQR(imageData.data, canvas.width, canvas.height);

        if (code) {
            // QR code ditemukan
            this.handleQRCode(code.data);
        } else {
            // Lanjutkan scanning
            setTimeout(() => this.scanQRCode(), 200);
        }
    }

    handleQRCode(nidn) {
        const now = Date.now();

        // Prevent duplicate scans in short time
        if (this.lastScannedId === nidn && now - this.lastScanTime < this.scanDelay) {
            return;
        }

        this.lastScannedId = nidn;
        this.lastScanTime = now;

        // Disable scanning sementara untuk feedback
        this.isScanning = false;
        this.startBtn.disabled = false;
        this.stopBtn.disabled = true;

        // Send to backend
        this.submitAttendance(nidn);
    }

    submitAttendance(nidn) {
        this.resultDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

        fetch('scan_absensi.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ nidn: nidn })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showSuccess(data);
                // Otomatis resume scanning setelah 3 detik
                setTimeout(() => {
                    if (this.isScanning === false) {
                        this.isScanning = true;
                        this.scanQRCode();
                    }
                }, 3000);
            } else {
                this.showError(data);
                // Resume scanning
                setTimeout(() => {
                    if (this.isScanning === false) {
                        this.isScanning = true;
                        this.scanQRCode();
                    }
                }, 2000);
            }
        })
        .catch(error => {
            this.resultDiv.innerHTML = `<div class="result-card"><i class="fas fa-wifi-slash"></i><h3>Koneksi Gagal</h3><p>${error.message}</p></div>`;
            console.error('Error:', error);
            // Resume scanning
            setTimeout(() => {
                if (this.isScanning === false) {
                    this.isScanning = true;
                    this.scanQRCode();
                }
            }, 2000);
        });
    }

    showSuccess(data) {
        this.resultDiv.innerHTML = `
            <div class="result-card success">
                <i class="fas fa-circle-check"></i>
                <h3>${data.title || 'Berhasil'}</h3>
                <p>${data.message || 'Absensi tercatat'}</p>
                <small>${new Date().toLocaleTimeString('id-ID')}</small>
            </div>
        `;
    }

    showError(data) {
        this.resultDiv.innerHTML = `
            <div class="result-card warning">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>${data.title || 'Gagal'}</h3>
                <p>${data.message || 'Silakan coba lagi'}</p>
            </div>
        `;
    }

    stop() {
        this.isScanning = false;
        this.startBtn.disabled = false;
        this.stopBtn.disabled = true;

        if (this.video && this.video.srcObject) {
            this.video.srcObject.getTracks().forEach(track => track.stop());
        }

        const reader = document.getElementById('reader');
        reader.innerHTML = '';
        this.resultDiv.innerHTML = '<i class="fas fa-qrcode"></i><p>Arahkan QR Code dosen ke kamera untuk mencatat kehadiran.</p>';
    }
}

// Initialize ketika DOM sudah siap
document.addEventListener('DOMContentLoaded', () => {
    const scanner = new AttendanceScanner();
    scanner.init();
});
