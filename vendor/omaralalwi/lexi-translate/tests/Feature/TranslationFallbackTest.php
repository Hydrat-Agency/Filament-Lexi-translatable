<?php

namespace Omaralalwi\LexiTranslate\Tests\Feature;

use Omaralalwi\LexiTranslate\Tests\Models\LexiTranslatePost as Post;
use Omaralalwi\LexiTranslate\Tests\TestCase;

class TranslationFallbackTest extends TestCase
{
    /** @test */
    public function it_falls_back_to_default_when_translation_is_missing()
    {
        $post = Post::create([
            'title' => 'Default Title',
        ]);

        $titleInArabic = $post->translate('title', 'ar');
        $this->assertEquals('Default Title', $titleInArabic);
    }
}
