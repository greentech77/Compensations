#!/bin/bash

# MySQL Server Setup Script for Kompenzacije Project

set -e

echo "=========================================="
echo "MySQL Server Setup - Kompenzacije"
echo "=========================================="
echo ""

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Check if MySQL/MariaDB is installed
if command -v mysql &> /dev/null || command -v mariadb &> /dev/null; then
    echo -e "${GREEN}✅ MySQL/MariaDB is installed${NC}"
else
    echo -e "${YELLOW}⚠️  MySQL/MariaDB not found${NC}"
    echo "Installing MySQL Server..."
    
    sudo apt update
    sudo apt install -y mysql-server
    
    echo -e "${GREEN}✅ MySQL Server installed${NC}"
fi

# Check if MySQL is running
if systemctl is-active --quiet mysql 2>/dev/null || systemctl is-active --quiet mariadb 2>/dev/null; then
    echo -e "${GREEN}✅ MySQL/MariaDB service is running${NC}"
else
    echo -e "${YELLOW}⚠️  MySQL service not running, starting...${NC}"
    sudo systemctl start mysql 2>/dev/null || sudo systemctl start mariadb
    sudo systemctl enable mysql 2>/dev/null || sudo systemctl enable mariadb
    echo -e "${GREEN}✅ MySQL service started${NC}"
fi

# Load .env variables
cd ~/www/compenzations
export $(grep -v '^#' .env | grep "^DB_" | xargs)

echo ""
echo "Creating database: ${DB_DATABASE}"

# Create database
sudo mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS ${DB_DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
FLUSH PRIVILEGES;
EOF

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Database created successfully${NC}"
else
    echo -e "${RED}❌ Failed to create database${NC}"
    exit 1
fi

# Test connection
echo ""
echo "Testing database connection..."

if php artisan migrate:status &> /dev/null; then
    echo -e "${GREEN}✅ Database connection successful!${NC}"
    echo ""
    echo "Next steps:"
    echo "  ${GREEN}php artisan migrate${NC}"
    echo "  ${GREEN}php artisan db:seed --class=PostNumberSeeder${NC}"
else
    ERROR=$(php artisan migrate:status 2>&1)
    
    if echo "$ERROR" | grep -q "Access denied"; then
        echo -e "${YELLOW}⚠️  Access denied - you may need to set MySQL root password${NC}"
        echo ""
        echo "Option 1: Set root password and update .env"
        echo "  sudo mysql -u root"
        echo "  ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';"
        echo "  FLUSH PRIVILEGES;"
        echo "  EXIT;"
        echo "  Then update DB_PASSWORD in .env"
        echo ""
        echo "Option 2: Create a new user for the application"
        echo "  sudo mysql -u root"
        echo "  CREATE USER 'kompenzacije_user'@'localhost' IDENTIFIED BY 'secure_password';"
        echo "  GRANT ALL PRIVILEGES ON kompenzacije_app.* TO 'kompenzacije_user'@'localhost';"
        echo "  FLUSH PRIVILEGES;"
        echo "  EXIT;"
        echo "  Then update DB_USERNAME and DB_PASSWORD in .env"
    else
        echo -e "${RED}❌ Connection failed:${NC}"
        echo "$ERROR"
    fi
fi

echo ""
echo "=========================================="

