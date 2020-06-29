<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class Admin
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
        // if (Auth::check() && Auth::user()->isRole()=='admin') {
        if ($request->session()->exists('sct_admin')) {
          return $next($request);
        }else {
          return redirect('/admin/login');
        }
    }
}
