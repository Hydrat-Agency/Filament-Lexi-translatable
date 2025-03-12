<?php

namespace Hydrat\FilamentLexiTranslate;

use Illuminate\Support\ServiceProvider;

class LexiTranslatablePluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/filament-lexi-translatable-plugin-translations'),
            ], 'filament-lexi-translatable-plugin-translations');
        }

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-lexi-translatable-plugin');
    }
}
