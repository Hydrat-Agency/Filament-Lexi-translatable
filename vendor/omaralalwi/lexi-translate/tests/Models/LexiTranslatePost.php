<?php

namespace Omaralalwi\LexiTranslate\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Omaralalwi\LexiTranslate\Models\Translation;
use Omaralalwi\LexiTranslate\Traits\LexiTranslatable;

/*
 * this model just for package's tests .
*/
class LexiTranslatePost extends Model
{
    use LexiTranslatable;

    protected $table = 'lexi_posts';

    protected $fillable = [
        'title',
        'description',
        'created_at',
        'updated_at',
    ];

    protected $translatableFields = ['title', 'description'];

}
