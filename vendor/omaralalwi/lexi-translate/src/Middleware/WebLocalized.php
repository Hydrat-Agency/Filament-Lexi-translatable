<?php

namespace Omaralalwi\LexiTranslate\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Omaralalwi\LexiTranslate\Traits\HasLocale;

class WebLocalized
{

    use HasLocale;

    /**
     * Handle an incoming request to switch the app locale.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestLocale = $request->route('locale') ?: $request->get('locale') ?: session('locale') ?: cookie('locale');
        $locale = $this->getValidatedLocale($requestLocale) ?: app()->getLocale();

        if ($locale && in_array($locale, Config::get('lexi-translate.supported_locales'))) {
            App::setLocale($locale);
            session(['locale' => $locale]);
            cookie()->queue(cookie('locale', $locale, 60 * 24 * 365, null, null, true, true, false, 'Lax'));
        }

        return $next($request);
    }
}
