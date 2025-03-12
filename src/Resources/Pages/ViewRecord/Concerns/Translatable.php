<?php

namespace Hydrat\FilamentLexiTranslate\Resources\Pages\ViewRecord\Concerns;

use Hydrat\FilamentLexiTranslate\Resources\Concerns\HasActiveLocaleSwitcher;
use Hydrat\FilamentLexiTranslate\Resources\Pages\Concerns\HasTranslatableFormWithExistingRecordData;
use Hydrat\FilamentLexiTranslate\Resources\Pages\Concerns\HasTranslatableRecord;
use Illuminate\Support\Arr;

trait Translatable
{
    use HasActiveLocaleSwitcher;
    use HasTranslatableFormWithExistingRecordData;
    use HasTranslatableRecord;

    protected ?string $oldActiveLocale = null;

    public function updatingActiveLocale(): void
    {
        $this->oldActiveLocale = $this->activeLocale;
    }

    public function updatedActiveLocale(string $newActiveLocale): void
    {
        if (blank($this->oldActiveLocale)) {
            return;
        }

        $translatableAttributes = static::getResource()::getTranslatableAttributes();

        $this->otherLocaleData[$this->oldActiveLocale] = Arr::only($this->data, $translatableAttributes);
        $this->data = [
            ...$this->data,
            ...$this->otherLocaleData[$this->activeLocale] ?? [],
        ];
    }

    public function getTranslatableLocales(): array
    {
        return static::getResource()::getTranslatableLocales();
    }
}
