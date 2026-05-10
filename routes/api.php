<?php

declare(strict_types=1);

use App\Http\Controllers\Api\TranslationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('translations', TranslationController::class);
    Route::get('translations/export/{locale}', [TranslationController::class, 'export'])
        ->name('translations.export');
});
