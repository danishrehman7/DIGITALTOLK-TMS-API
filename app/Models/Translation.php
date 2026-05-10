<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['locale_id', 'translation_key_id', 'content'];

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }

    public function translationKey(): BelongsTo
    {
        return $this->belongsTo(TranslationKey::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_translation');
    }
}
