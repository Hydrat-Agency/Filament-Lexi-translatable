<?php

namespace Hydrat\FilamentLexiTranslate\Resources\Pages\Concerns;

use Livewire\Attributes\Locked;

trait HasTranslatableFormWithExistingRecordData
{
    #[Locked]
    public $otherLocaleData = [];

    protected function fillForm(): void
    {
        $this->activeLocale = $this->getDefaultTranslatableLocale();

        $record = $this->getRecord();
        $translatableAttributes = static::getResource()::getTranslatableAttributes();

        foreach ($this->getTranslatableLocales() as $locale) {
            $translatedData = [];

            foreach ($translatableAttributes as $attribute) {
                $translatedData[$attribute] = $record->transAttr($attribute, $locale);
            }

            if (method_exists($this, 'mutateLocaleDataBeforeFill')) {
                $translatedData = $this->mutateLocaleDataBeforeFill($translatedData, $locale);
            }

            if ($locale !== $this->activeLocale) {
                $this->otherLocaleData[$locale] = $this->mutateFormDataBeforeFill($translatedData);

                continue;
            }

            /** @internal Read the DocBlock above the following method. */
            $this->fillFormWithDataAndCallHooks($record, $translatedData);
        }
    }

    protected function getDefaultTranslatableLocale(): string
    {
        $resource = static::getResource();

        // $availableLocales = array_keys($this->getRecord()->getTranslations($resource::getTranslatableAttributes()[0]));
        $availableLocales = $this->getRecord()->translations()->pluck('locale')->unique()->toArray();

        $defaultLocale = $resource::getDefaultTranslatableLocale();

        if (blank($availableLocales) ||in_array($defaultLocale, $availableLocales)) {
            return $defaultLocale;
        }

        $resourceLocales = $this->getTranslatableLocales();

        return array_intersect($availableLocales, $resourceLocales)[0] ?? $defaultLocale;
    }
}
