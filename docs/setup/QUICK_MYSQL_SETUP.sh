#!/bin/bash
# Hitra MySQL namestitev

echo "Namestitev MySQL strežnika..."

sudo apt update
sudo apt install -y mysql-server

echo "Zaganjanje MySQL..."
sudo systemctl start mysql
sudo systemctl enable mysql

echo "Ustvarjanje baze podatkov..."
sudo mysql -u root <<SQL
CREATE DATABASE IF NOT EXISTS kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
FLUSH PRIVILEGES;
SQL

echo "Preverjanje povezave..."
cd ~/www/compenzations
php artisan migrate:status 2>&1 | head -5

echo ""
echo "Če je povezava uspešna, zaženi:"
echo "  php artisan migrate"
echo "  php artisan db:seed --class=PostNumberSeeder"
