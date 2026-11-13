# StockPilot — Smart Inventory & Supply Chain Management System

Core PHP 8.2+ / MySQL 8 / PDO web application (no frameworks).
**Current status: Stage 1 of 12 — project foundation.**

## What Stage 1 contains

| File / folder | Purpose |
|---|---|
| `public/index.php` | Front controller. Every request enters here. |
| `public/.htaccess` | Sends non-file URLs to `index.php`. |
| `.htaccess` (root) | Lets XAMPP serve the app at `http://localhost/stockpilot-360/` while keeping source code unreachable. |
| `app/bootstrap.php` | Loads constants, autoloader, `.env`, timezone, error settings. |
| `app/helpers.php` | `e()` (HTML escape), `url()`, `asset()`, `config()`. |
| `app/Core/Env.php` | Reads the `.env` file. |
| `app/Core/Config.php` | Reads `config/*.php` with dot notation. |
| `app/Core/Database.php` | Lazy PDO connection (exceptions on, real prepared statements) and a connection check. |
| `app/Core/Router.php`, `Request.php`, `Response.php`, `View.php` | Minimal routing and rendering. |
| `app/Core/Logger.php` | Writes to `storage/logs/`. |
| `app/Controllers/HomeController.php` | System-check page (`/`). |
| `app/Controllers/HealthController.php` | JSON status endpoint (`/health`). |
| `routes/web.php` | Route table. |
| `config/app.php`, `config/database.php` | Settings read from `.env`. |
| `tests/run.php` | Stage 1 tests (plain PHP). |

Empty `Models/`, `Services/`, `Middleware/`, `Validators/`, `database/migrations/`, `database/seeders/` folders are placeholders for later stages.

## Setup on Windows with XAMPP and VS Code

### 1. Install the tools
- XAMPP with PHP 8.2 or newer (check with `C:\xampp\php\php.exe -v`).
- VS Code, and Git for Windows.

### 2. Put the project in XAMPP's web folder
```powershell
Copy-Item -Recurse .\stockpilot-360 C:\xampp\htdocs\stockpilot-360
cd C:\xampp\htdocs\stockpilot-360
code .
```

### 3. Start Apache and MySQL
Open the **XAMPP Control Panel** and click **Start** next to **Apache** and **MySQL**.
`mod_rewrite` is enabled by default in XAMPP. If the project's `.htaccess` is ignored, open `C:\xampp\apache\conf\httpd.conf` and make sure the `<Directory "C:/xampp/htdocs">` block has `AllowOverride All`, then restart Apache.

### 4. Create the database
1. Open http://localhost/phpmyadmin
2. Click **New**, name it `stockpilot`, choose collation `utf8mb4_unicode_ci`, click **Create**.

### 5. Create your `.env`
```powershell
Copy-Item .env.example .env
```
XAMPP's default (`root`, empty password) already matches `.env.example`. Never commit `.env`.

### 6. Add PHP to PATH (needed for the terminal commands)
For the current PowerShell window only:
```powershell
$env:Path += ";C:\xampp\php"
php -v
```

### 7. Open the app
- http://localhost/stockpilot-360/ — system check page (every row should say **Pass**)
- http://localhost/stockpilot-360/health — JSON status

### Alternative: PHP's built-in server (no Apache needed)
```powershell
php -S localhost:8000 -t public public/index.php
```
Then open http://localhost:8000/ (MySQL must still be running).

## Testing Stage 1

```powershell
php tests/run.php
```
Expected last line: `20 passed, 0 failed`.

Manual checks:
1. `/` shows five rows; with MySQL running and the `stockpilot` database created, all are **Pass**.
2. `/health` returns `"status": "ok"` and HTTP 200.
3. `/does-not-exist` shows the 404 page.
4. Stop MySQL in XAMPP and reload `/`: the MySQL row fails with a plain-English hint, `/health` returns HTTP 503.
5. Look in `storage/logs/` for the logged connection error.

## Common errors and fixes

| Symptom | Cause and fix |
|---|---|
| `'php' is not recognized` | PHP is not on PATH. Run `$env:Path += ";C:\xampp\php"`. |
| Apache shows the XAMPP dashboard or a directory listing | The folder is not named `stockpilot-360`, or `.htaccess` is ignored. Use the URL matching the folder name and set `AllowOverride All`. |
| 404 on `/health` under Apache | `mod_rewrite` is off or `AllowOverride` is `None`. See step 3. |
| CSS missing (unstyled page) | Same cause as above, or you opened `index.php` through a `file://` path. Use the `http://localhost/...` URL. |
| MySQL row: *Unknown database* | Create the `stockpilot` database (step 4) or fix `DB_DATABASE` in `.env`. |
| MySQL row: *Access denied* | Fix `DB_USERNAME` / `DB_PASSWORD` in `.env`. |
| MySQL row: *Cannot reach MySQL* | Start MySQL in XAMPP. If port 3306 is taken, change the port in XAMPP and set `DB_PORT`. |
| MySQL won't start in XAMPP | Another MySQL service owns port 3306. Stop it in `services.msc`, or change the XAMPP port. |
| Required extension missing | In `C:\xampp\php\php.ini` remove the leading `;` from `extension=pdo_mysql`, `extension=mbstring`, `extension=fileinfo`, then restart Apache. |
| Blank page | Set `APP_DEBUG=true` in `.env`, or read `storage/logs/php-errors.log`. |

## Git

```powershell
git init
git add .
git status        # confirm .env is NOT listed
git commit -m "Stage 1: project foundation, PDO connection, router and system check"
```
Create an empty repository on GitHub, then:
```powershell
git branch -M main
git remote add origin https://github.com/<your-username>/stockpilot-360.git
git push -u origin main
```

## Next: Stage 2 — database migrations and seed data
Stage 2 adds the SQL migrations for the full schema (the 29 listed tables plus the justified extras), a small migration runner, and demo seeders.
