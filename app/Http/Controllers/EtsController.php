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
use App\Models\CriterioAccessoServizio;
use App\Models\PadreFiglio;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;



class EtsController extends Controller
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

        private function materialeIndex(){
          //   $dataView['user'] = Auth::user();

            $dataView['ets_nome'] = DB::table('user_ets')
                    ->join('users', 'user_ets.user_id', '=', 'users.id')
                    ->join('ets', 'user_ets.ets_id', '=', 'ets.id')
                    ->where('users.id', '=', Auth::user()->id)
                    ->select('ets.descrizione')
                    ->first();

        $dataView['nazioni'] = DB::table('nazionalita')
            ->select('id', 'nome_nazione', 'codice_nazione')
            ->get();

        $dataView['criteri_persona'] = DB::table('criteri_persona')
            ->select('id', 'descrizione','codice')
            ->get();

        $dataView['criterio_contesto'] = DB::table('criteri_contesto')
            ->select('id', 'descrizione','codice')
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

        // Aggiungi la voce "Altro tipo di vulnerabilità" con id 7 solo se non esiste già
        if (!$dataView['categorie_di_vulnerabilita']->contains('id', 7)) {
            $dataView['categorie_di_vulnerabilita']->push((object)[
                'id' => 7,
                'descrizione' => 'Altro tipo di vulnerabilità'
            ]);
        }

        $dataView['nazionalita'] = DB::table('nazionalita')
            ->select('id', 'nome_nazione', 'codice_nazione')
            ->get();

        $dataView['ets'] = DB::table('ets')
            ->select('id', 'descrizione')
            ->get();

        $dataView['comuni'] = DB::table('comuni')
            ->select('istat_comune', 'comune')
            ->get();

       $dataView['ets_user'] = DB::table('user_ets')
            ->select('ets_id')
            ->where('user_id', Auth::user()->id)
            ->first();


        return $dataView; 
    }


    public function home(Request $request)
    {

         $dataView = $this->materialeIndex(); 
         
        $currentEtsId = Auth::user()->id; // id dell'ETS corrente



        //dd($dataView['etsAssociati']);

        return view('ets.dashboard', compact('dataView'));
    }

    public function prentoazioniETS(Request $request)
    {

        $currentEtsId = Auth::user()->id; // id dell'ETS corrente

                $dataView['etsAssociati'] = DB::table('user_ets')
            //->leftJoin('ets', 'user_ets.ets_id', '=', 'ets.id')
            ->leftJoin('criteri_accesso_servizio', 'user_ets.ets_id', '=', 'criteri_accesso_servizio.id_ets')
            ->join('users', 'criteri_accesso_servizio.id_user', 'users.id')
            ->join('prenotazioni', 'users.id', "=", "prenotazioni.id_paziente")
            ->join('ambulatori', 'prenotazioni.centro_vaccinale_id', "=", "ambulatori.id")
            ->where('user_id', '=', Auth::user()->id)
            ->select('users.nome', 'users.cognome', 'criteri_accesso_servizio.id_ets','ambulatori.descrizione','prenotazioni.data_inizio', 'prenotazioni.data_fine','users.codice_fiscale')
            ->get();
            

        $dataView['prenotazioniETS'] = DB::table('user_ets')
            ->leftJoin('criteri_accesso_servizio', 'user_ets.ets_id', '=', 'criteri_accesso_servizio.id_ets')
            ->join('users', 'criteri_accesso_servizio.id_user', 'users.id')
            ->join('prenotazioni', 'users.id', "=", "prenotazioni.id_paziente")
            ->join('ambulatori', 'prenotazioni.centro_vaccinale_id', "=", "ambulatori.id")
            ->where('user_id', '=', Auth::user()->id)
            ->select('users.nome', 'users.cognome', 'criteri_accesso_servizio.id_ets','ambulatori.descrizione','prenotazioni.data_inizio', 'prenotazioni.data_fine','users.codice_fiscale')
            ->get();

        return view('ets.etsPrenotazioni')->with('dataView', $dataView);
    }   

public function etsInserisciPazienteAPC (Request $request)
{
    // Crea un array con i dati dal form
    $dataView = [
        'nome' => $request->nome,
        'cognome' => $request->cognome,
        'codice_fiscale' => $request->codice_fiscale,
        'data_nascita' => $request->data_nascita,
        'telefono' => $request->telefono,
        'residenza' => $request->residenza
    ];

    
    $materialeData = $this->materialeIndex();

    // Unisci i due array
    $dataView = array_merge($dataView, $materialeData); // Unisci i dati


    return view('ets.dashboard')->with('dataView', $dataView);
}

public function registraNuovoUtenteEts(Request $request)
{

 // === PERMESSO DI SOGGIORNO ===
    $docFile = $request->file('permesso_soggiorno');
    $permessoSoggiornoPath = null;
    if ($docFile && $docFile->isValid()) {
        $docInputPath = $docFile->getRealPath();
        $docOutputPath = storage_path('app/public/allegati/' . $docFile->getClientOriginalName());
        $docCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                  "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$docOutputPath}' '{$docInputPath}'";
        exec($docCmd, $outputDocFile, $returnCode);
        
        if ($returnCode === 0 && file_exists($docOutputPath)) {
            $permessoSoggiornoPath = 'allegati/' . $docFile->getClientOriginalName();
        } else {
            $permessoSoggiornoPath = $docFile->store('allegati', 'public');
        }
    }

      

    // === TESSERA SANITARIA ===
    $tesseraFile = $request->file('allegato_tessera_sanitaria');
    $tesseraSanitariaPath = null;
    if ($tesseraFile && $tesseraFile->isValid()) {
        $tesseraInputPath = $tesseraFile->getRealPath();
        $tesseraOutputPath = storage_path('app/public/allegati/' . $tesseraFile->getClientOriginalName());
        $tesseraCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                      "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$tesseraOutputPath}' '{$tesseraInputPath}'";
        exec($tesseraCmd, $outputTesseraFile, $returnCode);
        
        if ($returnCode === 0 && file_exists($tesseraOutputPath)) {
            $tesseraSanitariaPath = 'allegati/' . $tesseraFile->getClientOriginalName();
        } else {
            $tesseraSanitariaPath = $tesseraFile->store('allegati', 'public');
        }
    }

    // === ISEE MINORENNE ===
    $iseeMinFile = $request->file('copia_primo_foglio_ISEE_minorenne');
    $iseeMinorennePath = null;
    if ($iseeMinFile && $iseeMinFile->isValid()) {
        $iseeMinInputPath = $iseeMinFile->getRealPath();
        $iseeMinOutputPath = storage_path('app/public/allegati/' . $iseeMinFile->getClientOriginalName());
        $iseeMinCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                      "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$iseeMinOutputPath}' '{$iseeMinInputPath}'";
        exec($iseeMinCmd, $outputIseeMinFile, $returnCode);
        
        if ($returnCode === 0 && file_exists($iseeMinOutputPath)) {
            $iseeMinorennePath = 'allegati/' . $iseeMinFile->getClientOriginalName();
        } else {
            $iseeMinorennePath = $iseeMinFile->store('allegati', 'public');
        }
    }

    // === DOCUMENTO GENITORE ===
    $docGenFile = $request->file('documento_genitore');
    $documentoGenitorePath = null;
    if ($docGenFile && $docGenFile->isValid()) {
        $docGenInputPath = $docGenFile->getRealPath();
        $docGenOutputPath = storage_path('app/public/allegati/' . $docGenFile->getClientOriginalName());
        $docGenCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                     "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$docGenOutputPath}' '{$docGenInputPath}'";
        exec($docGenCmd, $outputDocGenFile, $returnCode);
        
        if ($returnCode === 0 && file_exists($docGenOutputPath)) {
            $documentoGenitorePath = 'allegati/' . $docGenFile->getClientOriginalName();
        } else {
            $documentoGenitorePath = $docGenFile->store('allegati', 'public');
        }
    }

    // === ISEE ===
    $iseeFile = $request->file('copia_primo_foglio_ISEE');
    $iseePath = null;
    if ($iseeFile && $iseeFile->isValid()) {
        $iseeInputPath = $iseeFile->getRealPath();
        $iseeOutputPath = storage_path('app/public/allegati/' . $iseeFile->getClientOriginalName());
        $iseeCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                   "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$iseeOutputPath}' '{$iseeInputPath}'";
        exec($iseeCmd, $outputIseeFile, $returnCode);
        
        if ($returnCode === 0 && file_exists($iseeOutputPath)) {
            $iseePath = 'allegati/' . $iseeFile->getClientOriginalName();
        } else {
            $iseePath = $iseeFile->store('allegati', 'public');
        }
    }

    // === DOCUMENTO IDENTITÀ ===
    $docIdFile = $request->file('allegato_documento_identita');
    $documentoIdentitaPath = null;
    if ($docIdFile && $docIdFile->isValid()) {
        $docIdInputPath = $docIdFile->getRealPath();
        $docIdOutputPath = storage_path('app/public/allegati/' . $docIdFile->getClientOriginalName());
        $docIdCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                    "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$docIdOutputPath}' '{$docIdInputPath}'";
        exec($docIdCmd, $outputDocIdFile, $returnCode);
        
        if ($returnCode === 0 && file_exists($docIdOutputPath)) {
            $documentoIdentitaPath = 'allegati/' . $docIdFile->getClientOriginalName();
        } else {
            $documentoIdentitaPath = $docIdFile->store('allegati', 'public');
        }
    }

    // === ALTRI FILE ===
    $altriFiles = [];
    if ($request->hasFile('altri_file')) {
        foreach ($request->file('altri_file') as $file) {
            if ($file->isValid()) {
                $altroInputPath = $file->getRealPath();
                $altroOutputPath = storage_path('app/public/allegati/' . $file->getClientOriginalName());
                $altroCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                            "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$altroOutputPath}' '{$altroInputPath}'";
                exec($altroCmd, $outputAltroFile, $returnCode);
                
                if ($returnCode === 0 && file_exists($altroOutputPath)) {
                    $altriFiles[] = 'allegati/' . $file->getClientOriginalName();
                } else {
                    $altriFiles[] = $file->store('allegati', 'public');
                }
            }
        }
    }

    // Gestione della stringa per altri file PDF
    $altriPdfString = !empty($altriFiles) ? implode(',', $altriFiles) : null;

    // Verifica se il codice fiscale esiste già
    $esisteCodiceFiscale = User::where('codice_fiscale', $request->codice_fiscale)->first();
    if ($esisteCodiceFiscale) {
        return redirect()->route('operatore.dashboard')->with('error', 'Codice fiscale gia\' esistente!');
    }

    // Verifica se l'email esiste già
    if (!empty($request->email)) {
        $esisteEmail = User::where('email', $request->email)->exists();
        if ($esisteEmail) {
            return redirect()->route('operatore.dashboard')->with('error', 'Email già esistente!');
        }
    }

    // Estrai l'id_assistito dal database
    $id_assistito = DB::connection('oracleSA4WEB')->table('ASSISTITI')
        ->select('assistito_id')
        ->where('codice_fiscale', '=', $request->codice_fiscale)
        ->first();

    $id_assistito_tutore = DB::connection('oracleSA4WEB')->table('ASSISTITI')
        ->select('assistito_id')
        ->where('codice_fiscale', '=', $request->codice_fiscale_tutore)
        ->first();

    $randomPassword = Str::random(10);

    // Verifica se l'utente esiste già
    $esisteUtente = User::where('codice_fiscale', $request->codice_fiscale)->first();
    
    if (!$esisteUtente) {
        // Registra nuovo utente solo se non esiste
        $user = User::create([
            'nome' => $request->nome,
            'cognome' => $request->cognome,
            'sesso' => $request->sesso,
            'email' => $request->email ?? null,
            'ruolo' => $request->ruolo,
            'cellulare' => $request->cellulare,
            'indirizzo' => $request->indirizzo,
            'data_nascita' => $request->data_nascita,
            'password' => Hash::make($randomPassword),
            'codice_fiscale' => $request->codice_fiscale,
            'nazionalita' => $request->nazionalita,
            'indirizzo_residenza' => $request->indirizzo_residenza,
            'luogo_nascita' => $request->luogo_nascita,
            'comune' => $request->comune,
            'stato' => 0,
            'ruolo_id' => 1,
            'modalita_autenticazione' => 1,
            'creato_da' => Auth::id(),
            'id_assistito' => $id_assistito?->assistito_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crea il record CriterioAccessoServizio
        CriterioAccessoServizio::create([
            'id_user' => $user->id,
            'id_ets' => $request->ets,
            'titolo_studio' => $request->titolo_studio,
            'condizione_professionale' => $request->condizione_professionale,
            'cerca_lavoro' => $request->ricerca_lavoro,
            'categoria_vulnerabilita' => $request->categoria_vulnerabilita,
            'condizione_vulnerabilita_socio_economica' => $request->conferma,
            'criteri_contesto' => $request->criteri_contesto,
            'criteri_persona' => $request->criteri_persona,
            'allegato_tessera_sanitaria' => $tesseraSanitariaPath,
            'copia_primo_foglio_ISEE_minorenne' => $iseeMinorennePath,
            'documento_genitore' => $documentoGenitorePath,
            'copia_primo_foglio_ISEE' => $iseePath,
            'permesso_soggiorno' => $permessoSoggiornoPath,
            'allegato_documento_identita' => $documentoIdentitaPath,
            'note' => $request->note,
            'altri_pdf' => $altriPdfString,
            'note_pdf' => $request->descrizione_file,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Creazione del padre o tutore se presente
    if($request->codice_fiscale_tutore){
        $padre = User::create([
            'nome' => $request->nome_tutore,
            'cognome' => $request->cognome_tutore,
            'sesso' => $request->sesso_tutore,
            'email' => $request->email_tutore ?? null,
            'cellulare' => $request->cellulare_tutore,
            'indirizzo' => $request->indirizzo_residenza_tutore,
            'data_nascita' => $request->data_nascita_tutore,
            'password' => Hash::make($randomPassword),
            'codice_fiscale' => $request->codice_fiscale_tutore,
            'nazionalita' => $request->nazionalita_tutore,
            'indirizzo_residenza' => $request->indirizzo_residenza_tutore,
            'luogo_nascita' => $request->luogo_nascita_tutore,
            'comune' => $request->comune_tutore,
            'stato' => 0,
            'ruolo_id' => 1,
            'modalita_autenticazione' => 1,
            'creato_da' => Auth::id(),
            'id_assistito' => $id_assistito_tutore ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Collegamento genitore (padre) al minore nella tabella padre_figlio
        PadreFiglio::create([
            'padre_id' => $padre->id,
            'figlio_id' => $user->id,
            'stato' => 1,
            'ruolo_referente' => $request->relazione,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Dopo la registrazione, generazione del PDF o reindirizzamento
    //return $this->pdf($request);
    return redirect()->route('ets.dashboard')->with('success', 'Utente registrato con successo!');
}

}
