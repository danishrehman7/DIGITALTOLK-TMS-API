<?php

namespace App\Console\Commands;

use App\Services\TranslationService;
use Illuminate\Console\Command;

class BenchmarkTranslationExport extends Command
{
    protected $signature = 'translations:benchmark-export {locale=en}';

    protected $description = 'Benchmark the translation export endpoint/service.';

    public function handle(TranslationService $translationService): int
    {
        $locale = (string) $this->argument('locale');

        $start = microtime(true);

        $translations = $translationService->export($locale);

        $durationMs = round((microtime(true) - $start) * 1000, 2);

        $this->info("Exported " . count($translations) . " top-level translation groups.");
        $this->info("Duration: {$durationMs}ms");

        if ($durationMs <= 500) {
            $this->info('PASS: Export completed under 500ms.');
        } else {
            $this->warn('WARNING: Export took longer than 500ms.');
        }

        return self::SUCCESS;
    }
}