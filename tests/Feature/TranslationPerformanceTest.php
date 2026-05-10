<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_translation_index_responds_under_200ms(): void
    {
        $this->skipIfCoverageModeIsEnabled();

        $user = User::factory()->create();

        $service = app(TranslationService::class);

        for ($i = 1; $i <= 500; $i++) {
            $service->create([
                'locale' => 'en',
                'key' => "performance.item{$i}",
                'content' => "Performance item {$i}",
                'tags' => ['web'],
            ]);
        }

        $start = microtime(true);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/translations?tag=web')
            ->assertOk();

        $durationMs = (microtime(true) - $start) * 1000;

        $this->assertLessThan(
            200,
            $durationMs,
            "Translation index endpoint took {$durationMs}ms, expected under 200ms."
        );
    }

    public function test_translation_export_responds_under_500ms(): void
    {
        $this->skipIfCoverageModeIsEnabled();

        $user = User::factory()->create();

        $service = app(TranslationService::class);

        for ($i = 1; $i <= 1000; $i++) {
            $service->create([
                'locale' => 'en',
                'key' => "export.item{$i}",
                'content' => "Export item {$i}",
                'tags' => ['web'],
            ]);
        }

        $start = microtime(true);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/translations/export/en')
            ->assertOk();

        $durationMs = (microtime(true) - $start) * 1000;

        $this->assertLessThan(
            500,
            $durationMs,
            "Export endpoint took {$durationMs}ms, expected under 500ms."
        );
    }

    private function skipIfCoverageModeIsEnabled(): void
    {
        if (getenv('XDEBUG_MODE') === 'coverage') {
            $this->markTestSkipped('Performance timing test is skipped during Xdebug coverage mode.');
        }
    }
}
