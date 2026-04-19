# Laravel 11 Nadgradnja - Povzetek Sprememb

## Opravljene Spremembe

### 1. Composer.json
- ✅ Laravel framework: `^8.75` → `^11.0`
- ✅ Laravel Sanctum: `^2.8` → `^4.0`
- ✅ Laravel Sail: `^1.10` → `^1.26`
- ✅ Laravel Tinker: `^2.5` → `^2.9`
- ✅ Inertia.js tables: `^1.4` → `^2.0`
- ✅ Ziggy: `^1.0` → `^2.0`
- ✅ PHPUnit: `^9.5.10` → `^11.0`
- ✅ Collision: `^5.10` → `^8.0`
- ✅ Laravel Breeze: `^1.8` → `^2.0`
- ✅ Dodan Laravel Pint: `^1.13`
- ✅ Odstranjen `fruitcake/laravel-cors` (v Laravel 11 je vgrajen)
- ✅ Odstranjen `facade/ignition` (zamenjan z Laravel Pint)

### 2. Bootstrap/App.php
- ✅ Popolnoma prenovljen na Laravel 11 strukturo
- ✅ Middleware konfiguracija prenesena v `bootstrap/app.php`
- ✅ Routes konfiguracija prenesena v `bootstrap/app.php`
- ✅ Exception handling konfiguracija prenesena v `bootstrap/app.php`

### 3. HTTP Kernel
- ✅ Kernel.php ni več potreben (middleware se konfigurira v `bootstrap/app.php`)
- ✅ Vse middleware definicije prenesene v `bootstrap/app.php`

### 4. RouteServiceProvider
- ✅ Odstranjen iz `config/app.php` providers array
- ✅ RouteServiceProvider ni več potreben v Laravel 11
- ✅ Routes se konfigurirajo direktno v `bootstrap/app.php`

### 5. Middleware
- ✅ `RedirectIfAuthenticated` - odstranjen `RouteServiceProvider::HOME`, uporablja direktno `/dashboard`
- ✅ `TrustProxies` - dodana metoda `proxies()` za Laravel 11 kompatibilnost
- ✅ Vsi middleware so kompatibilni z Laravel 11

### 6. Konfiguracijske Datoteke
- ✅ `config/app.php` - odstranjen `RouteServiceProvider` iz providers
- ✅ `config/auth.php` - posodobljen `password_resets` → `password_reset_tokens`
- ✅ `phpunit.xml` - posodobljen schema lokacija

### 7. PHPUnit
- ✅ Posodobljen `phpunit.xml` za PHPUnit 11

## Status Nadgradnje

✅ **NADGRADNJA DOKONČANA!**

- ✅ Laravel verzija: **11.47.0**
- ✅ Composer update: **Dokončan**
- ✅ Routes: **Delujejo**
- ✅ Middleware: **Konfigurirani**
- ✅ Config cache: **Očiščen**

### Preverjeno
- ✅ Routes se naložijo pravilno
- ✅ Middleware so kompatibilni z Laravel 11
- ✅ Config datoteke so posodobljene
- ✅ PHPUnit je posodobljen na verzijo 11

### Opombe
- `Kernel.php` še vedno obstaja, vendar se v Laravel 11 ne uporablja več (middleware se konfigurira v `bootstrap/app.php`)
- `RouteServiceProvider` je odstranjen iz providers (v Laravel 11 ni več potreben)
- CORS se upravlja preko `config/cors.php`, ne več preko middleware

## Breaking Changes v Laravel 11

### 1. RouteServiceProvider
- RouteServiceProvider ni več potreben
- Routes se konfigurirajo v `bootstrap/app.php`

### 2. HTTP Kernel
- Kernel.php ni več potreben
- Middleware se konfigurira v `bootstrap/app.php`

### 3. CORS
- CORS se upravlja preko `config/cors.php`, ne več preko middleware

### 4. Password Reset
- Priporočena uporaba `password_reset_tokens` namesto `password_resets`

### 5. PHPUnit
- PHPUnit 11 ima drugačno strukturo

## Opombe

- Vse spremembe so kompatibilne z Laravel 11
- Aplikacija bi morala delovati po `composer update`
- Če so kakšne napake, jih popravi glede na Laravel 11 dokumentacijo

## Pomoč

Če imaš težave:
1. Preveri Laravel 11 upgrade guide: https://laravel.com/docs/11.x/upgrade
2. Preveri breaking changes: https://laravel.com/docs/11.x/releases
3. Preveri, ali so vsi paketi kompatibilni z Laravel 11

