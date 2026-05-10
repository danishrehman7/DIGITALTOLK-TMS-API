<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('locales', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('translation_keys', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 191)->unique();
            $table->timestamps();
        });

        Schema::create('translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('locale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('translation_key_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();

            $table->unique(['locale_id', 'translation_key_id'], 'translations_locale_key_unique');
            $table->index(['locale_id', 'updated_at'], 'translations_locale_updated_index');
            $table->fullText('content');
        });

        Schema::create('tags', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 80)->unique();
            $table->timestamps();
        });

        Schema::create('tag_translation', function (Blueprint $table): void {
            $table->foreignId('translation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();

            $table->primary(['translation_id', 'tag_id']);
            $table->index(['tag_id', 'translation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_translation');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('translations');
        Schema::dropIfExists('translation_keys');
        Schema::dropIfExists('locales');
    }
};
