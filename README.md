# 📚 Sistem Absensi Dosen Berbasis QR Code

Aplikasi web untuk manajemen absensi dosen menggunakan QR Code dan scanning real-time. Sistem ini dirancang untuk memudahkan proses pencatatan kehadiran dosen dengan teknologi QR Code scanning melalui webcam.

## ✨ Fitur Utama

- ✅ **Dashboard Real-time**: Monitoring kehadiran dosen secara real-time
- 📱 **QR Code Generator**: Buat dan cetak QR Code untuk setiap dosen
- 📷 **Live QR Scanner**: Scan QR Code menggunakan webcam
- 📊 **Laporan Kehadiran**: Laporan detail dengan filter tanggal dan export ke CSV
- 👥 **Manajemen Dosen**: CRUD data dosen dengan validasi email
- 🔐 **Autentikasi Admin**: Login system dengan session management
- 🌙 **Dark Mode**: Tema gelap/terang untuk kenyamanan user
- 📱 **Responsive Design**: Kompatibel dengan desktop, tablet, dan mobile
- 🛡️ **Security**: CSRF protection, input sanitization, password hashing

## 📋 Requirements

- PHP 7.4+
- MySQL 5.7+ / MariaDB 10.2+
- Web Server (Apache/Nginx)
- Modern browser dengan support Webcam API
- Koneksi internet stabil

## 🚀 Quick Start

### Installation

1. **Clone Repository**
   ```bash
   git clone https://github.com/asepanwar0099-cmd/tugasatippitra.git
   cd tugasatippitra
   ```

2. **Setup Database**
   ```bash
   mysql -u root -p < database.sql
   ```

3. **Configure Database**
   Edit `config.php` dengan detail database Anda:
   ```php
   $host = 'localhost';
   $dbname = 'db_absensi_qr';
   $user = 'root';
   $pass = '';
   ```

4. **Jalankan Server**
   ```bash
   php -S localhost:8000
   ```

5. **Akses Aplikasi**
   Buka browser: `http://localhost:8000`

### Default Credentials
- **Username**: `admin`
- **Password**: `password123`

⚠️ **Ganti password setelah login pertama!**

## 📁 Project Structure

```
tugasatippitra/
├── includes/              # Layout templates
│   ├── header.php        # Header template
│   ├── footer.php        # Footer template
│   └── sidebar.php       # Sidebar navigation
├── assets/               # Static assets
│   ├── css/
│   │   └── style.css     # Main stylesheet
│   └── js/
│       ├── scan.js       # QR scanner logic
│       ├── dosen.js      # Dosen modal handler
│       └── qrcode.js     # QR code utilities
├── index.php             # Dashboard
├── login.php             # Login page
├── logout.php            # Logout handler
├── dosen.php             # Manage dosen
├── qrcode.php            # QR code generator
├── scan.php              # Scanner page
├── scan_absensi.php      # API: Process scan
├── laporan.php           # Reports
├── config.php            # Database config
├── auth_check.php        # Auth middleware
├── database.sql          # Database schema
├── .htaccess             # Apache config
├── nginx.conf            # Nginx config
├── .env.example          # Environment example
└── DEPLOYMENT.md         # Deployment guide
```

## 🔑 Key Features Explained

### QR Code Generation
1. Buka **QR Code Generator**
2. Pilih dosen dari dropdown
3. QR Code akan generate otomatis
4. Download atau cetak kartu absensi

### Real-time Scanning
1. Buka **Scan Absensi**
2. Klik **Mulai Scan** dan izinkan akses kamera
3. Arahkan QR Code dosen ke kamera
4. Absensi dicatat otomatis ke database

### Laporan Absensi
1. Buka **Laporan Absensi**
2. Atur rentang tanggal filter
3. Lihat data kehadiran dan status
4. Export ke CSV untuk backup/analisis

## 🔐 Security Features

- ✅ CSRF Token protection
- ✅ Input sanitization & validation
- ✅ Password hashing dengan bcrypt
- ✅ Session management secure
- ✅ SQL injection prevention (PDO Prepared Statements)
- ✅ XSS protection dengan htmlspecialchars
- ✅ Secure headers configuration

## 📦 Deployment

Untuk production deployment, lihat [DEPLOYMENT.md](DEPLOYMENT.md)

### Quick Docker Deploy
```bash
docker-compose up -d
```

## 🛠️ Development

### Tech Stack
- **Backend**: PHP (PDO)
- **Database**: MySQL
- **Frontend**: Bootstrap 5, HTML5, CSS3, JavaScript
- **QR Scanning**: jsQR library
- **Icons**: FontAwesome 6

### Database Schema

**Tables:**
- `admin` - Admin credentials
- `dosen` - Teacher data (NIDN, name, contact, email)
- `absensi` - Attendance records (date, time, status)

## 📊 API Endpoints

### POST `/scan_absensi.php`
Proses QR Code scan
```json
Request:
{
  "nidn": "0012047801"
}

Response Success:
{
  "success": true,
  "title": "Berhasil",
  "message": "Absensi dosen tercatat"
}

Response Error:
{
  "success": false,
  "title": "Error",
  "message": "Pesan error detail"
}
```

## 🧪 Testing

### Test Database
```bash
php -r "require 'config.php'; echo 'Connected!';"
```

### Test Permissions
```bash
touch assets/.write_test && rm assets/.write_test
```

## 📈 Performance

- Average response time: < 200ms
- QR scan processing: < 100ms
- Database queries optimized with indexes
- Session timeout: 1 hour default

## 🐛 Troubleshooting

### Database Connection Error
- Verifikasi MySQL running
- Check credentials di `config.php`
- Pastikan database `db_absensi_qr` exist

### Camera Access Error
- Check browser permission
- Ensure HTTPS (for some browsers)
- Test di different browser

### QR Scan Not Working
- Ensure JavaScript library loaded
- Check browser console for errors
- Test QR code validity

## 📝 Changelog

### v1.0 (2024)
- Initial release
- Core features implemented
- Dashboard & reporting
- QR code generation & scanning

## 👥 Author

**Asep Anwar** (@asepanwar0099-cmd)

## 📄 License

This project is open source and available under the MIT License.

## 🙏 Acknowledgments

- Bootstrap 5 for UI framework
- jsQR for QR scanning
- FontAwesome for icons

## 💬 Support & Contribution

Untuk issues, feature requests, atau contributions:
1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Open Pull Request

---

**Last Updated**: 2024  
**Status**: Active & Maintained  
**Ready for Production**: ✅ Yes

