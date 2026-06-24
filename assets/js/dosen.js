// dosen.js - Modal handler untuk form dosen
function openCreateModal() {
    document.getElementById('modalAction').value = 'create';
    document.getElementById('modalTitle').textContent = 'Tambah Dosen';
    document.getElementById('dosenId').value = '';
    document.getElementById('nidn').value = '';
    document.getElementById('nama').value = '';
    document.getElementById('kontak').value = '';
    document.getElementById('email').value = '';
    document.getElementById('nidn').focus();
}

function openEditModal(dosen) {
    document.getElementById('modalAction').value = 'update';
    document.getElementById('modalTitle').textContent = 'Edit Dosen';
    document.getElementById('dosenId').value = dosen.id;
    document.getElementById('nidn').value = dosen.nidn;
    document.getElementById('nama').value = dosen.nama;
    document.getElementById('kontak').value = dosen.kontak;
    document.getElementById('email').value = dosen.email;
}
