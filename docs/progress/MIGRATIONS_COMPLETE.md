# ✅ Migracije Uspešno Zaključene

## Status

**Vse migracije so zažene uspešno!**

### Zažene Migracije

1. ✅ `2014_10_12_000000_create_users_table`
2. ✅ `2014_10_12_100000_create_password_resets_table`
3. ✅ `2019_08_19_000000_create_failed_jobs_table`
4. ✅ `2019_12_14_000001_create_personal_access_tokens_table`
5. ✅ `2022_03_22_103216_create_entities_table`
6. ✅ `2022_04_12_084759_create_compenzations_table`
7. ✅ `2024_11_29_144204_create_compenzations_entity_table`
8. ✅ `2024_12_03_083706_create_compenzations_proposals_table`
9. ✅ `2024_12_03_084132_create_implementation_agreement_table`
10. ✅ `2024_12_03_084203_create_realization_agreement_table`
11. ✅ `2024_12_03_102228_create_bills_table`
12. ✅ `2024_12_03_102314_create_bills_compenzations_table`
13. ✅ `2025_11_30_104758_add_commission_to_compenzations_table`
14. ✅ `2025_11_30_104800_modify_amount_precision_in_compenzations_table` (Popravljeno - uporablja raw SQL)
15. ✅ `2025_11_30_105824_create_post_numbers_table`

### Seedere

- ✅ `PostNumberSeeder` - Uspešno zažene (osnovne poštne številke dodane)

## Popravljene Napake

### 1. Doctrine DBAL Kompatibilnost

**Problem:** Laravel 8 zahteva Doctrine DBAL za spreminjanje stolpcev, vendar obstaja nekompatibilnost z verzijami.

**Rešitev:** Spremenjena migracija `modify_amount_precision_in_compenzations_table.php`, da uporablja raw SQL ukaze namesto `->change()` metode:

```php
DB::statement('ALTER TABLE compenzations MODIFY amount DECIMAL(10, 2) NOT NULL');
```

To omogoča spreminjanje stolpcev brez Doctrine DBAL paketa.

## Struktura Baze Podatkov

### Ustvarjene Tabele

- `users` - Uporabniki
- `password_resets` - Resetiranje gesel
- `failed_jobs` - Neuspešni jobi
- `personal_access_tokens` - API tokeni
- `entities` - Stranke/Entitete (Clients)
- `compenzations` - Kompenzacije
- `compenzations_entity` - Pivot tabela (kompenzacije ↔ entitete)
- `compenzations_proposals` - Predlogi kompenzacij
- `implementation_agreement` - Izvedbeni sporazumi
- `realization_agreement` - Sporazumi o realizaciji
- `bills` - Računi
- `bills_compenzations` - Pivot tabela (računi ↔ kompenzacije)
- `post_numbers` - Poštne številke

## Naslednji Koraki

### 1. Preveri Strukturo Baze

```bash
php artisan migrate:status
```

### 2. Testiraj Funkcionalnosti

```bash
# Testiraj povezavo
php artisan tinker
>>> \App\Models\PostNumber::count()

# Testiraj relacije
>>> \App\Models\Entity::first()
```

### 3. Zaženi Development Server

```bash
php artisan serve
# V drugem terminalu:
npm run dev
```

### 4. Razširi Post Numbers Seeder

Če potrebuješ vse slovenske poštne številke, dodaj podatke iz legacy sistema v `PostNumberSeeder`.

## Opombe

- Doctrine DBAL ni nameščen (ni potreben zaradi raw SQL pristopa)
- Vse tabele so ustvarjene z ustreznimi relacijami
- Post numbers seeder vsebuje osnovne podatke
- `amount` stolpec je sedaj `DECIMAL(10, 2)` namesto `DECIMAL(10, 4)`

---
*Zaključeno: 2025-11-30*

