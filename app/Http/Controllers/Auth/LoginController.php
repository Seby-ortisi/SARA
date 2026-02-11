<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;



    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {

        
        return route('ets.dashboard');
    }

    protected function authenticated(Request $request, $user)
    {
        /*
        if ($user->stato == 0) {
            // Se l'utente è disabilitato, esegui il logout e reindirizza alla pagina di login con un messaggio di errore
            auth()->logout();
            return redirect()->route('standardLogin')->withErrors(['error' => 'Il tuo account non è stato ancora validato.']);
        }
            */
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /*
       $this->middleware('guest')->except('logout');
       $this->middleware('auth')->only('logout');
       */
    }
}
