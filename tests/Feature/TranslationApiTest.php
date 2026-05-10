<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\TranslationKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_translation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/translations', [
            'locale' => 'en',
            'key' => 'home.title',
            'content' => 'Welcome Home',
            'tags' => ['web', 'desktop'],
        ]);

        $response->assertCreated()
            ->assertJsonPath('content', 'Welcome Home')
            ->assertJsonPath('locale.code', 'en')
            ->assertJsonPath('translation_key.key', 'home.title');
    }

    public function test_guest_cannot_create_translation(): void
    {
        $this->postJson('/api/translations', [
            'locale' => 'en',
            'key' => 'home.title',
            'content' => 'Welcome Home',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_search_by_tag(): void
    {
        $user = User::factory()->create();
        $translation = $this->createTranslation('en', 'checkout.title', 'Checkout Page');
        $tag = Tag::create(['name' => 'web']);
        $translation->tags()->attach($tag);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/translations?tag=web');

        $response->assertOk()
            ->assertJsonPath('data.0.content', 'Checkout Page');
    }

    public function test_authenticated_user_can_export_locale_json(): void
    {
        $user = User::factory()->create();
        $this->createTranslation('en', 'home.title', 'Welcome Home');
        $this->createTranslation('en', 'home.subtitle', 'Fast Translation API');

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/translations/export/en');

        $response->assertOk()
            ->assertHeader('Cache-Control')
            ->assertJsonPath('home.title', 'Welcome Home')
            ->assertJsonPath('home.subtitle', 'Fast Translation API');
    }

    public function test_authenticated_user_can_update_translation(): void
    {
        $user = User::factory()->create();
        $translation = $this->createTranslation('en', 'home.title', 'Old Content');

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/translations/{$translation->id}", [
            'content' => 'Updated Content',
            'tags' => ['mobile'],
        ]);

        $response->assertOk()
            ->assertJsonPath('content', 'Updated Content')
            ->assertJsonPath('tags.0.name', 'mobile');
    }

    public function test_export_endpoint_is_fast_for_small_dataset(): void
    {
        $user = User::factory()->create();

        for ($i = 1; $i <= 100; $i++) {
            $this->createTranslation('en', "key.{$i}", "Content {$i}");
        }

        $start = microtime(true);

        $this->actingAs($user, 'sanctum')->getJson('/api/translations/export/en')->assertOk();

        $durationMs = (microtime(true) - $start) * 1000;

        $this->assertLessThan(500, $durationMs);
    }

    private function createTranslation(string $localeCode, string $keyName, string $content): Translation
    {
        $locale = Locale::firstOrCreate(['code' => $localeCode]);
        $key = TranslationKey::firstOrCreate(['key' => $keyName]);

        return Translation::create([
            'locale_id' => $locale->id,
            'translation_key_id' => $key->id,
            'content' => $content,
        ]);
    }
}
