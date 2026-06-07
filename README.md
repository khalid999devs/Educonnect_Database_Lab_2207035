# EduConnect API

Personalized Academic Resource and Research Management System.

EduConnect is a Database Lab MVP backend for helping students manage academic profiles, resources, templates, documents, research topics, and personalized recommendations. This repository currently focuses only on the Laravel REST API and Oracle Database layer.

## Current Scope

- Laravel REST API backend
- Oracle Database connection through Laravel-OCI8
- Raw Oracle SQL and PL/SQL scripts for lab evaluation
- Postman/Insomnia-testable API endpoints
- Clean backend structure prepared for a future Next.js frontend

Not included in the current lab phase:

- Next.js frontend
- Real AI extraction
- Real payment gateway
- Mobile app features
- Real-time chat or mentor booking

## Tech Stack

- PHP 8.3+
- Laravel 13
- Oracle Instant Client
- PHP OCI8 extension
- `yajra/laravel-oci8`
- Oracle Autonomous Database
- Raw Oracle SQL and PL/SQL

## Planned Features

- Student registration and login
- Student academic onboarding
- Academic document metadata storage
- Simulated extracted document data
- Academic resources and tools
- Template marketplace records
- Saved resources and templates
- Simulated template purchases
- Research topics and collections
- Reviews and ratings
- Dashboard and recommendation endpoints
- Admin approval flow
- Oracle views, functions, procedures, triggers, and cursor reports

## Project Structure

```text
app/
  Http/Controllers/Api/
  Support/
config/
  oracle.php
database/
  oracle/
docs/
routes/
  api.php
```

Raw database scripts must live in `database/oracle`. Laravel migrations are not the main database implementation for this lab.

## Oracle Wallet Rule

Do not put Oracle wallet files inside this repository.

Use an external wallet path such as:

```text
/Users/khalid999devs/oracle/wallets/educonnect
```

The repository only stores the placeholder `TNS_ADMIN` variable in `.env.example`.

## Environment Setup

Copy the example environment file:

```bash
cp .env.example .env
php artisan key:generate
```

Set these local values in `.env`:

```env
APP_NAME=EduConnect
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=oracle
DB_TNS=educonnectdb_low
DB_USERNAME=educonnect
DB_PASSWORD=your_local_database_password
DB_CHARSET=AL32UTF8
TNS_ADMIN=/absolute/path/outside/repository/oracle/wallets/educonnect

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
CACHE_STORE=file
```

Never commit `.env`, Oracle wallet files, passwords, keys, or cloud credentials.

## Install Dependencies

```bash
composer install
```

Required local Oracle tools:

- Oracle Instant Client
- Oracle SQL*Plus, optional but useful
- PHP OCI8 extension

Verify OCI8:

```bash
php --ri oci8
composer check-platform-reqs
```

## Run The API

```bash
php artisan serve
```

Health check:

```text
http://127.0.0.1:8000/api/v1/health/database
```

If port `8000` is already busy, run:

```bash
php artisan serve --host=127.0.0.1 --port=8001
```

Expected response:

```json
{
  "success": true,
  "message": "Oracle database connection is healthy",
  "data": {
    "default_connection": "oracle",
    "oracle_connection_configured": true,
    "laravel_oci8_installed": true,
    "oci8_extension_loaded": true,
    "tns_alias_configured": true,
    "oracle_username_configured": true,
    "oracle_password_configured": true,
    "tns_admin_configured": true,
    "wallet_tnsnames_present": true
  }
}
```

## Test Commands

```bash
php artisan test
php artisan route:list --path=api/v1
composer validate --strict
composer check-platform-reqs
```

## Database Script Order

The Oracle scripts will be created and executed in this order:

```text
01_create_tables.sql
02_insert_seed_data.sql
03_views.sql
04_functions.sql
05_procedures.sql
06_triggers.sql
07_cursors_reports.sql
08_test_queries.sql
```

Use Oracle syntax only. Do not use MySQL syntax such as `AUTO_INCREMENT`, `BOOLEAN`, `TEXT`, `ENUM`, or `NOW()`.

## Current Status

- Laravel API scaffold is ready
- Oracle Instant Client and OCI8 are configured locally
- Laravel-OCI8 is installed
- Real Oracle health endpoint is working
- `database/oracle` is ready for raw SQL and PL/SQL scripts

Next phase: create `database/oracle/01_create_tables.sql`.
