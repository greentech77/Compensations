# Vodič za Začetek Razvoja — Compenzations

Cilj tega dokumenta je, da te čim hitreje spravi v produktivno stanje. Za arhitekturo, pravila in konvencije glej [`../CLAUDE.md`](../CLAUDE.md). Za načrt tekoče nadgradnje platforme glej [`../UPGRADE_2026.md`](../UPGRADE_2026.md).

---

## 1. Trenutno Stanje Projekta

- Laravel **11.47.0** → v nadgradnji proti **13.x** (veja `major-upgrade`).
- Vue **3.2** + Laravel Mix → v nadgradnji proti **Vue 3.5 + Vite**.
- Inertia (server **1.3.3**, client mix **0.6 / 1.2**) → v nadgradnji proti **Inertia v3**.
- DB: MySQL; vse osnovne migracije in seederji so pripravljeni.
- Izvozi (`/exports/bills`, `/exports/contracts`, `/exports/compenzations`), statistika, PDF predlogi in agreements so implementirani.

Kaj je odprto, preveri v `../.agent/PHASES.md` in [`PROGRESS.md`](PROGRESS.md).

---

## 2. Predpogoji

- WSL Ubuntu (razvojno okolje ekipe)
- PHP **8.2** (bo **8.3** po Phase 2 nadgradnje)
- Composer 2.x
- Node.js 20+ in npm
- MySQL 8
- Git

---

## 3. Hitra Nastavitev

```bash
git clone git@github.com:greentech77/Compensations.git compenzations
cd compenzations

# PHP odvisnosti
composer install

# JS odvisnosti
npm install

# Konfiguracija
cp .env.example .env
php artisan key:generate
```

Uredi `.env` (DB credentials):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kompenzacije_app
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

Baza + seederji:

```bash
php artisan migrate --seed
```

## 4. Zagon Razvojnega Okolja

```bash
# Backend
php artisan serve

# Frontend (v drugem terminalu)
npm run dev        # trenutno Laravel Mix; po Phase 3 bo "vite"
```

Ekipa uporablja nginx reverse proxy; najpogostejša lokalna URL-ja:
- `http://localhost:8000` — direktno `artisan serve`
- `http://localhost:8081` — skozi nginx

---

## 5. Struktura (kratko)

```
app/
  Http/Controllers/         Thin controllers (feature subdirs)
  Models/                   Eloquent
  Services/                 Domain logic
  Listeners/                Eventi (PDF generation)
bootstrap/app.php           Laravel 11+ bootstrap
database/migrations/        Migracije
database/seeders/           Seederji
resources/
  js/Pages/                 Inertia pages
  js/Layouts/               AdminLayout shell
  js/Components/            UI atoms
  js/Forms/                 Multi-step form (AddCompenzation)
  views/app.blade.php       Inertia root
  views/pdfs/               mPDF predloge
routes/web.php              Vse routes
tests/                      PHPUnit
```

Podrobneje v [`../CLAUDE.md`](../CLAUDE.md) — sekcija *Directory Map*.

---

## 6. Ključne Business Rules

Preden kaj spreminjaš, preveri v [`../CLAUDE.md`](../CLAUDE.md) → *Core Domain Rules*. Najpomembnejše:

1. `CompenzationEntity.num = 2` označuje **partnerja** (ciljna stranka pri exportih).
2. VAT številke se pri exportih čistijo `^SI` (case-insensitive).
3. Zneski so `decimal(10,2)`. Za DDV / proviziji / popuste uporabi `CalculationsService`.
4. OpPIS XML izvozi imajo fiksno `Glava` strukturo (`Program=OpPIS`, ...). Vse se pretty-printajo prek `DOMDocument`.
5. Statistika kompenzacij je v `CompenzationStatsService`, ne v kontrolerju.

---

## 7. Testiranje

```bash
php artisan test                                   # celoten paket
php artisan test --filter=ContractsExportTest      # posamezen test
php artisan test --filter='Export|Stats'           # več testov
./vendor/bin/pint                                  # formatter
```

Vsak nov endpoint naj ima vsaj validation-level feature test.

---

## 8. Pogoste Napake in Rešitve

### Composer
```bash
# Če composer javi "platform requirement":
composer install --ignore-platform-reqs
```

### NPM
```bash
# Peer dependency konflikti:
npm install --legacy-peer-deps
```

### Migracije
```bash
# Pregled:
php artisan migrate:status

# Destructive reset (vpraša se za potrditev):
php artisan migrate:fresh --seed
```

### Cache
```bash
php artisan optimize:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

### Permissions (WSL / bare metal)
```bash
./fix-permissions.sh   # obstoječi helper v repo rootu
```

---

## 9. Git Workflow

- Glavna veja: `main`.
- Trenutna nadgradnja platforme: `major-upgrade` (glej [`../UPGRADE_2026.md`](../UPGRADE_2026.md)).
- Backup: veja/tag `backup-v1`.
- Nova funkcionalnost: `feature/<slug>`.
- Bugfix: `fix/<slug>`.
- Ostalo (docs, config): `chore/<slug>`.

Pravila:
- Commit message kratek, imperative (angleško ali slovensko, kot doslej).
- Ne commitaj `.env`, dumpov, PDF-jev iz `storage/app/public/`.
- Nikoli force-push na `main`.
- Pre-upgrade backup naredi pred destruktivnimi operacijami (`git tag -a backup-...` + `git push --tags`).

Več v [`../CLAUDE.md`](../CLAUDE.md) → *Git* in *Security Must-Haves*.

---

## 10. Kam Naprej

- **Arhitektura in konvencije:** [`../CLAUDE.md`](../CLAUDE.md)
- **Nadgradnja platforme:** [`../UPGRADE_2026.md`](../UPGRADE_2026.md)
- **Trenutni task:** `../.agent/current-task.md`
- **Backlog:** `../.agent/task-data/backlog.md`
- **Napredek:** [`PROGRESS.md`](PROGRESS.md)
- **Izvozi (design):** [`EXPORT_IMPLEMENTATION.md`](EXPORT_IMPLEMENTATION.md)
- **PDF pipeline:** [`PDF_GENERATION_IMPLEMENTATION.md`](PDF_GENERATION_IMPLEMENTATION.md)

---

*Posodobljeno: 2026-04-19*
