# 📋 Deployment Guide - Sistem Absensi Dosen QR Code

## 📌 Prerequisites

Sebelum deploy, pastikan server Anda memiliki:
- PHP 7.4 atau lebih baru
- MySQL 5.7 atau MariaDB 10.2+
- Composer (opsional)
- SSL Certificate (disarankan untuk production)
- Web Server: Apache atau Nginx

## 🚀 Deployment Steps

### 1. Clone Repository
```bash
cd /var/www
git clone https://github.com/asepanwar0099-cmd/tugasatippitra.git
cd tugasatippitra
```

### 2. Setup Database
```bash
# Login ke MySQL
mysql -u root -p

# Jalankan script setup database
source database.sql;
```

Atau menggunakan command line:
```bash
mysql -u root -p < database.sql
```

### 3. Configure Application
```bash
# Copy file konfigurasi
cp .env.example .env

# Edit .env dengan detail server Anda
nano .env
```

Update file `config.php`:
```php
$host = 'localhost';
$dbname = 'db_absensi_qr';
$user = 'root';
$pass = 'your_password';
```

### 4. Set Permissions
```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/tugasatippitra

# Set permissions
sudo chmod -R 755 /var/www/tugasatippitra
sudo chmod -R 775 /var/www/tugasatippitra/assets
```

### 5. Configure Web Server

**Untuk Apache:**
```bash
# Copy konfigurasi .htaccess sudah tersedia
# Pastikan mod_rewrite enabled
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**Untuk Nginx:**
```bash
# Copy nginx configuration
sudo cp nginx.conf /etc/nginx/sites-available/tugasatippitra
sudo ln -s /etc/nginx/sites-available/tugasatippitra /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 6. Setup SSL (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-apache
# atau untuk nginx
sudo apt install certbot python3-certbot-nginx

# Generate certificate
sudo certbot certonly --standalone -d yourdomain.com

# Setup auto-renewal
sudo systemctl enable certbot.timer
```

### 7. Verify Installation
Akses aplikasi di browser:
```
http://yourdomain.com
```

Login dengan credentials default:
- Username: `admin`
- Password: `password123`

**⚠️ PENTING: Ganti password admin setelah login pertama kali!**

## 🔒 Security Checklist

- [ ] Ubah default admin password
- [ ] Setup HTTPS/SSL
- [ ] Batasi akses directory sensitif
- [ ] Enable CSRF protection (sudah ada)
- [ ] Setup firewall rules
- [ ] Enable error logging
- [ ] Disable PHP error display di production
- [ ] Regular backup database
- [ ] Update dependencies secara berkala

## 📊 Database Backup

### Manual Backup
```bash
# Export database
mysqldump -u root -p db_absensi_qr > backup_$(date +%Y%m%d).sql

# Import backup
mysql -u root -p db_absensi_qr < backup_20240101.sql
```

### Automated Backup (Cron)
```bash
# Edit crontab
sudo crontab -e

# Tambahkan:
# Backup database setiap hari jam 2 pagi
0 2 * * * mysqldump -u root -p'password' db_absensi_qr > /backup/db_$(date +\%Y\%m\%d).sql
```

## 🧪 Testing

### Test Database Connection
```bash
php -r "
require 'config.php';
echo 'Database connected successfully!';
"
```

### Test File Upload
Pastikan folder assets dapat ditulis:
```bash
touch /var/www/tugasatippitra/assets/.write_test
rm /var/www/tugasatippitra/assets/.write_test
```

## 📝 Troubleshooting

### Error: "Koneksi database gagal"
- Periksa kredensial database di `config.php`
- Pastikan MySQL service running: `sudo systemctl status mysql`
- Verifikasi database sudah dibuat

### Error: "Permission denied"
```bash
sudo chown -R www-data:www-data /var/www/tugasatippitra
sudo chmod -R 755 /var/www/tugasatippitra
```

### Error: "404 Not Found"
- Pastikan .htaccess ada untuk Apache
- Verifikasi nginx config untuk Nginx
- Reload/restart web server

### QR Code tidak bisa di-scan
- Test di browser: Settings > Camera > Allow
- Cek console browser untuk error
- Pastikan JavaScript library jsQR loaded

## 📞 Support

Jika ada masalah:
1. Check error logs: `/var/log/php_errors.log`
2. Check web server logs: `/var/log/apache2/error.log` atau `/var/log/nginx/error.log`
3. Test database: `mysql -u root -p -e "SELECT 1;"`

## 🔄 Maintenance

### Update Dependencies
```bash
# Check PHP version
php -v

# Update system
sudo apt update && sudo apt upgrade -y
```

### Monitor Performance
```bash
# Check disk usage
df -h

# Check MySQL size
du -sh /var/lib/mysql/db_absensi_qr
```

## 📋 Default Credentials

| Field | Value |
|-------|-------|
| Username | admin |
| Password | password123 |
| Database | db_absensi_qr |

**INGAT:** Ganti credentials ini setelah deployment!

---

**Version:** 1.0  
**Last Updated:** 2024  
**Author:** Asep Anwar
