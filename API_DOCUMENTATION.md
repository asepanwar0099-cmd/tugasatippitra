# API Documentation - Sistem Absensi QR Code

## Overview

API untuk sistem absensi dosen berbasis QR Code. Semua endpoint menggunakan JSON format dan memerlukan session authentication.

## Base URL

```
http://localhost:8000
```

## Authentication

Semua API endpoint memerlukan admin session yang valid. Login terlebih dahulu di `/login.php`.

## Endpoints

### 1. Scan Attendance (POST)

**Endpoint**: `/scan_absensi.php`

**Method**: `POST`

**Headers**:
```
Content-Type: application/json
```

**Request Body**:
```json
{
  "nidn": "0012047801"
}
```

**Response Success (200)**:
```json
{
  "success": true,
  "title": "Berhasil",
  "message": "Absensi Andi Pratama tercatat sebagai Hadir pada 08:30",
  "data": {
    "dosen_name": "Dr. Andi Pratama, M.Kom.",
    "time": "08:30:45",
    "status": "Hadir"
  }
}
```

**Response Error**:
```json
{
  "success": false,
  "title": "Dosen tidak ditemukan",
  "message": "NIDN pada QR Code belum terdaftar"
}
```

**Possible Errors**:
- QR Code tidak valid: "QR tidak valid"
- Dosen tidak terdaftar: "Dosen tidak ditemukan"
- Sudah absen hari ini: "Sudah Absen"
- Database error: "Gagal menyimpan data"

**Notes**:
- Attendance otomatis dicatat sebagai "Hadir" jika sebelum pukul 08:00
- Dicatat sebagai "Terlambat" jika sesudah pukul 08:00
- Satu dosen hanya bisa absen satu kali per hari
- Timezone: Asia/Jakarta

---

### 2. Get Teacher List

**Endpoint**: `/dosen.php` (GET)

**Response Format**:
```html
Halaman HTML dengan tabel daftar dosen
```

**Required**: Admin login

---

### 3. Add/Update Teacher (POST)

**Endpoint**: `/dosen.php` (POST)

**Form Data**:
```
csrf_token: [token]
action: create|update
nidn: [string]
nama: [string]
kontak: [string]
email: [string]
id: [number] (untuk update)
```

**Response**: Redirect dengan flash message

**Validasi**:
- NIDN: Required, unique
- Nama: Required, max 150 chars
- Kontak: Required, phone format
- Email: Required, valid email format

---

### 4. Delete Teacher (POST)

**Endpoint**: `/dosen.php` (POST)

**Form Data**:
```
csrf_token: [token]
action: delete
id: [number]
```

**Response**: Redirect dengan flash message

**Note**: Absensi terkait akan ikut terhapus (CASCADE)

---

### 5. Generate QR Code

**Endpoint**: `/qrcode.php` (GET)

**Query Parameters**:
```
nidn: [string]  # NIDN dosen
```

**Response**: HTML page dengan QR code

---

### 6. Get Attendance Report

**Endpoint**: `/laporan.php` (GET)

**Query Parameters**:
```
start_date: YYYY-MM-DD
end_date: YYYY-MM-DD
export: excel  # optional, untuk export CSV
```

**CSV Export Headers**:
```
NIDN,Nama Dosen,Tanggal,Jam Masuk,Status
```

---

### 7. Dashboard/Home

**Endpoint**: `/index.php` (GET)

**Displays**:
- Total dosen
- Hadir hari ini
- Terlambat hari ini
- Absensi terbaru (6 data terakhir)

---

## Status Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 302 | Redirect |
| 404 | Not Found |
| 500 | Server Error |

## Authentication Endpoints

### Login

**Endpoint**: `/login.php` (POST)

**Form Data**:
```
username: [string]
password: [string]
```

**Response**: Redirect to index.php or show error

---

### Logout

**Endpoint**: `/logout.php`

**Response**: Redirect to login.php

---

## Data Models

### Dosen (Teacher)
```json
{
  "id": "number",
  "nidn": "string(20)",
  "nama": "string(150)",
  "kontak": "string(20)",
  "email": "string(100)",
  "created_at": "timestamp"
}
```

### Absensi (Attendance)
```json
{
  "id": "number",
  "dosen_id": "number (FK)",
  "tanggal": "date",
  "jam_masuk": "time",
  "status": "enum(Hadir, Terlambat)",
  "created_at": "timestamp"
}
```

### Admin
```json
{
  "id": "number",
  "username": "string(50)",
  "password": "string(255) [hashed]",
  "nama_lengkap": "string(100)",
  "created_at": "timestamp"
}
```

## Security

- ✅ CSRF Token required for POST/DELETE
- ✅ Input validation & sanitization
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ XSS protection
- ✅ Authentication required
- ✅ Password hashing (bcrypt)

## Rate Limiting

Currently no rate limiting implemented. Recommended to add for production:
- Scan: 10 requests per minute per IP
- API: 100 requests per minute per session

## Error Handling

All API responses follow consistent format:
```json
{
  "success": true|false,
  "title": "title",
  "message": "description",
  "data": {} // optional
}
```

## Examples

### cURL - Scan Attendance
```bash
curl -X POST http://localhost:8000/scan_absensi.php \
  -H "Content-Type: application/json" \
  -d '{"nidn":"0012047801"}' \
  -b "PHPSESSID=your_session_id"
```

### JavaScript - Scan Attendance
```javascript
fetch('/scan_absensi.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({nidn: '0012047801'})
})
.then(r => r.json())
.then(data => console.log(data));
```

### Python - Scan Attendance
```python
import requests
import json

url = 'http://localhost:8000/scan_absensi.php'
headers = {'Content-Type': 'application/json'}
data = {'nidn': '0012047801'}

response = requests.post(url, headers=headers, json=data)
print(response.json())
```

## Troubleshooting

### 401 Unauthorized
- Not logged in, visit `/login.php` first
- Session expired, re-login

### 403 Forbidden
- CSRF token invalid/missing
- Ensure POST request includes csrf_token

### 404 Not Found
- Endpoint doesn't exist
- Check URL spelling

### 500 Internal Server Error
- Database connection failed
- Check config.php credentials
- Check server logs

## Version Info

- **API Version**: 1.0
- **Last Updated**: 2024
- **Status**: Stable

---

For more information, see [DEPLOYMENT.md](DEPLOYMENT.md) and [README.md](README.md)
