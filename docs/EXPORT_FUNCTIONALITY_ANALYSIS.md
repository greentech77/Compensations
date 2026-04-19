# Export Functionality Analysis

**Datum:** 2025-01-29  
**Task:** 20251130-001 - Verify Export Functionality for Bills  
**Status:** Dokončano - Analiza

## Pregled

Analiza codebase za export funkcionalnost za račune (bills) je pokazala, da **export funkcionalnost NI implementirana**.

## Ugotovitve

### 1. Routes
- ❌ **Ni export routes** v `routes/web.php`
- ❌ Ni routes za `/exports/bills` ali podobnih
- ✅ Obstajajo routes za compenzations in entities

### 2. Kontrolerji
- ❌ **Ni ExportController** v `app/Http/Controllers/`
- ❌ Ni export metod v obstoječih kontrolerjih
- ✅ Obstajajo: `CompenzationController`, `UserController`, `AutocompleteController`

### 3. Vue Komponente
- ❌ **Ni export komponent** v `resources/js/Pages/`
- ❌ Ni `Exports/Bills.vue` ali podobnih komponent
- ✅ Obstajajo komponente za: Dashboard, Entities, Compenzations

### 4. Modeli
- ✅ **Bill model obstaja** (`app/Models/BillModel.php`)
- ✅ Model ima osnovne polja: `id_entity`, `amount`, `year`, `date`
- ⚠️ Model nima relacij definiranih (potrebno dodati)

### 5. Database
- ✅ **Bills tabela obstaja** v migracijah
- ✅ **Bills_compenzations pivot tabela** obstaja
- ✅ Tabela je uspešno migrirana

## Kaj Manjka

### 1. ExportController
Potrebno ustvariti:
- `app/Http/Controllers/ExportController.php`
- Metode za XML export
- Metode za CSV export
- Metode za pripravo podatkov

### 2. Export Routes
Potrebno dodati v `routes/web.php`:
```php
Route::get('/exports/bills', [ExportController::class, 'bills'])->name('exports.bills');
Route::post('/exports/bills', [ExportController::class, 'exportBills'])->name('exports.bills.export');
```

### 3. Vue Export Komponenta
Potrebno ustvariti:
- `resources/js/Pages/Exports/Bills.vue`
- UI za izbiro parametrov exporta (leto, format, itd.)
- Integracija z Inertia.js

### 4. Bill Model Relacije
Potrebno dodati v `app/Models/BillModel.php`:
```php
public function entity() {
    return $this->belongsTo(Entity::class, 'id_entity');
}

public function compenzations() {
    return $this->belongsToMany(Compenzation::class, 'bills_compenzations');
}
```

### 5. Export Paketi
Potrebno preveriti/namestiti:
- Laravel Excel (maatwebsite/excel) za CSV/Excel export
- Ali custom XML generator

## Priporočeni Naslednji Koraki

### Faza 1: Osnovna Struktura
1. ✅ Ustvari ExportController
2. ✅ Dodaj export routes
3. ✅ Posodobi Bill model z relacijami

### Faza 2: Export Implementacija
4. ✅ Implementiraj CSV export
5. ✅ Implementiraj XML export
6. ✅ Dodaj validacijo in error handling

### Faza 3: Frontend
7. ✅ Ustvari Vue komponento za export interface
8. ✅ Integriraj z obstoječim layoutom
9. ✅ Dodaj loading states in error handling

### Faza 4: Testiranje
10. ✅ Testiraj export funkcionalnost
11. ✅ Preveri, da se podatki pravilno eksportirajo
12. ✅ Preveri, da se relacije pravilno vključijo

## Reference

- Laravel Excel dokumentacija: https://docs.laravel-excel.com/
- Inertia.js dokumentacija: https://inertiajs.com/
- Laravel 11 dokumentacija: https://laravel.com/docs/11.x

## Opombe

- Export funkcionalnost mora biti kompatibilna z Laravel 11
- Uporabiti mora obstoječe vzorce v aplikaciji (Inertia.js, Vue 3)
- Export mora podpirati slovenske znake (UTF-8 encoding)

