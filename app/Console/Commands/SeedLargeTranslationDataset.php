<?php

namespace App\Console\Commands;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedLargeTranslationDataset extends Command
{
    protected $signature = 'translations:seed-large {count=100000}';

    protected $description = 'Seed a large translation dataset for performance testing.';

    public function handle(): int
    {
        $count = (int) $this->argument('count');

        $this->info("Seeding {$count} translations...");

        DB::disableQueryLog();

        $localeCodes = ['en', 'fr', 'es', 'de', 'it'];

        $locales = collect($localeCodes)->mapWithKeys(function (string $code) {
            $locale = Locale::firstOrCreate(
                ['code' => $code],
                ['name' => strtoupper($code)]
            );

            return [$code => $locale->id];
        });

        $tags = collect(['web', 'mobile', 'desktop', 'admin', 'checkout'])->mapWithKeys(function (string $name) {
            $tag = Tag::firstOrCreate(['name' => $name]);

            return [$name => $tag->id];
        });

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $batchSize = 1000;
        $created = 0;

        while ($created < $count) {
            $currentBatchSize = min($batchSize, $count - $created);

            $keyRows = [];
            $translationRows = [];
            $now = now();

            for ($i = 0; $i < $currentBatchSize; $i++) {
                $number = $created + $i + 1;
                $keyRows[] = [
                    'key' => "section{$number}.title",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            TranslationKey::insertOrIgnore($keyRows);

            $keys = TranslationKey::query()
                ->whereIn('key', array_column($keyRows, 'key'))
                ->pluck('id', 'key');

            foreach ($keyRows as $keyRow) {
                $localeCode = $localeCodes[array_rand($localeCodes)];

                $translationRows[] = [
                    'locale_id' => $locales[$localeCode],
                    'translation_key_id' => $keys[$keyRow['key']],
                    'content' => 'Translation content for '.$keyRow['key'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            Translation::insert($translationRows);

            $created += $currentBatchSize;
            $bar->advance($currentBatchSize);
        }

        $bar->finish();
        $this->newLine();

        $this->info("Successfully seeded {$count} translations.");

        return self::SUCCESS;
    }
}
