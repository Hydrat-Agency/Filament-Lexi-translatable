<?php

namespace Omaralalwi\LexiTranslate\Tests\Feature;

use Omaralalwi\LexiTranslate\Tests\TestCase;
use Omaralalwi\LexiTranslate\Tests\Models\LexiTranslatePost as Post;

class RetrieveTranslationTest extends TestCase
{
    /** @test */
    public function it_can_retrieve_a_translation()
    {
        $post = Post::create([
            'title' => 'Original English Title',
            'description' => 'Original description',
        ]);

        $post->translations()->create([
            'locale' => 'ar',
            'column' => 'title',
            'text' => 'العنوان بالعربية',
        ]);

        $titleInArabic = $post->transAttr('title', 'ar');
        $this->assertEquals('العنوان بالعربية', $titleInArabic);
    }
}
