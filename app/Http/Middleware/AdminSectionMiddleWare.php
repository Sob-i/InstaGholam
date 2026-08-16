<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminSectionMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next ,$roles): Response
    {
        $roles = explode('|', $roles);

        if (Auth::check()) {
            $userRole = Auth::user()->role;
            foreach ($roles as $role) {
                if ($userRole === $role) {
                    return $next($request);
                }
            }
        }

        return redirect('/index');
    }
}
