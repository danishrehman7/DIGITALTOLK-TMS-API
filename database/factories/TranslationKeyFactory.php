<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TranslationKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TranslationKey> */
class TranslationKeyFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(3).'.'.fake()->unique()->word(),
        ];
    }
}
