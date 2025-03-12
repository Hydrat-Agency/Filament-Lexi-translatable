<?php

namespace Omaralalwi\LexiTranslate\Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Omaralalwi\LexiTranslate\Tests\TestCase;
use Omaralalwi\LexiTranslate\Tests\Models\LexiTranslatePost as Post;
use Omaralalwi\LexiTranslate\Traits\HasCache;

class TranslationCacheTest extends TestCase
{
    use HasCache;

    /** @test */
    public function it_does_not_cache_translations_when_disabled()
    {
        Config::set('lexi-translate.use_cache', false);
        $post = Post::create(['title' => 'Original Title']);
        $arTitle = 'العنوان بالعربية';
        $post->setTranslation('title', 'ar', $arTitle);

        $titleInArabic = $post->translate('title', 'ar');
        
        $modelType = class_basename($post);
        $cachePrefix = self::getCachePrefix();
        $cacheKey = "{$cachePrefix}_{$modelType}_{$post->id}_title_ar";

        $this->assertFalse(Cache::has($cacheKey), "Cache key should not exist when caching is disabled: {$cacheKey}");
    }
}
