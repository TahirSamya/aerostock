<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Empêche un utilisateur non-admin d'accéder à une route protégée
     * (ex: gestion des utilisateurs). Redirige avec un message clair
     * plutôt que de planter avec une erreur 403 brute.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', "Accès refusé : cette page est réservée aux administrateurs.");
        }

        return $next($request);
    }
}
