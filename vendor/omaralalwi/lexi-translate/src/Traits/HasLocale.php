<?php

namespace Omaralalwi\LexiTranslate\Traits;

use Illuminate\Support\Facades\Config;
use Omaralalwi\LexiTranslate\Enums\Language;

trait HasLocale
{
    /**
     * Get the supported locales for translations.
     *
     * @return array
     */
    public function getSupportedLocales(): array
    {
        return Config::get('lexi-translate.supported_locales', []);
    }

    /**
     * Get the validated locale.
     *
     * This function checks if the provided locale is in the list of supported locales.
     * If the locale is valid, it returns it; otherwise, it returns the application's default locale.
     *
     * @param string|null $locale The locale to check.
     * @return string The validated locale.
     */
    public function getValidatedLocale(?string $locale): string
    {
        return ($locale && $this->isValidLocale($locale)) ? $locale : app()->getLocale();
    }

    /**
     * Check if the given locale is valid (exists in the supported locales).
     *
     * @param string|null $locale The locale to check.
     * @return bool True if the locale is valid, false otherwise.
     */
    public function isValidLocale(?string $locale): bool
    {
        if (!$locale) {
            return false;
        }

        return in_array($locale, $this->getSupportedLocales());
    }
}
