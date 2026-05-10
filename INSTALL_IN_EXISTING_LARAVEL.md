# Install Into Existing Laravel Project

1. Create a fresh Laravel project.
2. Install Sanctum if not already installed.
3. Copy these folders/files into your project:

- app/Models/Locale.php
- app/Models/Translation.php
- app/Models/TranslationKey.php
- app/Models/Tag.php
- app/Services/TranslationService.php
- app/Http/Requests/*TranslationRequest.php
- app/Http/Controllers/Api/TranslationController.php
- database/migrations/2026_05_09_000001_create_translation_tables.php
- database/seeders/TranslationSeeder.php
- routes/api.php
- tests/Feature/TranslationApiTest.php
- tests/Unit/TranslationServiceTest.php
- docker-compose.yml
- docker/*
- docs/openapi.yaml

4. Run:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
php artisan db:seed --class=TranslationSeeder
php artisan test
```

5. Generate a token:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::factory()->create(['email' => 'admin@example.com']);
$token = $user->createToken('api-token')->plainTextToken;
echo $token;
```
