# DigitalTolk Translation Management API

A Laravel-based API-driven Translation Management Service built for the DigitalTolk Senior Laravel Developer code test.

This project provides a scalable API for storing, managing, searching, tagging, and exporting translations for frontend applications such as Vue.js, React, and mobile apps.

---

## Repository

```text
https://github.com/danishrehman7/DIGITALTOLK-TMS-API
```

---

## Project Overview

The goal of this project is to build a translation management service with clean Laravel architecture, secure API authentication, scalable database design, optimized queries, and Docker-based setup for easy review.

The application supports:

- Multiple locales such as `en`, `fr`, `es`
- Dynamic translation keys such as `home.title`, `button.submit`
- Context tags such as `web`, `mobile`, `desktop`
- Token-based API authentication using Laravel Sanctum
- Translation CRUD APIs
- Search by locale, key, tag, or content
- JSON export endpoint for frontend applications
- Seeder/command support for generating 100k+ records
- Docker environment for quick setup
- MySQL and Redis services
- OpenAPI/Swagger documentation
- Unit, feature, and performance-focused tests
- CDN-friendly export response headers

---

## Tech Stack

- PHP 8.3
- Laravel 11
- MySQL 8
- Redis
- Laravel Sanctum
- Docker
- Nginx
- PHPUnit
- Xdebug for coverage reports

---

## Main Features

### Translation Management

Each translation belongs to a locale, translation key, translation content, and optional context tags.

Example request:

```json
{
  "locale": "en",
  "key": "home.title",
  "content": "Welcome Home",
  "tags": ["web", "desktop"]
}
```

### Frontend JSON Export

The export endpoint returns translations as nested JSON.

```text
GET /api/translations/export/en
```

Example response:

```json
{
  "home": {
    "title": "Welcome Home",
    "subtitle": "Fast Translation API"
  },
  "button": {
    "submit": "Submit"
  }
}
```

The export endpoint also returns CDN-friendly headers:

```text
Cache-Control
ETag
```

### Authentication

The API is protected using Laravel Sanctum.

Protected endpoints require:

```text
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

---

## Database Design

The database uses a normalized schema:

- `locales`
- `translation_keys`
- `translations`
- `tags`
- `tag_translation`

This avoids duplicated locale/key/tag data and supports future scalability. Common lookup columns are indexed for better query performance.

---

## API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/translations` | List and search translations |
| POST | `/api/translations` | Create a translation |
| GET | `/api/translations/{id}` | View a translation |
| PUT/PATCH | `/api/translations/{id}` | Update a translation |
| DELETE | `/api/translations/{id}` | Delete a translation |
| GET | `/api/translations/export/{locale}` | Export translations for a locale |

### Search Examples

```text
/api/translations?locale=en
/api/translations?key=home.title
/api/translations?tag=web
/api/translations?content=Welcome
```

---

## Docker Setup

This project includes Docker so reviewers can run the application without manually installing PHP, MySQL, Redis, or Nginx.

### Requirements

- Docker Desktop
- Docker Compose

### Quick Start with Docker

Clone the repository:

```bash
git clone https://github.com/danishrehman7/DIGITALTOLK-TMS-API.git
cd DIGITALTOLK-TMS-API
```

Create the environment file:

```bash
cp .env.example .env
```

For Windows CMD:

```cmd
copy .env.example .env
```

Build and start containers:

```bash
docker compose up -d --build
```

Install Composer dependencies inside the container:

```bash
docker compose exec app composer install
```

Generate Laravel application key:

```bash
docker compose exec app php artisan key:generate
```

Run database migrations and seeders:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Clear Laravel cache:

```bash
docker compose exec app php artisan optimize:clear
```

The application will be available at:

```text
http://127.0.0.1:8000
```

---

## Docker Environment Services

| Service | Description |
|---|---|
| `app` | PHP 8.3 FPM Laravel application |
| `nginx` | Web server exposed on port `8000` |
| `mysql` | MySQL 8 database |
| `redis` | Redis service for cache-ready setup |

---

## Environment Configuration

For Docker, `.env` should use:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=digitaltolk
DB_USERNAME=digitaltolk
DB_PASSWORD=secret

CACHE_STORE=redis
QUEUE_CONNECTION=sync

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Inside Docker, `DB_HOST` must be `mysql`, not `localhost` or `127.0.0.1`.

---

## Generate API Token

Open Laravel Tinker:

```bash
docker compose exec app php artisan tinker
```

Create a reviewer user and token:

```php
$user = \App\Models\User::factory()->create([
    'name' => 'Reviewer',
    'email' => 'reviewer@example.com',
    'password' => bcrypt('password'),
]);

$token = $user->createToken('api-token')->plainTextToken;

$token;
```

Copy the generated token and use it in API requests:

```text
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

---

## Example API Requests

Replace `YOUR_TOKEN` with the generated Sanctum token.

### Create Translation

```bash
curl -X POST "http://127.0.0.1:8000/api/translations" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"locale":"en","key":"home.title","content":"Welcome Home","tags":["web","desktop"]}'
```

### List Translations

```bash
curl -X GET "http://127.0.0.1:8000/api/translations" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Search by Tag

```bash
curl -X GET "http://127.0.0.1:8000/api/translations?tag=web" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Export Locale JSON

```bash
curl -X GET "http://127.0.0.1:8000/api/translations/export/en" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## OpenAPI / Swagger Documentation

OpenAPI documentation is available at:

```text
docs/openapi.yaml
```

If Swagger UI is installed, it can be accessed at:

```text
http://127.0.0.1:8000/api/documentation
```

In Swagger UI:

1. Click **Authorize**
2. Paste only the token
3. Do not manually write `Bearer`
4. Execute API requests

---

## Run Tests

Run all tests normally:

```bash
docker compose exec -e XDEBUG_MODE=off app php artisan test
```

Run only translation tests:

```bash
docker compose exec -e XDEBUG_MODE=off app php artisan test --filter=Translation
```

Run coverage report:

```bash
docker compose exec -e XDEBUG_MODE=coverage app php artisan test --coverage
```

Performance timing should be measured with `XDEBUG_MODE=off`. Xdebug coverage mode slows PHP execution, so timing-based performance tests should not be judged while coverage is active.

---

## Performance and Scalability Testing

The application includes performance-focused tests for:

- Translation listing response time
- JSON export response time
- Authenticated API access
- Search and filtering

Run performance tests without Xdebug coverage:

```bash
docker compose exec -e XDEBUG_MODE=off app php artisan test --filter=TranslationPerformanceTest
```

---

## Large Dataset Seeding

Generate 100k translation records:

```bash
docker compose exec app php artisan translations:seed-large 100000
```

Generate a custom amount:

```bash
docker compose exec app php artisan translations:seed-large 250000
```

This command is intended for scalability and performance testing. It uses batch inserts to avoid unnecessary memory usage.

---

## Export Benchmark

Run benchmark without Xdebug coverage:

```bash
docker compose exec -e XDEBUG_MODE=off app php artisan translations:benchmark-export en
```

---

## Performance Considerations

The project was designed with performance in mind:

- Normalized database schema
- Indexed lookup columns
- Paginated listing endpoint
- Query-builder based export for reduced Eloquent overhead
- Chunked export processing for larger datasets
- Efficient relationship loading where needed
- Redis-ready Docker environment
- CDN-friendly `Cache-Control` and `ETag` headers
- JSON export endpoint suitable for frontend caching

---

## Useful Docker Commands

Start containers:

```bash
docker compose up -d
```

Build and start containers:

```bash
docker compose up -d --build
```

Stop containers:

```bash
docker compose down
```

Stop containers and remove database volume:

```bash
docker compose down -v
```

View app logs:

```bash
docker compose logs -f app
```

View Nginx logs:

```bash
docker compose logs -f nginx
```

View MySQL logs:

```bash
docker compose logs -f mysql
```

Run migrations again:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Clear cache:

```bash
docker compose exec app php artisan optimize:clear
```

Run tests:

```bash
docker compose exec -e XDEBUG_MODE=off app php artisan test
```

---

## Local Setup Without Docker

Requirements:

- PHP 8.2 or higher
- Composer
- MySQL 8
- Redis optional

Install dependencies:

```bash
composer install
```

Copy environment file:

```bash
cp .env.example .env
```

For Windows CMD:

```cmd
copy .env.example .env
```

Generate app key:

```bash
php artisan key:generate
```

Configure your `.env` database values, then run:

```bash
php artisan migrate:fresh --seed
```

Start local server:

```bash
php artisan serve
```

Run tests:

```bash
php artisan test
```

---

## Code Quality

Run Laravel Pint for PSR-12 formatting:

```bash
docker compose exec app ./vendor/bin/pint
```

Or locally:

```bash
vendor/bin/pint
```

---

## Design Choices

### Normalized Database Schema

Locales, translation keys, and tags are stored separately to avoid duplication and support future scalability.

### Service Layer

Translation business logic is handled in a service class instead of placing all logic inside controllers. This keeps controllers clean and makes the code easier to test.

### Form Request Validation

Validation is handled through dedicated request classes for clean and reusable validation logic.

### Sanctum Authentication

Laravel Sanctum is used for secure token-based API access.

### Query Builder Export

The translation export logic uses optimized SQL joins through Laravel's query builder to reduce model hydration overhead and improve performance for larger exports.

### Chunked Export Processing

The export process uses chunked processing to avoid loading very large datasets into memory all at once.

### Nested JSON Export

Translation keys such as `home.title` and `button.submit` are exported as nested JSON:

```json
{
  "home": {
    "title": "Welcome Home"
  },
  "button": {
    "submit": "Submit"
  }
}
```

### CDN-Friendly Headers

The export endpoint returns `Cache-Control` and `ETag` headers so it can be integrated with CDN/proxy caching strategies.

---

## Test Coverage

The project includes tests for:

- Translation creation
- Guest authentication protection
- Search by tag
- Locale JSON export
- Translation update
- Export endpoint performance
- Translation service logic

Run coverage report with:

```bash
docker compose exec -e XDEBUG_MODE=coverage app php artisan test --coverage
```

---

## Reviewer Quick Commands

For a clean Docker setup and test run:

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec -e XDEBUG_MODE=off app php artisan test
```

For Windows CMD:

```cmd
copy .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec -e XDEBUG_MODE=off app php artisan test
```

---

## Notes for Reviewer

This project was built for the DigitalTolk Laravel Senior Developer code test.

The implementation focuses on:

- Clean Laravel architecture
- API-first design
- Secure token authentication
- Scalable database schema
- Search and export functionality
- Docker-based setup
- Testable service-oriented code
- Performance-minded API responses
- OpenAPI documentation
