# Compenzations

Web application for managing **customers**, **debt compensations (kompenzacije)**, **contracts**, **bills**, **PDF documents** and **data exports** (XML / CSV for OpPIS accounting format).

- **Repo:** `greentech77/Compensations`
- **Docs index:** [`docs/README.md`](docs/README.md)
- **Agent guide:** [`CLAUDE.md`](CLAUDE.md)
- **Ongoing upgrade:** [`UPGRADE_2026.md`](UPGRADE_2026.md)

---

## Tech Stack

| Layer | Current | Target (in progress on `major-upgrade`) |
| --- | --- | --- |
| PHP | `^8.2` | `^8.3` |
| Laravel | `11.47.0` | `^13.0` |
| Inertia (server) | `inertia-laravel 1.3.3` | `inertia-laravel ^3.0` |
| Vue | `3.2.31` | `^3.5` |
| Inertia (client) | mix of legacy `@inertiajs/*` + `@inertiajs/vue3 1.2` | `@inertiajs/vue3 ^3.0` only |
| Build | `laravel-mix ^6.0.6` | `vite + @vitejs/plugin-vue + laravel-vite-plugin` |
| CSS | `tailwindcss ^3` | `tailwindcss ^3.4` |
| DB | MySQL | MySQL |
| PDF | mPDF | mPDF |
| Auth | Laravel Breeze | Laravel Breeze |

See [`UPGRADE_2026.md`](UPGRADE_2026.md) for the full upgrade plan and phase status.

---

## Quickstart

### Prerequisites
- PHP (currently 8.2, moving to 8.3)
- Composer 2.x
- Node.js 20+ and npm
- MySQL 8
- WSL Ubuntu (dev environment used by the team)

### Setup

```bash
git clone git@github.com:greentech77/Compensations.git compenzations
cd compenzations

composer install
npm install

cp .env.example .env
php artisan key:generate

# Configure DB credentials in .env (DB_DATABASE=kompenzacije_app by convention)

php artisan migrate --seed
```

### Run

```bash
# terminal 1
php artisan serve

# terminal 2
npm run dev   # currently Laravel Mix; becomes `vite` after Phase 3 upgrade
```

Open `http://localhost:8000` (or `:8081` if you proxy through nginx as in the team setup).

### Build for production

```bash
npm run production   # Laravel Mix; will become `vite build`
```

---

## Features

- Manage customers (**entities**) and their bank / tax info.
- Create, edit and track **compenzations** (debt settlements) between two entities.
- Generate **implementation** and **realization agreements** plus a **proposal** as PDFs (mPDF templates under `resources/views/pdfs/`).
- Manage **bills** linked to compenzations.
- Export **bills** and **contracts** to OpPIS-compatible XML.
- Compute **compenzation statistics** for a date range (amount difference, percent difference, with/without VAT).
- Unified `/exports` entry point for all data exports.
- Authenticated admin UI (Laravel Breeze) with Slovenian UI and English fallback.

---

## Project Layout

```
app/                    Laravel app (Controllers, Models, Services, Listeners)
bootstrap/app.php       Laravel 11+ bootstrap (routes, middleware, exceptions)
config/                 Laravel config
database/
  migrations/           Date-prefixed migrations (flat)
  seeders/              DatabaseSeeder + domain seeders
  factories/            Eloquent factories
resources/
  js/                   Vue 3 + Inertia frontend
    Pages/              Inertia routes
    Layouts/            Admin shell
    Components/         Reusable UI atoms
    Forms/              Multi-step compenzation form
  views/                Blade templates (Inertia root, PDFs)
  css/                  Tailwind entry
  lang/                 sl + en
routes/web.php          All routes (auth group)
tests/                  PHPUnit Feature + Unit
public/                 Public assets
docs/                   Documentation
.agent/                 Task-based workflow tracking
CLAUDE.md               Guide for AI coding agents
UPGRADE_2026.md         Major upgrade plan
```

See `CLAUDE.md` section **3. Directory Map** for more detail.

---

## Documentation

Full docs live under [`docs/`](docs/README.md):

- [`docs/START_DEVELOPMENT.md`](docs/START_DEVELOPMENT.md) — onboarding and local setup
- [`docs/PROGRESS.md`](docs/PROGRESS.md) — high-level tracker
- [`docs/EXPORT_IMPLEMENTATION.md`](docs/EXPORT_IMPLEMENTATION.md) — export module design
- [`docs/PDF_GENERATION_IMPLEMENTATION.md`](docs/PDF_GENERATION_IMPLEMENTATION.md) — PDF pipeline
- [`docs/setup/DATABASE_SETUP.md`](docs/setup/DATABASE_SETUP.md) — MySQL setup
- [`CLAUDE.md`](CLAUDE.md) — AI agent rules (conventions, domain rules, security)

---

## Development Workflow

1. Branch off `main` (`feature/<slug>`, `fix/<slug>`, `chore/<slug>`). Long-running upgrade work lives on `major-upgrade`.
2. Keep controllers thin — business logic goes into `app/Services/<Domain>/`.
3. Add at least one feature test for any new controller endpoint.
4. Run `php artisan test` and the build (`npm run dev` or `vite`) before handing off.
5. Never commit `.env`, dumps, or real credentials.

Agent-specific rules (imports, Inertia version bridging, security, domain invariants) are in [`CLAUDE.md`](CLAUDE.md).

---

## Security Notes

- All domain routes live under `auth:web` (see `routes/web.php`).
- CSRF token is exposed via `<meta name="csrf-token">` in `resources/views/app.blade.php` for non-GET fetches.
- XML output uses `htmlspecialchars($v, ENT_XML1, 'UTF-8')`.
- Always parameterize queries (Eloquent or bound Query Builder).
- See `CLAUDE.md` section **10. Security Must-Haves** for the full checklist.

---

## Contribution

- Follow conventions in [`CLAUDE.md`](CLAUDE.md).
- Small, focused PRs. One phase of the 2026 upgrade per PR.
- Update docs in the same PR when behavior changes.

---

## License

MIT.
