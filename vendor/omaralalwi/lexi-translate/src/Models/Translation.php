<?php

namespace Omaralalwi\LexiTranslate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Omaralalwi\LexiTranslate\Traits\HasCache;

class Translation extends Model
{
    use HasCache;

    protected $table;

    public function __construct(array $attributes = [])
    {
        $this->table = config('lexi-translate.table_name');
        parent::__construct($attributes);
    }

    protected $fillable = ['translatable_type', 'translatable_id', 'column', 'locale', 'text'];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        static::saved(function ($translate) {
            $translatable = $translate->translatable;

            if ($translatable && self::isCacheEnabled()) {
                self::clearBaseTranslationsCache($translatable);
                self::clearTranslationCache($translatable, $translate->column);
            }
        });

        static::deleted(function ($translate) {
            $translatable = $translate->translatable;

            if ($translatable && self::isCacheEnabled()) {
                self::clearBaseTranslationsCache($translatable);
                self::clearTranslationCache($translatable, $translate->column);
            }
        });
    }

}
