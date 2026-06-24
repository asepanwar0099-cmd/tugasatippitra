#!/bin/bash
# test-local.sh - Quick local testing script

echo "🧪 Sistem Absensi QR Code - Local Testing"
echo "=========================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check PHP
echo -e "${BLUE}📋 Checking environment...${NC}"
which php > /dev/null && echo -e "${GREEN}✓ PHP installed${NC}" || echo "✗ PHP not found"

# Check if port 8000 is available
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null > /dev/null 2>&1 ; then
    echo -e "${YELLOW}⚠️  Port 8000 already in use${NC}"
    PORT=8001
else
    PORT=8000
fi

echo -e "${GREEN}✓ Using port $PORT${NC}"
echo ""

# Check files
echo -e "${BLUE}📁 Checking files...${NC}"
FILES=("index.php" "login.php" "config.php" "database.sql" "assets/css/style.css" "includes/header.php")
for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓ $file${NC}"
    else
        echo -e "${YELLOW}✗ $file missing${NC}"
    fi
done

echo ""
echo -e "${BLUE}📚 Documentation files:${NC}"
DOCS=("START_HERE.md" "HOSTING_FINAL.md" "README.md" "QUICK_START.md")
for doc in "${DOCS[@]}"; do
    if [ -f "$doc" ]; then
        echo -e "${GREEN}✓ $doc${NC}"
    else
        echo -e "${YELLOW}✗ $doc missing${NC}"
    fi
done

echo ""
echo -e "${BLUE}🚀 Starting PHP server...${NC}"
echo -e "${YELLOW}Starting on http://localhost:$PORT${NC}"
echo ""
echo "Commands:"
echo "  - Exit server: Press Ctrl+C"
echo "  - Open in browser: open http://localhost:$PORT"
echo ""
echo -e "${YELLOW}Press Ctrl+C to stop${NC}"
echo ""

# Start server
php -S localhost:$PORT

