# Database Setup Guide

## Current Configuration

The `.env` file has been configured with the following database settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kompenzacije_app
DB_USERNAME=root
DB_PASSWORD=
```

## Next Steps

### 1. Create Database (if not exists)

If you need to create the database manually, connect to MySQL/MariaDB and run:

```sql
CREATE DATABASE IF NOT EXISTS kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or using command line:

```bash
# If MySQL/MariaDB is available via command line
mysql -u root -e "CREATE DATABASE IF NOT EXISTS kompenzacije_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 2. Update Database Credentials (if needed)

If your database uses different credentials, update `.env`:

```bash
# Edit .env file
nano .env

# Or use sed to update specific values
sed -i 's/^DB_USERNAME=.*/DB_USERNAME=your_username/' .env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=your_password/' .env
sed -i 's/^DB_DATABASE=.*/DB_DATABASE=your_database_name/' .env
```

### 3. Test Database Connection

```bash
php artisan migrate:status
```

If connection is successful, you should see a list of migrations.

### 4. Run Migrations

Once database connection is working:

```bash
php artisan migrate
```

This will create all necessary tables.

### 5. Seed Database

Run seeders to populate reference data:

```bash
# Seed only post numbers
php artisan db:seed --class=PostNumberSeeder

# Or seed all
php artisan db:seed
```

## Troubleshooting

### Error: "could not find driver"

This means PHP MySQL extension is not installed. Install it:

```bash
# For Ubuntu/Debian
sudo apt install php-mysql

# Then restart PHP-FPM or web server
```

### Error: "Access denied for user"

Check:
1. Database username and password in `.env`
2. User has permission to access the database
3. Database exists

### Error: "Unknown database"

Create the database first (see Step 1 above).

## Legacy Database Migration

If you want to migrate data from legacy database `kompenza_kompenzacije`, you'll need to:

1. Update `.env` with legacy database connection temporarily
2. Create seeder to migrate data
3. Run seeder
4. Switch back to new database

See `.cursor/rules/.cursorrules-compenzations` for detailed migration instructions.

---
*Last updated: 2025-11-30*

