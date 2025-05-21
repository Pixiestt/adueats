<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            if (auth()->check() && auth()->user()->isCustomer()) {
                return redirect()->route('customer.dashboard')->with('error', 'This area is for administrators only.');
            }
            
            return redirect()->route('login');
        }
        
        return $next($request);
    }
}
