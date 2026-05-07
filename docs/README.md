# Dokumentacija — Compenzations

Kazalo projektne dokumentacije. Za pregled arhitekture glej [`../CLAUDE.md`](../CLAUDE.md). Za načrt nadgradnje platforme glej [`../UPGRADE_2026.md`](../UPGRADE_2026.md).

---

## Hitra Navigacija

### Začetek
- [START_DEVELOPMENT.md](START_DEVELOPMENT.md) — Setup in lokalni razvoj
- [setup/DATABASE_SETUP.md](setup/DATABASE_SETUP.md) — MySQL nastavitev
- [setup/MYSQL_SETUP.md](setup/MYSQL_SETUP.md) — Dodatne MySQL opombe
- [setup/SETUP_INSTRUCTIONS.md](setup/SETUP_INSTRUCTIONS.md) — Splošna setup navodila

### Arhitektura in konvencije
- [../CLAUDE.md](../CLAUDE.md) — **Glavni vodič za agente / razvijalce** (stack, struktura, domenska pravila, varnost)
- [../UPGRADE_2026.md](../UPGRADE_2026.md) — Plan nadgradnje Laravel 11 → 13, Vite, Inertia v3, UI redesign

### Funkcionalnosti
- [EXPORT_FUNCTIONALITY_ANALYSIS.md](EXPORT_FUNCTIONALITY_ANALYSIS.md) — Analiza izvozov (legacy vs. novo)
- [EXPORT_IMPLEMENTATION.md](EXPORT_IMPLEMENTATION.md) — Implementacija izvoznega modula
- [PDF_GENERATION_IMPLEMENTATION.md](PDF_GENERATION_IMPLEMENTATION.md) — PDF generiranje (mPDF)
- [PDF_LEGACY_FORMAT_IMPLEMENTATION.md](PDF_LEGACY_FORMAT_IMPLEMENTATION.md) — Skladnost z legacy PDF obliko
- [PDF_TEMPLATES_ENHANCEMENT.md](PDF_TEMPLATES_ENHANCEMENT.md) — Izboljšave PDF predlog
- [DATABASE_FIELDS_ENHANCEMENT.md](DATABASE_FIELDS_ENHANCEMENT.md) — Nadgradnje polj v shemi

### Napredek
- [PROGRESS.md](PROGRESS.md) — Visokonivojski tracker
- [progress/DEVELOPMENT_PROGRESS.md](progress/DEVELOPMENT_PROGRESS.md) — Detajlen napredek
- [progress/MIGRATIONS_COMPLETE.md](progress/MIGRATIONS_COMPLETE.md) — Status migracij

### Task-based workflow (`.agent/`)
- `../.agent/PHASES.md` — Faze razvoja
- `../.agent/current-task.md` — Trenutna naloga
- `../.agent/previous-task.md` — Zadnja zaključena naloga
- `../.agent/task-data/backlog.md` — Backlog (P0 > P1 > P2)
- `../.agent/task-data/history.md` — Zgodovina nalog

---

## Struktura Dokumentacije

```
compenzations/
├── CLAUDE.md                         # Glavna navodila za agente
├── UPGRADE_2026.md                   # Načrt nadgradnje platforme
├── README.md                         # Pregled projekta
├── docs/
│   ├── README.md                     # Ta datoteka
│   ├── index.md                      # Kazalo (krajše)
│   ├── PROGRESS.md                   # Tracking napredka
│   ├── START_DEVELOPMENT.md          # Setup in workflow
│   ├── EXPORT_*.md                   # Izvozi
│   ├── PDF_*.md                      # PDF generacija
│   ├── DATABASE_FIELDS_ENHANCEMENT.md
│   ├── progress/                     # Detajlni napredek
│   │   ├── DEVELOPMENT_PROGRESS.md
│   │   └── MIGRATIONS_COMPLETE.md
│   └── setup/                        # Setup navodila
│       ├── DATABASE_SETUP.md
│       ├── MYSQL_SETUP.md
│       └── SETUP_INSTRUCTIONS.md
└── .agent/                           # Task-based tracking
    ├── PHASES.md
    ├── current-task.md
    ├── previous-task.md
    └── task-data/
        ├── backlog.md
        ├── history.md
        └── history.jsonl
```

---

## Za Začetek

1. **Nov v projektu?** → [`START_DEVELOPMENT.md`](START_DEVELOPMENT.md)
2. **Razumi pravila in konvencije** → [`../CLAUDE.md`](../CLAUDE.md)
3. **Preveri, kaj je v teku** → [`../UPGRADE_2026.md`](../UPGRADE_2026.md) + `../.agent/current-task.md`
4. **Preberi high-level napredek** → [`PROGRESS.md`](PROGRESS.md)
5. **Setup baze** → [`setup/DATABASE_SETUP.md`](setup/DATABASE_SETUP.md)

---

*Posodobljeno: 2026-04-19*
