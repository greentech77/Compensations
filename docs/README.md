# 📚 Dokumentacija - Kompenzacije System

## Struktura Dokumentacije

```
compenzations/
├── .agent/                      # 🎯 Task-based workflow tracking
│   ├── PHASES.md               # Tracking faz razvoja
│   ├── current-task.md         # Trenutno aktivna naloga
│   ├── previous-task.md        # Zadnja dokončana naloga
│   └── task-data/
│       ├── backlog.md          # Seznam nalog (prioritete P0-P2)
│       ├── history.md          # Zgodovina nalog (markdown)
│       └── history.jsonl       # Zgodovina nalog (JSONL)
└── docs/
    ├── README.md               # Ta datoteka (pregled dokumentacije)
    ├── PROGRESS.md             # 📊 Pregledni tracking napredka
    ├── START_DEVELOPMENT.md    # Vodič za začetek razvoja
    ├── progress/               # Detajlne datoteke o napredku
    │   ├── DEVELOPMENT_PROGRESS.md  # Podroben napredek razvoja
    │   └── MIGRATIONS_COMPLETE.md   # Status migracij
    └── setup/                  # Navodila za nastavitev
        ├── DATABASE_SETUP.md   # Nastavitev baze podatkov
        ├── MYSQL_SETUP.md      # MySQL namestitev
        ├── SETUP_INSTRUCTIONS.md # Splošna navodila
        └── *.sh                # Setup skripte
```

## 🎯 Začni Tukaj

### Novački v projektu
1. Preberi `START_DEVELOPMENT.md` - Osnove za začetek
2. Preberi `.agent/PHASES.md` - Trenutna faza razvoja
3. Preberi `PROGRESS.md` - Trenutno stanje projekta
4. Sledi navodilom v `setup/` mapi za nastavitev

### Razvijalci
1. **Faze in naloge:** `.agent/PHASES.md` - Vedno začni tukaj za faze! ⭐
2. **Trenutna naloga:** `.agent/current-task.md` - Aktivna naloga
3. **Backlog:** `.agent/task-data/backlog.md` - Seznam nalog (P0-P2)
4. **Pregledni tracking:** `PROGRESS.md` - Visokonivojski pregled
5. **Setup:** `setup/DATABASE_SETUP.md` - Baza podatkov
6. **Napredek:** `progress/` - Detajlne informacije

## 📊 Sledenje Napredka

### Task-Based Workflow (`.agent/`)
**Task-based tracking sistem za strukturiran razvoj:**

- **`.agent/PHASES.md`** - Tracking faz razvoja (Phase 1-9) ⭐ **ZAČNI TUKAJ**
- **`.agent/current-task.md`** - Trenutno aktivna naloga
- **`.agent/previous-task.md`** - Zadnja dokončana naloga
- **`.agent/task-data/backlog.md`** - Seznam nalog (prioritete P0 > P1 > P2)
- **`.agent/task-data/history.md`** - Zgodovina nalog (markdown)
- **`.agent/task-data/history.jsonl`** - Zgodovina nalog (JSONL format)

**Workflow:**
1. Preveri `.agent/PHASES.md` za trenutno fazo
2. Izberi nalogo iz `.agent/task-data/backlog.md` (najvišja prioriteta)
3. Kopiraj v `.agent/current-task.md` in nastavi State=`in_progress`
4. Delaj v majhnih korakih (≤30 min)
5. Posodabljaj napredek in zgodovino
6. Ko končano, premakni v `.agent/previous-task.md`

### Pregledna Datoteka
**`PROGRESS.md`** - Visokonivojski pregled napredka:
- ✅ Dokončane naloge
- 📋 Trenutna faza
- 🔄 Naslednji koraki
- 📈 Statistika

### Detajlne Datoteke
- `progress/DEVELOPMENT_PROGRESS.md` - Podroben napredek
- `progress/MIGRATIONS_COMPLETE.md` - Status migracij in popravki

## 🛠️ Setup Navodila

Vse navodila za nastavitev so v `setup/` mapi:
- **DATABASE_SETUP.md** - Nastavitev baze podatkov
- **MYSQL_SETUP.md** - MySQL namestitev in konfiguracija
- **SETUP_INSTRUCTIONS.md** - Splošna navodila

## 🔍 Hitra Navigacija

- **Katera faza je aktivna?** → `.agent/PHASES.md` ⭐
- **Katera naloga je aktivna?** → `.agent/current-task.md`
- **Kaj narediti naslednje?** → `.agent/task-data/backlog.md` (prioritete P0-P2)
- **Pregled napredka?** → `PROGRESS.md`
- **Kako nastaviti bazo?** → `setup/DATABASE_SETUP.md`
- **Kaj je bilo narejeno?** → `.agent/previous-task.md` ali `progress/DEVELOPMENT_PROGRESS.md`
- **Zgodovina nalog?** → `.agent/task-data/history.md`

---
*Posodobljeno: 2025-11-30*

