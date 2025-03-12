<?php

namespace Omaralalwi\LexiTranslate\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Omaralalwi\LexiTranslate\Middleware\ApiLocalized;
use Omaralalwi\LexiTranslate\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;


class ApiLocalizedTest extends TestCase
{

    public function testLocaleFromHeader()
    {
        // Define a test locale header
        $localeHeader = 'ar';

        // Create a mock request
        $request = Request::create('/api/test', 'GET', [], [], [], ['HTTP_Accept-Language' => $localeHeader]);

        // Instantiate the middleware
        $middleware = new ApiLocalized();

        // Process the request through the middleware
        $response = $middleware->handle($request, function ($req) {
            // This is the closure after the middleware processes the request
            return new Response('OK', 200);
        });

        // Assert that the locale was set to the header value
        $this->assertEquals('ar', App::getLocale());
        $this->assertEquals(200, $response->getStatusCode());
    }


    /**
     * Test that the middleware defaults to the app's default locale if no locale is provided.
     *
     * @return void
     */
    public function testDefaultLocale()
    {
        // Create a mock request with no locale specified
        $request = Request::create('/api/test', 'GET');

        // Instantiate the middleware
        $middleware = new ApiLocalized();

        // Process the request through the middleware
        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        // Assert that the default locale is used
        $this->assertEquals(config('app.locale'), App::getLocale());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test that an invalid locale falls back to the default locale.
     *
     * @return void
     */
    public function testInvalidLocale()
    {
        $invalidLocale = 'xyz'; // Invalid locale

        // Create a mock request with an invalid locale header
        $request = Request::create('/api/test', 'GET', [], [], [], ['HTTP_Accept-Language' => $invalidLocale]);

        // Instantiate the middleware
        $middleware = new ApiLocalized();

        // Process the request through the middleware
        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        // Assert that the locale falls back to the default locale
        $this->assertEquals(config('app.locale'), App::getLocale());
        $this->assertEquals(200, $response->getStatusCode());
    }
}