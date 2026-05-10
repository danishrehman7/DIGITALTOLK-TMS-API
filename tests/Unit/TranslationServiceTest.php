<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Locale;
use App\Models\TranslationKey;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_creates_translation_with_locale_key_and_tags(): void
    {
        $service = app(TranslationService::class);

        $translation = $service->create([
            'locale' => 'fr',
            'key' => 'common.save',
            'content' => 'Enregistrer',
            'tags' => ['web'],
        ]);

        $this->assertSame('fr', $translation->locale->code);
        $this->assertSame('common.save', $translation->translationKey->key);
        $this->assertSame('Enregistrer', $translation->content);
        $this->assertSame('web', $translation->tags->first()->name);
    }

    public function test_service_exports_translations_as_key_value_array(): void
    {
        $service = app(TranslationService::class);
        $locale = Locale::create(['code' => 'es']);
        $key = TranslationKey::create(['key' => 'button.submit']);

        $locale->translations()->create([
            'translation_key_id' => $key->id,
            'content' => 'Enviar',
        ]);

        $export = $service->export('es');

        $this->assertSame(
            [
                'button' => [
                    'submit' => 'Enviar',
                ],
            ],
            $export
        );
    }
}
