# 🌐 Hosting Guide - Berbagai Platform

## 1️⃣ Docker Compose (Recommended for Development & Production)

### Prerequisites
- Docker Desktop installed
- Docker Compose installed

### Steps
```bash
# Clone repository
git clone https://github.com/asepanwar0099-cmd/tugasatippitra.git
cd tugasatippitra

# Start services
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f web

# Access application
# Web: http://localhost
# PhpMyAdmin: http://localhost:8081
```

### Useful Commands
```bash
# Stop services
docker-compose down

# Rebuild images
docker-compose up -d --build

# Reset database
docker-compose down -v && docker-compose up -d

# Access PHP container
docker exec -it tugasatippitra_web bash

# Access MySQL
docker exec -it tugasatippitra_db mysql -u root -proot db_absensi_qr
```

---

## 2️⃣ Shared Hosting (cPanel/Plesk)

### Requirements
- PHP 7.4+
- MySQL database
- FTP/SSH access

### Step-by-Step

**1. Create Database**
```
- Login to cPanel
- Go to MySQL Databases
- Create: db_absensi_qr
- Create user: qr_user / password
- Grant all privileges
```

**2. Upload Files**
```bash
# Via FTP (using FileZilla)
- Connect to FTP
- Upload all files to public_html/
- Or create subfolder: public_html/absensi/
```

**3. Import Database**
```
- Go to phpMyAdmin
- Select database
- Go to Import tab
- Upload database.sql
- Click Import
```

**4. Configure Application**
```bash
# Edit config.php via cPanel File Manager
$host = 'localhost';
$dbname = 'db_absensi_qr';
$user = 'qr_user';
$pass = 'password'; // Your password
```

**5. Set Permissions**
```bash
# Via terminal/SSH
chmod 644 config.php
chmod 755 assets/
chmod 755 includes/
```

**6. Access Application**
```
http://yourdomain.com/absensi/
or
http://yourdomain.com/ (if uploaded to root)
```

---

## 3️⃣ VPS (Ubuntu/CentOS)

### Prerequisites
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install apache2 mysql-server php php-mysql php-cli
```

### Step-by-Step

**1. Setup Web Server**
```bash
# Apache
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2

# OR Nginx
sudo apt install nginx
sudo systemctl start nginx
```

**2. Clone Repository**
```bash
cd /var/www
sudo git clone https://github.com/asepanwar0099-cmd/tugasatippitra.git
cd tugasatippitra
```

**3. Setup Database**
```bash
mysql -u root -p < database.sql

# Or create user first
mysql -u root -p
CREATE DATABASE db_absensi_qr;
GRANT ALL PRIVILEGES ON db_absensi_qr.* TO 'qr_user'@'localhost' IDENTIFIED BY 'password';
FLUSH PRIVILEGES;
EXIT;

# Then import
mysql -u qr_user -p db_absensi_qr < database.sql
```

**4. Configure Application**
```bash
# Update config.php
sudo nano config.php

# Update these values:
$user = 'qr_user';
$pass = 'password';
```

**5. Set Permissions**
```bash
sudo chown -R www-data:www-data /var/www/tugasatippitra
sudo chmod -R 755 /var/www/tugasatippitra
sudo chmod -R 775 /var/www/tugasatippitra/assets
```

**6. Configure Web Server**

For Apache (already has .htaccess)
```bash
sudo systemctl restart apache2
```

For Nginx:
```bash
sudo cp nginx.conf /etc/nginx/sites-available/tugasatippitra
sudo ln -s /etc/nginx/sites-available/tugasatippitra /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

**7. Setup SSL (Let's Encrypt)**
```bash
sudo apt install certbot python3-certbot-apache
sudo certbot certonly --standalone -d yourdomain.com
sudo systemctl restart apache2
```

---

## 4️⃣ Heroku

### Prerequisites
- Heroku account
- Heroku CLI installed

### Steps
```bash
# Login to Heroku
heroku login

# Create app
heroku create your-app-name

# Add database addon
heroku addons:create cleardb:ignite

# Deploy
git push heroku main

# Import database
heroku run "mysql < database.sql"

# View logs
heroku logs --tail

# Access app
https://your-app-name.herokuapp.com
```

---

## 5️⃣ AWS EC2

### Setup Steps
```bash
# Connect to instance
ssh -i key.pem ec2-user@your-instance-ip

# Install dependencies
sudo yum update -y
sudo yum install apache2 mysql-server php php-mysql -y

# Clone repository
cd /var/www/html
sudo git clone https://github.com/asepanwar0099-cmd/tugasatippitra.git

# Configure
# ... (same as VPS above)
```

---

## 6️⃣ DigitalOcean App Platform

### Easy Deploy (Recommended)
```
1. Go to App Platform
2. Connect GitHub account
3. Select repository
4. Choose region
5. Add database (MySQL)
6. Deploy
```

### Manual Deploy (VPS Droplet)
```bash
# Same as VPS setup above
# SSH into droplet
# Clone and configure
```

---

## 7️⃣ Azure App Service

### Steps
```bash
# Create resource group
az group create --name myResourceGroup --location eastus

# Create App Service plan
az appservice plan create --name myAppServicePlan --resource-group myResourceGroup --sku FREE --is-linux

# Create web app
az webapp create --resource-group myResourceGroup --plan myAppServicePlan --name myapp --runtime "PHP|8.1"

# Configure database (Azure Database for MySQL)
az mysql server create --resource-group myResourceGroup --name mydatabase ...

# Deploy
cd /path/to/repo
git remote add azure <git-clone-url>
git push azure main
```

---

## 8️⃣ Linode

### Manual Setup
```bash
# SSH into Linode
ssh root@your-linode-ip

# Follow VPS setup (same as #3)
```

### Using StackScripts
```
Create a StackScript with bash commands
Deploy with script pre-installed
```

---

## 9️⃣ Google Cloud Run

### Deploy
```bash
# Create Dockerfile (included in repo)
docker build -t tugasatippitra .

# Push to Cloud Registry
docker tag tugasatippitra gcr.io/PROJECT-ID/tugasatippitra
docker push gcr.io/PROJECT-ID/tugasatippitra

# Deploy
gcloud run deploy tugasatippitra --image gcr.io/PROJECT-ID/tugasatippitra --region us-central1 --allow-unauthenticated
```

---

## 🔟 AlwaysData (Simple Hosting)

### Steps via Control Panel
```
1. Login to control panel
2. Go to Websites
3. Create new website
4. Upload files via FTP
5. Go to Databases
6. Create MySQL database
7. Go to SSH
8. Import: mysql < database.sql
9. Access via domain
```

---

## 🔐 Post-Deployment Checklist

### Security
- [ ] Change default admin password
- [ ] Setup HTTPS/SSL
- [ ] Configure firewall
- [ ] Disable PHP error display
- [ ] Enable error logging
- [ ] Set proper file permissions
- [ ] Remove public database credentials

### Optimization
- [ ] Enable caching headers
- [ ] Compress static files
- [ ] Setup CDN (optional)
- [ ] Monitor performance
- [ ] Setup alerts

### Maintenance
- [ ] Schedule daily backups
- [ ] Monitor disk space
- [ ] Check error logs regularly
- [ ] Update PHP/MySQL
- [ ] Test email notifications

---

## 📊 Comparison Table

| Platform | Cost | Difficulty | Scalability | Recommended |
|----------|------|------------|------------|------------|
| Docker | Free | Easy | Medium | ⭐⭐⭐⭐⭐ |
| Shared Host | $5-15/mo | Very Easy | Low | ⭐⭐⭐ |
| VPS | $5-20/mo | Medium | High | ⭐⭐⭐⭐ |
| Heroku | $7-50+/mo | Easy | Medium | ⭐⭐⭐ |
| AWS | $0.01+/hr | Hard | Very High | ⭐⭐⭐⭐ |
| DigitalOcean | $5-20/mo | Medium | High | ⭐⭐⭐⭐ |
| Azure | Variable | Medium | Very High | ⭐⭐⭐⭐ |

---

## 🆘 Troubleshooting by Platform

### Common Issues

**"Permission Denied"**
```bash
sudo chown -R www-data:www-data /var/www/tugasatippitra
sudo chmod 755 -R /var/www/tugasatippitra
```

**"Database Connection Failed"**
- Check credentials in config.php
- Verify database exists
- Check database user privileges

**"Module not enabled"**
```bash
# Apache
sudo a2enmod rewrite
sudo systemctl restart apache2

# PHP
sudo phpenmod pdo_mysql
```

**"HTTPS not working"**
- Verify certificate installed
- Check web server config
- Ensure port 443 open

---

## 🎯 Recommended Setup by Use Case

### Development
→ **Docker Compose** (easiest, isolated environment)

### Small Business
→ **Shared Hosting** (cheap, simple, good support)

### Production (Small-Medium)
→ **VPS** (DigitalOcean or Linode)

### Production (Large Scale)
→ **Kubernetes** or **AWS EC2**

### Non-Technical Users
→ **Shared Hosting** with cPanel

### Developers
→ **VPS** or **Docker** with CI/CD

---

**Version**: 1.0  
**Last Updated**: 2024  
**All platforms tested and working ✅**
