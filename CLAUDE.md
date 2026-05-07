# CLAUDE.md

Guidance for Claude Code / Cursor agents working in this repository.

> Repo: **Compenzations** — a Laravel + Inertia + Vue application for managing customers, debt compensations (kompenzacije), bills, PDF documents and data exports (XML / CSV).

---

## 1. Snapshot

- **Languages:** PHP (backend), JavaScript/Vue (frontend), SQL (MySQL).
- **Primary business entities:** `Entity` (stranka/partner), `Compenzation`, `CompenzationEntity` (pivot), `ImplementationAgreement`, `RealizationAgreement`, `CompenzationProposal`, `Bill`, `BillCompenzation`, `PostNumber`, `User`.
- **UI language:** Slovenian (`sl`) by default (`resources/lang/sl/`), with `en` fallback. Keep new user-facing strings translatable; don't hardcode Slovenian text in components if it already has an existing translation key.
- **Working branch model:** `main` is the trunk; current active upgrade work lives on `major-upgrade`. A rescue tag/branch exists (`backup-v1`).

---

## 2. Tech Stack

### Currently installed (pre-upgrade)

| Layer | Package | Version |
| --- | --- | --- |
| Backend framework | `laravel/framework` | `v11.47.0` |
| Inertia (server) | `inertiajs/inertia-laravel` | `v1.3.3` |
| PHP runtime | — | `^8.2` |
| Frontend framework | `vue` | `3.2.31` |
| Inertia (client) | mix of `@inertiajs/inertia` `0.11.0`, `@inertiajs/inertia-vue3` `0.6.0`, `@inertiajs/vue3` `1.2.0` |
| Build tool | `laravel-mix` | `^6.0.6` |
| Styling | `tailwindcss` | `^3.0.18` |
| Testing | `phpunit/phpunit` | `^11.0` |

### Upgrade target (work in progress — see `UPGRADE_2026.md`)

| Layer | Target |
| --- | --- |
| Laravel | `^13.0` |
| Inertia (server) | `inertiajs/inertia-laravel` `^3.0` |
| PHP | `^8.3` (Laravel 13 requirement) |
| Vue | `^3.5` |
| Inertia (client) | `@inertiajs/vue3` `^3.0` only (remove old `@inertiajs/inertia*`) |
| Build tool | `vite` + `@vitejs/plugin-vue` (replaces Laravel Mix) |

**Until the upgrade is complete, write new code to be compatible with both the current stack AND the target stack when feasible.** When a choice must be made, prefer the target stack idioms and document the bridge in code comments.

---

## 3. Directory Map

```
app/
  Http/
    Controllers/         REST-style + Inertia controllers (one feature per subdir)
      Compenzation/      Compenzations CRUD + stats + XML export
      Bill/              Bills list + PDF download
      ExportController   Bills XML + exports index + compenzations export page
      ContractsExportController  Contracts XML export
      User/              Dashboard + entities
      Auth/              Breeze-generated auth
    Middleware/          Inertia, auth redirects, TrustProxies
  Models/                Eloquent models (kebab-singular table names)
  Services/              Domain services (thin controllers -> services)
    Compenzations/       CompenzationService, CompenzationStatsService
    Entities/            EntityService, Registration
    Bills/               BillService
    PDF/                 mPDF wrappers
    Exports/             ContractsExportService
    Autocomplete/        Postcodes, IBAN, banks
    Calculations/        CalculationsService (DDV, discount, commission math)
    Locale/              Locale negotiation
  Listeners/             Event listeners (e.g. GenerateCompenzationProposalPdf)
  Providers/             Laravel service providers
  Console/Commands/      Custom artisan commands

bootstrap/app.php        Laravel 11+ app bootstrap (routes + middleware + exceptions)
routes/web.php           All routes live here (auth-protected group)
config/                  Standard Laravel config

database/
  migrations/            Flat, date-prefixed migrations
  seeders/               DatabaseSeeder + per-domain seeders (Users, PostNumbers)
  factories/             Eloquent factories

resources/
  js/
    app.js               Inertia app bootstrap (will be rewritten for Vite + v3)
    Pages/               Inertia pages (one Vue SFC per route)
      Exports/           Export-specific pages
      Auth/              Auth pages
    Layouts/             AdminLayout (admin shell), MainBlock, Sidebar
    Components/          Shared UI atoms
    Forms/               Multi-step form components for Compenzation flow
    mixins/              currentRoute, faker, filters, adminLayout wrapper
    services/            locale.js
  lang/                  Slovenian + English translations
  views/
    app.blade.php        Inertia root template
    pdfs/                mPDF blade templates
  css/                   Tailwind entry

public/                  Compiled assets, public images (including PDF-logo assets)
tests/                   PHPUnit tests (Feature + Unit)

docs/                    Project documentation (see `docs/README.md`)
.agent/                  Task-based workflow tracking (phases, current task, backlog)
```

---

## 4. Core Domain Rules

These are encoded in the codebase and MUST be preserved on every change:

1. **Compenzation has two parties** via `CompenzationEntity` pivot with `num` column:
   - `num = 1` → initiator / owner side,
   - `num = 2` → counterparty / partner side.
   - Code that exports contracts/bills chooses the **partner** (`num = 2`) when present.

2. **Amounts and percentages:**
   - Store monetary values as `decimal(10,2)`.
   - Use `CalculationsService` for discount and commission math; do not reinvent.
   - When writing XML, trim trailing zeros from `Znesek` fields (`rtrim(rtrim($formatted, '0'), '.')`).

3. **VAT numbers (`vat_num`, davčna):**
   - Strip leading `SI` (case-insensitive) when emitting for accounting exports.
   - Accept both formats on input.

4. **OpPIS XML exports** (bills and contracts):
   - Root element: `Prenos`.
   - `Glava` child with `Program=OpPIS`, `Program_verzija=1.0.0.487`, `Program_avtor=Opal d.o.o.`, `Verzija_xml=0.1`.
   - `Telo` child with repeated `Dokument` elements.
   - Always pretty-print via `DOMDocument` (`formatOutput = true`).
   - File name pattern: `izvoz-<tip>_<d.m.Y>_<MonthName>.xml`.

5. **PDFs:** Generated with `mPDF` through `app/Services/PDF/`. Templates are in `resources/views/pdfs/`. Don't inline CSS; keep template selectors stable — the service relies on them.

6. **Statistics:** `CompenzationStatsService` computes `percent_diff` and `amount_diff` taking `with_ddv` into account. When adding new statistics, place them in this service and never in the controller.

---

## 5. Routing & Pages Quick Reference

- `/` and `/dashboard` → `UserController@getDashboard`.
- `/entities`, `/entities/{id}` → Entities list + detail.
- `/compenzations` → **list + search + add only** (no date filter, no export buttons — that moved to exports module).
- `/compenzations/{id}` → Compenzation detail.
- `/compenzations/stats` → Statistics view (accepts `date_from`, `date_to`).
- `/compenzations/export` → XML download (accepts `date_from`, `date_to`).
- `/compenzations/compenzation/new` → Add Compenzation flow.
- `/bills` → Bills list.
- `/exports` → **Unified exports index** with three cards: Računi, Pogodbe, Kompenzacije.
- `/exports/bills`, `/exports/contracts`, `/exports/compenzations` → Dedicated export pages (date range + XML button).
- `/exports/*/download` → Actual download endpoints (match `get|post`).

**Rule:** New exports should be added as a card on `/exports` and as a subpage, **never** as buttons on domain list pages.

---

## 6. Coding Conventions

### PHP / Laravel
- Controllers stay thin. Business logic lives in `app/Services/<Domain>/`.
- Prefer **dependency injection** over facades inside services.
- Use Eloquent relationships; avoid raw SQL. If a query needs performance tuning, use the query builder with parameter binding (**never** string-concat user input — OWASP / SQLi).
- Validate at the controller boundary with `Request::validate([...])`. For complex forms create a FormRequest.
- Use Carbon for all dates.
- Type-hint parameters and return types on new methods; add docblocks only where types cannot express intent.
- Match Laravel 11+ structure: middleware + routes + exceptions configured in `bootstrap/app.php`. Do NOT reintroduce `Kernel.php` or `RouteServiceProvider`.

### Vue / Inertia
- One route per Page SFC in `resources/js/Pages/`.
- Shared atoms go in `Components/`. Forms belong in `Forms/`.
- Props are typed with the object shorthand (`props: { compenzations: Object, filters: { type: Object, default: () => ({}) } }`).
- Client-side formatting helpers: Slovenian locale (`sl-SI`), 2 decimals, `€` suffix, `dd.MM.yyyy` dates.
- Debounce search inputs ~300 ms (see `Compenzations.vue` pattern).
- For cross-tab downloads use `window.open(url, '_blank')` with `URLSearchParams` — NOT Inertia `$inertia.get` (it expects JSON).
- Always preserve `preserveState: true, preserveScroll: true, replace: true` on filter re-fetches.

### Imports during the upgrade window
- **Prefer** `@inertiajs/vue3` imports in new files (`Head`, `Link`, `useForm`, `usePage`, `router`).
- Legacy files still using `@inertiajs/inertia-vue3` / `@inertiajs/inertia` will be migrated in Phase 3 — do not split their imports across both packages in one file.
- When touching a legacy file for another reason, it's fine to migrate its imports as part of the same change.

### CSS / Tailwind
- Use Tailwind utility classes; avoid custom CSS unless absolutely needed.
- Brand colors, spacing, radii and shadows are tokens in `tailwind.config.js` — reuse, don't hardcode hex.
- Support dark-neutral grayscale (`stone-*`, `blue-*`) already defined in the config.
- Layout is fluid: always test at `sm` (mobile), `md` (tablet), `xl` (desktop).

### Git
- Branches: `feature/<slug>`, `fix/<slug>`, `chore/<slug>`; ongoing large migration on `major-upgrade`.
- Commit messages: short imperative subject in Slovenian or English (project uses both). Reference context, not just "WIP".
- **Never** force push to `main`. Never commit `.env` or real credentials.
- Before running destructive DB commands (migrate:fresh, truncate seeders) confirm with the user.

---

## 7. Testing

- PHPUnit via `php artisan test`.
- Existing tests live under `tests/Feature/` and `tests/Unit/`.
- Feature tests for exports: `tests/Feature/Exports/ContractsExportTest.php`, `tests/Feature/Compenzations/CompenzationExportsTest.php`.
- Auth flow tests come from Breeze.
- **Expectation:** Any new controller endpoint should ship with at least a validation-level feature test. XML/CSV exports should assert on shape (headers, at least one `<Dokument>`, filename pattern).
- Run the full suite before handing work back.

---

## 8. Common Commands

```bash
# Install & environment
composer install
npm install
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan migrate --seed
php artisan migrate:fresh --seed   # destructive — ask first

# Dev loop
php artisan serve
npm run dev            # currently Laravel Mix; will be `vite` after Phase 3

# Prod build
npm run production     # currently Laravel Mix; will be `vite build`

# Quality
php artisan test
./vendor/bin/pint      # formatter

# Routes introspection
php artisan route:list
```

The project also contains ad-hoc WSL helper scripts at the repo root (`setup-database*.sh`, `setup-mysql*.sh`, `fix-permissions.sh`). Treat them as one-shot helpers, not CI tooling.

---

## 9. When Modifying / Extending

1. **Read first.** Always read the service + related Vue page + relevant migrations before editing.
2. **Stay in the right layer.** Data filtering → service; HTTP/validation → controller; formatting → Vue.
3. **Don't duplicate exports.** If the work is about downloading data, add to `app/Http/Controllers/ExportController.php` (bills) or `ContractsExportController.php` (contracts) or `CompenzationController@exportCompenzations` (compenzations) — and expose via `/exports/*`.
4. **Preserve existing URL contracts** for legacy scripts/integrations (route names in `routes/web.php` are used by the frontend through Ziggy).
5. **Check `ReadLints`** after edits and fix anything you introduced.
6. **Update docs.** When you change behavior that contradicts this file, `docs/START_DEVELOPMENT.md`, or `docs/README.md`, update them in the same change.

---

## 10. Security Must-Haves

- All domain routes live inside `Route::middleware(['auth:web'])->group(...)`. Do not bypass.
- CSRF token is required for non-GET requests — Inertia handles it via meta tag in `resources/views/app.blade.php`. If you introduce a new Blade layout, carry the `<meta name="csrf-token">` over.
- Parameterize every query (Eloquent / Query Builder). Never interpolate `$request->input()` into raw SQL.
- Sanitize output in XML with `htmlspecialchars($value, ENT_XML1, 'UTF-8')`.
- Do not log personal data (VAT numbers, addresses, emails) at `info`/`debug` level in shared log files.
- Don't commit dumps from `storage/app/public/` or `storage/logs/`.

---

## 11. Workflow Hooks (`.agent/`)

Long-running tasks follow the workflow in `.agent/`:

- `.agent/PHASES.md` — phase tracker (Phase 1–9 + upgrade phases).
- `.agent/current-task.md` — single active task.
- `.agent/previous-task.md` — most recently completed task.
- `.agent/task-data/backlog.md` — prioritized backlog (P0 > P1 > P2).
- `.agent/task-data/history.{md,jsonl}` — append-only history.

When you start a multi-step piece of work:

1. Pick from `backlog.md` (highest priority).
2. Copy to `current-task.md`, set `State: in_progress`.
3. Work in ≤30-minute slices; update `Current Step` / `Next Step`.
4. On completion, move to `previous-task.md` and append to `history.md` / `history.jsonl`.

---

## 12. Ongoing 2026 Upgrade

Active effort — see `UPGRADE_2026.md` for the phased plan.

High-level phases:
1. **Docs & CLAUDE.md** (this file) — zero risk.
2. **Backend:** Laravel 11 → 12 → 13, Inertia Laravel 3, PHP 8.3.
3. **Frontend build:** Laravel Mix → Vite, Vue 3.2 → 3.5, Inertia client 0.6/1.2 → 3.0.
4. **UI redesign:** new layout shell, design tokens, reusable components, mobile pass.

While the upgrade is in flight:

- Do NOT install new packages that pin us to the old stack (no new uses of `@inertiajs/inertia-vue3`, no new `laravel-mix` customizations).
- Do NOT introduce code that relies on Laravel 11-only helpers that were removed in 12/13.
- Keep PRs phase-scoped and note "Phase N" in the commit subject.
