# Hitra Namestitev - Samo Kopiraj in Zaženi

```bash
cd ~/www/compenzations

# 1. Namesti PHP MySQL razširitev
sudo apt update
sudo apt install -y php8.3-mysql

# 2. Restartiraj PHP-FPM (če je potrebno)
sudo systemctl restart php8.3-fpm 2>/dev/null || true

# 3. Preveri namestitev
php -m | grep -i mysql

# 4. Ustvari bazo (če je MySQL na voljo)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || echo "Ustvari bazo ročno: CREATE DATABASE kompenzacije_app;"

# 5. Preveri povezavo
php artisan migrate:status

# 6. Če je povezava OK, zaženi migracije
# php artisan migrate
# php artisan db:seed --class=PostNumberSeeder
```

