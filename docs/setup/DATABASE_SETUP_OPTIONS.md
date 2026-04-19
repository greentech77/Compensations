# Možnosti Nastavitve Baze Podatkov

## Trenutno Stanje
- ❌ MySQL/MariaDB strežnik ne teče
- ❌ Port 3306 ni odprt
- ❌ MySQL proces ni najden

## Možnosti Nastavitve

### Možnost 1: Namesti in Zaženi MySQL/MariaDB (Priporočeno za Production)

```bash
# Namesti MySQL
sudo apt update
sudo apt install -y mysql-server

# Zaženi MySQL
sudo systemctl start mysql
sudo systemctl enable mysql

# Nastavi root geslo (če je potrebno)
sudo mysql_secure_installation

# Ustvari bazo
sudo mysql -u root -e "CREATE DATABASE kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -u root -e "CREATE USER IF NOT EXISTS 'kompenzacije_user'@'localhost' IDENTIFIED BY 'your_password';"
sudo mysql -u root -e "GRANT ALL PRIVILEGES ON kompenzacije_app.* TO 'kompenzacije_user'@'localhost';"
sudo mysql -u root -e "FLUSH PRIVILEGES;"

# Posodobi .env
# DB_USERNAME=kompenzacije_user
# DB_PASSWORD=your_password
```

### Možnost 2: Uporabi SQLite za Development (Najenostavnejše)

```bash
cd ~/www/compenzations

# Spremeni .env
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
sed -i 's/^DB_DATABASE=.*/DB_DATABASE=database\/database.sqlite/' .env

# Ustvari SQLite datoteko
touch database/database.sqlite

# Preveri povezavo
php artisan migrate:status
```

**Posodobi .env:**
```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
DB_DATABASE=database/database.sqlite
# DB_USERNAME=root
# DB_PASSWORD=
```

### Možnost 3: Uporabi Docker (Če imaš Docker)

```bash
# Zaženi MySQL v Dockerju
docker run --name kompenzacije-mysql \
  -e MYSQL_ROOT_PASSWORD=rootpassword \
  -e MYSQL_DATABASE=kompenzacije_app \
  -p 3306:3306 \
  -d mysql:8.0

# Posodobi .env
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_USERNAME=root
# DB_PASSWORD=rootpassword
```

### Možnost 4: Uporabi Existing MySQL Server

Če imaš že nameščen MySQL, vendar teče na drugem hostu ali portu:

```bash
# Preveri, kje teče MySQL
netstat -tlnp | grep mysql
# ali
ps aux | grep mysql

# Posodobi .env z ustreznimi podatki
# DB_HOST=tvoj_mysql_host
# DB_PORT=tvoj_mysql_port
# DB_USERNAME=tvoj_user
# DB_PASSWORD=tvoje_geslo
```

## Hitra SQLite Nastavitev (Za Development)

Najhitrejša možnost za začetek razvoja:

```bash
cd ~/www/compenzations

# Spremeni na SQLite
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
sed -i 's/^DB_DATABASE=.*/# DB_DATABASE=/' .env
echo "" >> .env
echo "# SQLite Database" >> .env
echo "DB_DATABASE=database/database.sqlite" >> .env

# Ustvari datoteko
mkdir -p database
touch database/database.sqlite

# Preveri
php artisan migrate:status

# Zaženi migracije
php artisan migrate
```

## Katera Možnost?

- **Development/Testing**: SQLite (najhitrejša)
- **Production/Staging**: MySQL/MariaDB (ustrezna za produkcijo)
- **Če že imaš Docker**: Docker MySQL (enostavna izolacija)

---
*Posodobljeno: 2025-11-30*

