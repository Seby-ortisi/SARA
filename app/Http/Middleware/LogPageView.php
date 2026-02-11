<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogPageView
{
    /*
    public function handle($request, Closure $next)
    {
        
        if (Auth::check()) {
            

            // Salva il log nella tabella 'activity_logs'
            DB::table('activity_logs')->insert([
                'user_id' => Auth::id(),
                'action_type' => 'Read',
                'description' => 'User visited ' . $request->url(),
                'created_at' => now(),
            ]);
        }

        return $next($request);
        
    }
    */
}
