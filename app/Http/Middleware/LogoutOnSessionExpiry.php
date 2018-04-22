<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Carbon\Carbon;

class LogoutOnSessionExpiry
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
        if (Auth::check()) {
            $now = Carbon::now();
            $last_login = Carbon::parse(Auth::user()->last_login);

            if($now > $last_login->addHour()) {
                Auth::logout();
                return redirect()->to('/')->with('warning', 'Your session has expired because your account is deactivated.');
            }
        }

        return $next($request);    
       
    }
}
