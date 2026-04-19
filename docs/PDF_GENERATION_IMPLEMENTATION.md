# PDF Generation Implementation

**Datum:** 2025-01-29  
**Status:** ✅ Implementirano

## Povzetek

Implementirana avtomatska generacija PDF dokumenta za predlog kompenzacije ob zaključku vnosa.

## Implementirane Komponente

### 1. Listener - GenerateCompenzationProposalPdf
**Datoteka:** `app/Listeners/GenerateCompenzationProposalPdf.php`

**Funkcionalnosti:**
- Posluša `AddCompenzationEvent` event
- Generira PDF dokument iz Blade view
- Shrani PDF v Laravel Storage (`storage/app/proposals/`)
- Posodobi `CompenzationProposal` z `file_path` in `file_name`
- Logira uspešno generiranje ali napake

**Ime datoteke:** `kompenzacija{id}_{year}.pdf`
**Primer:** `kompenzacija001_2024.pdf`

### 2. PDF View Template
**Datoteka:** `resources/views/pdfs/compenzation-proposal.blade.php`

**Vsebina PDF-ja:**
- Glava z naslovom "PREDLOG KOMPENZACIJE"
- Osnovni podatki kompenzacije (datum, leto, znesek)
- Seznam strank (entities) s podatki
- Izvedbeni sporazum (implementation agreement) z zneski
- Sporazum o realizaciji (realization agreement) z zneski
- Footer z datumom generiranja

### 3. Migracija - Dodana Polja
**Datoteka:** `database/migrations/2025_12_12_194429_add_file_path_to_compenzations_proposals_table.php`

Dodana polja:
- `file_path` - pot do PDF datoteke v Storage
- `file_name` - ime PDF datoteke

### 4. Model - CompenzationProposal
**Posodobljeno:** `app/Models/CompenzationProposal.php`

- Dodano v `$fillable`: `file_path`, `file_name`

### 5. Model - Compenzation
**Posodobljeno:** `app/Models/Compenzation.php`

- Dodana relacija `proposal()` - hasOne na CompenzationProposal

### 6. Event Service Provider
**Posodobljeno:** `app/Providers/EventServiceProvider.php`

- Registriran listener `GenerateCompenzationProposalPdf` za event `AddCompenzationEvent`

## Kako Deluje

1. **Uporabnik zaključi vnos kompenzacije**
   - Klikne "Zaključi vnos" v formi
   - `CompenzationController@postCompenzation` se izvede

2. **CompenzationService ustvari kompenzacijo**
   - Ustvari zapis v `compenzations` tabeli
   - Ustvari zapis v `compenzations_proposals` tabeli
   - Ustvari zapise v `implementation_agreement` in `realization_agreement`

3. **Event se sproži**
   - `AddCompenzationEvent::dispatch($compenzation)` se izvede

4. **Listener generira PDF**
   - `GenerateCompenzationProposalPdf` listener se izvede
   - Naloži vse relacije kompenzacije
   - Generira PDF iz Blade view
   - Shrani PDF v `storage/app/proposals/`
   - Posodobi `CompenzationProposal` z `file_path` in `file_name`

## Struktura PDF Dokumenta

### Vsebina:
1. **Glava**
   - Naslov: "PREDLOG KOMPENZACIJE"
   - Ime kompenzacije (npr. "Kompenzacija-0001/2024")

2. **Osnovni Podatki**
   - Datum
   - Leto
   - Znesek

3. **Stranke** (če obstajajo)
   - Tabela z:
     - Zaporedna številka
     - Ime podjetja
     - Naslov
     - Poštna številka
     - Kraj

4. **Izvedbeni Sporazum** (če obstaja)
   - Diskont (%)
   - Znesek diskonta
   - Neto znesek
   - Znesek za nakazilo

5. **Sporazum o Realizaciji** (če obstaja)
   - Provizija (%)
   - Znesek provizije
   - Znesek za nakazilo

6. **Footer**
   - Datum in čas generiranja

## Shranjevanje

- **Lokacija:** `storage/app/proposals/`
- **Ime datoteke:** `kompenzacija{id}_{year}.pdf`
- **Primer:** `kompenzacija1_2024.pdf`

## Naslednji Koraki

1. **Zaženi migracijo:**
   ```bash
   php artisan migrate
   ```

2. **Testiraj generiranje:**
   - Ustvari novo kompenzacijo
   - Preveri, ali se PDF generira
   - Preveri, ali je PDF shranjen v Storage
   - Preveri, ali je `file_path` posodobljen v bazi

3. **Preveri Storage:**
   ```bash
   ls -la storage/app/proposals/
   ```

## Opombe

- PDF se generira **avtomatsko** ob zaključku vnosa kompenzacije
- Če generiranje ne uspe, se napaka zabeleži v log, vendar ne prekine procesa
- PDF uporablja mPDF knjižnico (že nameščeno)
- PDF podpira slovenske znake (UTF-8)
- Font: DejaVu Sans (vgrajen v mPDF, podpira slovenske znake)

## Odpravljanje Težav

### PDF-ji se ne generirajo
1. Preveri log datoteko: `storage/logs/laravel.log`
2. Preveri, ali obstaja mapa: `storage/app/proposals/`
3. Preveri dovoljenja za pisanje v `storage/app/proposals/`

### Napaka: "Cannot find TTF TrueType font file"
- **Rešitev**: Posodobljen PDFService uporablja DejaVu Sans font, ki je že vključen v mPDF
- Če še vedno uporablja Roboto font, preveri, da je `PDFService.php` posodobljen

