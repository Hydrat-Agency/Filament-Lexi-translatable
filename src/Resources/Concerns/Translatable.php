<?php

namespace Hydrat\FilamentLexiTranslate\Resources\Concerns;

use Exception;
use Omaralalwi\LexiTranslate\Traits\LexiTranslatable;

trait Translatable
{
    public static function getDefaultTranslatableLocale(): string
    {
        return static::getTranslatableLocales()[0];
    }

    public static function getTranslatableAttributes(): array
    {
        $model = static::getModel();

        if (! method_exists($model, 'getTranslatableFields')) {
            throw new Exception("Model [{$model}] must use trait [" . LexiTranslatable::class . '].');
        }

        $attributes = app($model)->getTranslatableFields();

        if (! count($attributes)) {
            throw new Exception("Model [{$model}] must have [\$translatable] properties defined.");
        }

        return $attributes;
    }

    public static function getTranslatableLocales(): array
    {
        return filament('lexi-laravel-translatable')->getDefaultLocales();
    }
}
