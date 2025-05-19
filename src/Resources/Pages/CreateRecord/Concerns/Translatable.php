<?php

namespace Hydrat\FilamentLexiTranslate\Resources\Pages\CreateRecord\Concerns;

use Filament\Facades\Filament;
use Hydrat\FilamentLexiTranslate\Resources\Concerns\HasActiveLocaleSwitcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;

trait Translatable
{
    use HasActiveLocaleSwitcher;

    protected ?string $oldActiveLocale = null;

    #[Locked]
    public $otherLocaleData = [];

    public function mountTranslatable(): void
    {
        $this->activeLocale = static::getResource()::getDefaultTranslatableLocale();
    }

    public function getTranslatableLocales(): array
    {
        return static::getResource()::getTranslatableLocales();
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = app(static::getModel());

        $translatableAttributes = static::getResource()::getTranslatableAttributes();
        $defaultLocale = static::getResource()::getDefaultTranslatableLocale();

        if (blank($this->activeLocale)) {
            $this->activeLocale = $defaultLocale;
        }

        if ($this->activeLocale === $defaultLocale) {
            $record->fill($data);
        } else {
            $record->fill(Arr::except($data, $translatableAttributes));
        }

        $record->save();

        foreach (Arr::only($data, $translatableAttributes) as $key => $value) {
            if (filled($value)) {
                $record->setTranslation($key, $this->activeLocale, $value);
            }
        }

        if (method_exists($this, 'handleRecordLocaleUpdate')) {
            $this->handleRecordLocaleUpdate($record, $data, $this->activeLocale, $this->activeLocale === $defaultLocale);
        }

        $originalData = $this->data;

        foreach ($this->otherLocaleData as $locale => $localeData) {
            $this->data = [
                ...$this->data,
                ...$localeData,
            ];

            try {
                $this->form->validate();
            } catch (ValidationException $exception) {
                continue;
            }

            $localeData = $this->mutateFormDataBeforeCreate($localeData);

            foreach (Arr::only($localeData, $translatableAttributes) as $key => $value) {
                if (filled($value)) {
                    $record->setTranslation($key, $locale, $value);
                }
            }

            if (method_exists($this, 'handleRecordLocaleUpdate')) {
                $this->handleRecordLocaleUpdate($record, $localeData, $locale, $locale === $defaultLocale);
            }
        }

        $this->data = $originalData;

        if (
            static::getResource()::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            return $this->associateRecordWithTenant($record, $tenant);
        }

        $record->save();

        return $record;
    }

    public function updatingActiveLocale(): void
    {
        $this->oldActiveLocale = $this->activeLocale;
    }

    public function updatedActiveLocale(string $newActiveLocale): void
    {
        session()->put('filament_active_locale', $newActiveLocale);

        if (blank($this->oldActiveLocale)) {
            return;
        }

        $this->resetValidation();

        $translatableAttributes = static::getResource()::getTranslatableAttributes();

        if (method_exists($this, 'preserveLocaleDataKeys')) {
            $translatableAttributes = $this->preserveLocaleDataKeys($translatableAttributes);
        }

        $this->otherLocaleData[$this->oldActiveLocale] = Arr::only($this->data, $translatableAttributes);

        $this->data = [
            ...Arr::except($this->data, $translatableAttributes),
            ...$this->otherLocaleData[$this->activeLocale] ?? [],
        ];

        unset($this->otherLocaleData[$this->activeLocale]);
    }
}
