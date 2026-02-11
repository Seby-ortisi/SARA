<?php

use App\Http\Controllers\LDAPController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PazienteController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EtsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;






/*************************Route pubbliche**********************/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/testSPID', function () {
    return view('test_spid');
});

Route::get('/accesso_istituzionale', [App\Http\Controllers\IstituzionaleController::class, 'profiloUtente']);

Route::post('/modificaProfiloUtente', [App\Http\Controllers\HomeController::class, 'modificaProfiloUtente'])->name('modificaProfiloUtente');
Route::get('/modificaProfiloUtente', [App\Http\Controllers\HomeController::class, 'modificaProfiloUtenteAutenticazione'])->name('get.modificaProfiloUtente');
Route::post('/aggiornaProfiloUtente', [App\Http\Controllers\HomeController::class, 'aggiornaProfiloUtente'])->name('aggiornaProfiloUtente');

Route::get('/paziente/login', function () {
    return view('auth.standardLogin');
})->name('standardLogin');

Route::post('/login', [LoginController::class, 'login'])->name('standardLoginAuth');

Route::get('/operatore/login', function () {
    return view('auth.login');
})->name('login');

Route::post('authenticate', [LDAPController::class, 'authenticate'])->name('authenticate');

Route::post('/logout', function (Request $request) {
    $status = request()->query('status');
    Session::flush();
    Auth::logout();
    return redirect()->route('login')->with('status', $status);
})->name('logout');

Route::get('/logout', function (Request $request) {
    $status = request()->query('status');
    Session::flush();
    Auth::logout();
    return redirect()->route('login')->with('status', $status);
})->name('logoutGET');

//Route::get('/registrazioneUtente', [Controller::class, 'registrazione'])->name('registrazioneUtente');

Route::get('/registrazione', [RegisterController::class, 'showRegistrationForm'])->name('registrazione');
Route::post('/registrazione', [RegisterController::class, 'register'])->name('registrazione.submit');
Route::POST('/agenda', [PazienteController::class, 'creaPrenotazione'])->name('agenda.prenotazione');
Route::get('/agenda/filtra', [PazienteController::class, 'cercaDisponibilita'])->name('agenda.filtra');


/*******************Paziente*************************** */

Route::middleware(['checkRole:1'])->prefix("paziente")->group(function () {
    Route::get('/home', [App\Http\Controllers\PazienteController::class, 'index'])->name('paziente.dashboard');
    Route::get('/prenotazioni', [App\Http\Controllers\PazienteController::class, 'prenotazioni'])->name('paziente.prenotazioni');
    Route::get('/index', [App\Http\Controllers\PazienteController::class, 'home'])->name('paziente.home');
    Route::post('/prenotazioni/elimina', [App\Http\Controllers\PazienteController::class, 'eliminaPrenotazione'])->name('prenotazione.elimina');
});

/*******************Operatore*************************** */

Route::middleware(['checkRole:2,3'])->prefix("operatore")->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('operatore.dashboard');
    Route::post('/operatore/registraNuovoUtente', [App\Http\Controllers\HomeController::class, 'registraNuovoUtente'])->name('operatore.registraNuovoUtente');
    Route::post('/ricercaCF', [HomeController::class, "ricercaCF"])->name('operatore.ricercaCF');
    Route::post('/inserisciPazienteAPC', [HomeController::class, "inserisciPazienteAPC"])->name('operatore.inserisciPazienteAPC');
    Route::get('/prenotazioni', [HomeController::class, "prenotazioni"])->name('operatore.prenotazioni');
    Route::get('/mostra/prenotazioni', [HomeController::class, 'mostraPrenotazioni'])->name("operatore.mostraPrenotazioni");

    //Route::get('/prenotazioni/filtra', [HomeController::class, 'prenotazioni'])->name('operatore.prenotazioni');
    Route::get('/prenotazioni/slots', [HomeController::class, 'getSlots'])->name('prenotazioni.slots');
    Route::post('/operatore/prenota', [HomeController::class, 'prenota'])->name('operatore.prenota');
    Route::post('/operatore/registraScheda', [HomeController::class, 'registraScheda'])->name('operatore.registraScheda');
   // Route::post('/agenda', [HomeController::class, 'creaPrenotazione'])->name('operatore.creaPrenotazione');
    Route::post('/cercaPazienti', [HomeController::class, 'cercaPazienti'])->name('operatore.cercaPazienti');
    Route::post('/prenotazione/istantanea', [HomeController::class, 'prenotazioneIstantanea'])->name('prenotazione.istantanea');
    Route::get('/operatore/modifica', [App\Http\Controllers\HomeController::class, 'modifica'])->name('operatore.modifica');
    Route::get('/operatore/modificaBranche', [App\Http\Controllers\HomeController::class, 'modificaBranche'])->name('operatore.modificaBranche');
  
    Route::get('/operatore/modificaAgenda', [App\Http\Controllers\HomeController::class, 'modificaAgenda'])->name('operatore.modificaAgenda');
    Route::post('/operatore/modifica-agenda', [HomeController::class, 'modificaParametriAgenda'])->name('operatore.modificaParametriAgenda');
    Route::post('/operatore/aggiungi-giorno-agenda', [HomeController::class, 'aggiungiGiornoAgenda'])->name('operatore.aggiungiGiornoAgenda');
    Route::post('/operatore/agenda/elimina', [HomeController::class, 'eliminaGiornoAgenda'])->name('operatore.eliminaGiornoAgenda');

    Route::get('/storicoPaziente', [HomeController::class, 'storicoPaziente'])->name('operatore.storicoPazienti');
    Route::get('/cercaPaziente', [HomeController::class, 'cercaPaziente'])->name('operatore.cercaPaziente');
    Route::post('/scaricaPDF', [HomeController::class, 'schedaPDF'])->name('operatore.scaricaSchedaPDF');        
    // Modifica prestazioni esistenti
    Route::post('/operatore/modifica-branche', [HomeController::class, 'modificaParametriBranche'])->name('operatore.modificaParametriBranche');
    // Aggiungi prestazione a branca esistente
    Route::post('/operatore/aggiungi-prestazione', [HomeController::class, 'aggiungiPrestazione'])->name('operatore.aggiungiPrestazione');
    // Aggiungi nuova branca + prestazione
    Route::post('/operatore/aggiungi-branca-prestazione', [HomeController::class, 'aggiungiBrancaPrestazione'])->name('operatore.aggiungiBrancaPrestazione');
    Route::get('/operatore/prestazioni/{id}', [HomeController::class, 'rimuoviPrestazione'])->name('operatore.rimuoviPrestazione');
    Route::get('/operatore/eliminaSingolaBranca/{id}', [HomeController::class, 'eliminaSingolaBranca'])->name('operatore.eliminaSingolaBranca');

    Route::get('/modifica-farmaci', [HomeController::class, 'modificaFarmaci'])->name('operatore.modificaFarmaci');
    Route::post('/modifica-parametri-farmaci', [HomeController::class, 'modificaParametriFarmaci'])->name('operatore.modificaParametriFarmaci');
    Route::post('/aggiungi-farmaco', [HomeController::class, 'aggiungiFarmaco'])->name('operatore.aggiungiFarmaco');
    Route::get('/rimuovi-farmaco/{id}', [HomeController::class, 'rimuoviFarmaco'])->name('operatore.rimuoviFarmaco');

    Route::post('/aggiungi-tipologia-farmaco', [HomeController::class, 'aggiungiTipologiaFarmaco'])->name('operatore.aggiungiTipologiaFarmaco');
    Route::get('/elimina-tipologia-farmaco/{id}', [HomeController::class, 'eliminaSingolaTipologiaFarmaco'])->name('operatore.eliminaSingolaTipologiaFarmaco');

//
    Route::get('/prestazioni-per-branca/{brancaId}', [HomeController::class, 'getPrestazioniPerBranca'])->name('prestazioni.per.branca');
    Route::get('/icd9-search', [HomeController::class, 'search'])->name('icd9.search');
    Route::get('/api/farmaci/tipologia/{tipologiaId}', [HomeController::class, 'getFarmaciByTipologia'])->name('cercaFarmaci');

    Route::get('/download-pdf/{file}', function ($file) {
        $path = storage_path('app/private/' . $file);

        abort_unless(file_exists($path), 404);

        return response()->download($path);
    })->name('download.pdf');

Route::get('/notifiche', [HomeController::class, 'comunicazioni'])->name('notifiche.index');
    //
 Route::get('/utentiValidati', [App\Http\Controllers\AdminController::class, 'utentiValidati'])->name('admin.utentiValidati');
    //
});

/*******************Admin*************************** */

Route::middleware(['checkRole:3'])->prefix("admin")->group(function () {
    Route::get('/home', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/valida/{idUser}', [App\Http\Controllers\AdminController::class, 'validazione'])->name('admin.valida');
   
    Route::get('/flusso', [App\Http\Controllers\AdminController::class, 'flussoView'])->name('admin.flussoView');
    Route::get('/esportaFlusso', [App\Http\Controllers\AdminController::class, 'estraiDatiFlusso'])->name('admin.estraiDatiFlusso');
    Route::get('admin/esportaFlusso', [App\Http\Controllers\AdminController::class, 'esportaFlusso'])->name('admin.esportaFlusso');
    Route::get('/ets', [App\Http\Controllers\AdminController::class, 'etsView'])->name('admin.etsView');
    Route::get('/esportaFlussoETS', [App\Http\Controllers\AdminController::class, 'estraiDatiEts'])->name('admin.estraiDatiEts');
    Route::get('scaricaETS', [App\Http\Controllers\AdminController::class, 'scaricaETS'])->name('admin.scaricaETS');
    Route::get('/aggiornaDatiPaziente/{id}', [AdminController::class, 'aggiornaDatiPaziente'])->name('admin.aggiornaDatiPaziente');
    Route::post('/admin/aggiorna/{id}', [AdminController::class, 'aggiornaDati'])->name('admin.aggiornaDati');

    Route::get('/grafici', [App\Http\Controllers\AdminController::class, 'indexGrafici'])->name('operatore.grafici');

});


Route::middleware(['checkRole:4'])->prefix("ets")->group(function () {

    Route::get('/home', [App\Http\Controllers\EtsController::class, 'home'])->name('ets.dashboard');
    Route::get('/ets/prenotazioni', [App\Http\Controllers\EtsController::class, 'prentoazioniETS'])->name('ets.prenotazioni');
    Route::post('/ets/registraNuovoUtente', [App\Http\Controllers\EtsController::class, 'registraNuovoUtenteEts'])->name('ets.registraNuovoUtente');
    Route::post('/ets/ricercaCF', [EtsController::class, "ricercaCF"])->name('ets.ricercaCF');
    Route::post('/inserisciPazienteAPC', [EtsController::class, "etsInserisciPazienteAPC"])->name('ets.inserisciPazienteAPC');

});
