<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Prenotazione;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Nomenclatore;
use App\CommonTrait; 



class PazienteController extends Controller
{
     use CommonTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function index(Request $request)
    {
        $oggi = Carbon::now();
        $giornoSettimana = ucfirst($oggi->locale('it')->isoFormat('dddd'));

        $dataView['dataOggi'] = $oggi->toDateString();

        $dataView['branche'] = DB::table('branche')
            ->select('id', 'nome_branca')
            ->get();

        $dataView['ambulatori'] = DB::table('ambulatori')
            ->select('id', 'descrizione')
            ->get();

        $dataView['prestazioni'] = Nomenclatore::all();

        // Mostra agenda solo se è stato applicato almeno un filtro
        if (
            $request->has(['data', 'branca_id', 'prestazione_id']) &&
            $request->filled(['data', 'branca_id', 'prestazione_id'])
        ) {

            $dataView['data'] = $request->input('data');
            $dataView['prestazione_id'] = $request->input('prestazione_id');

            $giornoRichiesto = Carbon::parse($dataView['data']);
            $giornoSettimanaRichiesto = ucfirst($giornoRichiesto->locale('it')->isoFormat('dddd'));

            $agende = Agenda::select('agenda.id', 'giorno', 'ora_inizio', 'ora_fine', 'slot', 'ambulatori.id as id_ambulatorio', 'ambulatori.descrizione as descrizione_ambulatorio')
                ->join('ambulatori', 'agenda.id_ambulatorio', '=', 'ambulatori.id')
                ->where('giorno', $giornoSettimanaRichiesto)
                ->get();

            $dataView['agenda'] = [];

            foreach ($agende as $agenda) {
                $start = Carbon::createFromFormat('H:i:s', $agenda->ora_inizio);
                $end = Carbon::createFromFormat('H:i:s', $agenda->ora_fine);

                $slots = [];
                while ($start->lt($end)) {
                    $slots[] = $start->format('H:i');
                    $start->addMinutes(30);
                }

                $bookedSlots = Prenotazione::join('agenda', 'prenotazioni.centro_vaccinale_id', '=', 'agenda.id_ambulatorio')
                    ->where('prenotazioni.centro_vaccinale_id', $agenda->id_ambulatorio)
                    ->where('agenda.giorno', $giornoSettimanaRichiesto)
                    ->pluck('prenotazioni.ora_prenotazione')
                    ->map(function ($orario) {
                        return Carbon::parse($orario)->format('H:i');
                    })
                    ->toArray();

                $dataView['agenda'][] = [
                    'agenda' => $agenda,
                    'slots' => $slots,
                    'bookedSlots' => $bookedSlots,
                ];
            }
        } else {
            // Se non c'è filtro, non mostrare nulla
            $dataView['agenda'] = [];
        }

        return view('paziente.dashboard')->with('dataView', $dataView);
    }



    public function home(Request $request)
    {
        $user = Auth::user();
        $hasPrenotazioni = Prenotazione::where('id_paziente', $user->id)->exists();
        $dataView['hasPrenotazioni'] = $hasPrenotazioni;

        return view('paziente.index')->with('dataView', $dataView);
    }


    public function cercaDisponibilita(Request $request)
    {
        $brancaId = $request->input('branca_id');
        $dataInput = $request->input('data', Carbon::now()->toDateString());
        $dataView = $this->filtra( $dataInput, $brancaId);

        return view('paziente.dashboard', compact('dataView'));
    }


    public function logout()
    {
        Auth::logout(); // Effettua il logout dell'utente
        session()->invalidate(); // Invalidare la sessione
        session()->regenerateToken(); // Rigenera il token CSRF per sicurezza
        return redirect()->route('login')->with('success', 'Logout effettuato con successo');
    }


    public function creaPrenotazione(Request $request)
    {

        // 1. Login per ottenere il token
        $loginResponse = Http::post('http://10.61.28.63:8011/api/login', [
            'email' => 'servizioodontoiatria@asp.sr.it',
            'password' => 'K6_@4rD3#aa?xca'
        ]);

        $token = $loginResponse['access_token'] ;

        if (!$token) {
            return response()->json(['error' => 'Token non ottenuto'], 401);
        }

        // 2. Dati della prenotazione (possono anche arrivare da $request)
        $dataPrenotazione = [
            'datetime' => $request->data . " " . $request->orario,
            'duration' => 1,  // 2 slot = 1 ora
            'sector' => $request->idAmbulatorio,
            'description' => 'Prima prenotazione',
        ];

        // 3. Invio della richiesta POST
        $response = Http::withToken($token)
            ->post('http://10.61.28.63:8011/api/v1/booking', $dataPrenotazione);

        $apiData = $response['data'] ?? null;

        if (!$apiData) {
            return response()->json(['error' => 'Prenotazione fallita'], 400);
        }

        Prenotazione::create([
            'id_paziente' => Auth::user()->id,
            'data_prenotazione' => $apiData['date'],
            'ora_prenotazione' => $apiData['hour'],
            'centro_vaccinale_id' => $apiData['sector_id'],
            'stato_prenotazione' => $apiData['status'],
            'id_prenotazione' => $apiData['id'],
            'branca_id' => $request->branca_id,
            'creato_da' => Auth::user()->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('paziente.dashboard')->with('success', 'Operazione completata con successo!');
        // return response()->json($response->json());
    }


    public function prenotazioni()
    {
        $user = Auth::user();

        $dataView['prenotazioni'] = Prenotazione::where('id_paziente', $user->id)
            ->select(
                'prenotazioni.id',
                'prenotazioni.data_prenotazione',
                'prenotazioni.ora_prenotazione',
                'prenotazioni.stato_prenotazione',
                'prenotazioni.id_prenotazione',
                'ambulatori.descrizione as centro_vaccinale',
                'users.nome as nome_paziente',
                'users.cognome as cognome_paziente',
                'branche.nome_branca as branca_prestazione'
            )

            ->join('users', 'prenotazioni.id_paziente', '=', 'users.id')
            ->join('ambulatori', 'prenotazioni.centro_vaccinale_id', '=', 'ambulatori.id')
            ->join('branche', 'prenotazioni.branca_id', '=', 'branche.codice_branca')
            ->where('prenotazioni.stato_prenotazione', '=', -1)
            ->orderBy('data_prenotazione', 'desc')

            ->get();

        return view('paziente.prenotazioni')->with('dataView', $dataView);
    }

    public function eliminaPrenotazione(Request $request)
    {
        $prenotazione = Prenotazione::where('id_prenotazione', $request->input('id_prenotazione'));

        if (!$prenotazione) {
            return redirect()->route('paziente.prenotazioni')->with('error', 'Prenotazione non trovata.');
        }

        // 1. Login per ottenere il token
        $loginResponse = Http::post('http://10.61.28.63:8011/api/login', [
            'email' => 'servizioodontoiatria@asp.sr.it',
            'password' => 'K6_@4rD3#aa?xca'
        ]);

        $token = $loginResponse['access_token'] ?? null;

        $url = "http://10.61.28.63:8011/api/v1/bookings/{$request->input('id_prenotazione')}";

        $response = Http::withOptions([
            'verify' => false //Disabilita la verifica HTTPS
        ])->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])->delete($url);


        $prenotazione->update([
            'stato_prenotazione' => 2
        ]);

        return redirect()->route('paziente.prenotazioni')->with('success', 'Prenotazione eliminata con successo.');
    }

}
