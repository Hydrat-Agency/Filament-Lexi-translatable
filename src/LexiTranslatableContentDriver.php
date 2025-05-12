<?php

namespace Hydrat\FilamentLexiTranslate;

use Filament\Support\Contracts\TranslatableContentDriver;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

use function Filament\Support\generate_search_column_expression;

class LexiTranslatableContentDriver implements TranslatableContentDriver
{
    public function __construct(protected string $activeLocale)
    {
    }

    public function isAttributeTranslatable(string $model, string $attribute): bool
    {
        $model = app($model);

        if (! method_exists($model, 'isTranslatableAttribute')) {
            return false;
        }

        return $model->isTranslatableAttribute($attribute);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function makeRecord(string $model, array $data): Model
    {
        $record = new $model;

        $defaultLocale = Arr::first(lexi_locales());
        $translatableAttributes = method_exists($record, 'getTranslatableFields') ?
            $record->getTranslatableFields() :
            [];

        if (blank($this->activeLocale)) {
            $this->activeLocale = $defaultLocale;
        }

        $record->fill($data);
        $record->save(); // Save the record to get an ID

        foreach (Arr::only($data, $translatableAttributes) as $key => $value) {
            if (filled($value)) {
                $record->setTranslation($key, $this->activeLocale, $value);
            }
        }

        return $record;
    }

    public function setRecordLocale(Model $record): Model
    {
        return $record;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateRecord(Model $record, array $data): Model
    {
        $defaultLocale = Arr::first(lexi_locales());
        $translatableAttributes = method_exists($record, 'getTranslatableFields') ?
            $record->getTranslatableFields() :
            [];

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

        $record->save();

        return $record;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRecordAttributesToArray(Model $record): array
    {
        $attributes = $record->attributesToArray();

        if (! method_exists($record, 'getTranslatableFields')) {
            return $attributes;
        }

        foreach ($record->getTranslatableFields() as $attribute) {
            $attributes[$attribute] = $record->translate($attribute, $this->activeLocale, useFallbackLocale: false);
        }

        return $attributes;
    }

    public function applySearchConstraintToQuery(Builder $query, string $column, string $search, string $whereClause, ?bool $isCaseInsensitivityForced = null): Builder
    {
        $whereClause = $whereClause === 'where' ? 'whereHas' : 'orWhereHas';
        $locale = $this->activeLocale;

        return $query->{$whereClause}('translations', function ($q) use ($column, $search, $locale) {
            $q->where('column', $column)
                ->where('locale', $locale)
                ->where('text', 'like', '%'.$search.'%');
        });
    }
}
