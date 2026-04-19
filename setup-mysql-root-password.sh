#!/bin/bash

# Alternative: Setup root password only (simpler but less secure)

set -e

echo "=========================================="
echo "MySQL Root Password Setup"
echo "=========================================="
echo ""

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

if [ -z "$1" ]; then
    # Generate random password
    ROOT_PASSWORD=$(openssl rand -base64 12 | tr -d "=+/" | cut -c1-16)
    echo "Generated password: ${ROOT_PASSWORD}"
else
    ROOT_PASSWORD=$1
    echo "Using provided password"
fi

DB_NAME="kompenzacije_app"

echo ""
echo "Setting MySQL root password and creating database..."

# Set root password and create database
sudo mysql -u root <<EOF
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${ROOT_PASSWORD}';
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
FLUSH PRIVILEGES;
EOF

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Root password set and database created${NC}"
else
    echo -e "${RED}❌ Failed${NC}"
    exit 1
fi

# Update .env
cd ~/www/compenzations

sed -i 's/^DB_USERNAME=.*/DB_USERNAME=root/' .env

if grep -q "^DB_PASSWORD=" .env; then
    sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD='${ROOT_PASSWORD}'|' .env
else
    echo "DB_PASSWORD=${ROOT_PASSWORD}" >> .env
fi

sed -i 's/^DB_DATABASE=.*/DB_DATABASE='${DB_NAME}'/' .env

echo -e "${GREEN}✅ .env file updated${NC}"

echo ""
echo "=========================================="
echo -e "${YELLOW}MySQL Root Password: ${ROOT_PASSWORD}${NC}"
echo "=========================================="
echo ""

# Test connection
echo "Testing connection..."
if php artisan migrate:status &> /dev/null; then
    echo -e "${GREEN}✅ Connection successful!${NC}"
else
    php artisan migrate:status 2>&1 | head -3
fi

