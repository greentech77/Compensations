#!/bin/bash

# Database Setup Script for Kompenzacije Project
# This script helps set up the database connection and PHP MySQL extension

set -e

echo "=========================================="
echo "Kompenzacije - Database Setup Script"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${RED}Error: .env file not found!${NC}"
    echo "Please create .env file first: cp .env.example .env"
    exit 1
fi

# Load .env variables
export $(grep -v '^#' .env | xargs)

echo "Current Database Configuration:"
echo "  DB_CONNECTION: ${DB_CONNECTION}"
echo "  DB_HOST: ${DB_HOST}"
echo "  DB_PORT: ${DB_PORT}"
echo "  DB_DATABASE: ${DB_DATABASE}"
echo "  DB_USERNAME: ${DB_USERNAME}"
echo ""

# Step 1: Check PHP MySQL extension
echo "Step 1: Checking PHP MySQL extension..."
if php -m | grep -qi "pdo_mysql\|mysql"; then
    echo -e "${GREEN}✅ PHP MySQL extension is installed${NC}"
else
    echo -e "${YELLOW}⚠️  PHP MySQL extension not found${NC}"
    echo ""
    echo "Installing PHP MySQL extension..."
    
    # Detect PHP version
    PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;")
    echo "Detected PHP version: ${PHP_VERSION}"
    
    # Try to install extension
    if command -v apt-get &> /dev/null; then
        echo "Attempting to install php${PHP_VERSION}-mysql..."
        echo "Note: This may require sudo privileges"
        echo ""
        echo "Please run manually:"
        echo "  sudo apt update"
        echo "  sudo apt install php${PHP_VERSION}-mysql"
        echo "  sudo systemctl restart php${PHP_VERSION}-fpm  # if using FPM"
        echo ""
        read -p "Have you installed the extension? (y/n) " -n 1 -r
        echo ""
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            echo -e "${RED}Please install PHP MySQL extension first and run this script again.${NC}"
            exit 1
        fi
    else
        echo -e "${RED}Could not automatically install PHP MySQL extension.${NC}"
        echo "Please install it manually for your system."
        exit 1
    fi
fi

echo ""

# Step 2: Test database connection
echo "Step 2: Testing database connection..."
if php artisan migrate:status &> /dev/null; then
    echo -e "${GREEN}✅ Database connection successful!${NC}"
else
    ERROR=$(php artisan migrate:status 2>&1)
    
    if echo "$ERROR" | grep -q "could not find driver"; then
        echo -e "${RED}❌ PHP MySQL driver not found${NC}"
        echo "Please install PHP MySQL extension (see Step 1)"
        exit 1
    elif echo "$ERROR" | grep -q "Unknown database"; then
        echo -e "${YELLOW}⚠️  Database '${DB_DATABASE}' does not exist${NC}"
        echo ""
        echo "Creating database..."
        
        # Try to create database using MySQL command
        if command -v mysql &> /dev/null; then
            echo "Attempting to create database using MySQL client..."
            mysql -u "${DB_USERNAME}" ${DB_PASSWORD:+-p"${DB_PASSWORD}"} -h "${DB_HOST}" -e "CREATE DATABASE IF NOT EXISTS ${DB_DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null && echo -e "${GREEN}✅ Database created successfully${NC}" || {
                echo -e "${YELLOW}Could not create database automatically.${NC}"
                echo "Please create it manually:"
                echo "  mysql -u ${DB_USERNAME} -p"
                echo "  CREATE DATABASE ${DB_DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
                echo "  EXIT;"
                read -p "Have you created the database? (y/n) " -n 1 -r
                echo ""
                if [[ ! $REPLY =~ ^[Yy]$ ]]; then
                    exit 1
                fi
            }
        else
            echo "MySQL client not found. Please create database manually:"
            echo "  CREATE DATABASE ${DB_DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
            read -p "Have you created the database? (y/n) " -n 1 -r
            echo ""
            if [[ ! $REPLY =~ ^[Yy]$ ]]; then
                exit 1
            fi
        fi
    elif echo "$ERROR" | grep -q "Access denied"; then
        echo -e "${RED}❌ Database access denied${NC}"
        echo "Please check your database credentials in .env file:"
        echo "  DB_USERNAME=${DB_USERNAME}"
        echo "  DB_PASSWORD=${DB_PASSWORD:-'(empty)'}"
        exit 1
    else
        echo -e "${RED}❌ Database connection failed${NC}"
        echo "Error: $ERROR"
        exit 1
    fi
fi

echo ""

# Step 3: Check migration status
echo "Step 3: Checking migration status..."
MIGRATION_STATUS=$(php artisan migrate:status 2>&1)

if echo "$MIGRATION_STATUS" | grep -q "Migration table not found"; then
    echo -e "${YELLOW}⚠️  Migration table not found${NC}"
    echo "Database is ready for migrations."
elif echo "$MIGRATION_STATUS" | grep -q "Ran.*Pending"; then
    echo -e "${YELLOW}⚠️  Some migrations are pending${NC}"
else
    echo -e "${GREEN}✅ Migration system is ready${NC}"
fi

echo ""

# Step 4: Summary and next steps
echo "=========================================="
echo "Setup Summary"
echo "=========================================="
echo ""
echo -e "${GREEN}Database configuration is complete!${NC}"
echo ""
echo "Next steps:"
echo ""
echo "1. Run migrations:"
echo "   ${GREEN}php artisan migrate${NC}"
echo ""
echo "2. Seed database with initial data:"
echo "   ${GREEN}php artisan db:seed --class=PostNumberSeeder${NC}"
echo ""
echo "   Or seed all:"
echo "   ${GREEN}php artisan db:seed${NC}"
echo ""
echo "3. Start development server:"
echo "   ${GREEN}php artisan serve${NC}"
echo ""
echo "4. In another terminal, start frontend dev server:"
echo "   ${GREEN}npm run dev${NC}"
echo ""
echo "=========================================="

