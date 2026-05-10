<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationKey extends Model
{
    use HasFactory;

    protected $fillable = ['key'];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }
}
