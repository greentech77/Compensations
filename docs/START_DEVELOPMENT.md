# 🚀 Vodič za Začetek Razvoja - Kompenzacije

## 📋 Trenutno Stanje Projekta

Projekt že obstaja z:
- ✅ Laravel 8 + Inertia.js
- ✅ Migracije (entities, compenzations, bills, agreements)
- ✅ Modeli (Entity, Compenzation, Bill, itd.)
- ✅ Kontrolerji
- ✅ Vue komponente
- ⚠️ **ODVISNOSTI NISO NAMESTLJENE** (vendor folder manjka)
- ⚠️ **.env datoteka ne obstaja**

## 🎯 Korak 1: Nastavitev Okolja

### 1.1 Namestitev Odvisnosti

```bash
cd ~/www/compenzations

# Namesti PHP odvisnosti
composer install

# Namesti npm odvisnosti
npm install
```

### 1.2 Konfiguracija Okolja

```bash
# Ustvari .env datoteko
cp .env.example .env

# Generiraj aplikacijski ključ
php artisan key:generate
```

### 1.3 Nastavitev Baze Podatkov

Uredi `.env` datoteko:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kompenzacije_app
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**POMEMBNO:** 
- Če migriraš iz legacy sistema, uporabi isto bazo ali ustvari novo
- Preveri, ali legacy baza `kompenza_kompenzacije` že obstaja

### 1.4 Zaženi Migracije

```bash
# Preveri status migracij
php artisan migrate:status

# Če je baza prazna, zaženi migracije
php artisan migrate

# Če že obstajajo podatki v legacy bazi, najprej migriraj podatke (glej korak 2)
```

## 🔍 Korak 2: Analiza Trenutnega Stanja

### 2.1 Preveri Obstoječe Migracije

```bash
ls -la database/migrations/
```

**Obstoječe migracije:**
- ✅ `create_users_table.php`
- ✅ `create_entities_table.php` (to je Client/Stranka)
- ✅ `create_compenzations_table.php`
- ✅ `create_compenzations_entity_table.php` (pivot table)
- ✅ `create_compenzations_proposals_table.php`
- ✅ `create_implementation_agreement_table.php`
- ✅ `create_realization_agreement_table.php`
- ✅ `create_bills_table.php`
- ✅ `create_bills_compenzations_table.php`

### 2.2 Preveri Modele in Relacije

```bash
ls -la app/Models/
```

**Obstoječi modeli:**
- `Entity.php` (to je Client/Stranka)
- `Compenzation.php`
- `BillModel.php`
- `CompenzationEntity.php` (pivot)
- `CompenzationProposal.php`
- `ImplementationAgreement.php`
- `RealizationAgreement.php`

### 2.3 Preveri Kontrolerje

```bash
ls -la app/Http/Controllers/
```

**Obstoječi kontrolerji:**
- `Compenzation/CompenzationController.php`
- `User/UserController.php`
- `Auth/` kontrolerji

### 2.4 Preveri Vue Komponente

```bash
ls -la resources/js/Pages/
ls -la resources/js/Components/
```

**Obstoječe strani:**
- `Dashboard.vue`
- `Entities.vue` (seznam strank)
- `Entity.vue` (posamezna stranka)
- `RegisterEntity.vue`
- `Compenzations.vue`
- `Compenzation.vue`
- `AddCompenzation.vue`
- Auth strani (Login, Register, itd.)

## 🔄 Korak 3: Primerjava z Legacy Strukturo

### 3.1 Mapiranje Tabel

| Legacy tabela | Nova tabela | Status |
|--------------|-------------|--------|
| `clients` | `entities` | ✅ Implementirano |
| `compenzations` | `compenzations` | ✅ Implementirano |
| `compenzations_clients` | `compenzations_entity` | ✅ Implementirano |
| `bills` | `bills` | ✅ Implementirano |
| `bills_compenzations` | `bills_compenzations` | ✅ Implementirano |
| `compensation_proposal` | `compenzations_proposals` | ✅ Implementirano |
| `implementation_agreement` | `implementation_agreement` | ✅ Implementirano |
| `realization_agreement` | `realization_agreement` | ✅ Implementirano |
| `post_numbers` | ❌ Manjka | ⚠️ Potrebno dodati |

### 3.2 Preveri Razlike v Strukturi

**Entity (Client) model:**
- Legacy: `postal_code` + `post` (ločeno)
- Nova: `post_num` + `post_town` (ločeno) ✅
- Legacy: `tax_no` → Nova: `vat_num` ✅
- Legacy: `reg_no` → Nova: `registration_num` ✅
- Nova ima dodatna polja: `bank_account`, `bank_bic`, `bank_name` ✅

**Compenzation model:**
- Legacy: `amount`, `ddv` kot `varchar(10)` → Nova: `unsignedDecimal(10, 4)` ✅ (vendar bi moral biti 2 decimalni mesti, ne 4)
- Legacy: `commission` kot `varchar(4)` → Nova: ❌ **MANJKA** - moraš dodati!
- Legacy: `year` kot `varchar(4)` → Nova: `integer(4)` ✅
- Legacy: `vat`/`ddv` kot `varchar(10)` → Nova: `integer(2)` ✅
- ⚠️ **OPOZORILO:** V migraciji `entities` tabela ima pravopisno napako: `dafault` namesto `default` (vrstica 33-34)

## 📝 Korak 4: Kaj Potrebuješ Narediti?

### 4.1 Takojšnje Naloge

1. **Namesti odvisnosti**
   ```bash
   composer install
   npm install
   ```

2. **Nastavi .env datoteko**
   - Kopiraj `.env.example` v `.env`
   - Nastavi bazo podatkov
   - Generiraj aplikacijski ključ

3. **Preveri migracije**
   ```bash
   php artisan migrate:status
   ```
   - Če so že zažene, super!
   - Če ne, jih zaženi: `php artisan migrate`

4. **Preveri in popravi tip podatkov v migracijah**
   
   **OPAŽENE NAPAKE:**
   
   a) **Compenzations tabela:**
   - `amount` je `unsignedDecimal(10, 4)` - spremeni v `decimal(10, 2)` (2 decimalni mesti so dovolj)
   - `commission` polje **MANJKA** - dodaj kot `decimal(10, 2)`
   - Potrebna je nova migracija za popravek
   
   b) **Entities tabela:**
   - Pravopisna napaka: `dafault` namesto `default` (vrstica 33-34)
   - Potrebna je nova migracija za popravek
   
   ```bash
   # Ustvari migracijo za popravke
   php artisan make:migration fix_compenzations_table_add_commission
   php artisan make:migration fix_entities_table_typo
   ```

### 4.2 Manjkajoče Funkcionalnosti

1. **Poštne številke (post_numbers)**
   - Ustvari migracijo za `post_numbers` tabelo
   - Ustvari `PostNumber` model
   - Ustvari seeder z podatki
   - Dodaj autocomplete za poštne številke v formi

2. **Izvoz računov (Export Bills)**
   - Preveri, ali že obstaja funkcionalnost
   - Če ne, implementiraj export (XML/CSV)
   - Glej routes - ni export route definiran

3. **PDF Generacija**
   - Preveri, ali obstaja funkcionalnost za generiranje PDF-jev
   - Preveri `CompensationProposal` model
   - Implementiraj generiranje PDF-jev če manjka

4. **Migracija podatkov iz Legacy**
   - Če legacy baza še obstaja, migriraj podatke
   - Ustvari seederje za migracijo
   - Glej `.cursorrules-compenzations` za primere seederjev

### 4.3 Izbrušiti in Popraviti

1. **Preveri tip podatkov**
   - Zneski morajo biti `decimal(10,2)`, ne `varchar`
   - Leta morajo biti pravilno definirana
   - Boolean polja morajo biti `boolean`, ne `tinyint`

2. **Dodaj manjkajoče relacije**
   - Preveri, ali so vse relacije definirane v modelih
   - Dodaj `PostNumber` relacijo v `Entity` model

3. **Testiranje**
   - Preveri, ali vse funkcionalnosti delujejo
   - Testiraj CRUD operacije
   - Testiraj avtentikacijo

## 🛠️ Korak 5: Začetek Razvoja

### 5.1 Hitri Začetek (Če vse že deluje)

```bash
cd ~/www/compenzations

# Namesti odvisnosti (če še nisi)
composer install
npm install

# Nastavi .env
cp .env.example .env
php artisan key:generate

# Zaženi migracije
php artisan migrate

# Zaženi development server
php artisan serve
# V drugem terminalu:
npm run dev
```

### 5.2 Prijavljene Napake

Če se pojavijo napake:

1. **Composer napake:**
   ```bash
   composer install --ignore-platform-reqs
   ```

2. **NPM napake:**
   ```bash
   npm install --legacy-peer-deps
   ```

3. **Migracijske napake:**
   ```bash
   php artisan migrate:fresh  # POZOR: To zbriše vse podatke!
   ```

### 5.3 Razvojni Workflow

1. **Začni z analizo:**
   ```bash
   # Preveri status
   php artisan route:list
   php artisan migrate:status
   ```

2. **Dodaj novo funkcionalnost:**
   - Ustvari migracijo (če je potrebno)
   - Ustvari/posodobi model
   - Ustvari kontroler
   - Ustvari Vue komponento
   - Dodaj route

3. **Testiraj:**
   ```bash
   php artisan test
   ```

## 📚 Koristni Povezave

- **Pravila za razvoj:** `.cursor/rules/.cursorrules-compenzations`
- **Legacy struktura:** Glej v `.cursorrules-compenzations`
- **Laravel dokumentacija:** https://laravel.com/docs/8.x
- **Inertia.js dokumentacija:** https://inertiajs.com/

## 🎯 Naslednji Koraki

1. Namesti odvisnosti ✅ (prvi korak)
2. Nastavi .env ✅
3. Preveri migracije ✅
4. Identificiraj manjkajoče funkcionalnosti
5. Implementiraj poštne številke
6. Implementiraj export funkcionalnost
7. Testiraj vse funkcionalnosti

---

**Pomembno:** Vedno preveri obstoječo kodo pred dodajanjem novih funkcionalnosti!

