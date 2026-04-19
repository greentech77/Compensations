# PDF Templates Enhancement

**Datum:** 2025-12-20  
**Status:** ✅ Implementirano

## Povzetek

Posodobljen sistem generiranja PDF dokumentov za kompenzacije. Zdaj se ob dodajanju nove kompenzacije avtomatsko generirajo **3 PDF dokumenti**:
1. Predlog kompenzacije
2. Izvedbeni sporazum
3. Sporazum o realizaciji

## Implementirane Spremembe

### 1. Posodobljen Template: Predlog Kompenzacije

**Datoteka:** `resources/views/pdfs/compenzation-proposal.blade.php`

**Izboljšave:**
- ✅ Moderniziran dizajn z barvnimi akcenti
- ✅ Dodana referenčna številka (če obstaja)
- ✅ Prikazan status predloga
- ✅ Dodana davčna številka strank v tabeli
- ✅ Izboljšan prikaz izračunov (v barvnih okvirjih)
- ✅ Dodano polje za podpis
- ✅ Boljša tipografija in razmiki

**Nova polja:**
- Referenčna številka predloga
- Status predloga (osnutek, v obravnavi, odobren, zavrnjen, preklican)
- Davčna številka strank
- Podpisna polja

---

### 2. Nov Template: Izvedbeni Sporazum

**Datoteka:** `resources/views/pdfs/implementation-agreement.blade.php`

**Vsebina:**
- **Glava:** Naslov "IZVEDBENI SPORAZUM" z zeleno barvo
- **Osnovni podatki:**
  - Datum sporazuma
  - Veljavnost (od-do)
  - Status sporazuma
  - Podpisnik
  
- **Tabela strank:** Vse udeležene stranke s podatki
- **Izračun diskonta:**
  - Osnova za izračun
  - Diskont (%)
  - Z/brez DDV
  - Neto diskont
  - Neto znesek
  - **Znesek za nakazilo** (poudarjen)

- **Pogoji sporazuma:**
  - Predmet sporazuma
  - Diskont
  - Način plačila
  - Veljavnost
  - Opombe (če obstajajo)

- **Podpisna polja:** Za obe strani (izvajalec, naročnik)

**Ime datoteke:** `izvedbeni_sporazum_{id}_{year}.pdf`  
**Lokacija:** `storage/app/agreements/implementation/`

---

### 3. Nov Template: Sporazum o Realizaciji

**Datoteka:** `resources/views/pdfs/realization-agreement.blade.php`

**Vsebina:**
- **Glava:** Naslov "SPORAZUM O REALIZACIJI" z rdečo barvo
- **Osnovni podatki:**
  - Datum sporazuma
  - Veljavnost (od-do)
  - Status sporazuma
  - Podpisnik

- **Tabela strank:** Vse udeležene stranke s podatki
- **Izračun provizije:**
  - Osnova za izračun
  - Provizija (%)
  - DDV na provizijo (22%)
  - **Znesek za izplačilo** (poudarjen)

- **Informacije o plačilu:**
  - Status plačila (čaka, plačano, delno, preklicano)
  - Datum plačila
  - Barvno označen status

- **Pogoji sporazuma:**
  - Predmet sporazuma
  - Provizija
  - Način izplačila
  - Pogoji izplačila
  - Veljavnost
  - Opombe (če obstajajo)

- **Podpisna polja:** Za obe strani (izvajalec, naročnik)

**Ime datoteke:** `sporazum_realizacija_{id}_{year}.pdf`  
**Lokacija:** `storage/app/agreements/realization/`

---

## Posodobljen Listener

**Datoteka:** `app/Listeners/GenerateCompenzationProposalPdf.php`

**Spremembe:**
- ✅ Razdeljena logika na 3 metode:
  - `generateProposalPdf()` - Predlog kompenzacije
  - `generateImplementationAgreementPdf()` - Izvedbeni sporazum
  - `generateRealizationAgreementPdf()` - Sporazum o realizaciji

- ✅ Vsi PDF-ji se generirajo avtomatsko ob dodajanju kompenzacije
- ✅ Izboljšano logiranje za vsak PDF posebej
- ✅ Ločene lokacije shranjevanja

**Nova imena datotek:**
- `predlog_kompenzacije_{id}_{year}.pdf` (prej: `kompenzacija{id}_{year}.pdf`)
- `izvedbeni_sporazum_{id}_{year}.pdf` (novo)
- `sporazum_realizacija_{id}_{year}.pdf` (novo)

---

## Struktura Shranjevanja

```
storage/app/
├── proposals/                          # Predlogi kompenzacij
│   └── predlog_kompenzacije_14_2025.pdf
├── agreements/
│   ├── implementation/                 # Izvedbeni sporazumi
│   │   └── izvedbeni_sporazum_14_2025.pdf
│   └── realization/                    # Sporazumi o realizaciji
│       └── sporazum_realizacija_14_2025.pdf
```

---

## Dizajn in Stilizacija

### Barvna Shema

| Dokument | Glavna Barva | Uporaba |
|----------|--------------|---------|
| Predlog kompenzacije | Modra (#3498db) | Naslovi, tabele, poudarki |
| Izvedbeni sporazum | Zelena (#27ae60) | Naslovi, tabele, poudarki |
| Sporazum o realizaciji | Rdeča (#e74c3c) | Naslovi, tabele, poudarki |

### Skupne Značilnosti

- **Font:** DejaVu Sans (podpira slovenske znake)
- **Velikost pisave:** 11px (osnovna), 26px (naslovi)
- **Razmiki:** Konsistentni med vsemi dokumenti
- **Tabele:** Barvne glave, zebra vzorec
- **Izračuni:** V barvnih okvirjih z mejami
- **Podpisi:** Standardizirana polja za obe strani

---

## Kako Deluje

### 1. Ob Dodajanju Kompenzacije

Ko uporabnik klikne "Zaključi vnos":

```
CompenzationController@postCompenzation
    ↓
CompenzationService->addCompenzation()
    ↓
AddCompenzationEvent::dispatch()
    ↓
GenerateCompenzationProposalPdf->handle()
    ↓
├─ generateProposalPdf()              → predlog_kompenzacije_X_YYYY.pdf
├─ generateImplementationAgreementPdf() → izvedbeni_sporazum_X_YYYY.pdf
└─ generateRealizationAgreementPdf()   → sporazum_realizacija_X_YYYY.pdf
```

### 2. Shranjevanje

- **Predlog:** `storage/app/proposals/`
- **Izvedbeni sporazum:** `storage/app/agreements/implementation/`
- **Sporazum o realizaciji:** `storage/app/agreements/realization/`

### 3. Posodobitev Baze

- `compenzations_proposals.file_path` se posodobi s potjo do predloga
- `compenzations_proposals.file_name` se posodobi z imenom datoteke

---

## Primeri Uporabe

### Generiranje PDF-jev za Obstoječo Kompenzacijo

```bash
php artisan tinker
```

```php
$compenzation = App\Models\Compenzation::find(14);
event(new App\Services\Compenzations\Events\AddCompenzationEvent($compenzation));
```

### Preverjanje Generiranih PDF-jev

```bash
# Predlogi
ls -la storage/app/proposals/

# Izvedbeni sporazumi
ls -la storage/app/agreements/implementation/

# Sporazumi o realizaciji
ls -la storage/app/agreements/realization/
```

---

## Naslednji Koraki

### Frontend Implementacija

1. **Download gumbi** za vse 3 PDF-je v prikazu kompenzacije
2. **Preview funkcionalnost** za ogled PDF-jev v brskalniku
3. **Email pošiljanje** PDF-jev strankam
4. **Zgodovina PDF-jev** - sledenje verzijam

### Backend Implementacija

1. **Verzioniranje PDF-jev** - shranjevanje različnih verzij
2. **Digitalni podpis** PDF-jev
3. **Watermark** za osnutke
4. **Združevanje PDF-jev** v en dokument

### Dodatne Izboljšave

1. **Logotip podjetja** v glavi dokumenta
2. **QR koda** za verifikacijo dokumenta
3. **Številčenje strani** za daljše dokumente
4. **Prilagodljivi template-i** glede na tip kompenzacije

---

## Testiranje

### Preveri Generiranje

1. Dodaj novo kompenzacijo preko UI
2. Preveri, ali so vsi 3 PDF-ji generirani
3. Odpri PDF-je in preveri vsebino
4. Preveri, ali so vsi podatki pravilno prikazani

### Preveri Logiranje

```bash
tail -f storage/logs/laravel.log | grep "PDF generated"
```

Pričakovani output:
```
[INFO] Proposal PDF generated: proposals/predlog_kompenzacije_X_YYYY.pdf
[INFO] Implementation Agreement PDF generated: agreements/implementation/izvedbeni_sporazum_X_YYYY.pdf
[INFO] Realization Agreement PDF generated: agreements/realization/sporazum_realizacija_X_YYYY.pdf
[INFO] All PDFs generated successfully for compenzation X
```

---

## Odpravljanje Težav

### PDF-ji se ne generirajo

1. Preveri log: `storage/logs/laravel.log`
2. Preveri dovoljenja: `chmod -R 775 storage/app/agreements`
3. Preveri, ali obstajajo direktoriji
4. Preveri, ali je mPDF nameščen: `composer show mpdf/mpdf`

### Napake v PDF-jih

1. Preveri, ali so vsi podatki v bazi
2. Preveri relacije v modelih
3. Preveri Blade syntax v template-ih
4. Testiraj z `php artisan tinker`

### Manjkajoči Podatki

1. Preveri, ali so migracije zagnane
2. Preveri, ali so nova polja v `$fillable` arrayih
3. Preveri, ali se podatki pravilno shranjujejo

---

## Opombe

- Vsi PDF-ji uporabljajo DejaVu Sans font (vgrajen v mPDF)
- PDF-ji podpirajo slovenske znake (UTF-8)
- Generiranje ne prekine procesa, če pride do napake
- Napake se logirajo v `storage/logs/laravel.log`
- PDF-ji se generirajo sinhron (ne v queue)

