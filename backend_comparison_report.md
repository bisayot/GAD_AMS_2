# Backend Comparison Report
**Downloaded:** `C:\Users\HP\Downloads\GAD_AMS-main\GAD_AMS-main\backend`
**Local:** `c:\xampp\htdocs\GAD_AMS_2\backend`

> [!IMPORTANT]
> **Your local backend is NEWER overall** — it has 2 extra controllers, 2 extra migrations, and a significantly larger `AuthController.php`. The downloaded copy is an older snapshot.

---

## 🔴 Files MISSING from Local (in downloaded, not locally)

These files exist in the download but are absent in your local project:

### Migrations (2 files)
| File | Note |
|------|------|
| `app/Database/Migrations/2024-06-12-053500_CreateVenuesTable.php` | Empty (0 bytes) in download — likely a placeholder |
| `app/Database/Migrations/ActivityDesignMigration.php` | Empty (0 bytes) in download — likely a placeholder |

### Seeds (1 file)
| File | Note |
|------|------|
| `app/Database/Seeds/VenueSeeder.php` | Venue seeder — may have useful seed data |

### Tests directory (8 files)
These are CodeIgniter boilerplate test files. Your local project has **no tests folder** at all.

| File |
|------|
| `tests/.htaccess` |
| `tests/_support/Database/Migrations/2020-02-22-222222_example_migration.php` |
| `tests/_support/Database/Seeds/ExampleSeeder.php` |
| `tests/_support/Libraries/ConfigReader.php` |
| `tests/_support/Models/ExampleModel.php` |
| `tests/database/ExampleDatabaseTest.php` |
| `tests/index.html` |
| `tests/README.md` |
| `tests/session/ExampleSessionTest.php` |
| `tests/unit/HealthTest.php` |

### Other (1 file)
| File | Note |
|------|------|
| `public/uploads/.gitkeep` | Git placeholder for uploads directory |

---

## 🟢 Files EXTRA in Local (in local, not in downloaded)

Your local project has these files that don't exist in the download — these are **your additions**:

| File | Note |
|------|------|
| `.env` | Environment config — expected, never committed to repos |
| `app/Controllers/MandateController.php` (4,857 B) | ✅ New controller added locally |
| `app/Database/Migrations/2026-06-28-120229_AddDynamicFormTables.php` (18,634 B) | ✅ Large dynamic form migration — local only |
| `app/Database/Migrations/2026-06-28-124500_AlterGadMandatesAndIssues.php` (1,930 B) | ✅ GAD mandates/issues schema change — local only |
| `public/uploads/1780820589_6e5e33ba10c7537d9510.pdf` | An uploaded PDF file |

---

## 🟡 Files With Different Sizes

### Downloaded is LARGER (potentially has content your local is missing)

| File | Downloaded | Local | Diff |
|------|-----------|-------|------|
| `app/Config/App.php` | 8,288 B | 7,906 B | +382 B |
| `app/Config/Cors.php` | 1,275 B | 1,140 B | +135 B |
| `app/Controllers/AccomplishmentReportController.php` | 24,744 B | 24,184 B | +560 B |
| `app/Controllers/ActivityDesignController.php` | 32,560 B | 32,377 B | +183 B |
| `app/Controllers/ArchiveController.php` | 5,667 B | 5,197 B | +470 B |
| `app/Controllers/BudgetController.php` | 16,673 B | 16,075 B | +598 B |
| `app/Models/ApprovedControlModel.php` | 2,482 B | 2,441 B | +41 B |
| `database/migrations/add_archive_approval_columns.sql` | 1,117 B | 728 B | +389 B |

### Local is LARGER (your version is newer)

| File | Downloaded | Local | Diff |
|------|-----------|-------|------|
| `app/Config/Email.php` | 2,456 B | 3,567 B | +1,111 B |
| `app/Config/Routes.php` | 16,554 B | 16,923 B | +369 B |
| `app/Controllers/AuthController.php` | 13,973 B | 16,791 B | **+2,818 B** |
| `app/Controllers/ApprovedControlsController.php` | 1,738 B | 1,767 B | +29 B |
| `app/Controllers/MessageController.php` | 25,513 B | 26,121 B | +608 B |
| `app/Models/AccomplishmentReportModel.php` | 666 B | 1,710 B | **+1,044 B** |
| `app/Models/ActivityDesignModel.php` | 1,618 B | 1,679 B | +61 B |
| `Dockerfile` | 1,694 B | 1,740 B | +46 B |

---

## ⚠️ Summary: What You Should Check

The most significant gaps where the **downloaded version is larger** and may contain logic your local is missing:

1. **`app/Config/App.php`** (+382 B) — App config differences
2. **`app/Config/Cors.php`** (+135 B) — CORS settings may differ
3. **`app/Controllers/AccomplishmentReportController.php`** (+560 B) — May have methods/fixes not in local
4. **`app/Controllers/ArchiveController.php`** (+470 B) — Archive logic differences
5. **`app/Controllers/BudgetController.php`** (+598 B) — Budget logic differences
6. **`database/migrations/add_archive_approval_columns.sql`** (+389 B) — SQL migration may have more columns

> [!NOTE]
> The `tests/` folder is missing entirely from local — it contains only boilerplate CodeIgniter 4 tests, not custom tests. You can safely ignore this unless you plan to add automated testing.

> [!NOTE]
> The two empty migration files (`CreateVenuesTable.php` and `ActivityDesignMigration.php`) are 0 bytes in the download — they are likely empty placeholders and can be ignored.
