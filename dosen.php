<?php
require_once 'config.php';
require_once 'auth_check.php';

$pageTitle = 'Data Dosen';
$activePage = 'dosen';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        redirect_with_message('dosen.php', 'danger', 'Token keamanan tidak valid.');
    }

    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    $nidn = sanitize_input($_POST['nidn'] ?? '');
    $nama = sanitize_input($_POST['nama'] ?? '');
    $kontak = sanitize_input($_POST['kontak'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

    try {
        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO dosen (nidn, nama, kontak, email) VALUES (:nidn, :nama, :kontak, :email)");
            $stmt->execute(compact('nidn', 'nama', 'kontak', 'email'));
            redirect_with_message('dosen.php', 'success', 'Data dosen berhasil ditambahkan.');
        }

        if ($action === 'update' && $id > 0) {
            $stmt = $pdo->prepare("UPDATE dosen SET nidn = :nidn, nama = :nama, kontak = :kontak, email = :email WHERE id = :id");
            $stmt->execute(compact('nidn', 'nama', 'kontak', 'email', 'id'));
            redirect_with_message('dosen.php', 'success', 'Data dosen berhasil diperbarui.');
        }

        if ($action === 'delete' && $id > 0) {
            $stmt = $pdo->prepare("DELETE FROM dosen WHERE id = :id");
            $stmt->execute(['id' => $id]);
            redirect_with_message('dosen.php', 'success', 'Data dosen berhasil dihapus.');
        }
    } catch (PDOException $e) {
        redirect_with_message('dosen.php', 'danger', 'Gagal menyimpan data. Pastikan NIDN belum terdaftar.');
    }
}

$dosen = $pdo->query("SELECT * FROM dosen ORDER BY nama ASC")->fetchAll();
$totalDosen = count($dosen);
include 'includes/header.php';
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="mini-metric">
            <i class="fas fa-users"></i>
            <div><span>Total Dosen</span><strong><?= $totalDosen ?></strong></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mini-metric">
            <i class="fas fa-id-card"></i>
            <div><span>Identitas QR</span><strong>NIDN</strong></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mini-metric">
            <i class="fas fa-database"></i>
            <div><span>Status Data</span><strong>Aktif</strong></div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <div>
            <h2>Daftar Dosen</h2>
            <p class="panel-subtitle">Kelola identitas dosen yang digunakan untuk QR Code absensi.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dosenModal" onclick="openCreateModal()">
            <i class="fas fa-plus me-2"></i>Tambah Dosen
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>NIDN/ID</th>
                    <th>Nama</th>
                    <th>Kontak</th>
                    <th>Email</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$dosen): ?>
                    <tr>
                        <td colspan="5">
                            <div class="table-empty">
                                <i class="fas fa-user-plus"></i>
                                <strong>Belum ada data dosen</strong>
                                <span>Tambah dosen pertama untuk mulai membuat QR Code.</span>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($dosen as $row): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nidn']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['kontak']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary btn-icon" title="Edit"
                                onclick='openEditModal(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                <i class="fas fa-pen"></i>
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Hapus data dosen ini? Absensi terkait ikut terhapus.');">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger btn-icon" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="dosenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" id="modalAction" value="create">
            <input type="hidden" name="id" id="dosenId">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Dosen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">NIDN/ID</label>
                    <input type="text" class="form-control" name="nidn" id="nidn" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Dosen</label>
                    <input type="text" class="form-control" name="nama" id="nama" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kontak</label>
                    <input type="text" class="form-control" name="kontak" id="kontak" required>
                </div>
                <div class="mb-0">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk me-2"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = new bootstrap.Modal(document.getElementById('dosenModal'));
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Dosen';
        document.getElementById('modalAction').value = 'create';
        document.getElementById('dosenId').value = '';
        document.querySelector('#dosenModal form').reset();
    }
    function openEditModal(data) {
        document.getElementById('modalTitle').textContent = 'Edit Dosen';
        document.getElementById('modalAction').value = 'update';
        document.getElementById('dosenId').value = data.id;
        document.getElementById('nidn').value = data.nidn;
        document.getElementById('nama').value = data.nama;
        document.getElementById('kontak').value = data.kontak;
        document.getElementById('email').value = data.email;
        modal.show();
    }
</script>
<?php include 'includes/footer.php'; ?>
