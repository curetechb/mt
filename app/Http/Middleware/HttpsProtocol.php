<?php

namespace App\Http\Middleware;

use Closure;
use App;

class HttpsProtocol {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(App::environment('production')){
            if ($request->headers->has('X-Forwarded-Proto')) {
                if (strcmp($request->header('X-Forwarded-Proto'), 'https') === 0) {
                    return $next($request);
                }
            } elseif (env('FORCE_HTTPS') == "On" && !$request->secure()) {
                return redirect()->secure($request->getRequestUri());
            }
        }

	    return $next($request);
    }
}
