# Navodila za Nastavitev Baze Podatkov

## Trenutno Stanje

✅ `.env` datoteka je nastavljena
❌ PHP MySQL razširitev ni nameščena
❌ Baza podatkov morda ne obstaja

## Korak 1: Namesti PHP MySQL Razširitev

Odpri terminal in zaženi:

```bash
cd ~/www/compenzations

# Posodobi paketni seznam
sudo apt update

# Namesti PHP MySQL razširitev
sudo apt install -y php8.3-mysql

# Če uporabljaš PHP-FPM, ga restartiraj
sudo systemctl restart php8.3-fpm
```

**Ali uporabi hitro skripto:**
```bash
cd ~/www/compenzations
./setup-database-quick.sh
```

## Korak 2: Preveri Namestitev

```bash
php -m | grep -i mysql
```

Morali bi videti: `mysqli` ali `pdo_mysql`

## Korak 3: Ustvari Bazo Podatkov

### Možnost A: Če imaš MySQL/MariaDB dostop

```bash
mysql -u root -p
```

Nato v MySQL konzoli:
```sql
CREATE DATABASE IF NOT EXISTS kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Možnost B: Z uporabo skripte

Skripta `setup-database.sh` ti bo pomagala, če je MySQL na voljo.

## Korak 4: Preveri Povezavo

```bash
cd ~/www/compenzations
php artisan migrate:status
```

Če vidiš seznam migracij (ali sporočilo "Migration table not found"), je povezava uspešna!

## Korak 5: Zaženi Migracije

Ko je povezava uspešna:

```bash
# Zaženi vse migracije
php artisan migrate

# Zaženi seedere (poštne številke)
php artisan db:seed --class=PostNumberSeeder

# Ali zaženi vse seedere
php artisan db:seed
```

## Reševanje Težav

### Napaka: "could not find driver"
- PHP MySQL razširitev ni nameščena
- Glej Korak 1

### Napaka: "Unknown database"
- Baza podatkov ne obstaja
- Glej Korak 3

### Napaka: "Access denied"
- Preveri uporabniško ime in geslo v `.env`
- Preveri, ali ima uporabnik dovoljenja za dostop do baze

### Napaka: "Connection refused"
- Preveri, ali MySQL/MariaDB teče: `sudo systemctl status mysql`
- Preveri `DB_HOST` v `.env` (običajno `127.0.0.1` ali `localhost`)

## Hitri Ukazi (Po Namestitvi)

```bash
# Test povezave
php artisan migrate:status

# Zaženi migracije
php artisan migrate

# Zaženi seedere
php artisan db:seed --class=PostNumberSeeder

# Preveri vse seedere
php artisan db:seed

# Zaženi development server
php artisan serve
```

---
*Posodobljeno: 2025-11-30*

