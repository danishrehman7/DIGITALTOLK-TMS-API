<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Locale;
use App\Models\TranslationKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Translation> */
class TranslationFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'locale_id' => Locale::query()->inRandomOrder()->value('id') ?? Locale::factory()->create()->id,
            'translation_key_id' => TranslationKey::query()->inRandomOrder()->value('id') ?? TranslationKey::factory()->create()->id,
            'content' => fake()->sentence(),
        ];
    }
}
