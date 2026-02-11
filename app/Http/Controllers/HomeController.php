<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\CriterioAccessoServizio;
use App\Models\Prenotazione;
use Illuminate\Support\Carbon;
use App\Models\Nomenclatore;
use App\Models\Agenda;
use Illuminate\Support\Facades\Log;
use App\Models\Ambulatorio;
use App\Models\SchedaPaziente;
use App\CommonTrait;
use App\Models\CodiceICD9;
use mikehaertl\pdftk\Pdf;
use setasign\Fpdi\Fpdi;
use App\Models\Branca;
use App\Models\PadreFiglio;
use Mail;
use App\Mail\TestMail;

//use PDF;




class HomeController extends Controller
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

    public function modificaProfiloUtenteAutenticazione(Request $request)
    {
        $dataView['patient'] = Auth::user();
        $dataView['modalitaAccesso'] = $request->get('modalitaAccesso');

        return view('modificaProfiloUtente')->with('dataView', $dataView);
    }

    public function modificaProfiloUtente(Request $request)
    {

        $dataView['modalitaAccesso'] = 0;
        //Log::info("Inizio metodo: modificaProfiloUtente");
        if ($this->tiPuniscoSeSbagli($request->patientID))
            return redirect()->route("myLogoutGet");

        if (Auth::user()->id == $request->patientID)
            $dataView['patient'] = Auth::user();
        else
            $dataView['patient'] = Patient::find($request->patientID);

        //Log::info("fine metodo: modificaProfiloUtente");
        return view('modificaProfiloUtente')->with('dataView', $dataView);
    }

    public function aggiornaProfiloUtente(Request $request)
    {
        //Log::info("Inizio metodo: aggiornaProfiloUtente");
        if ($this->tiPuniscoSeSbagli($request->patientID))
            return redirect()->route("myLogoutGet");

        $dataView['aggiornaProfilo'] = Patient::where('id', $request->patientID)->first();

        // dd($dataView['aggiornaProfilo']->email);

        if (isset($request->phone) && $request->phone != $dataView['aggiornaProfilo']->phone) {
            $dataView['aggiornaProfilo']->update([
                'surname' => ucwords(strtolower($request->surname)),
                'name' => ucwords(strtolower($request->name)),
                'email' => $request->email,
                'phone' => $request->phone,
                'birth_date' => null,
                'phone_verified_at' => null,
            ]);

            return redirect()->route('smsOTP');
        }

        if (isset($request->email) && $request->email != $dataView['aggiornaProfilo']->email) {

            if ($request->email != $dataView['aggiornaProfilo']->first()->email) {
                $request->validate(
                    [
                        'email' => 'required|string|email|max:255|unique:patients',
                    ],
                    [
                        'email.unique' => 'L\'indirizzo email è già stato utilizzato.',
                    ]
                );
            }

            $dataView['aggiornaProfilo']->update([
                'surname' => ucwords(strtolower($request->surname)),
                'name' => ucwords(strtolower($request->name)),
                'email' => $request->email,
                'phone' => $request->phone,
                'birth_date' => null,
                'email_verified_at' => null,
            ]);
            return redirect()->route('emailOTP');
        }
        if ($request->modalitaAccesso == 2) {
            return redirect()->route('smsOTP');
        }

        //Log::info("fine metodo: aggiornaProfiloUtente");
        return redirect()->route("home");
    }


    /******************************Sezione PDF***************************************************************************** */

    private function pdf(Request $request)
    {


        $nazionalita = DB::table('nazionalita')
            ->where('codice_nazione', '=', $request->nazionalita)
            ->select('nome_nazione')
            ->first();

        $comune = DB::table('comuni')
            ->where('istat_comune', '=', $request->comune)
            ->select('comune', 'CAP')
            ->first();

        $pdfPath = storage_path('app/private/anagrafica.pdf');
        $outputPath = storage_path('app/private/test_compilato.pdf');
        $pdf = new Pdf($pdfPath);

        // ID del titolo di studio selezionato
        $titoloStudioId = $request->titolo_studio; //lettera x
        $condizioneProfessionale = $request->condizione_professionale;  //lettra h
        $ricercaLavoro = $request->ricerca_lavoro; //lettera i
        $categoriaVulnerabilita = $request->categoria_vulnerabilita; //lettera j
        // $criteriPersona = $request->criteri_persona ;//lettera d

        $criteriPersona = DB::table('criteri_persona')
            ->where('codice', '=', $request->criteri_persona)
            ->select('id')
            ->first();

        //dd( $criteriPersona);

        $codiceFiscale = strtoupper(str_replace(' ', '', $request->codice_fiscale));

        // Crea array con tutti i dati da inserire
        $pdfFields = [
            'nominativo' => $request->nome . " " . $request->cognome,
            'data_nascita' => implode('/', array_reverse(explode('-', $request->data_nascita))),
            'stato_nascita' =>  $nazionalita->nome_nazione ?? '',
            'provincia' => '',
            'comune_nascita' => $request->luogo_nascita ?? '',
            'indirizzo_residenza' => $request->indirizzo_residenza ?? '',
            'comune' => $comune->comune ?? '',
            'cap' => $comune->CAP ?? '',
            'provincia2' => 'Siracusa',
            'cellulare' => $request->cellulare ?? "nessun numero di cellulare",
            'email' => $request->email ?? 'Nessuna email',
            'altro_vulnerabilita' => $request->altro_categoria_vulnerabilita ?? '',
            //  'nominativo_operatore' => Auth::user()->nome . " " . Auth::user()->cognome,
        ];

        if ($request->sesso == 'M') {
            $pdfFields['M'] = 'Yes';
        } else {
            $pdfFields['F'] = 'Yes';
        }

   $tipoCodice = $request->input('tipo_codice');

for ($i = 0; $i < min(16, strlen($codiceFiscale)); $i++) {

    if ($tipoCodice === 'cf') {
        // Codice Fiscale
        $pdfFields['c' . ($i + 1)] = $codiceFiscale[$i];
    } else {
        // STP / ENI / NODOC
        $pdfFields['ci_' . ($i + 1)] = $codiceFiscale[$i];
    }
}



        /*
        for ($i = 0; $i < min(16, strlen($codiceFiscale)); $i++) {
            $pdfFields['cf' . ($i + 1)] = $codiceFiscale[$i];
        }
*/
        // Aggiungi la X nel campo PDF corrispondente al titolo di studio
        if ($titoloStudioId) {
            $pdfFields['x' . $titoloStudioId] = 'Yes';
        } else {
            $pdfFields['x0'] = 'Yes';
        }

        if ($condizioneProfessionale) {
            $pdfFields['h' . $condizioneProfessionale] = 'Yes';
        }

        if ($ricercaLavoro) {
            $pdfFields['i' . $ricercaLavoro] = 'Yes';
        }

        if ($categoriaVulnerabilita) {
            $pdfFields['j' . $categoriaVulnerabilita] = 'Yes';
        }

        if ($criteriPersona) {
            $pdfFields['d' . $criteriPersona->id] = 'Yes';
        }



           $success = $pdf->fillForm($pdfFields)
        ->flatten()
        ->saveAs($outputPath);

    if (!$success) {
        throw new \Exception('Errore nella generazione del PDF');
    }

    // 🔴 NIENTE download qui
    return $outputPath;
    }

    public function schedaPDF(Request $request)
    {

        $pdfPath = storage_path('app/private/scheda.pdf');
        $outputPath = storage_path('app/private/scheda_compilata.pdf');
        $pdf = new Pdf($pdfPath);

        // Controlla se il paziente è un figlio in padre_figlio
        $padreRecord = DB::table('padre_figlio')
            ->where('figlio_id', $request->paziente_id)
            ->first();

        // Se esiste un padre, usa l'id del padre, altrimenti usa il paziente stesso
        $idDaUsare = $padreRecord ? $padreRecord->padre_id : $request->paziente_id;

        // Recupera i dati del paziente (o del padre se trovato)
        $paziente = DB::table('users')
            ->join('nazionalita', 'users.nazionalita', '=', 'nazionalita.codice_nazione')
            ->join('comuni', 'users.comune', '=', 'comuni.istat_comune')
            ->where('users.id', $idDaUsare)
            ->select(
                'users.nome',
                'users.cognome',
                'users.data_nascita',
                'users.sesso',
                'users.codice_fiscale',
                'nazionalita.nome_nazione',
                'comuni.comune'
            )
            ->first();

        $figlio = DB::table('users')
            ->where('id', $request->paziente_id)
            ->select('codice_fiscale')
            ->first();

        $codiceFiscale = $figlio
            ? strtoupper(str_replace(' ', '', $figlio->codice_fiscale))
            : '';


        // Recupera la prestazione
        $prestazione = DB::table('nomenclatore')
            ->where('codice_nomenclatore', '=', $request->prestazione_id)
            ->select('denominazione_nomenclatore', 'codice_nomenclatore')
            ->first();

        // Gestione codici ICD9
        $codiciICD9 = $request->input('icd9_codes', []);
        $descrizioniICD9 = $request->input('icd9_descriptions', []);

        $codiciICD9String = '';
        $descrizioniICD9String = '';

        // Verifica se sono array e non stringhe
        if (is_array($codiciICD9) && !empty($codiciICD9)) {
            $codiciICD9String = implode(', ', $codiciICD9);
            $descrizioniICD9String = is_array($descrizioniICD9)
                ? implode(', ', $descrizioniICD9)
                : '';
        }

        // Prepara i campi per il PDF
        $pdfFields = [
            'nominativo_operatore' => Auth::user()->nome . " " . Auth::user()->cognome,
            'luogo_prestazione' => $request->luogo_prestazione,
            'nominativo_paziente' =>  $paziente->nome . " " . $paziente->cognome,
            'luogo_nascita' =>  $paziente->comune,
            'data_nascita' => $paziente->data_nascita ?? '',
            'stato_paziente' => $paziente->nome_nazione ?? '',
            'prestazione_erogata' => $prestazione->denominazione_nomenclatore ?? '',
            'codice_icd9' =>  $codiciICD9String,
            'luogo_prestazione_due' => $request->luogo_prestazione,
            'diagnosi' =>  $descrizioniICD9String,
        ];

        // Sesso M o F per il PDF
        if ($paziente->sesso == 'M') {
            $pdfFields['M'] = 'Yes';
        } else {
            $pdfFields['F'] = 'Yes';
        }

        if (isset($padreRecord)) {
            if ($padreRecord->ruolo_referente != 1) {
                $pdfFields['referente' . $padreRecord->ruolo_referente] = 'Yes';
            } else {
                $pdfFields['referente1'] = 'Yes';
            }
        }

        // Codice fiscale lettera per lettera
        for ($i = 0; $i < min(16, strlen($codiceFiscale)); $i++) {
            $pdfFields['cf' . ($i + 1)] = $codiceFiscale[$i];
        }

        // Compila e salva il PDF
        $success = $pdf->fillForm($pdfFields)
            ->flatten()
            ->saveAs($outputPath);

        return response()->download($outputPath);
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    private function materialeIndex()
    {
        //   $dataView['user'] = Auth::user();

        $dataView['nazioni'] = DB::table('nazionalita')
            ->select('id', 'nome_nazione', 'codice_nazione')
            ->get();

        $dataView['criteri_persona'] = DB::table('criteri_persona')
            ->select('id', 'descrizione', 'codice')
            ->get();

        $dataView['criterio_contesto'] = DB::table('criteri_contesto')
            ->select('id', 'descrizione', 'codice')
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

        return $dataView;
    }

    /****************************REGISTRAZIONE NUOVO UTENTE***************************************** */

    public function index()
    {
        $dataView = $this->materialeIndex();


        return view('operatore.dashboard')->with('dataView', $dataView);
    }


    public function logout()
    {
        Auth::logout(); // Effettua il logout dell'utente
        session()->invalidate(); // Invalidare la sessione
        session()->regenerateToken(); // Rigenera il token CSRF per sicurezza
        return redirect()->route('login')->with('success', 'Logout effettuato con successo');
    }


    public function registraNuovoUtente(Request $request)
    {
      

        $tipoCodice = $request->input('tipo_codice');

        // === GESTIONE FILE (come nel tuo codice) ===
        $permessoSoggiornoPath = null;
        if ($request->hasFile('permesso_soggiorno') && $request->file('permesso_soggiorno')->isValid()) {
            $docFile = $request->file('permesso_soggiorno');
            $docInputPath = $docFile->getRealPath();
            $docOutputPath = storage_path('app/public/allegati/' . $docFile->getClientOriginalName());
            $docCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$docOutputPath}' '{$docInputPath}'";
            exec($docCmd, $outputDocFile, $returnCode);

            $permessoSoggiornoPath = ($returnCode === 0 && file_exists($docOutputPath))
                ? 'allegati/' . $docFile->getClientOriginalName()
                : $docFile->store('allegati', 'public');
        }

        // [... resto della gestione file come nel tuo codice ...]

        $tesseraSanitariaPath = null;
        if ($request->hasFile('allegato_tessera_sanitaria') && $request->file('allegato_tessera_sanitaria')->isValid()) {
            $tesseraFile = $request->file('allegato_tessera_sanitaria');
            $tesseraInputPath = $tesseraFile->getRealPath();
            $tesseraOutputPath = storage_path('app/public/allegati/' . $tesseraFile->getClientOriginalName());
            $tesseraCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$tesseraOutputPath}' '{$tesseraInputPath}'";
            exec($tesseraCmd, $outputTesseraFile, $returnCode);

            $tesseraSanitariaPath = ($returnCode === 0 && file_exists($tesseraOutputPath))
                ? 'allegati/' . $tesseraFile->getClientOriginalName()
                : $tesseraFile->store('allegati', 'public');
        }

        $iseeMinorennePath = null;
        if ($request->hasFile('copia_primo_foglio_ISEE_minorenne') && $request->file('copia_primo_foglio_ISEE_minorenne')->isValid()) {
            $iseeMinFile = $request->file('copia_primo_foglio_ISEE_minorenne');
            $iseeMinInputPath = $iseeMinFile->getRealPath();
            $iseeMinOutputPath = storage_path('app/public/allegati/' . $iseeMinFile->getClientOriginalName());
            $iseeMinCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$iseeMinOutputPath}' '{$iseeMinInputPath}'";
            exec($iseeMinCmd, $outputIseeMinFile, $returnCode);

            $iseeMinorennePath = ($returnCode === 0 && file_exists($iseeMinOutputPath))
                ? 'allegati/' . $iseeMinFile->getClientOriginalName()
                : $iseeMinFile->store('allegati', 'public');
        }

        $documentoGenitorePath = null;
        if ($request->hasFile('documento_genitore') && $request->file('documento_genitore')->isValid()) {
            $docGenFile = $request->file('documento_genitore');
            $docGenInputPath = $docGenFile->getRealPath();
            $docGenOutputPath = storage_path('app/public/allegati/' . $docGenFile->getClientOriginalName());
            $docGenCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$docGenOutputPath}' '{$docGenInputPath}'";
            exec($docGenCmd, $outputDocGenFile, $returnCode);

            $documentoGenitorePath = ($returnCode === 0 && file_exists($docGenOutputPath))
                ? 'allegati/' . $docGenFile->getClientOriginalName()
                : $docGenFile->store('allegati', 'public');
        }

        $iseePath = null;
        if ($request->hasFile('copia_primo_foglio_ISEE') && $request->file('copia_primo_foglio_ISEE')->isValid()) {
            $iseeFile = $request->file('copia_primo_foglio_ISEE');
            $iseeInputPath = $iseeFile->getRealPath();
            $iseeOutputPath = storage_path('app/public/allegati/' . $iseeFile->getClientOriginalName());
            $iseeCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$iseeOutputPath}' '{$iseeInputPath}'";
            exec($iseeCmd, $outputIseeFile, $returnCode);

            $iseePath = ($returnCode === 0 && file_exists($iseeOutputPath))
                ? 'allegati/' . $iseeFile->getClientOriginalName()
                : $iseeFile->store('allegati', 'public');
        }

        $documentoIdentitaPath = null;
        if ($request->hasFile('allegato_documento_identita') && $request->file('allegato_documento_identita')->isValid()) {
            $docIdFile = $request->file('allegato_documento_identita');
            $docIdInputPath = $docIdFile->getRealPath();
            $docIdOutputPath = storage_path('app/public/allegati/' . $docIdFile->getClientOriginalName());
            $docIdCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$docIdOutputPath}' '{$docIdInputPath}'";
            exec($docIdCmd, $outputDocIdFile, $returnCode);

            $documentoIdentitaPath = ($returnCode === 0 && file_exists($docIdOutputPath))
                ? 'allegati/' . $docIdFile->getClientOriginalName()
                : $docIdFile->store('allegati', 'public');
        }

        $altriFiles = [];
        if ($request->hasFile('altri_file')) {
            foreach ($request->file('altri_file') as $file) {
                if ($file->isValid()) {
                    $altroInputPath = $file->getRealPath();
                    $altroOutputPath = storage_path('app/public/allegati/' . $file->getClientOriginalName());
                    $altroCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen " .
                        "-dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$altroOutputPath}' '{$altroInputPath}'";
                    exec($altroCmd, $outputAltroFile, $returnCode);

                    $altriFiles[] = ($returnCode === 0 && file_exists($altroOutputPath))
                        ? 'allegati/' . $file->getClientOriginalName()
                        : $file->store('allegati', 'public');
                }
            }
        }

        $altriPdfString = !empty($altriFiles) ? implode(',', $altriFiles) : null;

        // Verifica codice fiscale
        $esisteCodiceFiscale = User::where('codice_fiscale', $request->codice_fiscale)->first();
        if ($esisteCodiceFiscale) {
            return redirect()->route('operatore.dashboard')->with('error', 'Codice fiscale già esistente!');
        }

        // Verifica email
        if (!empty($request->email)) {
            $esisteEmail = User::where('email', $request->email)->exists();
            if ($esisteEmail) {
                return redirect()->route('operatore.dashboard')->with('error', 'Email già esistente!');
            }
        }

        // Estrai id_assistito
        $id_assistito = DB::connection('oracleSA4WEB')->table('ASSISTITI')
            ->select('assistito_id')
            ->where('codice_fiscale', '=', $request->codice_fiscale)
            ->first();

        $randomPassword = Str::random(10);

        // CORREZIONE: Crea sempre l'utente e assegna a $user
        $user = User::create([
            'nome' => $request->nome,
            'cognome' => $request->cognome,
            'sesso' => $request->sesso,
            'email' => $request->email ?? null,
            'cellulare' => $request->cellulare,
            'data_nascita' => $request->data_nascita,
            'password' => Hash::make($randomPassword),
            'codice_fiscale' => $request->codice_fiscale,
            'nazionalita' => $request->nazionalita,
            'indirizzo_residenza' => $request->indirizzo_residenza,
            'luogo_nascita' => $request->luogo_nascita,
            'comune' => $request->comune,
            'stato' => 1,
            'ruolo_id' => 1,
            'modalita_autenticazione' => 1,
            'creato_da' => Auth::id(),
            'id_assistito' => $id_assistito->assistito_id ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crea CriterioAccessoServizio
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

        // Gestione tutore
        if ($request->codice_fiscale_tutore) {
            $id_assistito_tutore = DB::connection('oracleSA4WEB')->table('ASSISTITI')
                ->select('assistito_id')
                ->where('codice_fiscale', '=', $request->codice_fiscale_tutore)
                ->first();

            $padre = User::create([
                'nome' => $request->nome_tutore,
                'cognome' => $request->cognome_tutore,
                'sesso' => $request->sesso_tutore,
                'email' => $request->email_tutore ?? null,
                'cellulare' => $request->cellulare_tutore,
                'data_nascita' => $request->data_nascita_tutore,
                'password' => Hash::make($randomPassword),
                'codice_fiscale' => $request->codice_fiscale_tutore,
                'nazionalita' => $request->nazionalita_tutore,
                'indirizzo_residenza' => $request->indirizzo_residenza_tutore,
                'luogo_nascita' => $request->luogo_nascita_tutore,
                'comune' => $request->comune_tutore,
                'stato' => 1,
                'ruolo_id' => 1,
                'modalita_autenticazione' => 1,
                'creato_da' => Auth::id(),
                'id_assistito' => $id_assistito_tutore->assistito_id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            PadreFiglio::create([
                'padre_id' => $padre->id,
                'figlio_id' => $user->id, // Ora $user è sempre definita!
                'stato' => 1,
                'ruolo_referente' => $request->relazione,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // CORREZIONE: Un solo return
 $pdfPath = $this->pdf($request);

return redirect()
    ->route('operatore.dashboard')
    ->with('success', 'Utente registrato correttamente!')
    ->with('pdf_download', basename($pdfPath));

    }


    public function ricercaCF(Request $request)
    {
        $data = [
            'email' => config('constants.MIDDLEWARE_LOGIN'),
            'password' => config('constants.MIDDLEWARE_PASSWORD'),
        ];

        $curl = curl_init(config('constants.MIDDLEWARE_URL') . config('constants.MIDDLEWARE_API_LOGIN'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            // gestisci l'errore
            // Log::channel('daily')->info('(OperatoreController) ricercaCF >' . $request->get('query') . "> " . curl_error($curl));
            return null;
        } else {
            $result = json_decode($response, true);
        }
        curl_close($curl);

        $data = null;
        $curl = curl_init(config('constants.MIDDLEWARE_URL') . config('constants.MIDDLEWARE_API_RICERCA_ANAGRAFICA_CODICEFISCALE') . strtoupper($request->get('query')));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $result['token'],
        ]);
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            // gestisci l'errore
            // Log::channel('daily')->info('(OperatoreController) ricercaCF >' . $request->get('query') . "> " . curl_error($curl));
            return null;
        } else {
            $data = json_decode($response, true);
        }
        curl_close($curl);


        return $data;
    }

    public function inserisciPazienteAPC(Request $request)
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


        return view('operatore.dashboard')->with('dataView', $dataView);
    }

    /********************************************VISUALIZZAZIONE E GESTIONE PRENOTAZIONI******************************************************************** */
    public function prenotazioni(Request $request, array $extraData = [])
    {
        $dataView = [];

        if (isset($extraData['dataView'])) {
            $dataView = array_merge($dataView, $extraData['dataView']);
        }

        $dataView['prenotazioni'] = Prenotazione::select(
            'prenotazioni.id',
            'id_paziente',
            'data_inizio',
            'data_fine',
            'stato_prenotazione',
            'nome',
            'cognome',
            'cellulare',
            'codice_fiscale',
            'ambulatori.descrizione as luogo_prestazione',
            'denominazione_nomenclatore as nome_prestazione',
            'branche.nome_branca as nome_branca',
            'nomenclatore.codice_nomenclatore as codice_nomenclatore',
            'branche.id as branca_id',
            DB::raw('(SELECT scheda_amnestica FROM scheda_paziente WHERE paziente_id = users.id ORDER BY updated_at DESC LIMIT 1) as scheda_amnestica')
        )
            ->join('users', 'prenotazioni.id_paziente', '=', 'users.id')
            ->join('ambulatori', 'prenotazioni.centro_vaccinale_id', '=', 'ambulatori.id')
            ->join('nomenclatore', 'prenotazioni.branca_id', '=', 'nomenclatore.codice_nomenclatore')
            ->join('branche', 'nomenclatore.id_branca', '=', 'branche.id')
            ->leftJoin('criteri_accesso_servizio', 'users.id', '=', 'criteri_accesso_servizio.id_user')
            ->get();


        $dataView['countPrenotazioni'] = $dataView['prenotazioni']->count();

        // 2. Parametri GET (prestazione + data)
        $prestazioneId = $request->input('prestazione_id');
        $dataInput = $request->input('data', Carbon::now()->toDateString());
        $giornoSettimana = ucfirst(Carbon::parse($dataInput)->locale('it')->isoFormat('dddd')); // es. "Martedì"

        // 3. Dropdown prestazioni
        $dataView['prestazioni'] = Nomenclatore::where('stato', 1)->get();

        $dataView['farmaci'] = DB::table('farmaci')
            ->select('id', 'descrizione', 'tipologia_farmaco', 'codice_farmaco')
            ->where('stato', 1)
            ->get();

        $dataView['tipologia_farmaco'] = DB::table('tipologia_farmaco')
            ->select('id', 'descrizione_tipologia_farmaco')
            ->where('stato', 1)
            ->get();

        $dataView['data'] = $dataInput;
        $dataView['prestazione_id'] = $prestazioneId;
        $dataView['agenda'] = [];
        $dataView['ambulatori'] = DB::table('ambulatori')->get();
        $dataView['branche'] = DB::table('branche')
            ->select('id', 'nome_branca')
            ->where('stato_branca', '=', 1)
            ->get();

        $dataView['dispositivo_medico'] = DB::table('dispositivo_medico')
            ->select('id', 'codice_dispositivo', 'nome_dispositivo')
            ->get();

        return view('operatore.prenotazioni')->with('dataView', $dataView);
    }

    public function mostraPrenotazioni(Request $request)
    {
        // Query corretta con LEFT JOIN per evitare perdita di dati
        $prenotazioni = DB::table('prenotazioni')
            ->select(
                'prenotazioni.id',
                'prenotazioni.id_paziente',
                'prenotazioni.data_inizio',
                'prenotazioni.data_fine',
                'prenotazioni.stato_prenotazione',
                'prenotazioni.note',
                'prenotazioni.branca_id',
                'prenotazioni.centro_vaccinale_id',
                'users.nome',
                'users.cognome',
                'users.cellulare',
                'users.codice_fiscale',
                'users.email',
                'nomenclatore.denominazione_nomenclatore as nome_prestazione',
                'ambulatori.descrizione as luogo_prestazione',
                'branche.nome_branca',
                'branche.id as id_branca',
                'ambulatori.id as id_ambulatorio',
                  DB::raw('(SELECT scheda_amnestica FROM scheda_paziente WHERE paziente_id = users.id ORDER BY updated_at DESC LIMIT 1) as scheda_amnestica')
            )
            ->leftJoin('users', 'prenotazioni.id_paziente', '=', 'users.id')
            ->leftJoin(
                DB::raw('(
                    SELECT 
                        codice_nomenclatore,
                        MIN(denominazione_nomenclatore) AS denominazione_nomenclatore,
                        MIN(id_branca) AS id_branca
                    FROM nomenclatore
                    GROUP BY codice_nomenclatore
                ) as nomenclatore'),
                'prenotazioni.branca_id',
                '=',
                'nomenclatore.codice_nomenclatore'
            )
            ->leftJoin('ambulatori', 'prenotazioni.centro_vaccinale_id', '=', 'ambulatori.id')
            ->leftJoin('branche', 'nomenclatore.id_branca', '=', 'branche.id')
            ->leftJoin('criteri_accesso_servizio', 'users.id', '=', 'criteri_accesso_servizio.id_user')
            ->orderByDesc('prenotazioni.data_inizio')
            ->get();


        // Debug: verifica quante prenotazioni hai
        \Log::info('Numero prenotazioni trovate: ' . $prenotazioni->count());

        // Parametri GET (prestazione + data)
        $prestazioneId = $request->input('prestazione_id');
        $dataInput = $request->input('data', Carbon::now()->toDateString());
        $giornoSettimana = ucfirst(Carbon::parse($dataInput)->locale('it')->isoFormat('dddd'));

        // Dropdown prestazioni
        $prestazioni = Nomenclatore::where('stato', 1)->get();

        // Branche
        $branche = DB::table('branche')
            ->select('id', 'nome_branca')
            ->where('stato_branca', '=', 1)
            ->get();

        $agenda = [];

        // Se è stata selezionata una prestazione, carica agende e slot
        if ($prestazioneId) {
            $agende = Agenda::select(
                'agenda.id',
                'agenda.giorno',
                'agenda.ora_inizio',
                'agenda.ora_fine',
                'agenda.slot',
                'ambulatori.id as id_ambulatorio',
                'ambulatori.descrizione as descrizione_ambulatorio'
            )
                ->join('ambulatori', 'agenda.id_ambulatorio', '=', 'ambulatori.id')
                ->where('agenda.stato', '=', 1)
                ->where('agenda.giorno', $giornoSettimana)
                ->get();

            $agenda = $agende;
        }

        //dd($prenotazioni);
        // Restituisci direttamente i dati delle prenotazioni come array, senza oggetto contenitore
        return response()->json($prenotazioni);
    }

public function getSlots(Request $request)
{
    $dataInput = $request->input('data', Carbon::now()->toDateString());
    $brancaId = $request->branca_id;
    $ambulatorioId = $request->input('ambulatorio_id', null); // Nuovo parametro opzionale
    
    $filtroSlot = $this->filtra($dataInput, $brancaId);
    
    // Se è stato selezionato un ambulatorio specifico, filtra
    if ($ambulatorioId) {
        $filtroSlot['agenda'] = array_filter($filtroSlot['agenda'], function($item) use ($ambulatorioId) {
            return $item['agenda']->id_ambulatorio == $ambulatorioId;
        });
        $filtroSlot['agenda'] = array_values($filtroSlot['agenda']); // Re-index array
    }
    
    // Estrai lista ambulatori unici disponibili
    $ambulatoriDisponibili = collect($filtroSlot['agenda'])
        ->pluck('agenda')
        ->unique('id_ambulatorio')
        ->map(function($agenda) {
            return [
                'id' => $agenda->id_ambulatorio,
                'descrizione' => $agenda->descrizione_ambulatorio
            ];
        })
        ->values();
    
    return response()->json([
        'agenda' => $filtroSlot['agenda'],
        'ambulatori' => $ambulatoriDisponibili
    ]);
}

    public function prenota(Request $request)
    {


        $dataView['ets_user'] = DB::table('users')
            ->select('id_ets')
            ->join('criteri_accesso_servizio', 'users.id', '=', 'criteri_accesso_servizio.id_user')
            ->join('ets', 'criteri_accesso_servizio.id_ets', '=', 'ets.id')
            ->leftjoin('user_ets', 'users.id', '=', 'user_ets.user_id')
            ->where('users.id', $request->id_paziente)
            ->first();

      /*
        $dataView['email_ets'] = DB::table('user_ets')
            ->where('ets_id', $dataView['ets_user']->id_ets)
            ->join('users', 'user_ets.user_id', '=', 'users.id')
            ->select('users.email')
            ->first();
      */
            
        if(!empty($dataView['ets_user'])){
                $dataView['email_ets'] = DB::table('ets')
                            ->where('ets.id', $dataView['ets_user']->id_ets)
                            ->select('ets.indirizzo_pec_mail as email')
                            ->first();
        }


        //$isIstantanea = $request->has('prenotazione_istantanea') || empty($request->orario);

        $isIstantanea = $request->prenotazione_istantanea == '1' || empty($request->orario);


        if ($isIstantanea) {
            $dataInizio = now();
            $dataFine = now();
        } else {

            $orari = $request->orario;
            $oraInizio = is_array($orari) ? $orari[0] : $orari;
            $oraFine = is_array($orari) ? end($orari) : $orari;

            $dataPrenotazione = $request->data;
            $dataInizio = $dataPrenotazione . ' ' . $oraInizio;
            $dataFine = $dataPrenotazione . ' ' . $oraFine;
        }

        Prenotazione::create([
            'id_paziente' => $request->id_paziente,
            'data_inizio' => $dataInizio,
            'data_fine' => $dataFine,
            'centro_vaccinale_id' => $request->idAmbulatorio ?? null,
            'stato_prenotazione' => -1,
            'id_prenotazione' => null,
            'branca_id' => $request->codice_nomenclatore,
            'creato_da' => Auth::user()->id,
            'note' => $request->note,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $dataView['datiEmail'] = DB::table('users')
            ->select(
                'nome',
                'cognome',
                'codice_fiscale',
                'data_nascita',
                'cellulare',
                'nomenclatore.denominazione_nomenclatore as nome_prestazione',
                'ambulatori.descrizione as luogo_prestazione',
                'prenotazioni.data_inizio',
                'prenotazioni.data_fine'
            )
            ->join('prenotazioni', 'users.id', '=', 'prenotazioni.id_paziente')
            ->join('nomenclatore', 'prenotazioni.branca_id', '=', 'nomenclatore.codice_nomenclatore')
            ->join('ambulatori', 'prenotazioni.centro_vaccinale_id', '=', 'ambulatori.id')
            ->where('users.id', $request->id_paziente)
            ->orderBy('prenotazioni.id', 'desc') // ordina per id decrescente → ultimo record
            ->first();

        

        if (!empty($dataView['email_ets'])) {
            Mail::to($dataView['email_ets'])->send(new TestMail($dataView));
        }



        return redirect()->route('operatore.prenotazioni')->with('success', 'Operazione completata con successo!');
    }


    public function registraScheda(Request $request)
    {
        
        $farmaciRaw = $request->input('farmaci_codes', []);

        // Se è una stringa JSON, la decodifico
        if (is_string($farmaciRaw)) {
            $farmaci = json_decode($farmaciRaw, true) ?: [];
        } else {
            $farmaci = is_array($farmaciRaw) ? $farmaciRaw : explode(',', $farmaciRaw);
        }

        // Pulizia: rimuovo null, 'null', stringhe vuote e duplicati
        $farmaciPuliti = array_unique(
            array_filter($farmaci, function ($item) {
                return !empty($item) && strtolower(trim($item)) !== 'null';
            })
        );

        // Creo la stringa finale per il database
        $tipologiaFarmaco = implode(',', $farmaciPuliti);

        // Aggiornamento della prenotazione
        Prenotazione::where('id', $request->prenotazione_id)
            ->update(['stato_prenotazione' => $request->presenza]);

        if ($request->presenza == 0) {
            return redirect()->route('operatore.prenotazioni')->with('success', 'Paziente Assente.');
        }

        // Gestione dei codici ICD9
        $codiciString = null;
        if (!empty($request->icd9_codes) && !empty($request->icd9_descriptions)) {
            $codiciString = collect($request->icd9_codes)
                ->map(fn($code, $index) => $code . ' - ' . ($request->icd9_descriptions[$index] ?? ''))
                ->implode('; ');
        }

        // Gestione "altro farmaco"
        $altroFarmaco = $request->input('altroFarmacoDescrizione') ?: null;
        $altroFarmacoCodice = $request->input('altroFarmacoCodice') ?: null;

        // Creazione o aggiornamento scheda paziente
        SchedaPaziente::updateOrCreate(
            ['id_prenotazione' => $request->prenotazione_id],
            [
                'paziente_id' => $request->paziente_id,
                'diagnosi' => $request->diagnosi ?? null,
                'prestazione_erogata' => $request->prestazione_id,
                'luogo_prestazione' => $request->luogo_prestazione,
                'codici_icd9' => $codiciString,
                'erogazione_farmaci' => $request->erogazioneFarmaci,
                'tipologia_farmaco' => $tipologiaFarmaco ?: $altroFarmacoCodice,
                'altro_farmaco_descrizione' => $altroFarmaco,
                'erogazione_dispositivo_medico' => $request->erogazioneDispositivo,
                'tipologia_dispositivo_medico' => $request->dispositivo_medico,
                'scheda_amnestica' => $request->schedaVisita,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        return $this->schedaPDF($request);
    }


    public function cercaPazienti(Request $request)
    {
        $search = trim($request->input('search'));


        if (!$search) {
            return response()->json(['error' => 'Parametro di ricerca mancante.'], 400);
        }

        $searchTerms = preg_split('/\s+/', $search);

        $dataView['paziente'] = User::query()
            ->when(count($searchTerms) === 2, function ($q) use ($searchTerms) {
                $q->where(function ($sub) use ($searchTerms) {
                    $sub->where('cognome', 'like', "%{$searchTerms[0]}%")
                        ->where('nome', 'like', "%{$searchTerms[1]}%");
                })->orWhere(function ($sub) use ($searchTerms) {
                    $sub->where('nome', 'like', "%{$searchTerms[0]}%")
                        ->where('cognome', 'like', "%{$searchTerms[1]}%");
                });
            })
            ->when(count($searchTerms) === 1, function ($q) use ($searchTerms, $search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('cognome', 'like', "%{$search}%")
                    ->orWhere('codice_fiscale', 'like', "%{$search}%");
            })
            ->get(); // restituisce solo il primo match

        return $this->prenotazioni($request, ['dataView' => $dataView]);

        //return view('operatore.prenotazioni')->with('dataView', $dataView);

    }


    public function prenotazioneIstantanea(Request $request)
    {

        Carbon::setLocale('it'); // Imposta lingua italiana
        $giorno = now()->translatedFormat('l'); // es. "lunedì"
        $agenda = Agenda::where('giorno', $giorno)
            ->select('id')
            ->first();

        Prenotazione::create([
            'id_paziente' => $request->input('paziente_id'),
            'data_inizio' => now(),
            'centro_vaccinale_id' => $request->ambulatorio_id,
            'stato_prenotazione' => -1,
            'id_prenotazione' => null,
            'branca_id' => $request->input('prestazione_id'),
            'creato_da' => Auth::user()->id,
            'note' => $request->note,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('operatore.prenotazioni')->with('success', 'Prneotazione instantanea effettuata con successo!');
    }

/*
    public function search(Request $request)
    {
        $term = $request->get('term');

        $results = CodiceICD9::where('codice', 'like', "%{$term}%")
            ->orWhere('descrizione', 'like', "%{$term}%")
            ->limit(10)
            ->get();

        return response()->json($results);
    }
*/
    /*************************************************MODIFICA BRANCA E PRESTAZIONI **************************************************/

    public function modifica(Request $request)
    {
        $dataView = [];
        return view('operatore.modifica')->with('dataView', $dataView);
    }

    public function modificaBranche(Request $request)
    {

        $dataView = [];

        // Tutte le branche (per il filtro)
        $dataView['brancheSelect'] = DB::table('branche')->where('stato_branca', '=', 1)->get();

        // Branca selezionata
        $selectedBrancaId = $request->input('branca_id');
        $dataView['selectedBrancaId'] = $selectedBrancaId;

        // Prestazioni solo se è selezionata una branca
        if ($selectedBrancaId) {
            $dataView['branche'] = DB::table('nomenclatore')
                ->join('branche', 'nomenclatore.id_branca', '=', 'branche.id')
                ->where('branche.id', $selectedBrancaId)
                ->where('nomenclatore.stato', '=', 1)
                ->select('nomenclatore.id as id_nomenclatore', 'branche.nome_branca', 'nomenclatore.denominazione_nomenclatore')
                ->get();
        } else {
            $dataView['branche'] = collect(); // vuoto
        }

        return view('operatore.modificaBranche')->with('dataView', $dataView);
    }


    public function modificaParametriBranche(Request $request)
    {
        $prestazioni = $request->input('prestazioni', []);
        foreach ($prestazioni as $prestazione) {
            // Trovo il record per ID
            $record = Nomenclatore::find($prestazione['id']);
            if ($record) {
                $record->denominazione_nomenclatore = $prestazione['denominazione'];
                $record->save();
            }
        }

        return redirect()->back()->with('success', 'Prestazioni aggiornate correttamente!');
    }

    public function aggiungiBrancaPrestazione(Request $request)
    {

        $brancaId = Branca::updateOrCreate(

            ['nome_branca' => $request->nome_branca],
            [
                'nome_branca' => $request->nome_branca,
                'stato_branca' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        /*
            $brancaId = DB::table('branche')->insertGetId([
                
                'nome_branca' => $request->nome_branca,
                'codice_branca' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    */

        // Creiamo la prestazione associata
        /*
        Nomenclatore::create([
            'codice_nomenclatore' => $request->codice_prestazione,
            'denominazione_nomenclatore' => $request->denominazione_prestazione,
            'id_branca' => $brancaId,
        ]);
        */

        return redirect()->back()->with('success', 'Branca e prestazione aggiunte correttamente!');
    }


    // Aggiungi prestazione a branca esistente
    public function aggiungiPrestazione(Request $request)
    {

        Nomenclatore::updateOrCreate(
            [
                'codice_nomenclatore' => $request->codice_prestazione,
                'id_branca' => $request->branca_id
            ],
            [ // Dati da aggiornare o inserire
                'codice_nomenclatore' => $request->codice_prestazione,
                'denominazione_nomenclatore' => $request->denominazione_prestazione,
                'stato' => 1, // Imposta lo stato a 1 (attivo)
                'id_branca' => $request->branca_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );


        return redirect()->back()->with('success', 'Prestazione aggiunta correttamente!');
    }


    public function rimuoviPrestazione($id)
    {


        Nomenclatore::where('id', $id)
            ->update(['stato' => 0]);


        return redirect()->route('operatore.modificaBranche')->with('success', 'Prestazione rimossa!');
    }



    public function eliminaSingolaBranca($id)
    {


        DB::table('branche')
            ->where('id', $id)
            ->update(['stato_branca' => 0]);


        return redirect()->route('operatore.modificaBranche')->with('success', 'Branca rimossa!');
    }

    /**********************************************MODIFICA AGENDA********************************************************************* */

    public function modificaAgenda(Request $request)
    {
        $dataView = [];
        $dataView['agenda'] = DB::table('agenda')
            // ->join('ambulatori', 'agenda.id_ambulatorio', '=', 'ambulatori.id')
            ->select('agenda.*')
            ->where('stato', 1) // Solo agende attive
            ->get();


        $dataView['ambulatori'] = DB::table('ambulatori')->get();
        return view('operatore.modificaAgenda')->with('dataView', $dataView);
    }

    public function aggiungiGiornoAgenda(Request $request)
    {
        // Controlla se esiste già un record con stesso giorno e ambulatorio
        $esistente = Agenda::where('id_ambulatorio', $request->id_ambulatorio)
            ->where('giorno', $request->giorno)
            ->first();

        if ($esistente) {
            // Ripristina (update) lo stato e aggiorna i dati
            $esistente->update([
                'ora_inizio' => $request->ora_inizio,
                'ora_fine' => $request->ora_fine,
                'slot' => $request->slot,
                'stato' => 1,
                'id_user' => Auth::id(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Giorno esistente ripristinato e aggiornato.');
        }

        // Altrimenti, crea un nuovo record
        Agenda::create([
            'id_ambulatorio' => $request->id_ambulatorio,
            'giorno' => $request->giorno,
            'ora_inizio' => $request->ora_inizio,
            'ora_fine' => $request->ora_fine,
            'slot' => $request->slot,
            'stato' => 1,
            'id_user' => Auth::id(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Nuovo giorno aggiunto.');
    }


    public function eliminaGiornoAgenda(Request $request)
    {

        $eliminaGiorno = Agenda::where('id', $request->agenda_id)->update([
            'stato' => 0,
            'updated_at' => now(),
            'id_user' => Auth::user()->id,
        ]);

        return redirect()->back()->with('success', 'Nuovo giorno aggiunto.');
    }


    public function modificaParametriAgenda(Request $request)
    {
        $aggiornaGiorno = Agenda::where('id', $request->agenda_id)->update([
            'id_ambulatorio' => $request->id_ambulatorio,
            'giorno' => $request->giorno,
            'ora_inizio' => $request->ora_inizio,
            'ora_fine' => $request->ora_fine,
            'slot' => $request->slot,
            'stato' => 1,
            'id_user' => Auth::id(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Giorno agenda aggiornato.');
    }


    /********************************STORICO PAZIENTE*************************************************** */

    public function storicoPaziente(Request $request)
    {
        $dataView = [];

        return view('operatore.storicoPaziente')->with('dataView', $dataView);
    }


    public function cercaPaziente(Request $request)
    {

        $search = trim($request->input('query'));
        if (!$search) {
            return response()->json(['error' => 'Parametro di ricerca mancante.'], 400);
        }

        $searchTerms = preg_split('/\s+/', $search);

        $dataView['paziente'] = User::query()
            ->when(count($searchTerms) === 2, function ($q) use ($searchTerms) {
                $q->where(function ($sub) use ($searchTerms) {
                    $sub->where('cognome', 'like', "%{$searchTerms[0]}%")
                        ->where('nome', 'like', "%{$searchTerms[1]}%");
                })->orWhere(function ($sub) use ($searchTerms) {
                    $sub->where('nome', 'like', "%{$searchTerms[0]}%")
                        ->where('cognome', 'like', "%{$searchTerms[1]}%");
                });
            })
            ->when(count($searchTerms) === 1, function ($q) use ($searchTerms, $search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('cognome', 'like', "%{$search}%")
                    ->orWhere('codice_fiscale', 'like', "%{$search}%");
            })
            ->first(); // restituisce solo il primo match

        if (!$dataView['paziente']) {
            return redirect()->back()->with('error', 'Nessun paziente trovato con i parametri forniti.');
        }
/*
        $dataView['storico'] = Prenotazione::select(
            'prenotazioni.id',
            'id_paziente',
            'data_inizio',
            'stato_prenotazione',
            'nome',
            'cognome',
            'codice_fiscale',
            'nomenclatore.denominazione_nomenclatore',
            'ambulatori.descrizione as luogo_prestazione',
            'branche.nome_branca as nome_branca'
        )
            ->join('users', 'prenotazioni.id_paziente', '=', 'users.id')
            ->join('nomenclatore', 'prenotazioni.branca_id', '=', 'nomenclatore.codice_nomenclatore')
            ->join('ambulatori', 'prenotazioni.centro_vaccinale_id', '=', 'ambulatori.id')
            ->join('branche', 'nomenclatore.id_branca', '=', 'branche.id')
            ->where('prenotazioni.id_paziente', $dataView['paziente']->id)
            ->where('prenotazioni.stato_prenotazione', '=', 1)
            ->orderBy('prenotazioni.data_inizio', 'desc')
            ->get();
*/
               $dataView['storico'] = DB::table('prenotazioni')
            ->select(
                'prenotazioni.id',
                'prenotazioni.id_paziente',
                'prenotazioni.data_inizio as data_prenotazione',
                'prenotazioni.data_fine',
                'prenotazioni.stato_prenotazione',
                'prenotazioni.note',
                'prenotazioni.branca_id',
                'prenotazioni.centro_vaccinale_id',
                'users.nome',
                'users.cognome',
                'users.cellulare',
                'users.codice_fiscale',
                'users.email',
                'nomenclatore.denominazione_nomenclatore as nome_prestazione',
                'ambulatori.descrizione as luogo_prestazione',
                'branche.nome_branca',
                'branche.id as id_branca',
                'ambulatori.id as id_ambulatorio'
            )
            ->leftJoin('users', 'prenotazioni.id_paziente', '=', 'users.id')
            ->leftJoin(
                DB::raw('(
                    SELECT 
                        codice_nomenclatore,
                        MIN(denominazione_nomenclatore) AS denominazione_nomenclatore,
                        MIN(id_branca) AS id_branca
                    FROM nomenclatore
                    GROUP BY codice_nomenclatore
                ) as nomenclatore'),
                'prenotazioni.branca_id',
                '=',
                'nomenclatore.codice_nomenclatore'
            )
            ->leftJoin('ambulatori', 'prenotazioni.centro_vaccinale_id', '=', 'ambulatori.id')
            ->leftJoin('branche', 'nomenclatore.id_branca', '=', 'branche.id')
                ->where('prenotazioni.id_paziente', $dataView['paziente']->id)
            ->where('prenotazioni.stato_prenotazione', '=', 1)
            ->orderByDesc('prenotazioni.data_inizio')
            ->get();



        return view('operatore.storicoPaziente')->with('dataView', $dataView);
    }
    /*********************************************************************************************************************************** */


    public function modificaFarmaci(Request $request)
    {
        $dataView = [];

        // Tipologie farmaco attive
        $dataView['tipologieFarmaco'] = DB::table('tipologia_farmaco')
            ->where('stato', 1)
            ->get();

        // Prendo la tipologia selezionata via query string (facoltativo)
        $dataView['selectedTipologiaId'] = $request->input('tipologia_id', '');

        // Query farmaci filtrati per tipologia, se selezionata
        $query = DB::table('farmaci')->where('stato', 1);
        if ($dataView['selectedTipologiaId']) {
            $query->where('tipologia_farmaco', $dataView['selectedTipologiaId']);
        }
        $dataView['farmaci'] = $query->get();

        return view('operatore.modificaFarmaci')->with('dataView', $dataView);
    }

    public function modificaParametriFarmaci(Request $request)
    {
        $farmaci = $request->input('farmaci', []);
        $tipologia_id = $request->input('tipologia_id', '');

        foreach ($farmaci as $farmaco) {
            DB::table('farmaci')
                ->where('id', $farmaco['id'])
                ->update([
                    'descrizione' => $farmaco['descrizione'],
                    'codice_farmaco' => $farmaco['codice_farmaco'],
                ]);
        }

        return redirect()->route('operatore.modificaFarmaci', ['tipologia_id' => $tipologia_id])
            ->with('success', 'Farmaci aggiornati con successo.');
    }

    public function aggiungiFarmaco(Request $request)
    {
        $request->validate([
            'tipologia_id' => 'required|exists:tipologia_farmaco,id',
            'descrizione_farmaco' => 'required|string|max:255',
            'codice_farmaco' => 'required|string|max:100',
        ]);

        DB::table('farmaci')->insert([
            'descrizione' => $request->descrizione_farmaco,
            'codice_farmaco' => $request->codice_farmaco,
            'tipologia_farmaco' => $request->tipologia_id,
            'stato' => 1,

        ]);

        return redirect()->route('operatore.modificaFarmaci', ['tipologia_id' => $request->tipologia_id])
            ->with('success', 'Farmaco aggiunto con successo.');
    }

    public function rimuoviFarmaco($id)
    {
        DB::table('farmaci')
            ->where('id', $id)
            ->update(['stato' => 0]); // soft delete

        return redirect()->back()->with('success', 'Farmaco eliminato con successo.');
    }

    public function aggiungiTipologiaFarmaco(Request $request)
    {
        $request->validate([
            'descrizione_tipologia' => 'required|string|max:255',
        ]);

        DB::table('tipologia_farmaco')->insert([
            'descrizione_tipologia_farmaco' => $request->descrizione_tipologia,
            'stato' => 1,

        ]);

        return redirect()->route('operatore.modificaFarmaci')->with('success', 'Tipologia farmaco aggiunta con successo.');
    }

    public function eliminaSingolaTipologiaFarmaco($id)
    {
        $farmaciCount = DB::table('farmaci')
            ->where('tipologia_farmaco', $id)
            ->where('stato', 1)
            ->count();

        if ($farmaciCount > 0) {
            return redirect()->back()->with('error', 'Non puoi eliminare questa tipologia perché contiene farmaci attivi.');
        }

        DB::table('tipologia_farmaco')
            ->where('id', $id)
            ->update(['stato' => 0]); // soft delete

        return redirect()->route('operatore.modificaFarmaci')->with('success', 'Tipologia farmaco eliminata con successo.');
    }

    /*********************************************************************** */

    public function getPrestazioniPerBranca($brancaId)
    {

        $prestazioni = Nomenclatore::where('id_branca', $brancaId)
            ->where('stato', 1)
            ->get();


        return response()->json($prestazioni);
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = DB::table('codici_icd9')
            ->where('codice', 'like', "%{$query}%")
            ->orWhere('descrizione', 'like', "%{$query}%")
            ->limit(15)
            ->get(['codice', 'descrizione']);


        return response()->json($results);
    }


    public function getFarmaciByTipologia($tipologiaId)
    {
        
        $farmaci = DB::table('farmaci')
            ->where('tipologia_farmaco', $tipologiaId)
            ->where('stato', 1) // Solo farmaci attivi
            ->select('id', 'codice_farmaco', 'descrizione', 'tipologia_farmaco')
            ->orderBy('descrizione', 'asc')
            ->get();

        return response()->json($farmaci);
    }

    public function comunicazioni(Request $request){


          return view('operatore.comunicazioni');
    }



}
