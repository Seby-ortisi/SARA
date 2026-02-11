@extends('bootstrap-italia::page')

@section('content')

{{-- 🔔 MESSAGGI DI SESSIONE --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Successo!</strong> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @elseif (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Errore!</strong> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

<style>
/* Forza il no-wrap su tutte le celle della tabella */
#tabulator-container .tabulator-cell {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Permette il wrap solo nel dettaglio responsive collapse */
#tabulator-container .tabulator-responsive-collapse-toggle-cell,
#tabulator-container .tabulator-responsive-collapse {
    white-space: normal !important;
}

/* Specifico per la colonna prestazione se espansa */
#tabulator-container .tabulator-responsive-collapse .tabulator-responsive-collapse-toggle-cell + div {
    white-space: normal !important;
    word-wrap: break-word !important;
}

/* Classe specifica per celle che non devono andare a capo */
.tabulator .no-wrap-cell {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

#selectPrestazione {
    max-width: 100%;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}

.modal-dialog {
    max-width: 50%;
    overflow-x: auto;
}

.modal-content {
    max-height: 80vh;
    overflow-y: auto;
}

#selectFarmaci {
    min-height: 200px;
}

#selectFarmaci option {
    padding: 8px;
    cursor: pointer;
}

#selectFarmaci option:hover {
    background-color: #f0f0f0;
}

.icd9-dropdown {
    background: white;
    border: 1px solid #ddd;
    max-height: 200px;
    overflow-y: auto;
}

.icd9-option {
    padding: 8px 10px;
    cursor: pointer;
}

.icd9-option:hover {
    background-color: #f0f0f0;
}

.circle {
    display: inline-block;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.circle-danger {
    background-color: #d32f2f;
}

.circle-warning {
    background-color: #f9a825;
}

.circle-info {
    background-color: #0288d1;
}

.circle-success {
    background-color: #2e7d32;
}

.circle-default {
    background-color: #6c757d;
}

/* Footer paginazione */
.tabulator-footer {
    background-color: #f8f9fa;
    border-top: 2px solid #dee2e6;
    padding: 15px;
    font-size: 13px;
}

.tabulator-page {
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 6px 12px;
    margin: 0 3px;
    background: white;
    transition: all 0.2s;
}

.tabulator-page:hover {
    background-color: #e9ecef;
    border-color: #0066cc;
}

.tabulator-page.active {
    background-color: #0066cc;
    color: white;
    border-color: #0066cc;
    font-weight: 600;
}

/* Placeholder */
.tabulator-placeholder {
    padding: 40px;
    color: #6c757d;
    font-size: 15px;
}

/* Stili per il bottone scheda */
.btn-azioni-group {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
}

.btn-scheda {
    white-space: nowrap;
    font-size: 14px;
    padding: 6px 12px;
    min-width: 90px;
}

.badge.bg-primary {
    background-color: #007bff;
    color: #ffffff;
    border-radius: 12px;
    padding: 0.5rem 1rem;
    margin-right: 0.5rem;
    font-size: 0.875rem;
}

.badge.bg-primary:hover {
    background-color: #0056b3;
    cursor: pointer;
}

.farmaci-dropdown {
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #fff;
    max-height: 200px;
    overflow-y: auto;
}

.farmaci-dropdown .dropdown-item:hover {
    background-color: #f1f1f1;
    cursor: pointer;
}
</style>

{{-- Modale Prenota Prestazione (fuori dal ciclo) --}}
<div class="modal fade" id="modalPrenotaPrestazione" tabindex="-1" aria-labelledby="modalPrenotaPrestazioneLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPrenotaPrestazioneLabel">Prenota nuova prestazione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>
            <form action="{{ route('operatore.prenota') }}" method="POST" id="formPrenotazione">
                @csrf
                <input type="hidden" name="id_paziente" id="idPazienteInput">
                <input type="hidden" name="data" id="dataPrenotazioneInput">
                <input type="hidden" name="codice_nomenclatore" id="codiceNomenclatoreInput">
                <input type="hidden" name="idAmbulatorio" id="idAmbulatoriInput">
                <input type="hidden" name="prenotazione_istantanea" id="prenotazioneIstantaneaInput" value="0">
                <div id="orariContainer"></div>

                <div class="modal-body">
                    {{-- Checkbox Prenotazione Istantanea --}}
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkPrenotazioneIstantanea" onchange="togglePrenotazioneIstantanea()">
                                <label class="form-check-label" for="checkPrenotazioneIstantanea">
                                    <strong>Prenotazione Istantanea</strong> (senza slot orario)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="selectBranca">Branca</label>
                            <select id="selectBranca" class="form-select" onchange="updatePrestazioniDropdown(); checkPrenotazioneIstantaneaReady();" required>
                                <option value="">-- Seleziona branca --</option>
                                @foreach ($dataView['branche'] as $branca)
                                <option value="{{ $branca->id }}">{{ $branca->nome_branca }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="selectPrestazione">Prestazione</label>
                            <select id="selectPrestazione" class="form-select" onchange="checkPrenotazioneIstantaneaReady();" required>
                                <option value="">-- Seleziona prestazione --</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3" id="containerAmbulatorioIstantanea" style="display: none;">
                        <div class="col-md-12">
                            <label for="selectAmbulatorioIstantanea">Ambulatorio</label>
                            <select id="selectAmbulatorioIstantanea" class="form-select" onchange="checkPrenotazioneIstantaneaReady();">
                                <option value="">-- Seleziona ambulatorio --</option>
                                @foreach ($dataView['ambulatori'] ?? [] as $ambulatorio)
                                <option value="{{ $ambulatorio->id }}">{{ $ambulatorio->descrizione }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    {{-- Container per data e slot (nascosto se istantanea) --}}
                    <div id="containerDataSlot">
                        {{-- Data --}}
                        @php $today = date('Y-m-d'); @endphp
                        <div class="col-md-4 mt-3">
                            <label for="inputDataPrenotazione">Data</label>
                            <input type="date" id="inputDataPrenotazione" class="form-control" min="{{ $today }}" onchange="caricaSlotsDisponibili()">
                        </div>

                        {{-- Container per gli slot --}}
                        <div id="containerSlotsPrenotazione" class="mt-4">
                            <p class="text-muted">Seleziona una branca, prestazione e data per vedere gli slot disponibili.</p>
                        </div>
                    </div>

                    {{-- Note --}}
                    <div class="col-md-12 mt-3">
                        <label for="inputNote">Note (opzionale)</label>
                        <textarea name="note" id="inputNote" class="form-control" rows="3" placeholder="Inserisci eventuali note..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvaBozza" disabled>
                        Salva Prenotazione
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal dettaglio scheda (unica, riutilizzabile) -->
<div class="modal fade" id="modalSchedaPaziente" tabindex="-1"
    aria-labelledby="modalPrenotazioneLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Primo form - Registrazione scheda -->
            <form method="POST" action="{{ route('operatore.registraScheda') }}" class="mb-4" id="formRegistrazione">
                @csrf
                <input type="hidden" name="prenotazione_id" id="prenotazione_id">
                <input type="hidden" name="paziente_id" id="paziente_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalPrenotazioneLabel">
                        Compila scheda
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                </div>

                <div class="modal-body">
                    {{-- Presenza --}}
                    <fieldset class="mb-4">
                        <legend class="fs-6">Presenza</legend>
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input sync-field" type="radio"
                                    id="presente"
                                    name="presenza" value="1"
                                    data-target="presenza_pdf"
                                    onchange="togglePresenza(1); syncField(this)"
                                    required>
                                <label class="form-check-label" for="presente">
                                    Presente
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input sync-field" type="radio"
                                    id="assente"
                                    name="presenza" value="0"
                                    data-target="presenza_pdf"
                                    onchange="togglePresenza(0); syncField(this)"
                                    required>
                                <label class="form-check-label" for="assente">
                                    Assente
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Campi visibili se presente --}}
                    <div id="campiPrenotazione" style="display: none;">
                        <div class="row g-4">
                            {{-- Diagnosi --}}
                            <div class="col-12">
                                <fieldset class="border p-3">
                                    <legend class="float-none w-auto px-2 mb-2 small">
                                        Diagnosi (ICD-9)
                                    </legend>
                                    <div class="mb-2">
                                        <label for="icd9_input" class="form-label mb-1">
                                            Diagnosi
                                        </label>

                                        <div id="icd9_tags" class="icd9-tags-container mb-2"></div>

                                        <div class="position-relative">
                                            <input type="text"
                                                id="icd9_input"
                                                name="icd9_search"
                                                class="form-control icd9-input"
                                                placeholder="Digita per cercare e aggiungere codici ICD-9..."
                                                autocomplete="off">
                                            <div id="icd9_dropdown"
                                                class="icd9-dropdown position-absolute w-100"
                                                style="display: none; z-index: 1050; top: 100%; left: 0;">
                                            </div>
                                        </div>

                                        <!-- Campo JSON con codice e descrizione -->
                                        <input type="hidden" id="icd9_selected"
                                            name="icd9_codes" value="[]"
                                            class="sync-field"
                                            data-target="icd9_codes_pdf">

                                        <!-- Container per input hidden separati -->
                                        <div id="icd9_hidden_inputs"></div>
                                    </div>
                                </fieldset>
                            </div>

                            {{-- Prestazione --}}
                            <div class="col-12">
                                <fieldset class="border p-3">
                                    <legend class="float-none w-auto px-2 mb-2 small">
                                        Prestazione
                                    </legend>
                                    <div class="row gy-3">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label for="selectBrancaScheda" class="form-label">
                                                    Branca
                                                </label>
                                                <select name="branca_id"
                                                    id="selectBrancaScheda"
                                                    class="form-select sync-field"
                                                    data-target="branca_id_pdf"
                                                    onchange="updatePrestazioniDropdownScheda(); syncField(this)">
                                                    <option value="">-- Seleziona branca --</option>
                                                    @foreach ($dataView['branche'] as $branca)
                                                    <option value="{{ $branca->id }}">
                                                        {{ $branca->nome_branca }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label for="selectPrestazioneScheda" class="form-label">
                                                    Prestazione
                                                </label>
                                                <select name="prestazione_id"
                                                    id="selectPrestazioneScheda"
                                                    class="form-select w-100 sync-field"
                                                    data-target="prestazione_id_pdf"
                                                    onchange="syncField(this)">
                                                    <option value="">-- Seleziona prestazione --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            {{-- Luogo della prestazione --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="luogo" class="form-label">
                                        Luogo della prestazione
                                    </label>
                                    <textarea class="form-control sync-field"
                                        name="luogo_prestazione"
                                        id="luogo"
                                        rows="3"
                                        placeholder="Inserisci il luogo..."
                                        data-target="luogo_prestazione_pdf"
                                        onchange="syncField(this)"
                                        oninput="syncField(this)"></textarea>
                                </div>
                            </div>

                            {{-- Erogazione farmaci --}}

                            <div class="col-12">
                                <fieldset class="border p-3 mb-3">
                                    <legend class="float-none w-auto px-2 mb-2 small">Erogazione farmaci</legend>
                                    <div class="d-flex gap-4 align-items-center">
                                        <!-- Radio buttons per "Sì" e "No" -->
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sync-field" type="radio"
                                                name="erogazioneFarmaci"
                                                id="erogazioneSi"
                                                value="si"
                                                data-target="tipologiaFarmacoContainer"
                                                onclick="toggleTipologiaFarmaco(true); syncField(this)">
                                            <label class="form-check-label" for="erogazioneSi">Sì</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sync-field" type="radio"
                                                name="erogazioneFarmaci"
                                                id="erogazioneNo"
                                                value="no"
                                                data-target="tipologiaFarmacoContainer"
                                                onclick="toggleTipologiaFarmaco(false); syncField(this)"
                                                checked>
                                            <label class="form-check-label" for="erogazioneNo">No</label>
                                        </div>
                                    </div>

                                    <!-- Sezione di selezione farmaci -->
                                    <div class="mt-3 tipologiaFarmacoContainer" id="tipologiaFarmacoContainer" style="display: none;">

                                        <!-- Select per tipologia farmaco -->
                                        <div class="mb-3">
                                            <label for="selectTipologiaFarmaco" class="form-label">
                                                <strong>Seleziona tipologia farmaco:</strong>
                                            </label>
                                            <select class="form-select"
                                                id="selectTipologiaFarmaco"
                                                name="tipologiaFarmaco"
                                                onchange="loadFarmaci(this.value)">
                                                <option value="">-- Seleziona una tipologia --</option>
                                                @foreach($dataView['tipologia_farmaco'] as $tipologia)
                                                <option value="{{ $tipologia->id }}">
                                                    {{ $tipologia->descrizione_tipologia_farmaco }}
                                                </option>
                                                @endforeach
                                                <option value="altro">Altro...</option>
                                            </select>
                                        </div>

                                        <!-- Campo "Altro" -->
                                        <div class="mb-3" id="altroFarmacoContainer" style="display: none;">
                                            <label for="altroFarmacoDescrizione" class="form-label">Descrizione farmaco:</label>
                                            <input type="text" class="form-control" id="altroFarmacoDescrizione"
                                                name="altroFarmacoDescrizione"
                                                placeholder="Specifica la descrizione del farmaco...">

                                            <label for="altroFarmacoCodice" class="form-label mt-2">Codice farmaco:</label>
                                            <input type="text" class="form-control" id="altroFarmacoCodice"
                                                name="altroFarmacoCodice"
                                                placeholder="Specifica il codice del farmaco...">
                                        </div>

                                        <!-- Select farmaci (apparirà dopo la selezione della tipologia) -->
                                        <div class="mb-3" id="selectFarmaciContainer" style="display: none;">
                                            <label for="selectFarmaci" class="form-label">
                                                <strong>Seleziona farmaci:</strong>
                                            </label>
                                            <select class="form-select"
                                                id="selectFarmaci"
                                                name="farmaci[]"
                                                multiple
                                                size="8">
                                                <option value="">-- Prima seleziona una tipologia --</option>
                                            </select>
                                            <small class="form-text text-muted">
                                                Tieni premuto Ctrl (o Cmd su Mac) per selezionare più farmaci
                                            </small>
                                        </div>

                                        <!-- Hidden inputs per i farmaci selezionati -->
                                        <input type="hidden" id="farmaci_selected"
                                            name="farmaci_codes"
                                            value="[]"
                                            class="sync-field"
                                            data-target="farmaci_codes_pdf">
                                    </div>
                                </fieldset>
                            </div>


                            {{-- Dispositivo medico --}}
                            <div class="col-12">
                                <fieldset class="border p-3">
                                    <legend class="float-none w-auto px-2 mb-2 small">
                                        Erogazione dispositivo medico
                                    </legend>

                                    <div class="d-flex gap-4 align-items-center mb-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sync-field"
                                                type="radio"
                                                name="erogazioneDispositivo"
                                                id="dispositivoSi"
                                                value="si"
                                                data-target="erogazioneDispositivo_pdf"
                                                onclick="toggleTipologiaDispositivo(true); syncField(this)">
                                            <label class="form-check-label" for="dispositivoSi">
                                                Sì
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sync-field"
                                                type="radio"
                                                name="erogazioneDispositivo"
                                                id="dispositivoNo"
                                                value="no"
                                                data-target="erogazioneDispositivo_pdf"
                                                onclick="toggleTipologiaDispositivo(false); syncField(this)"
                                                checked>
                                            <label class="form-check-label" for="dispositivoNo">
                                                No
                                            </label>
                                        </div>
                                    </div>

                                    <div id="tipologiaDispositivoContainer" style="display: none;">
                                        <label for="tipologiaDispositivo" class="form-label">
                                            Tipologia dispositivo medico erogato
                                        </label>
                                        <select class="form-select sync-field"
                                            id="tipologiaDispositivo"
                                            name="dispositivo_medico"
                                            data-target="dispositivo_medico_pdf"
                                            onchange="syncField(this)">
                                            <option value="">Seleziona un dispositivo medico...</option>
                                            @foreach ($dataView['dispositivo_medico'] as $dispositivo)
                                            <option value="{{ $dispositivo->codice_dispositivo }}">
                                                {{$dispositivo->codice_dispositivo . "-" . $dispositivo->nome_dispositivo }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="col-12">
                                <fieldset class="border p-3">
                                    <legend class="float-none w-auto px-2 mb-2 small">
                                        Scheda visita
                                    </legend>
                                    
                                    <div class="mb-3">
                           <textarea
                                class="form-control"
                                id="scheda_amnestica"
                                name="schedaVisita"
                                rows="5"
                            ></textarea>

                                    </div>
                                </fieldset>
                            </div>
                        </div> {{-- .row --}}
                    </div> {{-- campi presente --}}
                </div> {{-- .modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Chiudi
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Salva e scarica PDF
                    </button>
                </div>
            </form>

        </div> {{-- .modal-content --}}
    </div> {{-- .modal-dialog --}}
</div> {{-- .modal --}}

{{-- Card Ricerca Paziente --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Ricerca paziente</h5>
    </div>

    <div class="card-body">
        {{-- Form di ricerca paziente --}}
        <form method="POST" action="{{ route('operatore.cercaPazienti') }}" class="row g-3 align-items-end mb-4">
            @csrf

            <div class="col-md-8">
                <label for="search" class="form-label">Cerca paziente</label>
                <input type="text" name="search" id="search" class="form-control"
                    placeholder="Cerca per nome, cognome o codice fiscale" required
                    value="{{ old('search', request('search')) }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filtra pazienti
                </button>
            </div>
        </form>

        {{-- Risultato della ricerca --}}
        @if(isset($dataView['paziente']) && count($dataView['paziente']) > 0)
        @php
        $pazientiOrdinati = $dataView['paziente']->sortBy('cognome');
        @endphp

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Codice Fiscale</th>
                        <th>Data di nascita</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pazientiOrdinati as $paziente)
                    <tr>
                        <td>{{ $paziente->nome }}</td>
                        <td>{{ $paziente->cognome }}</td>
                        <td>{{ $paziente->codice_fiscale }}</td>
                        <td>{{ \Carbon\Carbon::parse($paziente->data_nascita)->format('d/m/Y') }}</td>
                        <td>
                            <button type="button" class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalPrenotaPrestazione"
                                onclick="document.getElementById('idPazienteInput').value = '{{ $paziente->id }}'">
                                Prenota
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @elseif(request()->has('search'))
        <div class="alert alert-warning">
            Nessun paziente trovato con i criteri di ricerca inseriti.
        </div>
        @endif
    </div>
</div>

<div class="card-header d-flex">
    Numero prenotazioni totali: <strong id="totalRows">0</strong>
</div>


<!-- Tabella -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div id="tabulator-container"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const dataTest = await recuperaPrenotazioni();

    console.log(dataTest);
    const table = new Tabulator("#tabulator-container", {
        data: dataTest,
        layout: "fitDataFill",
        responsiveLayout: "collapse",
        pagination: true,
        paginationSize: 10,
        paginationMode: "local",
        paginationSizeSelector: [10, 15, 25, 50, 100],
        headerFilterPlaceholder: "Filtra...",
        columns: [{
                title: "Cognome",
                field: "cognome",
                sorter: "string",
                headerFilter: "input",
                minWidth: 140,
                responsive: 0,
                hozAlign: "center",
                headerHozAlign: "center",
                headerFilterPlaceholder: "Cerca cognome...",
                cssClass: "no-wrap-cell"
            },
            {
                title: "Nome",
                field: "nome",
                sorter: "string",
                headerFilter: "input",
                minWidth: 130,
                responsive: 1,
                hozAlign: "center",
                headerHozAlign: "center",
                headerFilterPlaceholder: "Cerca nome...",
                cssClass: "no-wrap-cell"
            },
            {
                title: "Codice Fiscale",
                field: "codice_fiscale",
                sorter: "string",
                headerFilter: "input",
                minWidth: 160,
                responsive: 4,
                hozAlign: "center",
                headerHozAlign: "center",
                headerFilterPlaceholder: "Cerca CF...",
                cssClass: "no-wrap-cell",
                formatter: cell => `<code>${cell.getValue()}</code>`
            },
            {
                title: "Data e ora",
                field: "data_inizio",
                sorter: "datetime",
                headerFilter: "input",
                minWidth: 170,
                responsive: 2,
                hozAlign: "center",
                headerHozAlign: "center",
                headerFilterPlaceholder: "Filtra data...",
                cssClass: "no-wrap-cell",
                formatter: function(cell) {
                    const value = cell.getValue();
                    if (!value) return "";
                    const date = new Date(value);
                    return date.toLocaleString("it-IT", {
                        day: "2-digit",
                        month: "2-digit",
                        year: "numeric",
                        hour: "2-digit",
                        minute: "2-digit",
                    });
                },
                headerFilterFunc: function(headerValue, rowValue, rowData, filterParams) {
                    if (!headerValue) return true;
                    if (!rowValue) return false;
                    
                    const date = new Date(rowValue);
                    const formattedDate = date.toLocaleString("it-IT", {
                        day: "2-digit",
                        month: "2-digit",
                        year: "numeric",
                        hour: "2-digit",
                        minute: "2-digit",
                    });
                    
                    return formattedDate.toLowerCase().includes(headerValue.toLowerCase());
                }
            },
            {
                title: "Luogo",
                field: "luogo_prestazione",
                sorter: "string",
                headerFilter: "list",
                headerFilterPlaceholder: "Filtra luogo...",
                minWidth: 160,
                responsive: 3,
                hozAlign: "center",
                headerHozAlign: "center",
                cssClass: "no-wrap-cell",
                headerFilterParams: function() {
                    const values = {
                        "": "Tutti"
                    };
                    const unici = [...new Set(dataTest.map(p => p.luogo_prestazione).filter(Boolean))];
                    unici.forEach(l => values[l] = l);
                    return {
                        values
                    };
                },
                formatter: function(cell) {
                    return cell.getData().luogo_prestazione || "";
                }
            },
            {
                title: "Branca",
                field: "nome_branca",
                sorter: "string",
                headerFilter: "list",
                headerFilterPlaceholder: "Filtra branca...",
                minWidth: 140,
                responsive: 5,
                hozAlign: "center",
                headerHozAlign: "center",
                cssClass: "no-wrap-cell",
                headerFilterParams: function() {
                    const values = {
                        "": "Tutte"
                    };
                    const uniche = [...new Set(dataTest.map(p => p.nome_branca).filter(Boolean))];
                    uniche.forEach(b => values[b] = b);
                    return {
                        values
                    };
                }
            },
            {
                title: "Prestazione",
                field: "nome_prestazione",
                sorter: "string",
                headerFilter: "input",
                headerFilterPlaceholder: "Filtra prestazione...",
                minWidth: 200,
                maxWidth: 350,
                responsive: 6,
                hozAlign: "left",
                headerHozAlign: "center",
                cssClass: "no-wrap-cell"
            },
            {
                title: "Stato",
                field: "stato_prenotazione",
                minWidth: 140,
                responsive: 1,
                hozAlign: "center",
                headerHozAlign: "center",
                headerFilter: "list",
                headerFilterPlaceholder: "Filtra Stato",
                cssClass: "no-wrap-cell",
                headerFilterParams: {
                    values: {
                        "": "Tutti",
                        "0": "Assente",
                        "1": "Presente",
                        "2": "Annullato",
                        "-1": "Prenotato"
                    }
                },
                headerFilterFunc: function(headerValue, rowValue, rowData, filterParams) {
                    if (!headerValue || headerValue === "") return true;
                    return String(rowValue) === String(headerValue);
                },
                formatter: function(cell) {
                    const val = String(cell.getValue());
                    const map = {
                        "0": '<span class="badge" style="background-color: #f8d7da; color: #7a5256;">Assente</span>',
                        "1": '<span class="badge" style="background-color: #d4edda; color: #155724;">Presente</span>',
                        "2": '<span class="badge" style="background-color: #f5c6cb; color: #721c24;">Annullato</span>',
                        "-1": '<span class="badge" style="background-color: #fff3cd; color: #856404;">Prenotato</span>'
                    };
                    return map[val] || val;
                },
                formatterPrint: function(cell) {
                    const val = String(cell.getValue());
                    const textMap = {
                        "0": "Assente",
                        "1": "Presente",
                        "2": "Annullato",
                        "-1": "Prenotato"
                    };
                    return textMap[val] || val;
                }
            },
            {
                title: "Azioni",
                headerSort: false,
                minWidth: 100,
                width: 120,
                responsive: 0,
                hozAlign: "center",
                headerHozAlign: "center",
                formatter: function(cell) {
                    const row = cell.getRow().getData();
                    const isPresente = String(row.stato_prenotazione) === "1";

                    return `<div class="btn-azioni-group">
                        <button type="button" class="btn btn-primary btn-scheda" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalSchedaPaziente"
                            data-prenotazione-id="${row.id}"
                            data-paziente-id="${row.id_paziente}"
                            data-scheda-amnestica='${row.scheda_amnestica}'
                            data-prestazione-id="${row.branca_id}"
                            data-branca-id="${row.id_branca}"
                            data-luogo="${row.luogo_prestazione}"
                            ${isPresente ? 'disabled' : ''}>
                            Scheda
                            <i class="bi bi-file-earmark-pdf"></i>
                        </button>
                    </div>`;
                }
            }
        ],
        initialSort: [{
            column: "cognome",
            dir: "asc"
        }],
        placeholder: "Nessun paziente trovato",
        locale: "it-it",
        langs: {
            "it-it": {
                "pagination": {
                    "page_size": "Righe",
                    "first": "Prima",
                    "first_title": "Prima pagina",
                    "last": "Ultima",
                    "last_title": "Ultima pagina",
                    "prev": "Prec",
                    "prev_title": "Pagina precedente",
                    "next": "Succ",
                    "next_title": "Pagina successiva"
                }
            }
        }
    });

    table.on("dataFiltered", function(filters, rows) {
        console.log("Totale filtrati:", rows.length);
        console.log("Totale attivi:", table.getDataCount("active"));
        console.log("Filtri applicati:", filters);
        document.getElementById('totalRows').textContent = rows.length;
    });

    async function recuperaPrenotazioni() {
        const response = await fetch('/operatore/mostra/prenotazioni', {
            method: "GET"
        });

        const data = await response.json();
        return data;
    }
});

    let slotsSelezionati = [];
    let ambulatorioIdCorrente = null;
    let tuttiGliSlots = [];

    function resetSlots() {
        slotsSelezionati = [];
        tuttiGliSlots = [];
        ambulatorioIdCorrente = null;
        document.getElementById('containerSlotsPrenotazione').innerHTML = '<p class="text-muted">Seleziona una data per vedere gli slot disponibili.</p>';
        document.getElementById('btnSalvaBozza').disabled = true;
        document.getElementById('orariContainer').innerHTML = '';
    }

    function caricaSlotsDisponibili() {
        const brancaId = document.getElementById('selectBranca').value;
        const prestazioneId = document.getElementById('selectPrestazione').value;
        const dataInput = document.getElementById('inputDataPrenotazione').value;
        const container = document.getElementById('containerSlotsPrenotazione');

        console.log("Valori selezionati:", {
            brancaId,
            prestazioneId,
            dataInput
        });

        slotsSelezionati = [];
        tuttiGliSlots = [];
        ambulatorioIdCorrente = null;
        document.getElementById('btnSalvaBozza').disabled = true;

        container.innerHTML = '';

        if (!brancaId || !prestazioneId || !dataInput) {
            container.innerHTML = '<p class="text-danger">Seleziona una branca, prestazione e data.</p>';
            console.warn("Form incompleto, non si procede con la fetch.");
            return;
        }

        const url = `{{ route('prenotazioni.slots') }}?branca_id=${brancaId}&data=${dataInput}`;
        console.log("Chiamata fetch a URL:", url);

        fetch(url)
            .then(res => {
                console.log("Risposta fetch raw:", res);
                if (!res.ok) throw new Error(`HTTP error, status = ${res.status}`);
                return res.json();
            })
            .then(data => {
                console.log("Dati ricevuti dal server:", data);
                const agenda = data.agenda;

                if (!agenda || agenda.length === 0) {
                    container.innerHTML = '<p class="text-muted">Nessuno slot disponibile per la data selezionata.</p>';
                    console.info("Nessuno slot disponibile.");
                    return;
                }

                const html = agenda.map(item => {
                    const descrizione = item.agenda.descrizione_ambulatorio;
                    const ambulatorioId = item.agenda.id_ambulatorio;
                    const tuttiSlots = item.slots || [];
                    const slotsOccupati = item.bookedSlots || [];

                    console.log(`Tutti gli slot per ${descrizione}:`, tuttiSlots);
                    console.log(`Slot occupati per ${descrizione}:`, slotsOccupati);

                    let slotButtons = '';
                    if (tuttiSlots.length > 0) {
                        slotButtons = tuttiSlots.map((s, index) => {
                            const isOccupato = slotsOccupati.includes(s);

                            if (!isOccupato) {
                                // Slot disponibile - salvalo nell'array globale
                                const globalIndex = tuttiGliSlots.length;
                                tuttiGliSlots.push({
                                    ora: s,
                                    index: globalIndex,
                                    ambulatorioId: ambulatorioId,
                                    data: dataInput,
                                    codiceNomenclatore: prestazioneId
                                });

                                return `
                                <button type="button" 
                                        class="btn btn-outline-primary btn-sm m-1 slot-btn"
                                        data-slot="${s}"
                                        data-global-index="${globalIndex}"
                                        data-ambulatorio="${ambulatorioId}"
                                        onclick="toggleSlot(${globalIndex})">
                                    ${s}
                                </button>
                            `;
                            } else {
                                // Slot occupato - pulsante disabilitato
                                return `
                                <button type="button" 
                                        class="btn btn-secondary btn-sm m-1"
                                        disabled
                                        title="Slot già prenotato">
                                    ${s} 
                                </button>
                            `;
                            }
                        }).join('');
                    } else {
                        slotButtons = '<p class="text-muted">Nessuno slot disponibile per questo ambulatorio.</p>';
                    }

                    return `
                    <div class="mb-3 border p-2 rounded">
                        <h6>${descrizione}</h6>
                        <div class="d-flex flex-wrap">${slotButtons}</div>
                    </div>
                `;
                }).join('');

                container.innerHTML = html + `
                <div id="riepilogoSelezione" class="mt-3 p-3 bg-light rounded" style="display: none;">
                    <h6>Slot selezionati:</h6>
                    <p id="testoRiepilogo" class="mb-0"></p>
                </div>
            `;
            })
            .catch(err => {
                console.error('Errore nel recuperare gli slot:', err);
                container.innerHTML = '<p class="text-danger">Slot non disponibili.</p>';
            });
    }

    function toggleSlot(globalIndex) {
        const slotDaSelezionare = tuttiGliSlots[globalIndex];

        // Se è il primo slot selezionato, salva l'ambulatorio
        if (slotsSelezionati.length === 0) {
            ambulatorioIdCorrente = slotDaSelezionare.ambulatorioId;
        }

        // Verifica che l'ambulatorio sia lo stesso
        if (slotDaSelezionare.ambulatorioId !== ambulatorioIdCorrente) {
            alert('Non puoi selezionare slot di ambulatori diversi. Deseleziona gli slot correnti prima di selezionarne altri.');
            return;
        }

        // Se clicco su uno slot già selezionato, deseleziono tutto
        const isGiaSelezionato = slotsSelezionati.some(s => s.index === globalIndex);

        if (isGiaSelezionato) {
            // Deseleziona tutto
            slotsSelezionati = [];
            ambulatorioIdCorrente = null;
            aggiornaUI();
            return;
        }

        // Se non ho ancora selezionato nulla, seleziono solo questo slot
        if (slotsSelezionati.length === 0) {
            slotsSelezionati = [slotDaSelezionare];
        } else {
            // Calcolo il range tra il primo slot selezionato e quello appena cliccato
            const indiciSelezionati = slotsSelezionati.map(s => s.index);
            const minIndex = Math.min(...indiciSelezionati, globalIndex);
            const maxIndex = Math.max(...indiciSelezionati, globalIndex);

            // Seleziono tutti gli slot nel range dello stesso ambulatorio
            slotsSelezionati = tuttiGliSlots.filter(slot =>
                slot.index >= minIndex &&
                slot.index <= maxIndex &&
                slot.ambulatorioId === ambulatorioIdCorrente
            );
        }

        aggiornaUI();
    }

    function aggiornaUI() {
        // Aggiorna i pulsanti
        document.querySelectorAll('.slot-btn').forEach(btn => {
            const btnIndex = parseInt(btn.getAttribute('data-global-index'));
            const isSelected = slotsSelezionati.some(s => s.index === btnIndex);

            if (isSelected) {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
            } else {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            }
        });

        // Ordina gli slot per indice
        slotsSelezionati.sort((a, b) => a.index - b.index);

        // Aggiorna il riepilogo
        aggiornaRiepilogo();

        // Abilita/disabilita il pulsante
        document.getElementById('btnSalvaBozza').disabled = slotsSelezionati.length === 0;

        // Aggiorna i campi hidden
        if (slotsSelezionati.length > 0) {
            const primoSlot = slotsSelezionati[0];
            aggiornaHiddenInputs(primoSlot.data, primoSlot.codiceNomenclatore, primoSlot.ambulatorioId);
        } else {
            // Pulisci gli input hidden se non ci sono slot selezionati
            document.getElementById('orariContainer').innerHTML = '';
        }

        console.log('Slots selezionati:', slotsSelezionati);
    }

    function aggiornaRiepilogo() {
        const riepilogo = document.getElementById('riepilogoSelezione');
        const testo = document.getElementById('testoRiepilogo');

        if (slotsSelezionati.length === 0) {
            riepilogo.style.display = 'none';
            return;
        }

        riepilogo.style.display = 'block';

        if (slotsSelezionati.length === 1) {
            testo.innerHTML = `Orario: <strong>${slotsSelezionati[0].ora}</strong>`;
} else {
    const primoSlot = slotsSelezionati[0].ora;
    const ultimoSlot = slotsSelezionati[slotsSelezionati.length - 1].ora;
    
    // Aggiungi 10 minuti
    const [h, m] = ultimoSlot.split(':').map(Number);
    const fineSlot = new Date(0, 0, 0, h, m + 10).toTimeString().slice(0, 5);
    
    testo.innerHTML = `Dalle <strong>${primoSlot}</strong> alle <strong>${fineSlot}</strong> (${slotsSelezionati.length} slot consecutivi)`;
}
    }

    function aggiornaHiddenInputs(data, codiceNomenclatore, ambulatorioId) {
        document.getElementById('dataPrenotazioneInput').value = data;
        document.getElementById('codiceNomenclatoreInput').value = codiceNomenclatore;
        document.getElementById('idAmbulatoriInput').value = ambulatorioId;

        // Crea gli input per gli orari nel container dedicato
        const container = document.getElementById('orariContainer');
        container.innerHTML = ''; // Pulisci il container

        slotsSelezionati.forEach(slot => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'orario[]';
            input.value = slot.ora;
            container.appendChild(input);
        });

        console.log('Input orari creati:', slotsSelezionati.map(s => s.ora));
    }

    let isPrenotazioneIstantanea = false;

    function togglePrenotazioneIstantanea() {
        const checkbox = document.getElementById('checkPrenotazioneIstantanea');
        isPrenotazioneIstantanea = checkbox.checked;

        // Aggiorna il campo hidden
        document.getElementById('prenotazioneIstantaneaInput').value = isPrenotazioneIstantanea ? '1' : '0';

        const containerDataSlot = document.getElementById('containerDataSlot');
        const containerAmbulatorio = document.getElementById('containerAmbulatorioIstantanea');
        const inputData = document.getElementById('inputDataPrenotazione');
        const btnSalva = document.getElementById('btnSalvaBozza');

        if (isPrenotazioneIstantanea) {
            // Nascondi data e slot
            containerDataSlot.style.display = 'none';
            // Mostra il dropdown ambulatori
            containerAmbulatorio.style.display = 'block';

            // Resetta slot selezionati
            slotsSelezionati = [];
            tuttiGliSlots = [];
            ambulatorioIdCorrente = null;

            // Pulisci il container degli slot e degli orari
            document.getElementById('containerSlotsPrenotazione').innerHTML = '<p class="text-muted">Prenotazione istantanea: non è necessario selezionare data e orario.</p>';
            document.getElementById('orariContainer').innerHTML = '';

            // Aggiorna i campi hidden per prenotazione istantanea
            aggiornaHiddenInputsIstantanea();

            // Abilita il pulsante se branca, prestazione e ambulatorio sono selezionati
            const brancaId = document.getElementById('selectBranca').value;
            const prestazioneId = document.getElementById('selectPrestazione').value;
            const ambulatorioId = document.getElementById('selectAmbulatorioIstantanea').value;
            btnSalva.disabled = !(brancaId && prestazioneId && ambulatorioId);

        } else {
            // Mostra data e slot
            containerDataSlot.style.display = 'block';
            // Nascondi il dropdown ambulatori
            containerAmbulatorio.style.display = 'none';

            // Reset select ambulatorio
            document.getElementById('selectAmbulatorioIstantanea').value = '';

            // Resetta il form
            resetSlots();
            inputData.value = '';
            btnSalva.disabled = true;

            // Pulisci i campi hidden
            document.getElementById('dataPrenotazioneInput').value = '';
            document.getElementById('codiceNomenclatoreInput').value = '';
            document.getElementById('idAmbulatoriInput').value = '';
        }
    }

    function aggiornaHiddenInputsIstantanea() {
        const brancaId = document.getElementById('selectBranca').value;
        const prestazioneId = document.getElementById('selectPrestazione').value;
        const ambulatorioId = document.getElementById('selectAmbulatorioIstantanea').value;

        // Per prenotazione istantanea non serve data (sarà now() nel backend)
        document.getElementById('dataPrenotazioneInput').value = '';
        document.getElementById('codiceNomenclatoreInput').value = prestazioneId || '';
        document.getElementById('idAmbulatoriInput').value = ambulatorioId || '';

        console.log('Hidden inputs aggiornati per prenotazione istantanea:', {
            codice_nomenclatore: prestazioneId,
            ambulatorio: ambulatorioId
        });
    }


    function updatePrestazioniDropdown() {
        const brancaId = document.getElementById('selectBranca').value;
        const prestazioneSelect = document.getElementById('selectPrestazione');
        prestazioneSelect.innerHTML = '<option value="">-- Seleziona prestazione --</option>';

        if (!isPrenotazioneIstantanea) {
            resetSlots();
        }

        if (!brancaId) {
            if (isPrenotazioneIstantanea) {
                document.getElementById('btnSalvaBozza').disabled = true;
            }
            return;
        }

        fetch(`/operatore/prestazioni-per-branca/${brancaId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(prestazione => {
                    const opt = document.createElement('option');
                    opt.value = prestazione.codice_nomenclatore;
                    opt.textContent = prestazione.denominazione_nomenclatore;
                    prestazioneSelect.appendChild(opt);
                });

                // 👇 Importante: riassegna l'evento dopo il caricamento dinamico
                prestazioneSelect.onchange = checkPrenotazioneIstantaneaReady;

                // Verifica se abilitare il pulsante
                if (isPrenotazioneIstantanea) {
                    checkPrenotazioneIstantaneaReady();
                }
            })
            .catch(err => console.error('Errore nel recuperare le prestazioni:', err));
    }


    function checkPrenotazioneIstantaneaReady() {
        const brancaId = document.getElementById('selectBranca').value;
        const prestazioneId = document.getElementById('selectPrestazione').value;
        const ambulatorioId = document.getElementById('selectAmbulatorioIstantanea').value;
        const btnSalva = document.getElementById('btnSalvaBozza');




        if (isPrenotazioneIstantanea) {
            // Abilita il pulsante solo se tutti e tre i campi sono selezionati
            btnSalva.disabled = !(brancaId && prestazioneId && ambulatorioId);

            // Aggiorna i campi hidden se tutti sono selezionati
            if (brancaId && prestazioneId && ambulatorioId) {
                aggiornaHiddenInputsIstantanea();
            }
        }
    }

    const selectPrestazione = document.getElementById('selectPrestazione');
    if (selectPrestazione) {
        selectPrestazione.addEventListener('change', function() {
            if (isPrenotazioneIstantanea) {
                checkPrenotazioneIstantaneaReady();
            } else {
                // Comportamento normale: resetta gli slot
                resetSlots();
            }
        });
    }

    const modal = document.getElementById('modalSchedaPaziente');

    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const brancaId = button.getAttribute('data-branca-id');
        const prestazioneId = button.getAttribute('data-prestazione-id'); // codice_nomenclatore corretto

        const selectBranca = document.getElementById('selectBrancaScheda');
        const selectPrestazione = document.getElementById('selectPrestazioneScheda');

        // Seleziona la branca
        selectBranca.value = brancaId || '';

        // Popola le prestazioni e seleziona quella corretta
        updatePrestazioniDropdownScheda(prestazioneId);
    });

    function updatePrestazioniDropdownScheda(selectedPrestazioneId = null) {
        const brancaId = document.getElementById('selectBrancaScheda').value;
        const prestazioneSelect = document.getElementById('selectPrestazioneScheda');

        prestazioneSelect.innerHTML = '<option value="">-- Seleziona prestazione --</option>';

        if (!brancaId) return;

        fetch(`/operatore/prestazioni-per-branca/${brancaId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(prestazione => {
                    const opt = document.createElement('option');
                    opt.value = prestazione.codice_nomenclatore; // deve corrispondere a data-prestazione-id
                    opt.textContent = prestazione.denominazione_nomenclatore;
                    prestazioneSelect.appendChild(opt);
                });

                // Se è stato passato un codice prestazione, selezionalo
                if (selectedPrestazioneId) {
                    prestazioneSelect.value = selectedPrestazioneId;
                }
            })
            .catch(err => console.error('Errore nel recuperare le prestazioni (scheda):', err));
    }



    // Event listener per quando si apre la modale
    document.getElementById('modalSchedaPaziente').addEventListener('show.bs.modal', function(event) {
        // Bottone che ha triggerato la modale
        const button = event.relatedTarget;

        // Se il bottone ha passato dati, popoliamo i campi
        if (button) {
            const prenotazioneId = button.getAttribute('data-prenotazione-id');
            const pazienteId = button.getAttribute('data-paziente-id');
            const brancaId = button.getAttribute('data-branca-id');
            const luogoPrestazione = button.getAttribute('data-luogo');
            const schedaAmnestica = button.getAttribute('data-scheda-amnestica');   


            // Popola i campi hidden
            if (prenotazioneId) document.getElementById('prenotazione_id').value = prenotazioneId;
            if (pazienteId) document.getElementById('paziente_id').value = pazienteId;

            // Popola altri campi se presenti
            if (brancaId) {
                const selectBranca = document.getElementById('selectBrancaScheda');
                selectBranca.value = brancaId;
                updatePrestazioniDropdownScheda();
            }

            if (luogoPrestazione) {
                document.getElementById('luogo').value = luogoPrestazione;
            }
        }

        // Reset del form quando si apre la modale
        resetModalForm();
    });

    function togglePresenza(valore) {
        const campiPrenotazione = document.getElementById('campiPrenotazione');

        if (valore == 1) {
            // Se presente, mostra i campi
            campiPrenotazione.style.display = 'block';
        } else {
            // Se assente, nasconde i campi
            campiPrenotazione.style.display = 'none';
        }
    }

    // Funzione per mostrare/nascondere la sezione farmaci
    function toggleTipologiaFarmaco(mostra) {
        const container = document.getElementById('tipologiaFarmacoContainer');
        container.style.display = mostra ? 'block' : 'none';
    }

    // Funzione per mostrare/nascondere il campo "Altro" farmaco
    function toggleAltroFarmaco() {
        const checkbox = document.getElementById('tipologiaAltro');
        const container = document.getElementById('altroFarmacoContainer');
        container.style.display = checkbox.checked ? 'block' : 'none';
    }

    // Funzione per mostrare/nascondere la sezione dispositivo medico
    function toggleTipologiaDispositivo(mostra) {
        const container = document.getElementById('tipologiaDispositivoContainer');
        container.style.display = mostra ? 'block' : 'none';
    }

    // Event listener per quando si apre la modale
    document.getElementById('modalSchedaPaziente').addEventListener('show.bs.modal', function(event) {
        // Bottone che ha triggerato la modale
        const button = event.relatedTarget;

        // Se il bottone ha passato dati, popoliamo i campi
        if (button) {
            const prenotazioneId = button.getAttribute('data-prenotazione-id');
            const pazienteId = button.getAttribute('data-paziente-id');
            const brancaId = button.getAttribute('data-branca-id');
            const luogoPrestazione = button.getAttribute('data-luogo');

            // Popola i campi hidden
            if (prenotazioneId) document.getElementById('prenotazione_id').value = prenotazioneId;
            if (pazienteId) document.getElementById('paziente_id').value = pazienteId;

            // Popola altri campi se presenti
            if (brancaId) {
                const selectBranca = document.getElementById('selectBrancaScheda');
                selectBranca.value = brancaId;
                if (typeof updatePrestazioniDropdownScheda === 'function') {
                    updatePrestazioniDropdownScheda();
                }
            }

            if (luogoPrestazione) {
                document.getElementById('luogo').value = luogoPrestazione;
            }
        }

        // Reset del form quando si apre la modale
        resetModalForm();
    });

    function resetModalForm() {
        // Reset presenza
        document.getElementById('presente').checked = false;
        document.getElementById('assente').checked = false;
        document.getElementById('campiPrenotazione').style.display = 'none';

        // Reset altri campi
        document.getElementById('icd9_tags').innerHTML = '';
        document.getElementById('icd9_selected').value = '[]';
        document.getElementById('icd9_input').value = '';
        document.getElementById('farmaci_tags').innerHTML = '';
        document.getElementById('farmaci_selected').value = '[]';
        document.getElementById('farmaci_input').value = '';
        document.getElementById('luogo').value = '';

        // Reset select
        document.getElementById('selectBrancaScheda').value = '';
        document.getElementById('selectPrestazioneScheda').innerHTML = '<option value="">-- Seleziona prestazione --</option>';
        document.getElementById('tipologiaDispositivo').value = '';

        // Reset radio farmaci e dispositivi
        document.getElementById('erogazioneNo').checked = true;
        document.getElementById('dispositivoNo').checked = true;
        document.getElementById('tipologiaFarmacoContainer').style.display = 'none';
        document.getElementById('tipologiaDispositivoContainer').style.display = 'none';
        document.getElementById('altroFarmacoContainer').style.display = 'none';

        // Reset checkbox tipologie farmaco
        const checkboxes = document.querySelectorAll('.tipologiaCheckbox');
        checkboxes.forEach(checkbox => checkbox.checked = false);

        // Reset campi "Altro" farmaco
        document.getElementById('altroFarmacoDescrizione').value = '';
        document.getElementById('altroFarmacoCodice').value = '';
    }

    const modalElement = document.getElementById('modalSchedaPaziente');

    if (modalElement) {
        modalElement.addEventListener('show.bs.modal', function(event) {
            // Button che ha triggerato la modal
            const button = event.relatedTarget;

            // Estrai i dati dagli attributi data-*
            const prenotazioneId = button.getAttribute('data-prenotazione-id');
            const pazienteId = button.getAttribute('data-paziente-id');
            const brancaId = button.getAttribute('data-branca-id');
            const luogo = button.getAttribute('data-luogo');

            // Popola i campi hidden
            document.getElementById('prenotazione_id').value = prenotazioneId || '';
            document.getElementById('paziente_id').value = pazienteId || '';

            // Popola il campo branca se presente
            if (brancaId) {
                const selectBranca = document.getElementById('selectBrancaScheda');
                selectBranca.value = brancaId;

                // Trigghera l'evento change per aggiornare le prestazioni
                if (typeof updatePrestazioniDropdownScheda === 'function') {
                    updatePrestazioniDropdownScheda();
                }

                // Sincronizza il campo se la funzione esiste
                if (typeof syncField === 'function') {
                    syncField(selectBranca);
                }
            }

            // Popola il campo luogo prestazione se presente
            if (luogo) {
                const luogoTextarea = document.getElementById('luogo');
                luogoTextarea.value = luogo;

                // Sincronizza il campo se la funzione esiste
                if (typeof syncField === 'function') {
                    syncField(luogoTextarea);
                }
            }

     const schedaAmnestica = button.getAttribute('data-scheda-amnestica');

    const textarea = document.getElementById('scheda_amnestica');

    if (textarea) {
        textarea.value = schedaAmnestica ?? '';

        if (typeof syncField === 'function') {
            syncField(textarea);
        }
    }
        });
    }

    /****************************************ICD9*********************************/


    const input = document.getElementById('icd9_input');
    const dropdown = document.getElementById('icd9_dropdown');
    const tagsContainer = document.getElementById('icd9_tags');
    const hiddenInputs = document.getElementById('icd9_hidden_inputs');
    const hiddenField = document.getElementById('icd9_selected');

    let selectedCodes = [];
    let searchTimeout;

    // Carica eventuali dati esistenti
    try {
        const existingData = hiddenField.value;
        if (existingData && existingData !== '[]') {
            selectedCodes = JSON.parse(existingData);
            updateTags();
        }
    } catch (e) {
        console.error('Errore nel caricamento dati esistenti:', e);
    }

    // Ricerca con debouncing
    input.addEventListener('input', function() {
        const query = this.value.trim();

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            dropdown.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/operatore/icd9-search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    dropdown.innerHTML = '';

                    if (data.length === 0) {
                        const noResult = document.createElement('div');
                        noResult.className = 'icd9-option text-muted';
                        noResult.textContent = 'Nessun risultato trovato';
                        dropdown.appendChild(noResult);
                        dropdown.style.display = 'block';
                        return;
                    }

                    data.forEach(item => {
                        // Verifica se già selezionato
                        const isSelected = selectedCodes.some(c => c.codice === item.codice);

                        const option = document.createElement('div');
                        option.className = 'icd9-option list-group-item list-group-item-action';
                        if (isSelected) {
                            option.classList.add('disabled', 'bg-light');
                            option.style.cursor = 'not-allowed';
                        }
                        option.innerHTML = `<strong>${item.codice}</strong> - ${item.descrizione}`;

                        if (!isSelected) {
                            option.addEventListener('click', () => addIcd9Tag(item));
                        }

                        dropdown.appendChild(option);
                    });

                    dropdown.style.display = 'block';
                })
                .catch(err => {
                    console.error('Errore ricerca ICD-9:', err);
                    dropdown.innerHTML = '<div class="icd9-option text-danger">Errore durante la ricerca</div>';
                    dropdown.style.display = 'block';
                });
        }, 300);
    });

    // Aggiungi tag
    function addIcd9Tag(item) {
        // Verifica duplicati
        if (selectedCodes.some(c => c.codice === item.codice)) {
            alert('Questo codice è già stato aggiunto');
            input.value = '';
            dropdown.style.display = 'none';
            return;
        }

        selectedCodes.push({
            codice: item.codice,
            descrizione: item.descrizione
        });

        updateTags();
        input.value = '';
        dropdown.style.display = 'none';
    }

    // Aggiorna visualizzazione e input hidden
    function updateTags() {
        tagsContainer.innerHTML = '';
        hiddenInputs.innerHTML = '';

        if (selectedCodes.length === 0) {
            const placeholder = document.createElement('div');
            placeholder.className = 'text-muted small';
            placeholder.textContent = 'Nessuna diagnosi selezionata';
            tagsContainer.appendChild(placeholder);
        } else {
            selectedCodes.forEach((item, index) => {
                // Tag visibile
                const tag = document.createElement('span');
                tag.className = 'badge bg-primary me-2 mb-2 d-inline-flex align-items-center';
                tag.style.fontSize = '0.9rem';
                tag.innerHTML = `
                    <span>${item.codice} - ${item.descrizione}</span>
                    <span class="ms-2" style="cursor:pointer; font-size: 1.2rem;" title="Rimuovi">&times;</span>
                `;

                tag.querySelector('span:last-child').addEventListener('click', () => {
                    removeIcd9Tag(index);
                });

                tagsContainer.appendChild(tag);

                // Input hidden per codice (per compatibilità con form array)
                const hiddenInputCode = document.createElement('input');
                hiddenInputCode.type = 'hidden';
                hiddenInputCode.name = 'icd9_codes[]';
                hiddenInputCode.value = item.codice;
                hiddenInputs.appendChild(hiddenInputCode);

                // Input hidden per descrizione
                const hiddenInputDesc = document.createElement('input');
                hiddenInputDesc.type = 'hidden';
                hiddenInputDesc.name = 'icd9_descriptions[]';
                hiddenInputDesc.value = item.descrizione;
                hiddenInputs.appendChild(hiddenInputDesc);
            });
        }

        // Aggiorna campo JSON (contiene array completo con codice e descrizione)
        hiddenField.value = JSON.stringify(selectedCodes);

        // Trigger evento change per eventuali listener
        hiddenField.dispatchEvent(new Event('change'));
    }

    // Rimuovi tag
    function removeIcd9Tag(index) {
        if (confirm('Vuoi rimuovere questa diagnosi?')) {
            selectedCodes.splice(index, 1);
            updateTags();
        }
    }

    // Chiudi dropdown cliccando fuori
    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && e.target !== input) {
            dropdown.style.display = 'none';
        }
    });

    // Gestione tasto ESC per chiudere dropdown
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            dropdown.style.display = 'none';
            input.blur();
        }
    });

    // Esponi funzione globale per uso esterno (opzionale)
    window.getSelectedIcd9 = function() {
        return selectedCodes;
    };

    window.setSelectedIcd9 = function(codes) {
        selectedCodes = codes;
        updateTags();
    };


    /*********************************Farmaci****************************************** */

    const routeFarmaci = "{{ route('cercaFarmaci', ['tipologiaId' => '__TIPOLOGIA__']) }}";

    // Mostra o nasconde il container tipologia farmaco
    function toggleTipologiaFarmaco(show) {
        const container = document.getElementById('tipologiaFarmacoContainer');
        if (show) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
            // Reset dei campi
            document.getElementById('selectTipologiaFarmaco').value = '';
            document.getElementById('selectFarmaci').innerHTML = '<option value="">-- Prima seleziona una tipologia --</option>';
            document.getElementById('selectFarmaciContainer').style.display = 'none';
            document.getElementById('altroFarmacoContainer').style.display = 'none';
        }
    }

    // Carica i farmaci in base alla tipologia selezionata
    function loadFarmaci(tipologiaId) {
        const selectFarmaci = document.getElementById('selectFarmaci');
        const selectFarmaciContainer = document.getElementById('selectFarmaciContainer');
        const altroContainer = document.getElementById('altroFarmacoContainer');

        // Gestione opzione "Altro"
        if (tipologiaId === 'altro') {
            altroContainer.style.display = 'block';
            selectFarmaciContainer.style.display = 'none';
            selectFarmaci.innerHTML = '';
            updateSelectedFarmaci();
            return;
        } else {
            altroContainer.style.display = 'none';
        }

        if (!tipologiaId) {
            selectFarmaciContainer.style.display = 'none';
            selectFarmaci.innerHTML = '<option value="">-- Prima seleziona una tipologia --</option>';
            return;
        }

        selectFarmaciContainer.style.display = 'block';
        selectFarmaci.innerHTML = '<option value="">Caricamento...</option>';

        const url = routeFarmaci.replace('__TIPOLOGIA__', tipologiaId);

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Errore nella risposta');
                return response.json();
            })
            .then(data => {
                selectFarmaci.innerHTML = '';
                if (data.length === 0) {
                    selectFarmaci.innerHTML = '<option value="">Nessun farmaco disponibile</option>';
                } else {
                    data.forEach(farmaco => {
                        const option = document.createElement('option');
                        option.value = farmaco.codice_farmaco;
                        option.textContent = `${farmaco.codice_farmaco} - ${farmaco.descrizione}`;
                        option.dataset.id = farmaco.id;
                        selectFarmaci.appendChild(option);
                    });
                }
                updateSelectedFarmaci();
            })
            .catch(error => {
                console.error('Errore nel caricamento dei farmaci:', error);
                selectFarmaci.innerHTML = '<option value="">Errore nel caricamento</option>';
            });
    }

    // Aggiorna il campo hidden con i farmaci selezionati
    function updateSelectedFarmaci() {
        const selectFarmaci = document.getElementById('selectFarmaci');
        const selectedOptions = Array.from(selectFarmaci.selectedOptions);
        const selectedCodes = selectedOptions.map(opt => opt.value);
        document.getElementById('farmaci_selected').value = JSON.stringify(selectedCodes);
    }


    document.getElementById('selectFarmaci')?.addEventListener('change', updateSelectedFarmaci);

    function syncField(element) {
        console.log('Sync field:', element.name, element.value);
    }
</script>

@endsection