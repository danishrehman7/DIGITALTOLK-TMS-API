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
    public function __construct(private readonly TranslationService $translationService) {}

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

    public function export(string $locale, Request $request): JsonResponse
    {
        $translations = $this->translationService->export(
            $locale,
            $request->query('tag')
        );

        $etag = md5(json_encode($translations));

        return response()
            ->json($translations)
            ->header('Cache-Control', 'public, max-age=60, stale-while-revalidate=300')
            ->header('ETag', $etag);
    }
}
