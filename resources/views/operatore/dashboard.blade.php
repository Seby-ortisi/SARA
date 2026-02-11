@extends('bootstrap-italia::page')

@section('content')

<style>

#allegati_minorenne .form-label {
    writing-mode: horizontal-tb !important;
    text-orientation: mixed !important;
    display: block !important;
}
    </style>
<div class="container-fluid py-4">

    {{-- 🔔 MESSAGGI DI SESSIONE --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span class="fw-bold">✓</span> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @elseif (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="fw-bold">Errore!</span> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Si sono verificati i seguenti errori:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('pdf_download'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        async function downloadPDF() {
            try {
                const response = await fetch("{{ route('download.pdf', session('pdf_download')) }}");
                if (!response.ok) {
                    throw new Error('Errore nel download del PDF');
                }
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = "{{ session('pdf_download') }}";
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Errore durante il download del PDF:', error);
            }
        }
        setTimeout(async function() {
            await downloadPDF();
        }, 2000); 
    });
</script>
@endif



    {{-- 👋 INTESTAZIONE --}}
    <div class="text-center mb-4">
        <h2 class="border-bottom pb-3">{{ __('Modulo registrazione utenti') }}</h2>
    </div>

    {{-- 🔎 SEZIONE RICERCA PAZIENTE PER CF --}}
    <div class="card bg-white border-dark border-2 shadow mb-4">
        <div class="card-header border-bottom border-dark text-center fw-bold bg-light">
            <h5 class="mb-0">🔍 Ricerca da APC</h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label for="codiceFiscale" class="form-label">Codice Fiscale <span class="text-danger">*</span></label>
                    <input type="text" id="codiceFiscale" name="codiceFiscale" class="form-control" autocomplete="off">
                </div>
                <div class="col-md-8">
                    <span id="aiutoCF" class="d-block mt-4"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- 🧾 FORM DI REGISTRAZIONE UTENTE --}}
    <form action="@if(empty($dataView['user']->id)) {{ route('operatore.registraNuovoUtente') }} @else {{ route('admin.aggiornaDati', ['id' => $dataView['user']->id]) }} @endif" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- SEZIONE A DUE COLONNE: DATI ANAGRAFICI E CRITERI ACCESSO --}}
        <div class="row g-4 mb-4">

            {{-- COLONNA SINISTRA: DATI ANAGRAFICI --}}
            <div class="col-lg-6">
                <div class="card bg-white border-dark border-2 shadow h-100">
                    <div class="card-header border-bottom border-dark text-center fw-bold bg-primary text-white">
                        <h5 class="mb-0">📋 Dati Anagrafici</h5>
                    </div>
                    <div class="card-body">

                        {{-- Nome --}}
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" id="nome" name="nome"
                                value="{{ old('nome', $dataView['user']->nome ?? $dataView['nome'] ?? '') }}"
                                class="form-control form-control-sm" required>
                        </div>

                        {{-- Cognome --}}
                        <div class="mb-3">
                            <label for="cognome" class="form-label">Cognome <span class="text-danger">*</span></label>
                            <input type="text" id="cognome" name="cognome"
                                value="{{ old('cognome', $dataView['user']->cognome ?? $dataView['cognome'] ?? '') }}"
                                class="form-control form-control-sm" required>
                        </div>

                        {{-- Data di nascita --}}
                        <div class="mb-3">
                            <label for="data_nascita" class="form-label">Data di nascita <span class="text-danger">*</span></label>
                            <input type="date" id="data_nascita" name="data_nascita"
                                value="{{ old('data_nascita', $dataView['user']->data_nascita ?? $dataView['data_nascita'] ?? '') }}"
                                class="form-control form-control-sm" required>
                        </div>

                        {{-- Luogo di nascita --}}
                        <div class="mb-3">
                            <label for="luogo_nascita" class="form-label">Luogo di nascita</label>
                            <input type="text" id="luogo_nascita" name="luogo_nascita"
                                value="{{ old('luogo_nascita', $dataView['user']->luogo_nascita ?? '') }}"
                                class="form-control form-control-sm">
                        </div>

                        {{-- Sesso --}}
                        <div class="mb-3">
                            <label class="form-label">Sesso <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline" >
                                    <input class="form-check-input" type="radio" name="sesso" id="sesso_m" value="M" required
                                        @if(old('sesso', $dataView['user']->sesso ?? '') == 'M') checked @endif>
                                    <label class="form-check-label" for="sesso_m">Maschile</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="sesso" id="sesso_f" value="F"
                                        @if(old('sesso', $dataView['user']->sesso ?? '') == 'F') checked @endif>
                                    <label class="form-check-label" for="sesso_f">Femminile</label>
                                </div>
                            </div>
                        </div>

                        {{-- Tipo codice --}}
                        <div class="mb-3">
                            <label class="form-label">Tipo codice <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo_codice" id="radio_cf" value="cf">
                                    <label class="form-check-label" for="radio_cf">Codice Fiscale</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo_codice" id="radio_stp" value="stp">
                                    <label class="form-check-label" for="radio_stp">STP</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo_codice" id="radio_eni" value="eni">
                                    <label class="form-check-label" for="radio_eni">ENI</label>
                                </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo_codice" id="radio_nodoc" value="nodoc">
                                    <label class="form-check-label" for="radio_nodoc">Sprovvisto di documenti</label>
                                </div>
                            </div>
                        </div>

                        {{-- Codice Fiscale/STP/ENI --}}
                        <div class="mb-3 d-none" id="codice_container">
                            <label for="codice_fiscale" class="form-label" id="codice_label">
                                Codice <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="codice_fiscale" name="codice_fiscale"
                                class="form-control form-control-sm"
                                placeholder="Inserisci il Codice"
                                value="{{ old('codice_fiscale', $dataView['user']->codice_fiscale ?? $dataView['codice_fiscale'] ?? '') }}">
                        </div>

                        {{-- Cellulare --}}
                        <div class="mb-3">
                            <label for="cellulare" class="form-label">Cellulare</label>
                            <input type="text" id="cellulare" name="cellulare"
                                value="{{ old('telefono', $dataView['user']->cellulare ?? $dataView['telefono'] ?? '') }}"
                                class="form-control form-control-sm">
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', $dataView['user']->email ?? '') }}"
                                class="form-control form-control-sm">
                        </div>

                        {{-- Indirizzo di residenza --}}
                        <div class="mb-3">
                            <label for="indirizzo_residenza" class="form-label">Indirizzo di residenza</label>
                            <input type="text" id="indirizzo_residenza" name="indirizzo_residenza"
                                value="{{ old('indirizzo_residenza', $dataView['user']->indirizzo_residenza ?? '') }}"
                                class="form-control form-control-sm">
                        </div>

                        {{-- Nazionalità --}}
                        <div class="mb-3">
                            <label for="nazionalita" class="form-label">Nazionalità</label>
                            <select id="nazionalita" name="nazionalita" class="form-select form-select-sm">
                                <option value="">Seleziona nazionalità</option>
                                @foreach ($dataView['nazioni'] as $nazione)
                                <option value="{{ $nazione->codice_nazione }}"
                                    @if(old('nazionalita', $dataView['user']->nazionalita ?? '') == $nazione->codice_nazione) selected @endif>
                                    {{ $nazione->nome_nazione }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Comune --}}
                        <div class="mb-3">
                            <label for="comune" class="form-label">Comune di residenza</label>
                            <select id="comune" name="comune" class="form-select form-select-sm">
                                <option value="">Seleziona Comune</option>
                                @foreach ($dataView['comuni'] as $comune)
                                <option value="{{ $comune->istat_comune }}"
                                    @if(old('comune', $dataView['user']->comune ?? '') == $comune->istat_comune) selected @endif>
                                    {{ $comune->comune }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            {{-- COLONNA DESTRA: CRITERI DI ACCESSO AL SERVIZIO --}}
            <div class="col-lg-6">
                <div class="card bg-white border-dark border-2 shadow h-100">
                    <div class="card-header border-bottom border-dark text-center fw-bold bg-info text-white">
                        <h5 class="mb-0">✓ Criteri di Accesso al Servizio</h5>
                    </div>
                    <div class="card-body">
                        {{-- Titolo di studio --}}
                        <div class="mb-3">
                            <label for="titolo_studio" class="form-label">Titolo di studio</label>
                            <select id="titolo_studio" name="titolo_studio" class="form-select form-select-sm w-100" style="max-width: 100%; overflow-x: auto;">
                                <option value="">Nessun titolo di studio</option>
                                @foreach ($dataView['titoli_di_studio'] as $titolo)
                                <option value="{{ $titolo->id }}" title="{{ $titolo->descrizione }}"
                                    @if(old('titolo_studio', $dataView['criteriAccesso']->titolo_studio ?? '') == $titolo->id) selected @endif>
                                    {{ \Illuminate\Support\Str::limit($titolo->descrizione, 60) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ETS --}}
                        <div class="mb-3">
                            <label for="ets" class="form-label">Seleziona ETS</label>
                            <select id="ets" name="ets" class="form-select form-select-sm w-100" style="max-width: 100%; overflow-x: auto;">
                                <option value="0">Nessun ETS</option>
                                @foreach ($dataView['ets'] as $ets)
                                <option value="{{ $ets->id }}"
                                    @if(old('ets', $dataView['criteriAccesso']->ets ?? '') == $ets->id) selected @endif>
                                    {{ $ets->descrizione }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Condizione professionale --}}
                        <div class="mb-3">
                            <label for="condizione_professionale" class="form-label">Condizione professionale attuale</label>
                            <select id="condizione_professionale" name="condizione_professionale" class="form-select form-select-sm w-100" style="max-width: 100%; overflow-x: auto;">
                                <option value="">Seleziona condizione professionale</option>
                                @foreach ($dataView['condizione_professionale'] as $condizione)
                                <option value="{{ $condizione->id }}" title="{{ $condizione->descrizione }}"
                                    @if(old('condizione_professionale', $dataView['criteriAccesso']->condizione_professionale ?? '') == $condizione->id) selected @endif>
                                    {{ $condizione->descrizione }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ricerca lavoro --}}
                        <div class="mb-3">
                            <label for="ricerca_lavoro" class="form-label">Sei alla ricerca di lavoro da (durata)</label>
                            <select id="ricerca_lavoro" name="ricerca_lavoro" class="form-select form-select-sm w-100" style="max-width: 100%; overflow-x: auto;">
                                <option value="">Seleziona una risposta</option>
                                @foreach ($dataView['ricerca_lavoro'] as $opzione)
                                <option value="{{ $opzione->id }}"
                                    @if(old('ricerca_lavoro', $dataView['criteriAccesso']->cerca_lavoro ?? '') == $opzione->id) selected @endif>
                                    {{ $opzione->descrizione }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Categoria vulnerabilità --}}
                        <div class="mb-3">
                            <label for="categoria_vulnerabilita" class="form-label">Categoria di vulnerabilità</label>
                            <select id="categoria_vulnerabilita" name="categoria_vulnerabilita" class="form-select form-select-sm w-100" style="max-width: 100%; overflow-x: auto;">
                                <option value="">Seleziona categoria</option>
                                @foreach ($dataView['categorie_di_vulnerabilita'] as $categoria)
                                <option value="{{ $categoria->id }}"
                                    {{ old('categoria_vulnerabilita', $dataView['criteriAccesso']->categoria_vulnerabilita ?? '') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->descrizione }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Altro vulnerabilità --}}
                        <div class="mb-3" id="altro-vulnerabilita-wrapper" style="display: none;">
                            <label for="altro_categoria_vulnerabilita" class="form-label">Specifica altro</label>
                            <input type="text" id="altro_categoria_vulnerabilita" name="altro_categoria_vulnerabilita"
                                class="form-control form-control-sm w-100"
                                value="{{ old('altro_categoria_vulnerabilita') ?? ($dataView['user']->altro_categoria_vulnerabilita ?? '') }}"
                                placeholder="Descrivi l'altra vulnerabilità">
                        </div>

                        {{-- Condizione vulnerabilità socio economica --}}
                        <div class="mb-3">
                            <label class="form-label">Condizione di vulnerabilità socio economica</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="conferma" id="conferma_si" value="si"
                                        {{ old('conferma', $dataView['criteriAccesso']->condizione_vulnerabilita_socio_economica ?? '') == 'si' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="conferma_si">Sì</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="conferma" id="conferma_no" value="no"
                                        {{ old('conferma', $dataView['criteriAccesso']->condizione_vulnerabilita_socio_economica ?? '') == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="conferma_no">No</label>
                                </div>
                            </div>
                        </div>

                        {{-- Blocchi vulnerabilità --}}
                        <div id="blocchi_vulnerabilita" style="display: none;">
                            <div class="mb-3">
                                <label for="criteri_contesto" class="form-label">Criteri riferiti al contesto</label>
                                <select id="criteri_contesto" name="criteri_contesto" class="form-select form-select-sm w-100" style="max-width: 100%; overflow-x: auto;">
                                    <option value="">Seleziona criterio riferito al contesto</option>
                                    @foreach ($dataView['criterio_contesto'] as $criterio)
                                    <option value="{{ $criterio->codice }}"
                                        {{ old('criteri_contesto', $dataView['criteriAccesso']->criteri_contesto ?? '') == $criterio->codice ? 'selected' : '' }}>
                                        {{ $criterio->descrizione }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="criteri_persona" class="form-label">Criteri riferiti alla persona</label>
                                <select id="criteri_persona" name="criteri_persona" class="form-select form-select-sm w-100" style="max-width: 100%; overflow-x: auto;">
                                    <option value="">Seleziona criterio riferito alla persona</option>
                                    @foreach ($dataView['criteri_persona'] as $criterio)
                                    <option value="{{ $criterio->codice }}"
                                        {{ old('criteri_persona', $dataView['criteriAccesso']->criteri_persona ?? '') == $criterio->codice ? 'selected' : '' }}>
                                        {{ $criterio->descrizione }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">

                           <div class="col-md-12 d-none" id="allegato_minore_container">
                           <label id="label-minorenni" class="form-label fw-bold text-danger">
                                Documenti obbligatori per minorenni
                            </label>

                        <div class="mb-2">
                            <label for="doc3" class="form-label">Copia del primo foglio dell'ISEE *</label>
                            <input type="file" id="doc3" name="copia_primo_foglio_ISEE_minorenne" class="form-control form-control-sm">

                            @if (isset($dataView['criteriAccesso']->copia_primo_foglio_ISEE_minorenne) && $dataView['criteriAccesso']->copia_primo_foglio_ISEE_minorenne)
                                <div class="mt-2">
                                    <p>File caricato: 
                                        <a href="{{ asset('storage/' . $dataView['criteriAccesso']->copia_primo_foglio_ISEE_minorenne) }}" target="_blank">
                                            {{ basename($dataView['criteriAccesso']->copia_primo_foglio_ISEE_minorenne) }}
                                        </a>
                                    </p>
                                </div>
                            @elseif (old('copia_primo_foglio_ISEE_minorenne'))
                                <div class="mt-2">
                                    <p>Hai selezionato un file: {{ basename(old('copia_primo_foglio_ISEE_minorenne')) }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="mb-2">
                            <label for="doc4" class="form-label">Documento del genitore *</label>
                            <input type="file" id="doc4" name="documento_genitore" class="form-control form-control-sm">

                            @if (isset($dataView['criteriAccesso']->documento_genitore) && $dataView['criteriAccesso']->documento_genitore)
                                <div class="mt-2">
                                    <p>File caricato: 
                                        <a href="{{ asset('storage/' . $dataView['criteriAccesso']->documento_genitore) }}" target="_blank">
                                            {{ basename($dataView['criteriAccesso']->documento_genitore) }}
                                        </a>
                                    </p>
                                </div>
                            @elseif (old('documento_genitore'))
                                <div class="mt-2">
                                    <p>Hai selezionato un file: {{ basename(old('documento_genitore')) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                            {{-- ISEE Maggiorenni --}}
                            <div class="col-md-6 mb-3 d-none" id="allegato_isee_container">
                                <label for="allegato_isee" class="form-label text-danger">
                                    Copia del primo foglio dell'ISEE *
                                </label>
                                <input type="file" class="form-control form-control-sm" id="allegato_isee" name="copia_primo_foglio_ISEE">
                                @if (isset($dataView['criteriAccesso']->copia_primo_foglio_ISEE) && $dataView['criteriAccesso']->copia_primo_foglio_ISEE)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $dataView['criteriAccesso']->copia_primo_foglio_ISEE) }}" target="_blank" class="text-decoration-none">
                                        📄 {{ basename($dataView['criteriAccesso']->copia_primo_foglio_ISEE) }}
                                    </a>
                                </div>
                                @endif
                            </div>

                            {{-- Permesso soggiorno --}}
                            <div class="col-md-6 mb-3">
                                <label for="permesso_soggiorno" class="form-label">Permesso soggiorno</label>
                                <input type="file" id="permesso_soggiorno" name="permesso_soggiorno" class="form-control form-control-sm">
                                @if (isset($dataView['criteriAccesso']->permesso_soggiorno) && $dataView['criteriAccesso']->permesso_soggiorno)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $dataView['criteriAccesso']->permesso_soggiorno) }}" target="_blank" class="text-decoration-none">
                                        📄 {{ basename($dataView['criteriAccesso']->permesso_soggiorno) }}
                                    </a>
                                </div>
                                @endif
                            </div>

                            {{-- Documento identità --}}
                            <div class="col-md-6 mb-3">
                                <label for="allegato" class="form-label">Documento di identità</label>
                                <input type="file" id="allegato" name="allegato_documento_identita" class="form-control form-control-sm">
                                @if (isset($dataView['criteriAccesso']->allegato_documento_identita) && $dataView['criteriAccesso']->allegato_documento_identita)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $dataView['criteriAccesso']->allegato_documento_identita) }}" target="_blank" class="text-decoration-none">
                                        📄 {{ basename($dataView['criteriAccesso']->allegato_documento_identita) }}
                                    </a>
                                </div>
                                @endif
                            </div>

                            {{-- Tessera sanitaria --}}
                            <div class="col-md-6 mb-3">
                                <label for="allegato_tessera_sanitaria" class="form-label">Tessera sanitaria</label>
                                <input type="file" id="allegato_tessera_sanitaria" name="allegato_tessera_sanitaria" class="form-control form-control-sm">
                                @if (isset($dataView['criteriAccesso']->allegato_tessera_sanitaria) && $dataView['criteriAccesso']->allegato_tessera_sanitaria)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $dataView['criteriAccesso']->allegato_tessera_sanitaria) }}" target="_blank" class="text-decoration-none">
                                        📄 {{ basename($dataView['criteriAccesso']->allegato_tessera_sanitaria) }}
                                    </a>
                                </div>
                                @endif
                            </div>

                            

                            {{-- Altri file --}}
                            <div class="col-md-8 mb-3">
                                <label for="altri_file" class="form-label">Altri file</label>
                                <input type="file" id="altri_file" name="altri_file[]" class="form-control form-control-sm" multiple>
                                @if (isset($dataView['criteriAccesso']->altri_pdf) && $dataView['criteriAccesso']->altri_pdf)
                                <div class="mt-2">
                                    @php $files = explode(',', $dataView['criteriAccesso']->altri_pdf); @endphp
                                    @foreach ($files as $file)
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="d-block text-decoration-none">
                                        📄 {{ basename($file) }}
                                    </a>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="descrizione_file" class="form-label">Descrizione file</label>
                                <input type="text" id="descrizione_file" name="descrizione_file" class="form-control form-control-sm" placeholder="Descrivi il file">
                            </div>

                            {{-- Note --}}
                            <div class="col-12 mb-3">
                                <label for="note" class="form-label">Note</label>
                                <textarea id="note" name="note" rows="3" class="form-control form-control-sm"
                                    placeholder="Inserisci eventuali note">{{ $dataView['criteriAccesso']->note ?? '' }}</textarea>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>


        {{-- SEZIONE DATI TUTORE/GENITORE (per minorenni) --}}
        <div class="col-12 d-none" id="card_tutore">
            <div class="it-card it-card--danger shadow-sm mb-4">
                <div class="card-header border-bottom border-dark text-center fw-bold bg-danger text-white">
                    <h5 class="mb-0">👨‍👩‍👧 Dati del Genitore o Tutore (Obbligatorio per minorenni)</h5>
                </div>

            <div class="it-card-body">

            {{-- Nome --}}
            <div class="mb-3">
                <label for="nome_tutore" class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" id="nome_tutore" name="nome_tutore" 
                    value="{{ old('nome_tutore') }}" class="form-control form-control-sm" required>
            </div>

            {{-- Cognome --}}
            <div class="mb-3">
                <label for="cognome_tutore" class="form-label">Cognome <span class="text-danger">*</span></label>
                <input type="text" id="cognome_tutore" name="cognome_tutore" 
                    value="{{ old('cognome_tutore') }}" class="form-control form-control-sm" required>
            </div>

            {{-- Data di nascita --}}
            <div class="mb-3">
                <label for="data_nascita_tutore" class="form-label">Data di nascita <span class="text-danger">*</span></label>
                <input type="date" id="data_nascita_tutore" name="data_nascita_tutore" 
                    value="{{ old('data_nascita_tutore') }}" class="form-control form-control-sm" required>
            </div>

            {{-- Luogo di nascita --}}
            <div class="mb-3">
                <label for="luogo_nascita_tutore" class="form-label">Luogo di nascita <span class="text-danger">*</span></label>
                <input type="text" id="luogo_nascita_tutore" name="luogo_nascita_tutore" 
                    value="{{ old('luogo_nascita_tutore') }}" class="form-control form-control-sm" required>
            </div>

            {{-- Stato di nascita --}}
            <div class="mb-3">
                <label for="stato_nascita_tutore" class="form-label">Stato di nascita <span class="text-danger">*</span></label>
                <input type="text" id="stato_nascita_tutore" name="stato_nascita_tutore" 
                    value="{{ old('stato_nascita_tutore') }}" class="form-control form-control-sm" required>
            </div>

            {{-- Sesso --}}
            <div class="mb-3">
                <label class="form-label">Sesso <span class="text-danger">*</span></label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sesso_tutore" id="sesso_tutore_m" value="M"
                            {{ old('sesso_tutore') == 'M' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="sesso_tutore_m">Maschile</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sesso_tutore" id="sesso_tutore_f" value="F"
                            {{ old('sesso_tutore') == 'F' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="sesso_tutore_f">Femminile</label>
                    </div>
                </div>
            </div>

            {{-- Codice Fiscale --}}
            <div class="mb-3">
                <label for="codice_fiscale_tutore" class="form-label">Codice Fiscale <span class="text-danger">*</span></label>
                <input type="text" id="codice_fiscale_tutore" name="codice_fiscale_tutore" 
                    value="{{ old('codice_fiscale_tutore') }}" class="form-control form-control-sm" required>
            </div>

            {{-- Cellulare --}}
            <div class="mb-3">
                <label for="cellulare_tutore" class="form-label">Cellulare <span class="text-danger">*</span></label>
                <input type="text" id="cellulare_tutore" name="cellulare_tutore" 
                    value="{{ old('cellulare_tutore') }}" class="form-control form-control-sm" required>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email_tutore" class="form-label">Email <span class="text-danger"></span></label>
                <input type="email" id="email_tutore" name="email_tutore" 
                    value="{{ old('email_tutore') }}" class="form-control form-control-sm" >
            </div>

            {{-- Indirizzo --}}
            <div class="mb-3">
                <label for="indirizzo_residenza_tutore" class="form-label">Indirizzo di residenza <span class="text-danger">*</span></label>
                <input type="text" id="indirizzo_residenza_tutore" name="indirizzo_residenza_tutore" 
                    value="{{ old('indirizzo_residenza_tutore') }}" class="form-control form-control-sm" required>
            </div>

            {{-- Nazionalità --}}
            <div class="mb-3">
                <label for="nazionalita_tutore" class="form-label">Nazionalità <span class="text-danger">*</span></label>
                <select id="nazionalita_tutore" name="nazionalita_tutore" class="form-select form-select-sm" required>
                    <option value="">Seleziona nazionalità</option>
                    @foreach ($dataView['nazioni'] as $nazione)
                        <option value="{{ $nazione->codice_nazione }}"
                            {{ old('nazionalita_tutore') == $nazione->codice_nazione ? 'selected' : '' }}>
                            {{ $nazione->nome_nazione }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Comune --}}
            <div class="mb-3">
                <label for="comune_tutore" class="form-label">Comune di residenza <span class="text-danger">*</span></label>
                <select id="comune_tutore" name="comune_tutore" class="form-select form-select-sm" required>
                    <option value="">Seleziona Comune</option>
                    @foreach ($dataView['comuni'] as $comune)
                        <option value="{{ $comune->istat_comune }}"
                            {{ old('comune_tutore') == $comune->istat_comune ? 'selected' : '' }}>
                            {{ $comune->comune }}
                        </option>
                    @endforeach
                </select>
            </div>

            <hr class="my-3">

            {{-- Qualifica - radio required --}}
            <h6 class="mb-2 text-primary fw-bold">In qualità di:</h6>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="relazione" value="1" id="diretto_interessato" required>
                <label class="form-check-label" for="diretto_interessato">
                    Diretto interessato
                </label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="relazione" value="3" id="familiare" required>
                <label class="form-check-label" for="familiare">
                    Familiare 
                </label>
            </div>

            <div class="form-check mt-2">
                <input class="form-check-input" type="radio" name="relazione" value="4" id="accompagnatore_responsabile" required>
                <label class="form-check-label" for="accompagnatore_responsabile">
                    Accompagnatore / Responsabile struttura
                    
                </label>
            </div>

            <div class="form-check mt-2">
                <input class="form-check-input" type="radio" name="relazione" value="5" id="rappresentante_legale" required>
                <label class="form-check-label" for="rappresentante_legale">
                    Rappresentante legale
                </label>
            </div>

            <div class="form-check mt-2">
                <input class="form-check-input" type="radio" name="relazione" value="6" id="altro" required>
                <label class="form-check-label" for="altro">
                    Altro
                </label>
            </div>

        </div>

    </div>
</div>


        
    @if(empty($dataView['user']->id))
        <button type="submit" class="btn btn-primary btn-block" id="submitBtn" style="display:flex; align-items:center; justify-content:center;">
            <i class="bi bi-floppy"></i>&nbsp;&nbsp;{{ __('Registrati') }}
        </button>
    @else
        <button type="submit" class="btn btn-info btn-sm ms-2" style="display:flex; align-items:center; justify-content:center;">
            <i class="bi bi-pencil"></i>&nbsp;&nbsp;{{ __('Aggiorna') }}
        </button>
    @endif

</div>

</form>


@endsection

@section('bootstrapitalia_js')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF Token per Ajax (jQuery)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // === ELEMENTI DOM ===
    const selectCategoria = document.getElementById('categoria_vulnerabilita');
    const wrapperAltroVulnerabilita = document.getElementById('altro-vulnerabilita-wrapper');

    const radioConfermaSi = document.getElementById('conferma_si');
    const radioConfermaNo = document.getElementById('conferma_no');
    const blocchiVulnerabilita = document.getElementById('blocchi_vulnerabilita');

    const selectContesto = document.getElementById('criteri_contesto');
    const selectPersona = document.getElementById('criteri_persona');

    const codiceFiscaleInput = document.getElementById('codiceFiscale');
    const aiutoCFDiv = document.getElementById('aiutoCF');

    const radioCf = document.getElementById('radio_cf');
    const radioStp = document.getElementById('radio_stp');
    const radioEni = document.getElementById('radio_eni');
    const radioNodoc = document.getElementById('radio_nodoc');

    const codiceContainer = document.getElementById('codice_container');
    const codiceLabel = document.getElementById('codice_label');
    const codiceInput = document.getElementById('codice_fiscale');

    const dataNascitaInput = document.getElementById('data_nascita');
    const allegatiMinorenne = document.getElementById('allegato_minore_container');
    const cardTutore = document.getElementById('card_tutore');

    const allegatoIseeContainer = document.getElementById('allegato_isee_container');
    const selectVulnerabilita = document.getElementById('categoria_vulnerabilita');

    // === FUNZIONI HELPER ===
    function escapeHtml(text) {
        return text.replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function calcolaEta(dataString) {
        if (!dataString) return null;
        const oggi = new Date();
        const nascita = new Date(dataString);
        let eta = oggi.getFullYear() - nascita.getFullYear();
        const m = oggi.getMonth() - nascita.getMonth();
        if (m < 0 || (m === 0 && oggi.getDate() < nascita.getDate())) {
            eta--;
        }
        return eta;
    }

    // === GESTIONE CARD MINORENNE E CAMPI REQUIRED ===
    function mostraCardMinorenne() {
        const dataNascita = dataNascitaInput.value;
        
        if (!dataNascita) {
            // Nessuna data inserita, nascondi tutto
            if (cardTutore) cardTutore.classList.add('d-none');
            if (allegatiMinorenne) allegatiMinorenne.classList.add('d-none');
            disabilitaCampiTutore();
            return;
        }

        const eta = calcolaEta(dataNascita);

        if (eta < 18) {
            // MINORENNE: mostra card e allegati
            if (cardTutore) cardTutore.classList.remove('d-none');
            if (allegatiMinorenne) allegatiMinorenne.classList.remove('d-none');
            abilitaCampiTutore();
        } else {
            // MAGGIORENNE: nascondi card e allegati
            if (cardTutore) cardTutore.classList.add('d-none');
            if (allegatiMinorenne) allegatiMinorenne.classList.add('d-none');
            disabilitaCampiTutore();
        }
    }

    function disabilitaCampiTutore() {
        if (!cardTutore) return;
        
        // Disabilita tutti gli input, select e textarea nella card tutore
        cardTutore.querySelectorAll('input, select, textarea').forEach(field => {
            field.disabled = true;
            field.removeAttribute('required');
        });
    }

    function abilitaCampiTutore() {
        if (!cardTutore) return;
        
        // Riabilita tutti i campi e ripristina required dove necessario
        cardTutore.querySelectorAll('input, select, textarea').forEach(field => {
            field.disabled = false;
            
            // Ripristina required sui campi che devono averlo
            const label = field.closest('.mb-3')?.querySelector('label');
            if (label && label.innerHTML.includes('*')) {
                field.setAttribute('required', 'required');
            }
        });
    }

    // Listener sulla data di nascita
    if (dataNascitaInput) {
        dataNascitaInput.addEventListener('change', mostraCardMinorenne);
        // Esegui al caricamento se c'è già un valore
        mostraCardMinorenne();
    }

    // === GESTIONE SUBMIT FORM ===
    const form = document.querySelector('form[action*="registraNuovoUtente"], form[action*="aggiornaDati"]');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Assicurati che i campi disabilitati non blocchino il submit
            if (cardTutore && cardTutore.classList.contains('d-none')) {
                cardTutore.querySelectorAll('input, select, textarea').forEach(field => {
                    field.disabled = true;
                    field.removeAttribute('required');
                });
            }
        });
    }

    // === TOGGLE CATEGORIA VULNERABILITÀ (ALTRO) ===
    function toggleAltroVulnerabilita() {
        if (selectCategoria && wrapperAltroVulnerabilita) {
            wrapperAltroVulnerabilita.style.display = selectCategoria.value === '7' ? '' : 'none';
        }
    }

    if (selectCategoria) {
        selectCategoria.addEventListener('change', toggleAltroVulnerabilita);
        toggleAltroVulnerabilita();
    }

    // === TOGGLE BLOCCHI VULNERABILITÀ (CONFERMA SI/NO) ===
    function toggleBlocchiVulnerabilita() {
        if (radioConfermaSi && blocchiVulnerabilita) {
            blocchiVulnerabilita.style.display = radioConfermaSi.checked ? 'block' : 'none';
        }
    }

    if (radioConfermaSi && radioConfermaNo) {
        radioConfermaSi.addEventListener('change', toggleBlocchiVulnerabilita);
        radioConfermaNo.addEventListener('change', toggleBlocchiVulnerabilita);
        toggleBlocchiVulnerabilita();
    }

    // === AGGIORNA SELECT PERSONA IN BASE A CONTESTO ===
    function aggiornaSelectPersona() {
        if (!selectContesto || !selectPersona) return;

        const haValoreContesto = selectContesto.value !== '';

        if (haValoreContesto) {
            const opzioneF = Array.from(selectPersona.options).find(opt => opt.value === 'F');
            if (opzioneF) {
                selectPersona.value = 'F';
            }
            selectPersona.style.pointerEvents = 'none';
            selectPersona.style.backgroundColor = '#e9ecef';
            selectPersona.style.color = '#6c757d';
        } else {
            selectPersona.style.pointerEvents = '';
            selectPersona.style.backgroundColor = '';
            selectPersona.style.color = '';
        }
    }

    if (selectContesto) {
        selectContesto.addEventListener('change', aggiornaSelectPersona);
        aggiornaSelectPersona();
    }

    // === RICERCA CODICE FISCALE (AJAX) ===
    if (codiceFiscaleInput && aiutoCFDiv) {
        codiceFiscaleInput.addEventListener('keyup', function() {
            const valore = this.value;

            if (valore.length >= 8) {
                $.ajax({
                    url: '{{ route("operatore.ricercaCF") }}',
                    method: 'POST',
                    data: {
                        query: valore
                    },
                    success: function(response) {
                        let html = '<ul class="list-group">';
                        $.each(response, function(index, item) {
                            if (index === 0) {
                                html += '<li class="list-group-item list-group-item-info fw-bold">Lista Pazienti</li>';
                            }
                            html += `
                            <li class="list-group-item">
                                <form method="POST" action="{{ route('operatore.inserisciPazienteAPC') }}" id="${item.codfis}">
                                    @csrf
                                    <input type="hidden" name="codice_fiscale" value="${item.codfis}">
                                    <input type="hidden" name="cognome" value="${item.cognome}">
                                    <input type="hidden" name="nome" value="${item.nome}">
                                    <input type="hidden" name="telefono" value="${item.telefono}">
                                    <input type="hidden" name="data_nascita" value="${item.datanas}">
                                    <input type="hidden" name="residenza" value="${escapeHtml(item.residenza)}">
                                    <a href="#" onclick="document.getElementById('${item.codfis}').submit(); return false;">
                                        <h6>${escapeHtml(item.cognome)} ${escapeHtml(item.nome)}</h6>
                                        <small class="text-muted">Codice fiscale: ${item.codfis} | Data nascita: ${item.datanas}</small>
                                    </a>
                                </form>
                            </li>
                        `;
                        });
                        html += '</ul>';
                        aiutoCFDiv.innerHTML = html;
                    },
                    error: function() {
                        aiutoCFDiv.innerHTML = '<div class="alert alert-danger">Errore nella richiesta Ajax.</div>';
                    }
                });
            } else {
                aiutoCFDiv.innerHTML = '';
            }
        });
    }

    // === AGGIORNA CAMPO CODICE (CF/STP/ENI) ===
    function aggiornaCampoCodice() {
        if (!codiceContainer || !codiceLabel || !codiceInput) return;

        if (radioCf && radioCf.checked) {
            codiceContainer.classList.remove('d-none');
            codiceLabel.textContent = 'Codice Fiscale *';
            codiceInput.placeholder = 'Inserisci il Codice Fiscale';
            codiceInput.required = true;
        } else if (radioStp && radioStp.checked) {
            codiceContainer.classList.remove('d-none');
            codiceLabel.textContent = 'Codice STP *';
            codiceInput.placeholder = 'Inserisci il Codice STP';
            codiceInput.required = true;
        } else if (radioEni && radioEni.checked) {
            codiceContainer.classList.remove('d-none');
            codiceLabel.textContent = 'Codice ENI *';
            codiceInput.placeholder = 'Inserisci il Codice ENI';
            codiceInput.required = true;
        } else if (radioNodoc && radioNodoc.checked) {
            codiceContainer.classList.remove('d-none');
            codiceLabel.textContent = 'Codice Temporaneo*';
            codiceInput.placeholder = 'Inserisci il Codice Temporaneo nel formato TEMP001';
            codiceInput.required = true;
        } else {
            codiceContainer.classList.add('d-none');
            codiceInput.required = false;
        }
    }

    document.querySelectorAll('input[name="tipo_codice"]').forEach((radio) => {
        radio.addEventListener('change', aggiornaCampoCodice);
    });

    // === TOGGLE ALLEGATO ISEE ===
    function toggleAllegatoIsee() {
        if (!selectPersona || !allegatoIseeContainer) return;

        const selectedValue = selectPersona.value;
        if (selectedValue === 'A' || selectedValue === '1') {
            allegatoIseeContainer.classList.remove('d-none');
        } else {
            allegatoIseeContainer.classList.add('d-none');
        }
    }

    if (selectPersona) {
        selectPersona.addEventListener('change', toggleAllegatoIsee);
        toggleAllegatoIsee();
    }

    // === TOGGLE ALLEGATO PERMESSO ===
    function toggleAllegatoPermesso() {
        // Funzione placeholder se necessaria
    }

    if (selectVulnerabilita) {
        selectVulnerabilita.addEventListener('change', toggleAllegatoPermesso);
        toggleAllegatoPermesso();
    }

    // === INIZIALIZZAZIONE ===
    // Nascondi card tutore all'avvio se non c'è data o maggiorenne
    if (cardTutore && (!dataNascitaInput.value || calcolaEta(dataNascitaInput.value) >= 18)) {
        cardTutore.classList.add('d-none');
        disabilitaCampiTutore();
    }
});


 const doc3 = document.getElementById('doc3');
    const doc4 = document.getElementById('doc4');
    const label = document.getElementById('label-minorenni');

    function checkFiles() {
        if (doc3.files.length > 0 && doc4.files.length > 0) {
            label.classList.remove('text-danger');
        }
    }

    doc3.addEventListener('change', checkFiles);
    doc4.addEventListener('change', checkFiles);


 // Nascondi card "Sei alla ricerca di lavoro da (durata)" in base alle condizioni dekka condizione di lavoro attuale
document.addEventListener('DOMContentLoaded', function () {
    const condizione = document.getElementById('condizione_professionale');
    const ricerca = document.getElementById('ricerca_lavoro');

    const condizioniAbilitate = ['1', '3'];

    function toggleRicercaLavoro() {
        if (condizioniAbilitate.includes(condizione.value)) {
            ricerca.disabled = false;
        } else {
            ricerca.disabled = true;
            ricerca.value = '';
        }
    }

    condizione.addEventListener('change', toggleRicercaLavoro);

    // utile per edit / old()
    toggleRicercaLavoro();
});

</script>
@endsection