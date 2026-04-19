# PDF Legacy Format Implementation

**Datum:** 2025-12-20  
**Status:** ✅ Implementirano

## Povzetek

Posodobljen template za predlog kompenzacije, da ustreza legacy formatu iz leta 2012. Template zdaj sledi strukturi originalnih dokumentov KORENJAK Finančno svetovanje.

## Spremembe

### 1. Struktura Dokumenta

Template je bil popolnoma preoblikovan, da ustreza legacy formatu:

#### **Glava Dokumenta**
```
KORENJAK Finančno svetovanje
Matevž Korenjak s.p., Litostrojska cesta 12, 1000 Ljubljana
Email: matevz.korenjak@kompenzacije.eu
Telefon: 031 227 139, Faks: 08 288 00 77
SI98789309
```

#### **Številka Kompenzacije**
```
ŠT. KOMPENZACIJE: 1 / 2012
```

#### **Datum in Lokacija**
```
Ljubljana, 04.01.2012
```

#### **Glavni Naslov**
```
PREDLOG VERIŽNE KOMPENZACIJE V ZNESKU: 1.339,87 €
```

---

### 2. Vsebina Dokumenta

#### **A. Uvod**
Sklicevanje na zakonsko podlago:
> "Na osnovi **Zakona o obligacijskih razmerjih** podpisniki predloga verižne kompenzacije soglašamo, da se terjatve in obveznosti poravnajo na naslednji način:"

#### **B. Seznam Strank (Verižna Struktura)**

Stranke so prikazane v oštevilčenem seznamu z "dolguje" povezavo:

```
1. MATEVŽ KORENJAK S.P., Litostrojska cesta 12, 1000 Ljubljana
   matevz.korenjak@kompenzacije.eu
   dolguje:

2. INKOM D.O.O., Agrokombinatska cesta 4A, 1000 Ljubljana
   inkom@amis.net
   dolguje:

3. STEKLARNA ROGAŠKA D.O.O., Ulica talcev 1, 3250 Rogaska Slatina
   mira.kralj@stek-rogaska.si
   dolguje:

...
```

**Implementacija:**
- Avtomatsko številčenje z CSS counter
- Prikazan naziv podjetja, naslov, poštna številka, kraj
- Prikazan email (če obstaja)
- Beseda "dolguje:" med strankami (razen za zadnjo)

#### **C. Navodila za Potrditev**

```
Stranke naprošamo, da potrjen izvod pošljejo predlagatelju.
Kompenzacija se izvrši po prejetju vseh potrditev.

Datum izvršitve: ___________________________
```

**Implementacija:**
- Sivo ozadje z obrobo
- Poudarjena navodila
- Polje za vnos datuma izvršitve

#### **D. Izjava o Običajnem Načinu Plačila**

```
Dolžniki kompenzacije izjavljamo, da je predlagana kompenzacija običajen 
način plačila obveznosti in zato ne bomo uveljavljali morebitnih zahtev 
po Zakonu o finančnem poslovanju podjetij (ZFPP) in po Zakonu o prisilni 
poravnavi, stečaju in likvidaciji (ZPPSL).
```

**Implementacija:**
- Črna obroba (pomembno opozorilo)
- Sklicevanje na ZFPP in ZPPSL
- Poravnan tekst (justify)

#### **E. Podpisna Polja**

Podpisno polje za vsako stranko:
```
_________________________________
MATEVŽ KORENJAK S.P.
(podpis pooblaščene osebe)
```

**Implementacija:**
- Tabela s 2 stolpci (2 podpisa na vrstico)
- Črta za podpis
- Naziv podjetja
- Opomba "(podpis pooblaščene osebe)"

---

### 3. Stilizacija

#### **Tipografija**
- **Font:** DejaVu Sans (podpira slovenske znake)
- **Velikost:** 11px (osnovna), 12-16px (naslovi)
- **Barva:** Črna (#000) - klasičen izgled
- **Razmiki:** 1.6-1.8 line-height za berljivost

#### **Layout**
- **Padding:** 30px 40px (večji robovi)
- **Poravnava:** Center za glave, justify za besedilo
- **Ozadje:** Belo (brez barvnih okvirjev)

#### **Elementi**
- **Navodila:** Sivo ozadje (#f5f5f5) z obrobo
- **Izjava:** Belo ozadje s črno obrobo
- **Podpisi:** Črne črte, minimalistično

---

### 4. Primerjava: Stari vs. Novi Format

| Element | Stari Format (Moderniziran) | Novi Format (Legacy) |
|---------|----------------------------|----------------------|
| Barvna shema | Modra (#3498db) | Črno-bela |
| Tabele | Barvne glave, zebra vzorec | Oštevilčen seznam |
| Ozadja | Barvni okvirji | Minimalno (samo navodila) |
| Izračuni | V barvnih okvirjih | Odstranjeni (ločeni dokumenti) |
| Podpisi | 2 polja (generična) | Polje za vsako stranko |
| Struktura | Tabela strank | Verižna struktura (dolguje) |

---

### 5. Ključne Razlike

#### **Odstranjeno iz Novega Formata:**
- ❌ Tabela strank (zamenjana z verižno strukturo)
- ❌ Povzetek izvedbenega sporazuma
- ❌ Povzetek sporazuma o realizaciji
- ❌ Barvni okvirji in moderne barve
- ❌ Status predloga in referenčna številka

#### **Dodano v Novem Formatu:**
- ✅ Glava s podatki podjetja (KORENJAK)
- ✅ Verižna struktura strank z "dolguje"
- ✅ Sklicevanje na ZOR
- ✅ Navodila za potrditev
- ✅ Izjava o ZFPP in ZPPSL
- ✅ Podpisno polje za vsako stranko

---

### 6. Ločeni Dokumenti

Izvedbeni sporazum in sporazum o realizaciji ostajata **ločena PDF dokumenta** z moderniziranim dizajnom:

- `izvedbeni_sporazum_{id}_{year}.pdf` - Zelena barvna shema
- `sporazum_realizacija_{id}_{year}.pdf` - Rdeča barvna shema

**Razlog:** Legacy format predloga ne vključuje podrobnih izračunov, ti so v ločenih sporazumih.

---

### 7. Primer Uporabe

#### **Generiranje PDF-ja**

Ob dodajanju nove kompenzacije se avtomatsko generirajo 3 PDF-ji:

```
storage/app/
├── proposals/
│   └── predlog_kompenzacije_15_2025.pdf    ← Legacy format
├── agreements/
│   ├── implementation/
│   │   └── izvedbeni_sporazum_15_2025.pdf  ← Modernen format
│   └── realization/
│       └── sporazum_realizacija_15_2025.pdf ← Modernen format
```

#### **Testiranje**

```bash
# Dodaj novo kompenzacijo preko UI
# Ali testiraj z obstoječo:

php artisan tinker
```

```php
$c = App\Models\Compenzation::latest()->first();
event(new App\Services\Compenzations\Events\AddCompenzationEvent($c));
```

---

### 8. Pravne Podlage

Template se sklicuje na:

1. **Zakon o obligacijskih razmerjih (ZOR)**
   - Podlaga za kompenzacijo terjatev

2. **Zakon o finančnem poslovanju podjetij (ZFPP)**
   - Izjava, da se ne bo uveljavljalo zahtev

3. **Zakon o prisilni poravnavi, stečaju in likvidaciji (ZPPSL)**
   - Izjava, da se ne bo uveljavljalo zahtev

---

### 9. Naslednji Koraki

#### **Prilagoditve Podatkov Podjetja**

Trenutno so podatki podjetja hardcoded. Za produkcijo:

1. **Ustvari tabelo `company_settings`:**
```sql
CREATE TABLE company_settings (
    id BIGINT PRIMARY KEY,
    company_name VARCHAR(255),
    company_address VARCHAR(255),
    company_email VARCHAR(100),
    company_phone VARCHAR(50),
    company_fax VARCHAR(50),
    company_vat VARCHAR(20),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

2. **Posodobi template:**
```blade
<div class="company-header">
    <h2>{{ $settings->company_name }}</h2>
    <p>{{ $settings->company_address }}</p>
    <p>Email: {{ $settings->company_email }}</p>
    <p>Telefon: {{ $settings->company_phone }}, Faks: {{ $settings->company_fax }}</p>
    <p><strong>{{ $settings->company_vat }}</strong></p>
</div>
```

3. **Dodaj v listener:**
```php
$settings = CompanySetting::first();
$pdf = $this->pdfService->generateFromView(
    'pdfs.compenzation-proposal',
    [
        'compenzation' => $compenzation,
        'settings' => $settings
    ]
);
```

#### **Dodatne Izboljšave**

1. **Dinamični datum izvršitve** - izračun na podlagi rokov
2. **Verzioniranje dokumentov** - sledenje spremembam
3. **Email template** za pošiljanje potrditev
4. **Sledenje potrditvam** - kdo je že potrdil

---

### 10. Opombe

- Template uporablja legacy format samo za **predlog kompenzacije**
- Izvedbeni in realizacijski sporazum ostajata v modernem formatu
- Vsi PDF-ji se še vedno generirajo avtomatsko
- Font DejaVu Sans podpira vse slovenske znake
- Format je prilagojen za tiskanje na A4 papir

---

## Reference

- **Legacy PDF:** `c:\Users\User\Documents\Kompenzacije\predlog_kompenzacije\kompenzacija1_2012.pdf`
- **Nov template:** `resources/views/pdfs/compenzation-proposal.blade.php`
- **Listener:** `app/Listeners/GenerateCompenzationProposalPdf.php`

