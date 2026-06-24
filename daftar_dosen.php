<?php
require_once 'config.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid. Silakan muat ulang halaman.';
    } else {
        $nidn = sanitize_input($_POST['nidn'] ?? '');
        $nama = sanitize_input($_POST['nama'] ?? '');
        $kontak = sanitize_input($_POST['kontak'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

        if ($nidn === '' || $nama === '' || $kontak === '' || $email === '') {
            $error = 'Semua field wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO dosen (nidn, nama, kontak, email) VALUES (:nidn, :nama, :kontak, :email)");
                $stmt->execute([
                    'nidn' => $nidn,
                    'nama' => $nama,
                    'kontak' => $kontak,
                    'email' => $email
                ]);
                $success = 'Pendaftaran berhasil. Silakan hubungi admin untuk QR Code absensi.';
                $_POST = [];
            } catch (PDOException $e) {
                $error = 'NIDN sudah terdaftar atau data tidak dapat disimpan.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Dosen - Absensi QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">

<main class="auth-shell">
    <div class="card auth-card">
        <div class="row g-0">
            <div class="col-lg-5 d-none d-lg-block">
                <section class="auth-hero h-100">
                    <div class="auth-logo"><i class="fas fa-chalkboard-user"></i></div>
                    <h1>Pendaftaran data dosen.</h1>
                    <p>Data yang masuk akan tersedia di dashboard admin dan dapat dibuatkan QR Code absensi.</p>
                    <div class="auth-feature-list">
                        <div><i class="fas fa-id-card"></i><span>NIDN menjadi identitas QR Code</span></div>
                        <div><i class="fas fa-qrcode"></i><span>QR dapat dicetak dari dashboard admin</span></div>
                    </div>
                </section>
            </div>
            <div class="col-lg-7">
                <section class="auth-form-panel h-100">
                    <div class="mb-4">
                        <span class="badge text-bg-primary mb-3">Form Dosen</span>
                        <h4 class="auth-title mb-1">Daftar Dosen</h4>
                        <p class="auth-subtitle">Isi data dasar dosen untuk keperluan QR absensi.</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-circle-check me-2"></i><?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">NIDN / ID Dosen</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-id-card text-muted"></i></span>
                                <input type="text" name="nidn" class="form-control border-start-0 ps-0" placeholder="Masukkan NIDN / ID" value="<?= htmlspecialchars($_POST['nidn'] ?? '') ?>" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-user-tie text-muted"></i></span>
                                <input type="text" name="nama" class="form-control border-start-0 ps-0" placeholder="Masukkan nama dosen" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kontak</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                <input type="text" name="kontak" class="form-control border-start-0 ps-0" placeholder="Nomor HP / WhatsApp" value="<?= htmlspecialchars($_POST['kontak'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="nama@kampus.ac.id" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i>Daftar
                            </button>
                            <a href="login.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Login
                            </a>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
