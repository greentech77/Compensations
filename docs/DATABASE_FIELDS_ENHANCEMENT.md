# Database Fields Enhancement

**Datum:** 2025-12-20  
**Status:** ✅ Implementirano

## Povzetek

Dodana dodatna polja v tabele `compenzations_proposals`, `implementation_agreement` in `realization_agreement` za boljše sledenje poslovnim procesom in statusom.

## Implementirane Spremembe

### 1. Tabela: `compenzations_proposals`

**Dodana polja:**

| Polje | Tip | Nullable | Default | Namen |
|-------|-----|----------|---------|-------|
| `status` | enum | Ne | 'draft' | Status predloga (draft, pending, approved, rejected, cancelled) |
| `sent_date` | date | Da | NULL | Datum pošiljanja predloga |
| `response_date` | date | Da | NULL | Datum odobritve/zavrnitve |
| `notes` | text | Da | NULL | Opombe/razlog zavrnitve |
| `approved_by` | unsignedBigInteger | Da | NULL | FK na users - kdo je odobril/zavrnil |

**Relacije:**
- `approved_by` → Foreign key na `users.id` (onDelete: set null)

**Model posodobitve:**
- Dodano v `$fillable`: vsa nova polja
- Dodano v `$casts`: `sent_date`, `response_date` kot 'date'
- Nova relacija: `approvedBy()` → belongsTo(User::class)

---

### 2. Tabela: `implementation_agreement`

**Dodana polja:**

| Polje | Tip | Nullable | Default | Namen |
|-------|-----|----------|---------|-------|
| `signed_date` | date | Da | NULL | Datum podpisa sporazuma |
| `valid_from` | date | Da | NULL | Datum začetka veljavnosti |
| `valid_until` | date | Da | NULL | Datum konca veljavnosti |
| `status` | enum | Ne | 'draft' | Status sporazuma (draft, active, completed, cancelled) |
| `reference_number` | varchar(50) | Da | NULL | Referenčna številka sporazuma |
| `notes` | text | Da | NULL | Opombe |
| `signed_by` | varchar(100) | Da | NULL | Kdo je podpisal sporazum |

**Model posodobitve:**
- Dodano v `$fillable`: vsa nova polja
- Dodano v `$casts`: `signed_date`, `valid_from`, `valid_until` kot 'date'

---

### 3. Tabela: `realization_agreement`

**Dodana polja:**

| Polje | Tip | Nullable | Default | Namen |
|-------|-----|----------|---------|-------|
| `signed_date` | date | Da | NULL | Datum podpisa sporazuma |
| `valid_from` | date | Da | NULL | Datum začetka veljavnosti |
| `valid_until` | date | Da | NULL | Datum konca veljavnosti |
| `status` | enum | Ne | 'draft' | Status sporazuma (draft, active, completed, cancelled) |
| `reference_number` | varchar(50) | Da | NULL | Referenčna številka sporazuma |
| `payment_date` | date | Da | NULL | Datum izplačila provizije |
| `payment_status` | enum | Ne | 'pending' | Status plačila (pending, paid, partial, cancelled) |
| `notes` | text | Da | NULL | Opombe |
| `signed_by` | varchar(100) | Da | NULL | Kdo je podpisal sporazum |

**Model posodobitve:**
- Dodano v `$fillable`: vsa nova polja
- Dodano v `$casts`: `signed_date`, `valid_from`, `valid_until`, `payment_date` kot 'date'

---

## Migracije

### Ustvarjene migracije:

1. `2025_12_20_224508_add_additional_fields_to_compenzations_proposals_table.php`
2. `2025_12_20_224517_add_additional_fields_to_implementation_agreement_table.php`
3. `2025_12_20_224531_add_additional_fields_to_realization_agreement_table.php`

### Zagon migracij:

```bash
php artisan migrate
```

**Status:** ✅ Vse migracije uspešno zagnane v Batch 2

---

## Uporaba Novih Polj

### Primer: Odobritev Predloga

```php
$proposal = CompenzationProposal::find($id);
$proposal->update([
    'status' => 'approved',
    'response_date' => now(),
    'approved_by' => auth()->id(),
    'notes' => 'Predlog odobren'
]);
```

### Primer: Podpis Izvedbenega Sporazuma

```php
$agreement = ImplementationAgreement::find($id);
$agreement->update([
    'status' => 'active',
    'signed_date' => now(),
    'valid_from' => now(),
    'valid_until' => now()->addYear(),
    'reference_number' => 'IA-2025-001',
    'signed_by' => 'Janez Novak'
]);
```

### Primer: Izplačilo Provizije

```php
$realization = RealizationAgreement::find($id);
$realization->update([
    'payment_status' => 'paid',
    'payment_date' => now(),
    'notes' => 'Provizija izplačana na račun'
]);
```

---

## Enum Vrednosti

### `compenzations_proposals.status`:
- `draft` - Osnutek
- `pending` - V obravnavi
- `approved` - Odobren
- `rejected` - Zavrnjen
- `cancelled` - Preklican

### `implementation_agreement.status`:
- `draft` - Osnutek
- `active` - Aktiven
- `completed` - Zaključen
- `cancelled` - Preklican

### `realization_agreement.status`:
- `draft` - Osnutek
- `active` - Aktiven
- `completed` - Zaključen
- `cancelled` - Preklican

### `realization_agreement.payment_status`:
- `pending` - Čaka na plačilo
- `paid` - Plačano
- `partial` - Delno plačano
- `cancelled` - Preklicano

---

## Naslednji Koraki

### Frontend Implementacija

1. **Dodaj status badge komponento** za prikaz statusov
2. **Ustvari forme za urejanje** novih polj
3. **Dodaj filtre** po statusih v seznamih
4. **Implementiraj workflow** za odobritev predlogov

### Backend Implementacija

1. **Ustvari kontrolerje** za upravljanje statusov
2. **Dodaj validacijo** za nova polja
3. **Implementiraj notifikacije** ob spremembi statusa
4. **Ustvari poročila** po statusih in datumih

### Testiranje

1. Testiraj dodajanje kompenzacije z novimi polji
2. Testiraj posodabljanje statusov
3. Testiraj relacije (approvedBy)
4. Testiraj rollback migracij

---

## Rollback

Če je potrebno razveljaviti spremembe:

```bash
php artisan migrate:rollback --step=3
```

To bo razveljavilo zadnje 3 migracije (vsa nova polja).

---

## Opombe

- Vsa nova polja so **nullable**, razen enum polj, ki imajo privzete vrednosti
- Foreign key `approved_by` ima `onDelete('set null')` - če se uporabnik izbriše, se polje nastavi na NULL
- Datumska polja so avtomatsko pretvorjena v Carbon instance s `$casts`
- Enum polja omogočajo samo vnaprej določene vrednosti

