# Educonnect

**Personalized Academic Resource and Research Management System**

Educonnect organizes student academic profiles, documents, learning resources, templates, tools, and research materials. It combines a lightweight Laravel Blade interface with a REST API connected to Oracle Database while keeping its raw Oracle SQL and PL/SQL implementation visible for evaluation.

## Project Scope

The current repository contains the Oracle-backed API and a lightweight Laravel Blade demo interface.

Implemented features:

- User registration and session login
- Browser-based sign-up, sign-in, protected workspace, and sign-out flow
- Academic profile onboarding with university, field, level, and goal selection
- Oracle-backed dashboard with profile completion, activity totals, and recommendations
- Searchable resource, tool, and template catalogs with filters, details, and save actions
- Student onboarding and academic preferences
- Academic document metadata and simulated extracted facts
- Resource, tool, and template catalogues
- Saved resources and templates
- Simulated paid-template purchases
- Research topics and collection items
- Reviews and automatic aggregate ratings
- Student dashboard and personalized recommendations
- Administrator approval and audit records
- Oracle views, functions, procedures, triggers, and cursor reports
- Responsive Blade interface foundation with a reusable design system

## Technology

- PHP 8.3+
- Laravel 13
- Oracle Autonomous Database 19c
- Oracle Instant Client and PHP OCI8
- Laravel-OCI8
- Raw Oracle SQL and PL/SQL
- Laravel Blade, plain CSS, and vanilla JavaScript
- PHPUnit and Laravel Pint

## Project Structure

```text
app/Http/Controllers/Api/   API controllers
app/Http/Requests/          Request validation
app/Models/                 Oracle models and relationships
app/Services/               Procedures, dashboard, and recommendations
app/Support/                JSON response formatting
config/oracle.php           Oracle connection configuration
database/oracle/            Raw SQL, PL/SQL, seed, and test scripts
docs/                       Detailed project documentation
resources/views/            Blade layouts, pages, and UI components
public/assets/              Frontend styles, scripts, and local imagery
routes/api.php              Versioned REST routes
routes/web.php              Demo frontend routes
tests/                      Unit and feature tests
```

Laravel migrations are not the main database implementation. The authoritative database implementation is in `database/oracle`.

## Database Components

The Oracle schema includes:

- 21 tables and 9 supporting indexes
- 5 reporting views
- 5 PL/SQL functions
- 6 transactional procedures
- 5 triggers
- 4 cursor-report procedures
- 21 SQL verification sections

Run the scripts in this order:

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

## API Overview

All application endpoints use the `/api/v1` prefix.

| Area                | Route group                     |
| ------------------- | ------------------------------- |
| Database health     | `/health/database`              |
| Reference options   | `/reference-data`               |
| Authentication      | `/auth/*`                       |
| Student profiles    | `/students/*`                   |
| Academic documents  | `/documents/*`                  |
| Learning resources  | `/resources/*`                  |
| Academic tools      | `/tools/*`                      |
| Templates           | `/templates/*`                  |
| Research workspaces | `/research-topics/*`            |
| Reviews             | `/reviews/*`                    |
| Student dashboard   | `/dashboard/student/{id}`       |
| Recommendations     | `/recommendations/student/{id}` |

The API uses consistent success, validation, authentication, not-found, conflict, and Oracle business-error responses.

The Blade interface starts at `/login`, supports student registration at `/register`, guides new accounts through `/app/onboarding`, and serves the signed-in dashboard at `/app` with Laravel sessions. Discovery pages are available under `/app/resources`, `/app/tools`, and `/app/templates`.

## Local Setup

Install dependencies and create the environment file:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure the Oracle connection in `.env`:

```env
DB_CONNECTION=oracle
DB_TNS=educonnectdb_low
DB_USERNAME=educonnect
DB_PASSWORD=your-local-oracle-password
DB_CHARSET=AL32UTF8
DB_SERVER_VERSION=19c
ORA_MAX_NAME_LEN=128
TNS_ADMIN=/absolute/path/to/external/oracle/wallet
```

Clear cached configuration after changing `.env`:

```bash
php artisan optimize:clear
```

Run the API:

```bash
php artisan serve
```

The default address is `http://127.0.0.1:8000`.

## Oracle Wallet Safety

Keep the Oracle wallet outside this repository. Never commit `.env`, wallet ZIP files, `cwallet.sso`, `ewallet.p12`, `tnsnames.ora`, passwords, or cloud credentials.

## Verification

```bash
curl -s http://127.0.0.1:8000/api/v1/health/database
php artisan route:list --path=api/v1
php artisan test
./vendor/bin/pint --test
composer validate --strict
composer check-platform-reqs
```

API workflows can be tested with Postman or Insomnia using:

```text
http://127.0.0.1:8000/api/v1
```

## Future Direction

The backend is prepared for a future Next.js frontend, production token authentication, cloud document storage, real AI document understanding, real payments, creator analytics, mentor features, notifications, and mobile clients.
