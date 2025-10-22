<?php

namespace App\Http\Middleware;

use App\Http\Controllers\SpellbookController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanCastSpells
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::id() != 1) return back()->with("toast", ["error", SpellbookController::$MISSPELL_ERROR]);

        return $next($request);
    }
}
