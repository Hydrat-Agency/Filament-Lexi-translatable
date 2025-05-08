<?php

namespace Hydrat\FilamentLexiTranslate;

use Closure;
use Filament\Panel;
use Filament\Contracts\Plugin;
use Illuminate\Support\Facades\Config;

class LexiTranslatablePlugin implements Plugin
{
    /**
     * @var array<string>
     */
    protected array $defaultLocales = [];

    protected ?Closure $getLocaleLabelUsing = null;

    final public function __construct()
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'lexi-laravel-translatable';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * @return array<string>
     */
    public function getDefaultLocales(): array
    {
        return filled($this->defaultLocales)
            ? $this->defaultLocales
            : lexi_locales();
    }

    /**
     * @param  array<string> | null  $defaultLocales
     */
    public function defaultLocales(?array $defaultLocales = null): static
    {
        $this->defaultLocales = $defaultLocales;

        Config::set('lexi-translate.supported_locales', $defaultLocales);

        return $this;
    }

    public function getLocaleLabelUsing(?Closure $callback): static
    {
        $this->getLocaleLabelUsing = $callback;

        return $this;
    }

    public function getLocaleLabel(string $locale, ?string $displayLocale = null): ?string
    {
        $displayLocale ??= app()->getLocale();

        $label = null;

        if ($callback = $this->getLocaleLabelUsing) {
            $label = $callback($locale, $displayLocale);
        }

        return ucfirst($label ?? (locale_get_display_name($locale, $displayLocale) ?: null));
    }
}
