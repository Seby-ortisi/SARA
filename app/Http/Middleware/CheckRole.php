<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles   // uno o più ruoli accettati
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        
        $user = Auth::user();

   
        // Controlla se utente è loggato
        if (!$user) {
            return redirect()->route('login');
        }

        // Controlla se ruolo utente è uno di quelli permessi
        // Supponendo che ruoli siano salvati come id in ruolo_id
        if (!in_array($user->ruolo_id, $roles)) {
            abort(403, 'Accesso negato: ruolo non autorizzato.');
        }

        return $next($request);
    }
}
