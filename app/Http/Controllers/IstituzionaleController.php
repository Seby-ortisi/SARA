<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use DB;
use Exception;
class IstituzionaleController extends Controller
{

    public function profiloUtente(Request $request)
    {

        /*
            if ($request->ch4e != md5( substr($request->fiscalNumber, -5) )) {
                return redirect('/login')->withErrors(['error' => 'Autenticazione fallita.']);                        
            }
        */
        $cf = str_replace("TINIT-", "", $request->fiscalNumber);
        $paziente = User::where("codice_fiscale", $cf)->first();

        Log::channel("daily")->info("Accesso: " . $request->type . ">" . $cf);

        //se non lotrovo per cf lo cerco per email
        if ($paziente == null && !empty($request->email)) {
            $paziente = User::where("email", $request->email)->first();
        }

        DB::beginTransaction();
        try {
            // Verifica se il paziente è stato trovato
            if ($paziente == null) {
                if ($request->extendedData['type'] == "cie") {

                    $phoneVerified = null;
                    $emailVerified = null;
                } elseif ($request->extendedData['type'] == "spid") {
  
                    $phoneVerified = Carbon::now();
                    $emailVerified = Carbon::now();
                }

                // Se non trovato, crea un nuovo paziente
                $paziente = User::create([
                    'cognome' => ucwords($request->familyName),
                    'nome' => ucwords($request->name),
                    'codice_fiscale' => strtoupper($cf),
                    'cellulare' => !empty($request->mobilePhone) ? $request->mobilePhone : null,
                    //'vaccination_center_id' => -1,
                    'data_nascita' => $request->dateOfBirth,
                    'email' => $request->email,
                    'password' => Hash::make(Str::random(20)),
                    'ruolo_id' => 4, // 1. paziente
                    'phone_verified_at' => $phoneVerified,
                    'email_verified_at' => $emailVerified,
                    'stato' => 1
                ]);
            } else {
                // Se il paziente esiste, aggiorna i dettagli
                if ($request->extendedData['type'] == "cie") {
               
                    $phoneVerified = isset($paziente->phone_verified_at) ? $paziente->phone_verified_at : null;
                    $emailVerified = isset($paziente->email_verified_at) ? $paziente->email_verified_at : null;
                } elseif ($request->extendedData['type'] == "spid") {
                    
                    $phoneVerified = isset($paziente->phone_verified_at) ? $paziente->phone_verified_at : Carbon::now();
                    $emailVerified = isset($paziente->email_verified_at) ? $paziente->email_verified_at : Carbon::now();
                    
                }

                $paziente->update([
                    'cognome' => ucwords($request->familyName),
                    'nome' => ucwords($request->name),
                    'codice_fiscale' => strtoupper($cf),
                    'cellulare' => !empty($request->mobilePhone) ? $request->mobilePhone : null,
                    //'vaccination_center_id' => -1,
                    'data_nascita' => $request->dateOfBirth,
                    'email' => $request->email,
                    'password' => Hash::make(Str::random(20)),
                    'ruolo_id' => 4, // 1. paziente
                    'phone_verified_at' => $phoneVerified,
                    'email_verified_at' => $emailVerified,
                    'stato' => 1
                ]);
            }

        
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            if ($exception instanceof \Illuminate\Database\QueryException && str_contains($exception->getMessage(), 'Duplicate entry')) {
                $customException = new Exception(' L’indirizzo email risulta già registrato. Se ritieni che sia un errore, contatta l’assistenza.');
                return view('errors.500', [
                    'exception' => $customException,
                ]);
            }
        }

        if ($paziente instanceof User) {

            //Log::channel("daily")->info("provo a loggarmi");
            Auth::login($paziente);
            /*
            if (Auth::check()) {
                Log::info('Paziente loggato:', ['id' => $paziente->id, 'ruolo' => $paziente->ruolo()->first()->nome]);
            } else {
                Log::error('L\'utente non è loggato.');
                return redirect('/login')->withErrors(['error' => 'Autenticazione fallita.']);
            }
        } else {
            Log::error('Login fallito: $paziente non è un\'istanza di Paziente.');
            return redirect('/login')->withErrors(['error' => 'Autenticazione fallita.']);
        }
            */

        if (!$paziente->email || !$paziente->cellulare) {
            $dataView['modalitaAccesso'] = 2;

            return redirect()->route('get.modificaProfiloUtente', ['modalitaAccesso' => $dataView['modalitaAccesso']]);
        }

        if ($paziente->phone_verified_at === null) {
            return redirect()->route('smsOTP');
        }

        if ($paziente->email_verified_at === null) {
            return redirect()->route('emailOTP');
        }
        
        // Ritorna una risposta con il token di sessione e la URL di redirezione
        //Log::info('redirect:' .  url("/home"));
        //return response()->json(['status' => 'success', 'redirect_url' => url('/dashboard')]);
        return redirect()->route('ets.dashboard'); // Assicurati che la rotta esista
        //return (new HomeController())->index();
    }

}

}