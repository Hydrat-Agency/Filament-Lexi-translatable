<?php

namespace Omaralalwi\LexiTranslate\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

trait HasCache
{
    /**
     * Define a common cache TTL (Time-To-Live).
     * If no TTL is set in the config, fallback to a default value (e.g., 60 minutes).
     *
     * @return int
     */
    protected static function cacheTtl(): int
    {
        return Config::get('lexi-translate.cache_ttl');
    }

    /**
     * Check if caching is enabled.
     *
     * @return bool
     */
    protected static function isCacheEnabled(): bool
    {
        return Config::get('lexi-translate.use_cache',);
    }

    /**
     * Get cache prefix to prevent collisions with other cached keys.
     *
     * @return string
     */
    protected static function getCachePrefix(): string
    {
        return Config::get('lexi-translate.cache_prefix');
    }

    /**
     * Centralized cache key generator.
     *
     * @param Model $translatable
     * @param string|null $column
     * @param string|null $locale
     * @return string
     */
    protected static function getCacheKey(Model $translatable, ?string $column = null, ?string $locale = null): string
    {
        $modelType = class_basename($translatable);
        $cachePrefix = self::getCachePrefix();
        $key = "{$cachePrefix}_{$modelType}_{$translatable->id}";

        if ($column) {
            $key .= "_{$column}";
        }

        if ($locale) {
            $key .= "_{$locale}";
        }

        return $key;
    }

    /**
     * Clear the base translations cache for the translatable model.
     *
     * @param Model $translatable
     * @return void
     */
    protected static function clearBaseTranslationsCache(Model $translatable): void
    {
        if (!self::isCacheEnabled()) {
            return;
        }

        $cacheKey = self::getCacheKey($translatable);
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
            Log::info("Base cache cleared: {$cacheKey}");
        }
    }

    /**
     * Clear translation cache for a specific column and locale.
     *
     * @param Model $translatable
     * @param string $column
     * @return void
     */
    protected static function clearTranslationCache(Model $translatable, string $column): void
    {
        if (!self::isCacheEnabled()) {
            return;
        }

        $supportedLocales = Config::get('lexi-translate.supported_locales', []);

        foreach ($supportedLocales as $locale) {
            $cacheKey = self::getCacheKey($translatable, $column, $locale);
            if (Cache::has($cacheKey)) {
                Cache::forget($cacheKey);
                Log::info("Translation cache cleared: {$cacheKey}");
            }
        }
    }
}
