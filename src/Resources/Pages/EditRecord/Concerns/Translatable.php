<?php

namespace Hydrat\FilamentLexiTranslate\Resources\Pages\EditRecord\Concerns;

use Hydrat\FilamentLexiTranslate\Resources\Concerns\HasActiveLocaleSwitcher;
use Hydrat\FilamentLexiTranslate\Resources\Pages\Concerns\HasTranslatableFormWithExistingRecordData;
use Hydrat\FilamentLexiTranslate\Resources\Pages\Concerns\HasTranslatableRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

trait Translatable
{
    use HasActiveLocaleSwitcher;
    use HasTranslatableFormWithExistingRecordData;
    use HasTranslatableRecord;

    protected ?string $oldActiveLocale = null;

    public function getTranslatableLocales(): array
    {
        return static::getResource()::getTranslatableLocales();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
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

        foreach (Arr::only($data, $translatableAttributes) as $key => $value) {
            if (filled($value)) {
                $record->setTranslation($key, $this->activeLocale, $value);
            }
        }

        if (method_exists($this, 'handleRecordLocaleUpdate')) {
            $this->handleRecordLocaleUpdate($record, $data, $this->activeLocale, $this->activeLocale === $defaultLocale);
        }

        $originalData = $this->data;

        $existingLocales = null;

        foreach ($this->otherLocaleData as $locale => $localeData) {
            $existingLocales ??= $record->translations()
                ->pluck('locale')
                ->unique()
                ->all();

            $this->data = [
                ...$this->data,
                ...$localeData,
            ];

            try {
                $this->form->validate();
            } catch (ValidationException $exception) {
                if (! array_key_exists($locale, $existingLocales)) {
                    continue;
                }

                $this->setActiveLocale($locale);

                throw $exception;
            }

            $localeData = $this->mutateFormDataBeforeSave($localeData);

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

    public function setActiveLocale(string $locale): void
    {
        $this->updatingActiveLocale();
        $this->activeLocale = $locale;
        $this->updatedActiveLocale($locale);
    }
}
