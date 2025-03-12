<?php

namespace Omaralalwi\LexiTranslate\Tests\Unit;

use Omaralalwi\LexiTranslate\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Omaralalwi\LexiTranslate\Middleware\WebLocalized;
use Symfony\Component\HttpFoundation\Response;

class WebLocalizedTest extends TestCase
{
    /**
     * Test that the locale is set from the route parameter.
     *
     * @return void
     */
    public function testLocaleFromRouteParameter()
    {
        $routeLocale = 'fr';

        // Create a request with the route parameter 'locale'
        $request = Request::create('/api/test?locale=' . $routeLocale, 'GET');


        // Instantiate the middleware
        $middleware = new WebLocalized();

        // Process the request through the middleware
        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        // Assert that the locale was set to the route parameter value
        $this->assertEquals('fr', App::getLocale());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test that the locale is set from the query string.
     *
     * @return void
     */
    public function testLocaleFromQueryString()
    {
        $queryLocale = 'es';

        // Create a request with a 'locale' query parameter
        $request = Request::create('/api/test', 'GET', ['locale' => $queryLocale]);

        // Instantiate the middleware
        $middleware = new WebLocalized();

        // Process the request through the middleware
        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        // Assert that the locale was set to the query string value
        $this->assertEquals('es', App::getLocale());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test that the locale is set from the session.
     *
     * @return void
     */
    public function testLocaleFromSession()
    {
        $sessionLocale = 'de';

        // Simulate a session locale
        Session::put('locale', $sessionLocale);

        // Create a request without a locale in the route or query string
        $request = Request::create('/api/test', 'GET');

        // Instantiate the middleware
        $middleware = new WebLocalized();

        // Process the request through the middleware
        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        // Assert that the locale was set from the session
        $this->assertEquals('de', App::getLocale());
        $this->assertEquals(200, $response->getStatusCode());
    }


    /**
     * Test that the locale falls back to the default if no locale is found.
     *
     * @return void
     */
    public function testLocaleFallbackToDefault()
    {
        $defaultLocale = 'en';

        // Create a request without any locale
        $request = Request::create('/api/test', 'GET');

        // Instantiate the middleware
        $middleware = new WebLocalized();

        // Process the request through the middleware
        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        // Assert that the locale falls back to the default locale
        $this->assertEquals($defaultLocale, App::getLocale());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test that only supported locales are set.
     *
     * @return void
     */
    public function testLocaleIsValidated()
    {
        $supportedLocales = ['en', 'fr', 'de'];

        // Set supported locales in config
        Config::set('lexi-translate.supported_locales', $supportedLocales);

        $validLocale = 'fr';
        $invalidLocale = 'es'; // Assume 'es' is not supported

        // Create a request with a locale query parameter
        $request = Request::create('/api/test', 'GET', ['locale' => $invalidLocale]);

        // Instantiate the middleware
        $middleware = new WebLocalized();

        // Process the request through the middleware
        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        // Assert that the locale was set to the default because 'es' is not supported
        $this->assertEquals('en', App::getLocale());
        $this->assertEquals(200, $response->getStatusCode());
    }
}