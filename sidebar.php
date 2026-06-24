<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-qrcode"></i></div>
        <div>
            <strong>Absensi QR</strong>
            <span>Dosen</span>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li><a class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" href="index.php"><i class="fas fa-chart-line"></i><span>Beranda</span></a></li>
        <li><a class="<?= $activePage === 'dosen' ? 'active' : '' ?>" href="dosen.php"><i class="fas fa-chalkboard-user"></i><span>Data Dosen</span></a></li>
        <li><a class="<?= $activePage === 'qrcode' ? 'active' : '' ?>" href="qrcode.php"><i class="fas fa-qrcode"></i><span>QR Code Generator</span></a></li>
        <li><a class="<?= $activePage === 'scan' ? 'active' : '' ?>" href="scan.php"><i class="fas fa-camera"></i><span>Scan Absensi</span></a></li>
        <li><a class="<?= $activePage === 'laporan' ? 'active' : '' ?>" href="laporan.php"><i class="fas fa-file-lines"></i><span>Laporan Absensi</span></a></li>
    </ul>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
