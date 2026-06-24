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
        $namaLengkap = sanitize_input($_POST['nama_lengkap'] ?? '');
        $username = sanitize_input($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($namaLengkap === '' || $username === '' || $password === '' || $confirmPassword === '') {
            $error = 'Semua field wajib diisi.';
        } elseif (strlen($username) < 4) {
            $error = 'Username minimal 4 karakter.';
        } elseif (strlen($password) < 8) {
            $error = 'Password minimal 8 karakter.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Konfirmasi password tidak sama.';
        } else {
            try {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("
                    INSERT INTO admin (username, password, nama_lengkap)
                    VALUES (:username, :password, :nama_lengkap)
                ");
                $stmt->execute([
                    'username' => $username,
                    'password' => $passwordHash,
                    'nama_lengkap' => $namaLengkap
                ]);

                $success = 'Akun berhasil dibuat. Silakan login menggunakan username dan password baru.';
                $_POST = [];
            } catch (PDOException $e) {
                $error = 'Username sudah digunakan. Pilih username lain.';
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
    <title>Daftar Akun - Absensi QR Code</title>
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
                    <div class="auth-logo"><i class="fas fa-user-shield"></i></div>
                    <h1>Buat akun pengelola absensi.</h1>
                    <p>Akun ini dapat login ke dashboard untuk mengelola dosen, QR Code, scanner, dan laporan.</p>
                    <div class="auth-feature-list">
                        <div><i class="fas fa-lock"></i><span>Password disimpan dengan hash bcrypt</span></div>
                        <div><i class="fas fa-shield-halved"></i><span>Form dilindungi token CSRF</span></div>
                    </div>
                </section>
            </div>
            <div class="col-lg-7">
                <section class="auth-form-panel h-100">
                    <div class="mb-4">
                        <span class="badge text-bg-primary mb-3">Registrasi Admin</span>
                        <h4 class="auth-title mb-1">Daftar Akun Baru</h4>
                        <p class="auth-subtitle">Lengkapi data akun untuk masuk ke dashboard.</p>
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
                            <a href="login.php" class="alert-link">Login sekarang</a>.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" name="nama_lengkap" class="form-control border-start-0 ps-0" placeholder="Masukkan nama lengkap" value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-at text-muted"></i></span>
                                <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="Minimal 4 karakter" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 ps-0" placeholder="Minimal 8 karakter" required>
                                <span class="input-group-text bg-white" role="button" onclick="togglePassword('password', 'toggleIcon1')">
                                    <i class="fas fa-eye text-muted" id="toggleIcon1"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="confirm_password" id="confirmPassword" class="form-control border-start-0 border-end-0 ps-0" placeholder="Ulangi password" required>
                                <span class="input-group-text bg-white" role="button" onclick="togglePassword('confirmPassword', 'toggleIcon2')">
                                    <i class="fas fa-eye text-muted" id="toggleIcon2"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                                <i class="fas fa-user-plus me-2"></i>Buat Akun
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
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const show = input.type === 'password';

        input.type = show ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !show);
        icon.classList.toggle('fa-eye-slash', show);
    }
</script>
</body>
</html>
