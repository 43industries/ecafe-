# School e-Café

A modern, responsive e-Café ordering system for schools. Students order food through a web portal; staff manage orders and inventory; administrators access analytics and system management.

## Features

- **Student portal:** Menu browse, cart, checkout (Cash/M-Pesa/Card), order tracking, notifications, loyalty points, favorites, coupons
- **Staff portal:** Order queue, accept/reject, status updates, inventory, receipt printing
- **Admin portal:** Dashboard analytics (Chart.js), student/staff/menu CRUD, payments, announcements, PDF/Excel reports
- **Security:** Password hashing, PDO prepared statements, CSRF protection, XSS escaping, role-based access, session management
- **M-Pesa:** Safaricom Daraja STK Push integration
- **SSO-ready:** Pluggable auth provider for future school portal integration

## Tech Stack

- PHP 8.1+, MySQL 8+, Bootstrap 5, JavaScript ES6, Chart.js, Font Awesome
- Composer: Guzzle, PHPMailer, endroid/qr-code, PhpSpreadsheet, Dompdf

## Prerequisites

- [XAMPP](https://www.apachefriends.org/) or similar (Apache + PHP 8.1+ + MySQL)
- [Composer](https://getcomposer.org/)
- Optional: [ngrok](https://ngrok.com/) for M-Pesa callback testing

## Installation

### 1. Clone / copy project

Place the `ecafe` folder in your web server directory, e.g.:
```
C:\xampp\htdocs\ecafe
```

### 2. Install PHP dependencies

```bash
cd ecafe
composer install
```

### 3. Configure environment

```bash
copy .env.example .env
```

Edit `.env` with your database and app settings:

```env
APP_URL=http://localhost/ecafe/public
DB_HOST=127.0.0.1
DB_NAME=ecafe_db
DB_USER=root
DB_PASS=
```

### 4. Create database

Import schema and seed data via phpMyAdmin or MySQL CLI:

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

### 5. Apache configuration

Point your browser to:
```
http://localhost/ecafe/public
```

**Recommended:** Set Apache `DocumentRoot` to `ecafe/public` for cleaner URLs.

Ensure `mod_rewrite` is enabled. The included `.htaccess` handles URL rewriting.

### 6. Folder permissions

Ensure these directories are writable by the web server:
- `storage/logs/`
- `storage/receipts/`
- `storage/uploads/`
- `public/assets/img/qr/`

## Default Login Credentials

| Role    | Username/ID | Password     |
|---------|-------------|--------------|
| Admin   | admin       | Password123! |
| Staff   | staff01     | Password123! |
| Student | STU001      | Password123! |

## M-Pesa Setup (Daraja Sandbox)

1. Register at [Safaricom Daraja](https://developer.safaricom.co.ke/)
2. Create an app and get Consumer Key, Consumer Secret, and Passkey
3. Add credentials to `.env`:

```env
MPESA_CONSUMER_KEY=your_key
MPESA_CONSUMER_SECRET=your_secret
MPESA_PASSKEY=your_passkey
MPESA_SHORTCODE=174379
MPESA_ENV=sandbox
MPESA_CALLBACK_URL=https://your-ngrok-url.ngrok.io/ecafe/public/api/mpesa/callback
```

4. Start ngrok: `ngrok http 80`
5. Update `MPESA_CALLBACK_URL` with the ngrok HTTPS URL
6. At checkout, select M-Pesa and enter a sandbox test number

## Future SSO Integration

Set in `.env`:
```env
SSO_ENABLED=true
SSO_ENDPOINT=https://school-portal.example.com/api/auth
SSO_CLIENT_ID=your_client_id
SSO_CLIENT_SECRET=your_secret
```

Extend `src/Services/SSOAuthProvider.php` to call your school's authentication API and link external IDs via the `sso_tokens` table.

## Project Structure

```
ecafe/
├── public/          # Web root (index.php, assets)
├── config/          # App, database, M-Pesa, mail config
├── database/        # schema.sql, seed.sql
├── src/             # Controllers, Models, Services, Middleware
├── views/           # PHP templates
├── storage/         # Logs, uploads, receipts
├── routes.php       # Route definitions
└── bootstrap.php    # App bootstrap
```

## Deploy to Railway

The repo includes config for [Railway](https://railway.app) deployment with Railpack (FrankenPHP), a MySQL plugin, and persistent volumes for storage.

### 1. Create the Railway project

1. Go to [railway.app](https://railway.app) → **New Project** → **Deploy from GitHub repo** → select this repo
2. Add **MySQL**: **New** → **Database** → **MySQL**
3. Generate a public domain: web service → **Settings** → **Networking** → **Generate Domain**

### 2. Configure environment variables

On the **web service** Variables tab, add:

| Variable | Value |
|----------|-------|
| `RAILPACK_PHP_ROOT_DIR` | `public` |
| `RAILPACK_PHP_EXTENSIONS` | `pdo_mysql,gd,zip,mbstring,dom` |
| `DB_HOST` | `${{MySQL.MYSQLHOST}}` |
| `DB_PORT` | `${{MySQL.MYSQLPORT}}` |
| `DB_NAME` | `${{MySQL.MYSQLDATABASE}}` |
| `DB_USER` | `${{MySQL.MYSQLUSER}}` |
| `DB_PASS` | `${{MySQL.MYSQLPASSWORD}}` |
| `APP_URL` | `https://${{RAILWAY_PUBLIC_DOMAIN}}` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_TIMEZONE` | `Africa/Nairobi` |
| `MPESA_CALLBACK_URL` | `https://${{RAILWAY_PUBLIC_DOMAIN}}/api/mpesa/callback` |

Add M-Pesa and mail credentials when ready (same keys as `.env.example`).

`APP_URL` must **not** include `/public` — Railway serves from the `public` directory directly.

### 3. Volumes

[`railway.toml`](railway.toml) mounts two volumes automatically:

- `ecafe-storage` → `/app/storage` (logs, receipts, uploads)
- `ecafe-qr` → `/app/public/assets/img/qr` (order QR codes)

If volumes are not created on first deploy, add them manually under **Settings** → **Volumes**.

### 4. Initialize the database (one-time)

After the first successful deploy, import schema and seed data. **Run this only once** — the file contains `DROP TABLE` statements.

```bash
npm install -g @railway/cli
railway login
railway link
railway connect mysql < database/railway-init.sql
```

Or use the helper script:

```powershell
# Windows
.\scripts\init-railway-db.ps1
```

```bash
# macOS / Linux
./scripts/init-railway-db.sh
```

Or use the MySQL TCP credentials from the Railway MySQL service **Connect** tab:

```bash
mysql -h <host> -P <port> -u <user> -p<pass> <database> < database/railway-init.sql
```

### 5. Verify deployment

- Home page loads at `https://<your-domain>/`
- Login: `STU001` / `Password123!`
- Place a test order and confirm a QR code is generated
- Redeploy and confirm QR codes and receipts persist (volumes)
- Update Daraja callback URL to `https://<your-domain>/api/mpesa/callback`

### Railway config files

| File | Purpose |
|------|---------|
| `railway.toml` | Build/deploy settings and volume mounts |
| `Caddyfile` | FrankenPHP URL rewriting (front controller) |
| `start-container.sh` | Ensures writable dirs, then starts FrankenPHP |
| `scripts/ensure-storage.sh` | Creates storage directories on volume mount |
| `database/railway-init.sql` | Schema + seed for Railway MySQL (no `CREATE DATABASE`) |
| `scripts/init-railway-db.ps1` | One-time database import (Windows) |
| `scripts/init-railway-db.sh` | One-time database import (macOS/Linux) |
| `scripts/verify-railway-deploy.ps1` | Pre-deploy artifact and syntax check |

## Netlify (hybrid proxy)

This PHP app cannot run natively on Netlify. Use Netlify as a **reverse proxy** in front of Railway instead.

See **[docs/NETLIFY.md](docs/NETLIFY.md)** for the full hybrid setup guide.

```powershell
.\scripts\setup-netlify-hybrid.ps1 `
  -RailwayBackendUrl "https://your-app.up.railway.app" `
  -NetlifyDomain "https://your-site.netlify.app"
```

## API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/cart/add` | POST | Add item to cart |
| `/api/cart/update` | POST | Update cart quantity |
| `/api/menu/search` | GET | Search menu items |
| `/api/orders/status/{id}` | GET | Poll order status |
| `/api/notifications` | GET | Get notifications |
| `/api/favorites/toggle` | POST | Toggle favorite |
| `/api/mpesa/stk-push` | POST | Initiate M-Pesa payment |
| `/api/mpesa/callback` | POST | M-Pesa webhook |
| `/api/admin/charts` | GET | Admin chart data |

## License

Educational project — free to use and modify for school purposes.
