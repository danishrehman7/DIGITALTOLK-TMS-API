<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TranslationService
{
    /** @param array<string, mixed> $data */
    public function create(array $data): Translation
    {
        return DB::transaction(function () use ($data): Translation {
            $locale = Locale::firstOrCreate(['code' => $data['locale']]);
            $key = TranslationKey::firstOrCreate(['key' => $data['key']]);

            $translation = Translation::updateOrCreate(
                [
                    'locale_id' => $locale->id,
                    'translation_key_id' => $key->id,
                ],
                ['content' => $data['content']]
            );

            $this->syncTags($translation, Arr::get($data, 'tags', []));

            return $translation->load(['locale', 'translationKey', 'tags']);
        });
    }

    /** @param array<string, mixed> $data */
    public function update(Translation $translation, array $data): Translation
    {
        return DB::transaction(function () use ($translation, $data): Translation {
            if (isset($data['locale'])) {
                $translation->locale_id = Locale::firstOrCreate(['code' => $data['locale']])->id;
            }

            if (isset($data['key'])) {
                $translation->translation_key_id = TranslationKey::firstOrCreate(['key' => $data['key']])->id;
            }

            if (array_key_exists('content', $data)) {
                $translation->content = $data['content'];
            }

            $translation->save();

            if (array_key_exists('tags', $data)) {
                $this->syncTags($translation, $data['tags']);
            }

            return $translation->load(['locale', 'translationKey', 'tags']);
        });
    }

    /** @param array<string, mixed> $filters */
    public function search(array $filters): LengthAwarePaginator
    {
        return Translation::query()
            ->select('translations.*')
            ->with(['locale:id,code', 'translationKey:id,key', 'tags:id,name'])
            ->when($filters['locale'] ?? null, function (Builder $query, string $locale): void {
                $query->whereHas('locale', fn (Builder $q) => $q->where('code', $locale));
            })
            ->when($filters['key'] ?? null, function (Builder $query, string $key): void {
                $query->whereHas('translationKey', fn (Builder $q) => $q->where('key', 'like', "%{$key}%"));
            })
            ->when($filters['content'] ?? null, function (Builder $query, string $content): void {
                $query->where('content', 'like', "%{$content}%");
            })
            ->when($filters['tag'] ?? null, function (Builder $query, string $tag): void {
                $query->whereHas('tags', fn (Builder $q) => $q->where('name', $tag));
            })
            ->latest('translations.updated_at')
            ->paginate((int) ($filters['per_page'] ?? 25));
    }

    /** @return array<string, string> */
    public function export(string $localeCode, ?string $tag = null): array
    {
        $query = DB::table('translations')
            ->join('locales', 'translations.locale_id', '=', 'locales.id')
            ->join('translation_keys', 'translations.translation_key_id', '=', 'translation_keys.id')
            ->where('locales.code', $localeCode)
            ->select([
                'translations.id as translation_id',
                'translation_keys.key',
                'translations.content',
            ]);

        if ($tag !== null) {
            $query->join('tag_translation', 'translations.id', '=', 'tag_translation.translation_id')
                ->join('tags', 'tag_translation.tag_id', '=', 'tags.id')
                ->where('tags.name', $tag);
        }

        $export = [];

        $query->orderBy('translations.id')
            ->chunkById(1000, function ($translations) use (&$export): void {
                foreach ($translations as $translation) {
                    Arr::set($export, $translation->key, $translation->content);
                }
            }, 'translations.id', 'translation_id');

        return $export;
    }

    public function latestUpdateTimestamp(string $localeCode, ?string $tag = null): ?string
    {
        $query = Translation::query()
            ->join('locales', 'locales.id', '=', 'translations.locale_id')
            ->where('locales.code', $localeCode);

        if ($tag !== null) {
            $query->join('tag_translation', 'tag_translation.translation_id', '=', 'translations.id')
                ->join('tags', 'tags.id', '=', 'tag_translation.tag_id')
                ->where('tags.name', $tag);
        }

        return $query->max('translations.updated_at');
    }

    /** @param array<int, string> $tags */
    private function syncTags(Translation $translation, array $tags): void
    {
        $tagIds = collect($tags)
            ->filter()
            ->unique()
            ->map(fn (string $tag): int => Tag::firstOrCreate(['name' => $tag])->id)
            ->values()
            ->all();

        $translation->tags()->sync($tagIds);
    }
}
