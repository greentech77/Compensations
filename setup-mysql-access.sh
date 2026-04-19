#!/bin/bash

# MySQL Access Setup Script

set -e

echo "=========================================="
echo "MySQL Access Setup - Kompenzacije"
echo "=========================================="
echo ""

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Generate random password or use provided one
if [ -z "$1" ]; then
    # Generate random password
    MYSQL_PASSWORD=$(openssl rand -base64 12 | tr -d "=+/" | cut -c1-16)
    echo "Generated password: ${MYSQL_PASSWORD}"
else
    MYSQL_PASSWORD=$1
    echo "Using provided password"
fi

DB_NAME="kompenzacije_app"
DB_USER="kompenzacije_user"

echo ""
echo "Setting up MySQL access..."
echo "  Database: ${DB_NAME}"
echo "  User: ${DB_USER}"
echo ""

# Create database and user
sudo mysql -u root <<EOF
-- Create database
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user with password
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${MYSQL_PASSWORD}';

-- Grant privileges
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';

-- Also update root to use password authentication (optional)
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${MYSQL_PASSWORD}';

FLUSH PRIVILEGES;
EOF

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Database and user created successfully${NC}"
else
    echo -e "${RED}❌ Failed to create database/user${NC}"
    exit 1
fi

# Update .env file
cd ~/www/compenzations

echo ""
echo "Updating .env file..."

# Update DB_USERNAME
sed -i 's/^DB_USERNAME=.*/DB_USERNAME='${DB_USER}'/' .env

# Update DB_PASSWORD
if grep -q "^DB_PASSWORD=" .env; then
    sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD='${MYSQL_PASSWORD}'/' .env
else
    echo "DB_PASSWORD=${MYSQL_PASSWORD}" >> .env
fi

# Ensure DB_DATABASE is correct
sed -i 's/^DB_DATABASE=.*/DB_DATABASE='${DB_NAME}'/' .env

echo -e "${GREEN}✅ .env file updated${NC}"

# Display password
echo ""
echo "=========================================="
echo -e "${YELLOW}IMPORTANT: Save this password!${NC}"
echo "=========================================="
echo "MySQL Password: ${MYSQL_PASSWORD}"
echo "Username: ${DB_USER}"
echo "Database: ${DB_NAME}"
echo "=========================================="
echo ""

# Test connection
echo "Testing database connection..."
if php artisan migrate:status &> /dev/null; then
    echo -e "${GREEN}✅ Database connection successful!${NC}"
    echo ""
    echo "Next steps:"
    echo "  ${GREEN}php artisan migrate${NC}"
    echo "  ${GREEN}php artisan db:seed --class=PostNumberSeeder${NC}"
else
    ERROR=$(php artisan migrate:status 2>&1)
    echo -e "${YELLOW}⚠️  Connection test returned:${NC}"
    echo "$ERROR" | head -3
    echo ""
    echo "Please check the error above."
fi

echo ""
echo "=========================================="

