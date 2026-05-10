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
- Seeder/factory support for 100k+ records
- Docker environment for quick setup
- OpenAPI/Swagger documentation
- Unit and feature tests

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

---

## Main Features

### Translation Management

Each translation belongs to:

- A locale
- A translation key
- Translation content
- Optional tags

Example request:

```json
{
  "locale": "en",
  "key": "home.title",
  "content": "Welcome Home",
  "tags": ["web", "desktop"]
}
```

---

### Frontend JSON Export

The export endpoint returns translations as nested JSON.

Example endpoint:

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

This structure is easy to use in frontend i18n systems.

---

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

This design avoids duplicated locale/key/tag data and allows future scalability.

Common lookup columns are indexed for better performance.

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

---

## Quick Start with Docker

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

## Run Tests

Run all tests:

```bash
docker compose exec app php artisan test
```

Run only translation tests:

```bash
docker compose exec app php artisan test --filter=Translation
```

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

Copy the generated token.

Use it in API requests as:

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
4. Execute the API requests

---

## Seeder for Scalability Testing

The project includes seeders/factories to generate large translation datasets.

To reset and seed the database:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

The seeder can be configured to generate 100k+ translation records for scalability testing.

---

## Performance Considerations

The project was designed with performance in mind:

- Normalized database schema
- Indexed lookup columns
- Paginated listing endpoint
- Efficient Eloquent relationships
- Optimized export query
- Redis-ready Docker environment
- CDN-friendly response headers
- JSON export endpoint with `Cache-Control` and `ETag` headers

---

## Useful Docker Commands

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

Rebuild containers:

```bash
docker compose up -d --build
```

Run migrations again:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Run tests:

```bash
docker compose exec app php artisan test
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

## Design Choices

### Normalized Database Schema

Locales, translation keys, and tags are stored separately to avoid duplication and support future scalability.

### Service Layer

Translation business logic is handled in a service class instead of placing all logic inside controllers. This keeps controllers clean and makes the code easier to test.

### Form Request Validation

Validation is handled through dedicated request classes for clean and reusable validation logic.

### Sanctum Authentication

Laravel Sanctum is used for secure token-based API access.

### Nested JSON Export

Translation keys such as:

```text
home.title
button.submit
```

are exported as nested JSON:

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

This is easier for frontend applications to consume.

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

Run tests with:

```bash
docker compose exec app php artisan test
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
