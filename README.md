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

## Hosting note

This is a **server-rendered PHP + MySQL** application. It requires a PHP runtime, MySQL database, and writable storage directories.

**Supported:** Local development with [`launch.ps1`](launch.ps1) or XAMPP (see Installation above).

**Not supported:** [Netlify](https://www.netlify.com) and similar static/JAMstack hosts cannot run this codebase as-is — they do not execute PHP or host MySQL. Deploying here would require rebuilding the app (e.g. separate frontend + API + external database).

### Quick local start (Windows)

```powershell
.\launch.ps1
```

Open `http://localhost:8000` and log in with `STU001` / `Password123!`.

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
