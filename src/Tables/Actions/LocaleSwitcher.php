<?php

namespace Hydrat\FilamentLexiTranslate\Tables\Actions;

use Hydrat\FilamentLexiTranslate\Actions\Concerns\HasTranslatableLocaleOptions;
use Filament\Tables\Actions\SelectAction;

class LocaleSwitcher extends SelectAction
{
    use HasTranslatableLocaleOptions;

    public static function getDefaultName(): ?string
    {
        return 'activeLocale';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-spatie-laravel-translatable-plugin::actions.active_locale.label'));

        $this->setTranslatableLocaleOptions();
    }
}
