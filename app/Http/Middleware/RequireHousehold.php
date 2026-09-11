<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireHousehold
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        $householdId = $request->cookie('household_id');

        if (! $householdId) {
            return redirect()->route('households.index');
        }

        $household = $user
            ->households()
            ->findOrFail($householdId);

        $request->attributes->set(
            'current_household',
            $household
        );

        return $next($request);
    }
}
