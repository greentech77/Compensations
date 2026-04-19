# MySQL Nastavitev - Kompenzacije

## Trenutno Stanje
- ✅ `.env` nastavljen na MySQL
- ✅ PHP MySQL razširitev nameščena (php8.3-mysql)
- ❌ MySQL strežnik ni nameščen ali ne teče

## Hitra Namestitev

### Korak 1: Namesti MySQL Server

```bash
cd ~/www/compenzations

# Uporabi skripto (zahteva sudo)
./setup-mysql-server.sh

# ALI ročno:
sudo apt update
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql
```

### Korak 2: Ustvari Bazo Podatkov

```bash
# Poveži se na MySQL kot root
sudo mysql -u root

# V MySQL konzoli:
CREATE DATABASE kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
FLUSH PRIVILEGES;
EXIT;
```

### Korak 3: Nastavi Dostop

#### Možnost A: Uporabi Root (Preprosto, vendar manj varno)

```bash
# Preveri, ali root zahteva geslo
sudo mysql -u root

# Če se uspešno povežeš brez gesla, vse je OK
# .env je že nastavljen:
# DB_USERNAME=root
# DB_PASSWORD=
```

#### Možnost B: Ustvari Dedičnega Uporabnika (Priporočeno)

```bash
sudo mysql -u root

# V MySQL konzoli:
CREATE USER 'kompenzacije_user'@'localhost' IDENTIFIED BY 'tvoje_geslo';
GRANT ALL PRIVILEGES ON kompenzacije_app.* TO 'kompenzacije_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Posodobi .env
sed -i 's/^DB_USERNAME=.*/DB_USERNAME=kompenzacije_user/' ~/www/compenzations/.env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=tvoje_geslo/' ~/www/compenzations/.env
```

### Korak 4: Preveri Povezavo

```bash
cd ~/www/compenzations
php artisan migrate:status
```

Če vidiš seznam migracij (ali "Migration table not found"), je povezava uspešna!

### Korak 5: Zaženi Migracije

```bash
php artisan migrate
php artisan db:seed --class=PostNumberSeeder
```

## Reševanje Težav

### Napaka: "Access denied for user 'root'@'localhost'"

**Rešitev 1:** Preveri, ali root zahteva geslo:
```bash
sudo mysql -u root
# Če se poveže, root ne zahteva gesla (OK)
```

**Rešitev 2:** Če root zahteva geslo, nastavi geslo ali ustvari novega uporabnika:
```bash
sudo mysql -u root -p
# Vnesi geslo, nato:
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'novo_geslo';
FLUSH PRIVILEGES;
# Posodobi DB_PASSWORD v .env
```

### Napaka: "Connection refused"

**Rešitev:** MySQL strežnik ne teče
```bash
sudo systemctl start mysql
sudo systemctl status mysql
```

### Napaka: "Unknown database"

**Rešitev:** Baza ne obstaja - ustvari jo (glej Korak 2)

---
*Posodobljeno: 2025-11-30*

