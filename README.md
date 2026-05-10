# DigitalTolk Translation Management API - Laravel Implementation

A Laravel API-driven Translation Management Service built for the DigitalTolk senior developer code test.

## Features

- Token-based API authentication using Laravel Sanctum
- Store translations for unlimited locales: `en`, `fr`, `es`, etc.
- Tag translations by context: `mobile`, `desktop`, `web`, etc.
- Create, update, view, delete, and search translations
- Search by translation key, locale, content, and tags
- Fast JSON export endpoint for frontend apps such as Vue.js
- Optimized database indexes for large datasets
- Seeder/factory support for 100k+ records
- Docker setup
- OpenAPI documentation
- Unit, feature, and basic performance tests

## Quick Setup

```bash
composer create-project laravel/laravel digitaltolk-translation-api
cd digitaltolk-translation-api
```

Copy the files from this package into the Laravel project.

Install Sanctum if your Laravel project does not already include it:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Configure `.env`:

```env
APP_NAME="DigitalTolk Translation API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=digitaltolk_translations
DB_USERNAME=root
DB_PASSWORD=password

CACHE_STORE=redis
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```

Run migrations and seed data:

```bash
php artisan migrate
php artisan db:seed --class=TranslationSeeder
```

Create a test user/token:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::factory()->create(['email' => 'admin@example.com']);
$token = $user->createToken('api-token')->plainTextToken;
echo $token;
```

Run the API:

```bash
php artisan serve
```

Use this header for protected routes:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

## API Endpoints

### Authentication

Sanctum token is used for API protection. Generate a token with Tinker or implement a login endpoint if required.

### Create Translation

```http
POST /api/translations
```

```json
{
  "locale": "en",
  "key": "home.title",
  "content": "Welcome Home",
  "tags": ["web", "desktop"]
}
```

### Update Translation

```http
PUT /api/translations/{translation}
```

```json
{
  "content": "Welcome to our website",
  "tags": ["web", "mobile"]
}
```

### View Translation

```http
GET /api/translations/{translation}
```

### Search Translations

```http
GET /api/translations?locale=en&key=home&content=welcome&tag=web
```

### Export JSON

```http
GET /api/translations/export/en
```

Response:

```json
{
  "home.title": "Welcome Home",
  "home.subtitle": "Fast translation service"
}
```

Optional tag filter:

```http
GET /api/translations/export/en?tag=web
```

## Design Choices

### Database Schema

The schema uses normalized tables:

- `locales`: stores language codes.
- `translation_keys`: stores reusable translation keys.
- `translations`: stores translated content for each key and locale.
- `tags`: stores context tags.
- `tag_translation`: pivot table for many-to-many translation tags.

This avoids duplicated keys/locales and keeps filtering scalable.

### Performance Choices

- Unique indexes on locale codes, translation keys, and `(locale_id, translation_key_id)`.
- Full-text index on translation content for faster content search.
- Composite index on export-critical columns.
- Export endpoint uses `pluck()` instead of loading full Eloquent models.
- Export responses include cache-friendly headers and ETag support.
- For production, place CDN/proxy cache in front of export endpoint.

### CDN Support

The export endpoint returns:

```http
Cache-Control: public, max-age=60, stale-while-revalidate=300
ETag: generated_hash
```

Because the endpoint must always return updated translations whenever requested, the ETag is based on the locale plus latest `updated_at` value. Clients/CDNs can revalidate quickly.

## Docker

```bash
docker compose up -d --build
```

Then run:

```bash
docker compose exec app php artisan migrate --seed
```

## Tests

```bash
php artisan test
```

For coverage:

```bash
php artisan test --coverage
```

## Notes

This implementation is designed to satisfy the assessment requirements while keeping code clean, testable, scalable, and easy to explain in an interview.
