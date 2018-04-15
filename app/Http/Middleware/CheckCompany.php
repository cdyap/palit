<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckCompany
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
        if(!Auth::check()) {
            return redirect('/');
        }

        if ($request->route('slug') != Auth::user()->company->slug) {
            // return redirect('/' . Auth::user()->company->slug);
            // $request->route('slug') = Auth::user()->company->slug;
            $request->route()->setParameter('slug',  Auth::user()->company->slug);
            $request->merge([
                'slug' => Auth::user()->company->slug
            ]);
            return $next($request);
        }
        
        return $next($request);
        
    }
}
