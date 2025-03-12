<?php

namespace Omaralalwi\LexiTranslate\Models\Scopes;

trait TranslationsScopes
{
    /**
     * Search the model by translated fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field The translatable field to search.
     * @param string $keyword The keyword to search for.
     * @param string|null $locale The locale to search in. Defaults to app locale.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByTranslation($query, string $field, string $keyword, ?string $locale = null)
    {
        return $this->applyTranslationFilter($query, $field, "%{$keyword}%", $locale, 'LIKE');
    }

    /**
     * Filter the model by translated field values.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field The translatable field to filter.
     * @param mixed $value The exact value to filter for.
     * @param string|null $locale The locale to filter in. Defaults to app locale.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByTranslation($query, string $field, $value, ?string $locale = null)
    {
        return $this->applyTranslationFilter($query, $field, $value, $locale);
    }

    /**
     * Apply a translation filter to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field The translatable field to filter.
     * @param mixed $value The value or pattern to match.
     * @param string|null $locale The locale to filter in. Defaults to app locale.
     * @param string $operator The operator for the comparison. Defaults to '='.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyTranslationFilter($query, string $field, $value, ?string $locale, string $operator = '=')
    {
        $locale = $this->resolveLocale($locale);

        return $query->whereHas('translations', function ($q) use ($field, $value, $locale, $operator) {
            $q->where('column', $field)
                ->where('locale', $locale)
                ->where('text', $operator, $value);
        });
    }

    /**
     * Resolve the locale to use for the query.
     *
     * @param string|null $locale The locale provided by the user.
     * @return string The resolved locale.
     */
    protected function resolveLocale(?string $locale): string
    {
        return $locale && $this->isSupportedLocal($locale) ? $locale : app()->getLocale();
    }
}
