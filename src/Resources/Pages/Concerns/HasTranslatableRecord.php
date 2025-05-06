<?php

namespace Hydrat\FilamentLexiTranslate\Resources\Pages\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasTranslatableRecord
{
    public function getRecord(): Model
    {
        return $this->record;

        // if (blank($this->activeLocale)) {
        //     return $this->record;
        // }

        // return $this->record->setLocale($this->activeLocale);
    }
}
