<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPublicLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('languages.supported'));

        $locale = $request->query('lang');

        if ($locale && in_array($locale, $supported, true)) {
            session(['locale' => $locale]);
        } else {
            $locale = session('locale');
        }

        if (! $locale || ! in_array($locale, $supported, true)) {
            $locale = config('languages.default');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
