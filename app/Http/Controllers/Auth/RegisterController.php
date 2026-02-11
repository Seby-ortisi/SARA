<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CriterioAccessoServizio; 

class RegisterController extends Controller
{
    public function __construct()
    {
        //    $this->middleware('guest'); // <-- questo ora funziona
    }

    public function showRegistrationForm()
    {
        $dataView['user'] = Auth::user();

        $dataView['nazioni'] = DB::table('nazionalita')
            ->select('id', 'nome_nazione', 'codice_nazione')
            ->get();

        $dataView['criteri_persona'] = DB::table('criteri_persona')
            ->select('id', 'descrizione')
            ->get();

        $dataView['criterio_contesto'] = DB::table('criteri_contesto')
            ->select('id', 'descrizione')
            ->get();

        $dataView['titoli_di_studio'] = DB::table('titoli_di_studio')
            ->select('id', 'descrizione')
            ->get();

        $dataView['condizione_professionale'] = DB::table('condizione_professionale')
            ->select('id', 'descrizione')
            ->get();

        $dataView['ricerca_lavoro'] = DB::table('ricerca_lavoro')
            ->select('id', 'descrizione')
            ->get();

        $dataView['categorie_di_vulnerabilita'] = DB::table('categorie_di_vulnerabilita')
            ->select('id', 'descrizione')
            ->get();

        return view('auth.register')->with('dataView', $dataView);
    }


    public function register(Request $request)
   {

    dd(Hash::make($request->password));

        $request->validate([
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'cellulare' => 'nullable|string|max:20',
            'data_nascita' => 'nullable|date',
            'codice_fiscale' => 'nullable|string|max:16|unique:users,codice_fiscale',
            
        ], [
            'nome.required' => 'Il campo nome è obbligatorio.',
            'cognome.required' => 'Il campo cognome è obbligatorio.',
            'email.required' => 'Il campo email è obbligatorio.',
            'email.email' => 'Inserisci un indirizzo email valido.',
            'email.unique' => 'Questa email è già registrata.',
            'password.required' => 'La password è obbligatoria.',
            'password.min' => 'La password deve contenere almeno 6 caratteri.',
            'password.confirmed' => 'Le password non corrispondono.',
            'codice_fiscale.unique' => 'Questo codice fiscale è già registrato.',
            'data_nascita.date' => 'Inserisci una data di nascita valida.',
            // aggiungi altri se vuoi messaggi specifici
        ]);

    
        $user = User::create([
            'nome' => $request->nome,
            'cognome' => $request->cognome,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cellulare' => $request->cellulare,
            'data_nascita' => $request->data_nascita,
            'codice_fiscale' => $request->codice_fiscale,
            'nazionalita' => $request->nazionalita,
            'provincia' => $request->provincia,
            'ruolo_id' => 1,
            'stato' => 0,
            'modalita_autenticazione' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user->update(['creato_da' => $user->id]);


        CriterioAccessoServizio::create([
            'id_user' => User::where('email', $request->email)->first()->id,
            'titolo_studio' => $request->titolo_studio,
            'condizione_professionale' => $request->condizione_professionale,
            'cerca_lavoro' => $request->ricerca_lavoro,
            'categoria_vulenerabilita' => $request->categoria_vulnerabilita,
            'criteri_contesto' => $request->criteri_contesto,
            'criteri_persona' => $request->criteri_persona,

            // Allegati salvati nel disco 'public'
            'allegato_tessera_sanitaria' => $request->hasFile('allegato') 
                ? $request->file('allegato')->store('allegati', 'public') 
                : null,

            'copia_primo_foglio_ISEE_minorenne' => $request->hasFile('copia_primo_foglio_ISEE_minorenne') 
                ? $request->file('copia_primo_foglio_ISEE_minorenne')->store('allegati', 'public') 
                : null,

            'documento_genitore' => $request->hasFile('documento_genitore') 
                ? $request->file('documento_genitore')->store('allegati', 'public') 
                : null,

            'copia_primo_foglio_ISEE' => $request->hasFile('copia_primo_foglio_ISEE') 
                ? $request->file('copia_primo_foglio_ISEE')->store('allegati', 'public') 
                : null,

            'permesso_soggiorno' => $request->hasFile('permesso_soggiorno') 
                ? $request->file('permesso_soggiorno')->store('allegati', 'public') 
                : null,

            'allegato_documento_identita' => $request->hasFile('allegato_documento_identita') 
                ? $request->file('allegato_documento_identita')->store('allegati', 'public') 
                : null,

            'note' => $request->note,
            
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        return redirect()->route('standardLogin')->with('success', 'Registrazione completata! Il tuo profilo è in attesa di validazione');
    }
}
