#!/bin/bash
# setup-production.sh - Production setup script

set -e

echo "🚀 Sistem Absensi QR Code - Production Setup"
echo "=============================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root (for VPS setup)
if [[ $EUID -ne 0 ]]; then
   echo -e "${YELLOW}⚠️  Script harus dijalankan dengan sudo untuk permission setup${NC}"
   echo "Run: sudo bash setup-production.sh"
   exit 1
fi

# Detect web server
if command -v apache2 &> /dev/null; then
    WEB_SERVER="apache2"
    WEB_USER="www-data"
elif command -v nginx &> /dev/null; then
    WEB_SERVER="nginx"
    WEB_USER="www-data"
else
    echo -e "${RED}❌ Web server (Apache/Nginx) tidak ditemukan${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Detected web server: $WEB_SERVER${NC}"

# Set permissions
echo ""
echo "📁 Setting up permissions..."
INSTALL_DIR="/var/www/tugasatippitra"

if [ -d "$INSTALL_DIR" ]; then
    chown -R $WEB_USER:$WEB_USER $INSTALL_DIR
    chmod -R 755 $INSTALL_DIR
    chmod -R 775 $INSTALL_DIR/assets
    chmod 644 $INSTALL_DIR/.htaccess 2>/dev/null || true
    echo -e "${GREEN}✓ Permissions updated${NC}"
else
    echo -e "${RED}❌ Directory not found: $INSTALL_DIR${NC}"
    exit 1
fi

# Enable Apache modules
if [ "$WEB_SERVER" = "apache2" ]; then
    echo ""
    echo "📦 Enabling Apache modules..."
    a2enmod rewrite > /dev/null 2>&1 && echo -e "${GREEN}✓ mod_rewrite enabled${NC}"
    a2enmod headers > /dev/null 2>&1 && echo -e "${GREEN}✓ mod_headers enabled${NC}"
    a2enmod ssl > /dev/null 2>&1 && echo -e "${GREEN}✓ mod_ssl enabled${NC}"
fi

# MySQL setup
echo ""
echo "🗄️  Database setup..."

read -p "Enter MySQL username [root]: " MYSQL_USER
MYSQL_USER=${MYSQL_USER:-root}

read -sp "Enter MySQL password: " MYSQL_PASS
echo ""

# Test database connection
if mysql -u"$MYSQL_USER" -p"$MYSQL_PASS" -e "SELECT 1" > /dev/null 2>&1; then
    echo -e "${GREEN}✓ Database connection successful${NC}"
    
    # Import database
    echo "Importing database schema..."
    mysql -u"$MYSQL_USER" -p"$MYSQL_PASS" < $INSTALL_DIR/database.sql
    echo -e "${GREEN}✓ Database schema imported${NC}"
else
    echo -e "${RED}❌ Database connection failed${NC}"
    exit 1
fi

# Create .env file
echo ""
echo "⚙️  Creating configuration..."
cat > $INSTALL_DIR/.env << EOF
# Production Configuration
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta

# Database
DB_HOST=localhost
DB_PORT=3306
DB_NAME=db_absensi_qr
DB_USER=$MYSQL_USER
DB_PASSWORD=$MYSQL_PASS

# Security
ENABLE_HTTPS=true
SESSION_TIMEOUT=3600

# Attendance
ATTENDANCE_DEADLINE_HOUR=08
ATTENDANCE_DEADLINE_MINUTE=00
EOF

chmod 600 $INSTALL_DIR/.env
echo -e "${GREEN}✓ Configuration created${NC}"

# Restart web server
echo ""
echo "🔄 Restarting web server..."
systemctl restart $WEB_SERVER
echo -e "${GREEN}✓ Web server restarted${NC}"

# Final checklist
echo ""
echo -e "${GREEN}✅ Production setup completed!${NC}"
echo ""
echo "📋 Post-Setup Checklist:"
echo "  ☐ Edit config.php with database credentials"
echo "  ☐ Access http://localhost/tugasatippitra"
echo "  ☐ Login with admin/password123"
echo "  ☐ CHANGE DEFAULT PASSWORD IMMEDIATELY!"
echo "  ☐ Setup HTTPS/SSL certificate"
echo "  ☐ Configure domain name"
echo "  ☐ Setup automated backups"
echo "  ☐ Monitor application logs"
echo ""
echo "📄 Important files:"
echo "  - Config: $INSTALL_DIR/config.php"
echo "  - Env: $INSTALL_DIR/.env"
echo "  - Docs: $INSTALL_DIR/DEPLOYMENT.md"
echo ""
echo "🎉 Ready for production!"
