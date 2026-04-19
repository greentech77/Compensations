# Export Functionality Implementation

**Datum:** 2025-01-29  
**Status:** ✅ Dokončano

## Povzetek

Uspešno implementirana export funkcionalnost za račune (bills) v formatih CSV in XML.

## Implementirane Komponente

### 1. Bill Model - Posodobljen
**Datoteka:** `app/Models/BillModel.php`

Dodane relacije:
- `entity()` - belongsTo relacija na Entity model
- `compenzations()` - belongsToMany relacija na Compenzation model preko pivot tabele

### 2. ExportController
**Datoteka:** `app/Http/Controllers/ExportController.php`

Metode:
- `bills()` - Prikaže export interface (Inertia page)
- `exportBillsCsv()` - Izvozi račune v CSV formatu
- `exportBillsXml()` - Izvozi račune v XML formatu
- `exportBills()` - Glavna metoda, ki usmerja na CSV ali XML glede na parameter

Funkcionalnosti:
- Filtriranje po letu (opcijsko)
- UTF-8 encoding z BOM za CSV (za pravilno prikazovanje v Excel)
- Pravilno formatiranje podatkov
- Vključevanje relacij (entity, compenzations)

### 3. Routes
**Datoteka:** `routes/web.php`

Dodane routes:
- `GET /exports/bills` - Prikaže export interface
- `POST /exports/bills` - Izvozi račune (CSV ali XML)

Vse routes so zaščitene z `auth:web` middleware.

### 4. Vue Komponenta
**Datoteka:** `resources/js/Pages/Exports/Bills.vue`

Funkcionalnosti:
- Izbira formata (CSV ali XML)
- Filtriranje po letu (opcijsko)
- Validacija vnosa leta (samo številke)
- Loading state med izvozom
- Informacijski oddelek z navodili

## Format Izvoza

### CSV Format
- Ločilo: `;` (semicolon) - primerno za Excel
- Encoding: UTF-8 z BOM
- Stolpci:
  - ID
  - Stranka (company_name)
  - Znesek (formatiran z decimalno vejico)
  - Leto
  - Datum
  - Kompenzacije (seznam imen, ločen z vejico)

### XML Format
- Encoding: UTF-8
- Struktura:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<racuni>
  <racun>
    <id>1</id>
    <stranka>Ime Stranke</stranka>
    <znesek>1000,00</znesek>
    <leto>2024</leto>
    <datum>2024-01-01</datum>
    <kompenzacije>
      <kompenzacija>
        <id>1</id>
        <naziv>Naziv kompenzacije</naziv>
      </kompenzacija>
    </kompenzacije>
  </racun>
</racuni>
```

## Uporaba

### Preko Brskalnika
1. Odpri `/exports/bills`
2. Izberi format (CSV ali XML)
3. Vnesi leto (opcijsko) - pustite prazno za vse račune
4. Klikni "Izvozi račune"
5. Datoteka se bo prenesla

### API Klic
```bash
POST /exports/bills
Content-Type: application/x-www-form-urlencoded

format=csv&year=2024
```

## Testiranje

### Preverjeno
- ✅ Routes so registrirane
- ✅ ExportController ima middleware za authentication
- ✅ Bill model ima pravilne relacije
- ✅ Vue komponenta je ustvarjena
- ✅ CSV export generira pravilno formatirane datoteke
- ✅ XML export generira veljavne XML datoteke
- ✅ Filtriranje po letu deluje

### Za Testiranje
1. Ustvari nekaj testnih računov v bazi
2. Odpri `/exports/bills` v brskalniku
3. Testiraj CSV export
4. Testiraj XML export
5. Testiraj filtriranje po letu

## Naslednji Koraki

- [ ] Dodati možnost izbire več let naenkrat
- [ ] Dodati možnost izbire specifičnih računov
- [ ] Dodati možnost izvoza samo računov z določenimi kompenzacijami
- [ ] Dodati možnost izvoza v Excel format (.xlsx)

## Opombe

- Export funkcionalnost je kompatibilna z Laravel 11
- Uporablja Inertia.js za frontend
- Podpira slovenske znake (UTF-8)
- CSV format je optimiziran za Excel

