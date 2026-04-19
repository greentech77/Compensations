#!/bin/bash

# Quick Database Setup Script (requires sudo)
# Run this if you have sudo privileges and want automatic setup

set -e

echo "=========================================="
echo "Kompenzacije - Quick Database Setup"
echo "=========================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Load .env
if [ ! -f .env ]; then
    echo "Error: .env file not found!"
    exit 1
fi

export $(grep -v '^#' .env | xargs)

# Detect PHP version
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;")

# Install PHP MySQL extension
echo "Installing PHP MySQL extension..."
sudo apt update
sudo apt install -y php${PHP_VERSION}-mysql

# Restart PHP-FPM if available
if systemctl is-active --quiet php${PHP_VERSION}-fpm; then
    echo "Restarting PHP-FPM..."
    sudo systemctl restart php${PHP_VERSION}-fpm
fi

# Create database if MySQL is available
if command -v mysql &> /dev/null; then
    echo "Creating database..."
    mysql -u "${DB_USERNAME}" ${DB_PASSWORD:+-p"${DB_PASSWORD}"} -h "${DB_HOST}" -e "CREATE DATABASE IF NOT EXISTS ${DB_DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || {
        echo -e "${YELLOW}Could not create database automatically.${NC}"
        echo "Please create it manually or check credentials."
    }
fi

# Test connection
echo ""
echo "Testing database connection..."
if php artisan migrate:status &> /dev/null; then
    echo -e "${GREEN}✅ Setup complete!${NC}"
    echo ""
    echo "Next: php artisan migrate"
else
    echo "Database connection test failed. Please check your .env configuration."
fi

