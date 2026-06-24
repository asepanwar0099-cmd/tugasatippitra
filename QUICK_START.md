# Quick Start Guide - Sistem Absensi QR Code

## ⚡ 3 Menit Quick Start

### Option 1: Docker (Recommended)

**Requirements**: Docker & Docker Compose

```bash
# 1. Clone dan masuk folder
git clone https://github.com/asepanwar0099-cmd/tugasatippitra.git
cd tugasatippitra

# 2. Start aplikasi
docker-compose up -d

# 3. Tunggu 30 detik untuk database siap
sleep 30

# 4. Access aplikasi
# Web: http://localhost
# PhpMyAdmin: http://localhost:8081
```

### Option 2: Local Development

**Requirements**: PHP 7.4+, MySQL 5.7+

```bash
# 1. Clone repository
git clone https://github.com/asepanwar0099-cmd/tugasatippitra.git
cd tugasatippitra

# 2. Setup database
mysql -u root -p < database.sql

# 3. Configure (jika diperlukan)
# Edit config.php dengan credentials database Anda
nano config.php

# 4. Start server
php -S localhost:8000

# 5. Open browser
# http://localhost:8000
```

### Option 3: Hosting Provider (Shared Hosting)

1. Upload semua files ke public_html/
2. Buat database baru di cPanel
3. Import database.sql
4. Update config.php dengan credentials
5. Access via domain Anda

---

## 🔑 Default Login

```
URL: http://localhost:8000/login.php
Username: admin
Password: password123
```

⚠️ **PENTING**: Ganti password setelah login pertama!

---

## 📱 First Steps

### 1. Add Teacher Data
```
1. Click "Data Dosen" di sidebar
2. Klik tombol "Tambah Dosen"
3. Isi: NIDN, Nama, Kontak, Email
4. Simpan
```

### 2. Generate QR Code
```
1. Click "QR Code Generator" di sidebar
2. Pilih dosen dari dropdown
3. QR code akan generate otomatis
4. Download atau cetak
```

### 3. Test Scanner
```
1. Click "Scan Absensi" di sidebar
2. Klik "Mulai Scan"
3. Izinkan akses kamera
4. Arahkan QR code ke kamera
5. Absensi tercatat otomatis
```

### 4. View Reports
```
1. Click "Laporan Absensi" di sidebar
2. Pilih date range
3. Lihat data kehadiran
4. Export ke CSV jika diperlukan
```

---

## 🛠️ Troubleshooting Quick Fixes

| Issue | Solution |
|-------|----------|
| Blank page | Check PHP error logs |
| Database error | Verify config.php credentials |
| Camera not working | Check browser permissions |
| QR code not scanning | Ensure JavaScript enabled |
| Port already in use | Change port: `php -S localhost:9000` |

---

## 📝 Important Files

```
config.php           # Database configuration
database.sql         # Database schema
includes/header.php  # Layout template
assets/css/style.css # Styling
assets/js/scan.js    # QR scanner logic
```

---

## 🔐 Security Tips

1. Change default password immediately
2. Keep database credentials safe
3. Use HTTPS in production
4. Regular database backups
5. Update PHP/MySQL regularly

---

## 📊 Database Info

```
Database: db_absensi_qr
Tables:
  - admin (user credentials)
  - dosen (teacher data)
  - absensi (attendance records)
```

Default credentials:
- Username: admin
- Password: password123
- DB User: root

---

## 🆘 Need Help?

1. Check [DEPLOYMENT.md](DEPLOYMENT.md) for detailed guide
2. See [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for API details
3. Check [README.md](README.md) for complete documentation
4. Review error logs in `/var/log/`

---

## 📈 Next Steps

- [ ] Change admin password
- [ ] Add teacher data
- [ ] Generate QR codes
- [ ] Print QR cards
- [ ] Start scanning attendance
- [ ] Setup automated backups
- [ ] Configure HTTPS
- [ ] Setup production server

---

**Version**: 1.0  
**Last Updated**: 2024  
**Status**: Production Ready ✅
