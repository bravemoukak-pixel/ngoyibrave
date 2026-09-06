<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user || ($user->role !== 'admin' && !in_array($user->role, $roles))) {
            return redirect()->route('dashboard')->with('error', "Vous n'avez pas accès à cette rubrique.");
        }

        return $next($request);
    }
}
