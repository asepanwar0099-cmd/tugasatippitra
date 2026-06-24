<?php
require_once 'config.php';

// Jika sudah login, langsung arahkan ke index (dashboard)
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; // Jangan disanitize agar karakter khusus password tidak hilang
    
    // Ambil data admin berdasarkan username
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch();
    
    // Verifikasi password menggunakan password_verify
    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        // Set session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['nama_lengkap'];
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Absensi QR Code</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">

<main class="auth-shell">
    <div class="card auth-card">
        <div class="row g-0">
            <div class="col-lg-6 d-none d-lg-block">
                <section class="auth-hero h-100">
                    <div class="auth-logo"><i class="fas fa-qrcode"></i></div>
                    <h1>Absensi dosen lebih cepat dengan QR Code.</h1>
                    <p>Dashboard admin untuk mengelola data dosen, membuat QR, melakukan scan webcam, dan melihat laporan kehadiran.</p>
                    <div class="auth-feature-list">
                        <div><i class="fas fa-camera"></i><span>Scan absensi langsung dari kamera</span></div>
                        <div><i class="fas fa-chart-line"></i><span>Statistik hadir dan terlambat realtime</span></div>
                        <div><i class="fas fa-file-export"></i><span>Laporan dapat dicetak atau diekspor</span></div>
                    </div>
                </section>
            </div>
            <div class="col-lg-6">
                <section class="auth-form-panel h-100">
                    <div class="mb-4">
                        <span class="badge text-bg-primary mb-3">Admin Area</span>
                        <h4 class="auth-title mb-1">Masuk Dashboard</h4>
                        <p class="auth-subtitle">Gunakan akun admin untuk mengelola sistem absensi.</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="Masukkan username" required autofocus>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 ps-0" placeholder="Masukkan password" required>
                                <span class="input-group-text bg-white bg-transparent" style="cursor: pointer;" onclick="togglePassword()">
                                    <i class="fas fa-eye text-muted" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                                <i class="fas fa-right-to-bracket me-2"></i>Login
                            </button>
                        </div>
                        <div class="row g-2 mt-4">
                            <div class="col-sm-6">
                                <a href="register.php" class="auth-link-card">
                                    <strong class="d-block text-primary"><i class="fas fa-user-plus me-1"></i>Daftar Akun</strong>
                                    <span class="small text-muted">Buat login baru</span>
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <a href="daftar_dosen.php" class="auth-link-card">
                                    <strong class="d-block text-primary"><i class="fas fa-chalkboard-user me-1"></i>Daftar Dosen</strong>
                                    <span class="small text-muted">Isi data dosen</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</main>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
</body>
</html>
