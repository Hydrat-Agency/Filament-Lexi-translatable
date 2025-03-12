<?php


if (!function_exists('lexi_locales')) {
    /**
     * Get the supported locales for translations.
     *
     * @return array
     */
    function lexi_locales(): array
    {
        return config('lexi-translate.supported_locales', ['en']);
    }
}

