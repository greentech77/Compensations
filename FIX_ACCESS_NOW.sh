#!/bin/bash
# Hitri popravek MySQL dostopa

echo "MySQL Access Fix - Izberi možnost:"
echo ""
echo "1. Nastavi root geslo (preprosto)"
echo "2. Ustvari dedičnega uporabnika (priporočeno)"
echo ""
read -p "Izberi (1 ali 2): " choice

if [ "$choice" == "1" ]; then
    read -sp "Vnesi geslo za root: " PASSWORD
    echo ""
    
    sudo mysql -u root <<SQL
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${PASSWORD}';
CREATE DATABASE IF NOT EXISTS kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
FLUSH PRIVILEGES;
SQL
    
    cd ~/www/compenzations
    sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD='${PASSWORD}'|' .env
    echo "✅ Root geslo nastavljeno"
    
elif [ "$choice" == "2" ]; then
    read -p "Vnesi uporabniško ime (default: kompenzacije_user): " USER
    USER=${USER:-kompenzacije_user}
    
    read -sp "Vnesi geslo: " PASSWORD
    echo ""
    
    sudo mysql -u root <<SQL
CREATE DATABASE IF NOT EXISTS kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${USER}'@'localhost' IDENTIFIED BY '${PASSWORD}';
GRANT ALL PRIVILEGES ON kompenzacije_app.* TO '${USER}'@'localhost';
FLUSH PRIVILEGES;
SQL
    
    cd ~/www/compenzations
    sed -i 's/^DB_USERNAME=.*/DB_USERNAME='${USER}'/' .env
    sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD='${PASSWORD}'|' .env
    echo "✅ Uporabnik ${USER} ustvarjen"
fi

echo ""
echo "Preverjanje povezave..."
php artisan migrate:status 2>&1 | head -5

echo ""
echo "Če je povezava OK, zaženi:"
echo "  php artisan migrate"
