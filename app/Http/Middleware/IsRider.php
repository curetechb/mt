<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class IsRider
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (Auth::check() && (Auth::user()->user_type == 'delivery_boy') ) {

            return $next($request);
        }
        else{
            return response()->json(['error' => "You dont have permission"]);
            // session(['link' => url()->current()]);
            // return redirect()->route('user.login');
        }
    }
}
