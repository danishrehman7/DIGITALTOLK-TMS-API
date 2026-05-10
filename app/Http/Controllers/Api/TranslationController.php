<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function __construct(private readonly TranslationService $translationService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $translations = $this->translationService->search($request->query());

        return response()->json($translations);
    }

    public function store(StoreTranslationRequest $request): JsonResponse
    {
        $translation = $this->translationService->create($request->validated());

        return response()->json($translation, 201);
    }

    public function show(Translation $translation): JsonResponse
    {
        return response()->json($translation->load(['locale', 'translationKey', 'tags']));
    }

    public function update(UpdateTranslationRequest $request, Translation $translation): JsonResponse
    {
        $translation = $this->translationService->update($translation, $request->validated());

        return response()->json($translation);
    }

    public function destroy(Translation $translation): JsonResponse
    {
        $translation->delete();

        return response()->json(status: 204);
    }

    public function export(Request $request, string $locale): JsonResponse
    {
        $tag = $request->query('tag');
        $latestUpdate = $this->translationService->latestUpdateTimestamp($locale, $tag);
        $etag = sha1($locale . '|' . $tag . '|' . $latestUpdate);

        if ($request->headers->get('If-None-Match') === $etag) {
            return response()->json(null, 304);
        }

        $translations = $this->translationService->export($locale, $tag);

        return response()
            ->json($translations)
            ->setEtag($etag)
            ->header('Cache-Control', 'public, max-age=60, stale-while-revalidate=300');
    }
}
