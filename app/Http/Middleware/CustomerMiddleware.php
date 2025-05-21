<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isCustomer()) {
            if (auth()->check() && auth()->user()->isAdmin()) {
                return redirect()->route('dashboard')->with('error', 'This area is for customers only.');
            }
            
            return redirect()->route('login');
        }
        
        return $next($request);
    }
}
