<?php

namespace Hydrat\FilamentLexiTranslate\Actions\Concerns;

use Hydrat\FilamentLexiTranslate\LexiTranslatablePlugin;

trait HasTranslatableLocaleOptions
{
    public function setTranslatableLocaleOptions(): static
    {
        $this->options(function (): array {
            $livewire = $this->getLivewire();

            if (! method_exists($livewire, 'getTranslatableLocales')) {
                return [];
            }

            $locales = [];

            /** @var LexiTranslatablePlugin $plugin */
            $plugin = filament('lexi-laravel-translatable');

            foreach ($livewire->getTranslatableLocales() as $locale) {
                $locales[$locale] = $plugin->getLocaleLabel($locale) ?? $locale;
            }

            return $locales;
        });

        return $this;
    }
}
