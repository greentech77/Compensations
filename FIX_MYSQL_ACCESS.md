# Popravi MySQL Dostop - Navodila

## Problem
```
Access denied for user 'root'@'localhost'
```

## Rešitev

### Možnost 1: Nastavi Root Geslo (Preprosto)

```bash
cd ~/www/compenzations

# Poveži se na MySQL kot root (brez gesla)
sudo mysql -u root

# V MySQL konzoli izvedi:
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'tvoje_geslo';
CREATE DATABASE IF NOT EXISTS kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
FLUSH PRIVILEGES;
EXIT;

# Posodobi .env
sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD=tvoje_geslo|' .env

# Preveri povezavo
php artisan migrate:status
```

### Možnost 2: Ustvari Dedičnega Uporabnika (Priporočeno)

```bash
cd ~/www/compenzations

# Poveži se na MySQL
sudo mysql -u root

# V MySQL konzoli:
CREATE DATABASE IF NOT EXISTS kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kompenzacije_user'@'localhost' IDENTIFIED BY 'močno_geslo_123';
GRANT ALL PRIVILEGES ON kompenzacije_app.* TO 'kompenzacije_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Posodobi .env
sed -i 's/^DB_USERNAME=.*/DB_USERNAME=kompenzacije_user/' .env
sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD=močno_geslo_123|' .env

# Preveri povezavo
php artisan migrate:status
```

### Možnost 3: Uporabi Skripto (Avtomatsko)

```bash
cd ~/www/compenzations

# Ustvari dedičnega uporabnika z geslom
./setup-mysql-access.sh

# ALI samo nastavi root geslo
./setup-mysql-root-password.sh

# ALI nastavi svojim geslom
./setup-mysql-access.sh "moje_geslo"
./setup-mysql-root-password.sh "moje_geslo"
```

## Po Nastavitvi

Ko je dostop nastavljen:

```bash
# Preveri povezavo
php artisan migrate:status

# Če je uspešno, zaženi migracije
php artisan migrate

# Zaženi seedere
php artisan db:seed --class=PostNumberSeeder
```

## Hitri Ukazi (Kopiraj in Zaženi)

### Za Root z Geslom:

```bash
cd ~/www/compenzations
PASSWORD="tvoje_geslo_123"

sudo mysql -u root <<EOF
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${PASSWORD}';
CREATE DATABASE IF NOT EXISTS kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
FLUSH PRIVILEGES;
EOF

sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD='${PASSWORD}'|' .env
php artisan migrate:status
```

### Za Dedičnega Uporabnika:

```bash
cd ~/www/compenzations
USER="kompenzacije_user"
PASSWORD="močno_geslo_123"

sudo mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${USER}'@'localhost' IDENTIFIED BY '${PASSWORD}';
GRANT ALL PRIVILEGES ON kompenzacije_app.* TO '${USER}'@'localhost';
FLUSH PRIVILEGES;
EOF

sed -i 's/^DB_USERNAME=.*/DB_USERNAME='${USER}'/' .env
sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD='${PASSWORD}'|' .env
php artisan migrate:status
```

---
*Posodobljeno: 2025-11-30*

