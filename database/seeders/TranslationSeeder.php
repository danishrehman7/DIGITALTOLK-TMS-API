<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $locales = collect(['en', 'fr', 'es', 'de', 'it'])
            ->map(fn (string $code): Locale => Locale::firstOrCreate(['code' => $code]));

        $tags = collect(['web', 'mobile', 'desktop', 'admin', 'checkout'])
            ->map(fn (string $name): Tag => Tag::firstOrCreate(['name' => $name]));

        $totalKeys = 20_000;
        $now = now();

        for ($i = 1; $i <= $totalKeys; $i += 1000) {
            $keys = [];

            for ($j = $i; $j < $i + 1000 && $j <= $totalKeys; $j++) {
                $keys[] = [
                    'key' => "section.{$j}.title",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            TranslationKey::insertOrIgnore($keys);
        }

        TranslationKey::query()
            ->select(['id', 'key'])
            ->chunkById(1000, function ($keys) use ($locales, $tags, $now): void {
                $translations = [];

                foreach ($keys as $key) {
                    foreach ($locales as $locale) {
                        $translations[] = [
                            'locale_id' => $locale->id,
                            'translation_key_id' => $key->id,
                            'content' => "Content for {$key->key} in {$locale->code}",
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                DB::table('translations')->insertOrIgnore($translations);
            });

        Translation::query()
            ->select('id')
            ->chunkById(5000, function ($translations) use ($tags): void {
                $pivotRows = [];

                foreach ($translations as $translation) {
                    $tag = $tags->random();
                    $pivotRows[] = [
                        'translation_id' => $translation->id,
                        'tag_id' => $tag->id,
                    ];
                }

                DB::table('tag_translation')->insertOrIgnore($pivotRows);
            });
    }
}
