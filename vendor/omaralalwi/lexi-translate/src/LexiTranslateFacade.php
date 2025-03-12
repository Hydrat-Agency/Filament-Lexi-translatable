<?php

namespace Omaralalwi\LexiTranslate;

use Illuminate\Support\Facades\Facade;

class LexiTranslateFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     * @see \Omaralalwi\LexiTranslate\LexiTranslate
     */
    protected static function getFacadeAccessor()
    {
        return 'lexi-translate';
    }
}
