<?php

namespace Omaralalwi\LexiTranslate\Tests\Feature;

use Omaralalwi\LexiTranslate\Tests\Models\LexiTranslatePost as Post;
use Omaralalwi\LexiTranslate\Tests\TestCase;

class StoreTranslationTest extends TestCase
{
    /** @test */
    public function it_can_store_a_translation()
    {
        $post = Post::create([
            'title' => 'Original Title',
            'description' => 'Original description',
        ]);

        $arTitle = 'العنوان بالعربية';
        $enTitle = ' English Title';

        $post->translations()->create([
            'locale' => 'ar',
            'column' => 'title',
            'text' => $arTitle,
        ]);

        $post->setTranslations([
            'ar' => [
                'name' => $arTitle,
            ],
            'en' => [
                'name' => $enTitle,
            ]
        ]);

        $this->assertDatabaseHas(config('lexi-translate.table_name'), [
            'translatable_id' => $post->id,
            'translatable_type' => Post::class,
            'locale' => 'ar',
            'column' => 'title',
            'text' => $arTitle,
        ]);
    }
}
