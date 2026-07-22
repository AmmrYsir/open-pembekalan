# Open Pembekalan

A web-based procurement management system built for the Malaysian public sector, designed to streamline and digitize the end-to-end procurement process across government agencies.

> **Pembekalan** is a Malay term referring to the supply and procurement process within government operations.

## Overview

Open Pembekalan manages the full lifecycle of government procurement — from acquisition initiation (sebutharga / tender), agency and supplier registration, committee assignments, MOF code classification, all the way through to reporting and notifications.

### Key Modules

| Module | Description |
|---|---|
| **Acquisition** | Manage procurement requests — Quotation (Sebutharga) and Tender (Lembaga Tender) |
| **Agencies & Subagencies** | Register and manage government agencies and their sub-units |
| **Suppliers** | Vendor registry and supplier management |
| **Committees** | Tender committee setup and assignment management |
| **Agency Officers** | Designated officers per agency overseeing procurement activities |
| **MOF Codes** | Ministry of Finance (MOF) budget classification codes by category and subcategory |
| **VOT Types** | Vote/expenditure type management for budget allocation |
| **States** | Malaysian states and territories reference data |
| **Notifications** | System notifications with email tracking and delivery logs |
| **Linked Accounts** | Multi-account linking support |
| **Feature Flags** | Gradual feature rollout via Laravel Pennant |
| **Queues** | Job queue monitoring and management |

### Tech Stack

- **Backend:** [Laravel 13](https://laravel.com) — PHP 8.4
- **Frontend:** [Livewire 4](https://livewire.laravel.com) + [Tailwind CSS 4](https://tailwindcss.com)
- **Bundler:** [Vite](https://vitejs.dev) via [Bun](https://bun.sh)
- **Database:** MariaDB / MySQL
- **Testing:** [Pest PHP](https://pestphp.com)
- **State Management:** [Spatie Model States](https://spatie.be/docs/laravel-model-states)
- **Email Dev:** Mailtrap

## Prerequisites

- **PHP** >= 8.4 (with extensions: curl, xml, zip, mbstring, mysql, sqlite3)
- **Composer** (PHP package manager)
- **Bun** (JavaScript runtime & package manager — v1.x)
- **MariaDB** >= 10.x or MySQL >= 8.0
- **Node.js** >= 20 (optional — Bun handles all JS dependencies)

## Getting Started

### 1. Clone & Install

```bash
git clone https://github.com/AmmrYsir/open-pembekalan.git
cd open-pembekalan

# Install PHP dependencies
composer install

# Install frontend dependencies
bun install

# Copy environment file
cp .env.example .env
```

### 2. Configure Environment

Edit `.env` with your database and mail settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=openpembekal
DB_USERNAME=root
DB_PASSWORD=
```

For email testing, Mailtrap is pre-configured:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
```

### 3. Generate App Key & Run Migrations

```bash
php artisan key:generate
php artisan migrate
```

### 4. Start the Dev Server

```bash
# Start all services (PHP server + queue listener + Vite)
composer run dev

# Or start them individually:
php artisan serve           # Laravel dev server
bun run dev                 # Vite hot-reload
php artisan queue:listen    # Queue worker
```

### 5. Access the Application

Open **http://localhost:8000** in your browser.

## Development

### Commands

```bash
composer run dev          # Start dev servers (PHP + queue + Vite)
composer run lint         # Format PHP code with Laravel Pint
composer run test         # Run full test suite
composer run types:check  # Run PHPStan static analysis
bun run build             # Build frontend assets for production
```

### Testing

```bash
# Run all tests
php artisan test --compact

# Run a specific test
php artisan test --compact --filter=test_name
```

### Code Style

This project uses [Laravel Pint](https://laravel.com/docs/pint) with a PSR-12-based coding standard. Run before committing:

```bash
vendor/bin/pint --format agent
```

## Deployment

The application can be deployed via [Laravel Cloud](https://cloud.laravel.com/), the officially recommended deployment platform for Laravel applications. For traditional server setups, point your web root to the `public/` directory and configure a queue worker.

## License

[MIT](LICENSE)
