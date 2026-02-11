@extends('bootstrap-italia::page')

@section('content')

<div class="card-header border-dark mb-2">
    <legend style="text-align:center">{{ __('Benvenuto utente ') }}</legend>
</div>

<p>In questa pagina puoi gestire le tue informazioni personali, completando i dati mancanti.</p>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif



<form method="POST" action="{{ route('registrazione.submit') }}" enctype="multipart/form-data"> 
  @csrf

<div class="card bg-white border-dark border-2 shadow mb-4">
 <div class="card bg-white border-dark border-2 shadow mb-4">
  <div class="card-header border-bottom border-dark text-center fw-bold">
    <h5 class="mb-0">Dati anagrafici</h5>
  </div>

  <div class="card-body">

    {{-- Campo: Nome --}}
    <div class="mb-3 row align-items-center">
      <label for="nome" class="col-sm-3 col-form-label">Nome</label>
      <div class="col-sm-9">
        <input type="text" id="nome" name="nome"
          class="form-control form-control-sm @error('nome') is-invalid @enderror"
          value="{{ old('nome', $dataView['user']->nome ?? '') }}">
        @error('nome')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    {{-- Campo: Cognome --}}
    <div class="mb-3 row align-items-center">
      <label for="cognome" class="col-sm-3 col-form-label">Cognome</label>
      <div class="col-sm-9">
        <input type="text" id="cognome" name="cognome"
          class="form-control form-control-sm @error('cognome') is-invalid @enderror"
          value="{{ old('cognome', $dataView['user']->cognome ?? '') }}">
        @error('cognome')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    {{-- Campo: Email --}}
    <div class="mb-3 row align-items-center">
      <label for="email" class="col-sm-3 col-form-label">Email</label>
      <div class="col-sm-9">
        <input type="email" id="email" name="email"
          class="form-control form-control-sm @error('email') is-invalid @enderror"
          value="{{ old('email', $dataView['user']->email ?? '') }}">
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="mb-3">
            <label class="form-label">Tipo codice <span class="text-danger">*</span></label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipo_codice" id="radio_cf" value="cf">
                <label class="form-check-label" for="radio_cf">Codice Fiscale</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipo_codice" id="radio_stp" value="stp">
                <label class="form-check-label" for="radio_stp">STP</label>
            </div>
        </div>

        {{-- Campo dinamico: Codice Fiscale o STP --}}
        <div class="mb-3 d-none" id="codice_container">
            <label for="codice_fiscale" class="form-label" id="codice_label" style="display: block;">
                Codice <span class="text-danger">*</span>
            </label>
            <input type="text" id="codice_fiscale" name="codice_fiscale"
                class="form-control form-control-sm"
                placeholder="Inserisci il Codice"
                value="{{ old('codice_fiscale', $dataView['codice_fiscale'] ?? '') }}">
        </div>

    {{-- Campo: Password --}}
    <div class="mb-3 row align-items-center">
      <label for="password" class="col-sm-3 col-form-label">Password</label>
      <div class="col-sm-9">
        <input type="password" id="password" name="password"
          class="form-control form-control-sm @error('password') is-invalid @enderror"
          placeholder="Inserisci una nuova password">
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    {{-- Campo: Conferma Password --}}
    <div class="mb-3 row align-items-center">
      <label for="password_confirmation" class="col-sm-3 col-form-label">Conferma Password</label>
      <div class="col-sm-9">
        <input type="password" id="password_confirmation" name="password_confirmation"
          class="form-control form-control-sm"
          placeholder="Conferma la password">
      </div>
    </div>

    {{-- Campo: Cellulare --}}
    <div class="mb-3 row align-items-center">
      <label for="cellulare" class="col-sm-3 col-form-label">Cellulare</label>
      <div class="col-sm-9">
        <input type="text" id="cellulare" name="cellulare"
          class="form-control form-control-sm @error('cellulare') is-invalid @enderror"
          value="{{ old('cellulare', $dataView['user']->cellulare ?? '') }}">
        @error('cellulare')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    {{-- Campo: Data di nascita --}}
    <div class="mb-3 row align-items-center">
      <label for="data_nascita" class="col-sm-3 col-form-label">Data di nascita</label>
      <div class="col-sm-9">
        <input type="date" id="data_nascita" name="data_nascita"
          class="form-control form-control-sm @error('data_nascita') is-invalid @enderror"
          value="{{ old('data_nascita', $dataView['user']->data_nascita ?? '') }}">
        @error('data_nascita')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

  

    {{-- Campo: Nazionalità --}}
    <div class="mb-3 row align-items-center">
      <label for="nazionalita" class="col-sm-3 col-form-label">Nazionalità</label>
      <div class="col-sm-9">
        <select id="nazionalita" name="nazionalita"
          class="form-select form-select-sm @error('nazionalita') is-invalid @enderror">
          <option value="">Seleziona nazionalità</option>
          @foreach ($dataView['nazioni'] as $nazione)
            <option value="{{ $nazione->codice_nazione }}"
              {{ old('nazionalita', $dataView['user']->nazionalita ?? '') == $nazione->codice_nazione ? 'selected' : '' }}>
              {{ $nazione->nome_nazione }}
            </option>
          @endforeach
        </select>
        @error('nazionalita')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    {{-- Campo: Provincia --}}
    <div class="mb-3 row align-items-center">
      <label for="provincia" class="col-sm-3 col-form-label">Provincia</label>
      <div class="col-sm-9">
        <select id="provincia" name="provincia"
          class="form-select form-select-sm @error('provincia') is-invalid @enderror">
          <option value="">Seleziona provincia</option>
          @foreach ($dataView['province'] ?? [] as $provincia)
            <option value="{{ $provincia->id }}"
              {{ old('provincia', $dataView['user']->provincia ?? '') == $provincia->id ? 'selected' : '' }}>
              {{ $provincia->nome }}
            </option>
          @endforeach
        </select>
        @error('provincia')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

  </div>
</div>



<div class="card bg-white border-dark border-2 shadow mb-4">
  <div class="card-header border-bottom border-dark text-center fw-bold">
    <h5 class="mb-0">Criteri di accesso al servizio</h5>
  </div>

 <div class="container px-4 py-3">
          <div class="row mb-3 align-items-center">
            <label for="titolo_studio" class="col-sm-3 col-form-label">Titolo di studio</label>
            <div class="col-sm-9">
              <select id="titolo_studio" name="titolo_studio" class="form-select form-select-sm w-100">
                <option value="">Seleziona titolo di studio</option>
                @foreach ($dataView['titoli_di_studio'] as $titolo)
                  <option value="{{ $titolo->id }}" title="{{ $titolo->descrizione }}"
                    {{ isset($dataView['user']->titolo_studio) && $dataView['user']->titolo_studio == $titolo->id ? 'selected' : '' }}>
                    {{ $titolo->descrizione }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

    <div class="row mb-3 align-items-center">
      <label for="condizione_professionale" class="col-sm-3 col-form-label">Condizione professionale</label>
      <div class="col-sm-9">
        <select id="condizione_professionale" name="condizione_professionale" class="form-select form-select-sm w-100">
          <option value="">Seleziona condizione professionale</option>
          @foreach ($dataView['condizione_professionale'] as $condizione)
            <option value="{{ $condizione->id }}" title="{{ $condizione->descrizione }}"
              {{ isset($dataView['user']->condizione_professionale) && $dataView['user']->condizione_professionale == $condizione->id ? 'selected' : '' }}>
              {{ $condizione->descrizione }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="row mb-3 align-items-center">
      <label for="ricerca_lavoro" class="col-sm-3 col-form-label">Stai cercando lavoro?</label>
      <div class="col-sm-9">
        <select id="ricerca_lavoro" name="ricerca_lavoro" class="form-select form-select-sm">
          <option value="">Seleziona una risposta</option>
          @foreach ($dataView['ricerca_lavoro'] as $opzione)
            <option value="{{ $opzione->id }}"
              {{ isset($dataView['user']->ricerca_lavoro) && $dataView['user']->ricerca_lavoro == $opzione->id ? 'selected' : '' }}>
              {{ $opzione->descrizione }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="row mb-3 align-items-center">
      <label for="categoria_vulnerabilita" class="col-sm-3 col-form-label">Categoria di vulnerabilità</label>
      <div class="col-sm-9">
        <select id="categoria_vulnerabilita" name="categoria_vulnerabilita" class="form-select form-select-sm">
          <option value="">Seleziona categoria</option>
          @foreach ($dataView['categorie_di_vulnerabilita'] as $categoria)
            <option value="{{ $categoria->id }}"
              {{ isset($dataView['user']->categoria_vulnerabilita) && $dataView['user']->categoria_vulnerabilita == $categoria->id ? 'selected' : '' }}>
              {{ $categoria->descrizione }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <fieldset class="row mb-3">
      <legend class="col-form-label col-sm-3 pt-0">Condizione di vulnerabilità socio economica</legend>
      <div class="col-sm-9 d-flex align-items-center">
          <div class="form-check me-3">
            <input class="form-check-input" type="radio" name="conferma" id="conferma_si" value="si" 
              {{ (isset($dataView['user']->conferma) && $dataView['user']->conferma == 'si') ? 'checked' : '' }}>
            <label class="form-check-label" for="conferma_si">Sì</label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="radio" name="conferma" id="conferma_no" value="no"
              {{ (isset($dataView['user']->conferma) && $dataView['user']->conferma == 'no') ? 'checked' : '' }}>
            <label class="form-check-label" for="conferma_no">No</label>
          </div>
      </div>
    </fieldset>

    <div id="blocchi_vulnerabilita" style="display: none;">
    <!-- entrambi i blocchi dentro qui -->

    <div class="row mb-3 align-items-center">
      <label for="criteri_contesto" class="col-sm-3 col-form-label">Criteri riferiti al contesto</label>
      <div class="col-sm-9">
        <select id="criteri_contesto" name="criteri_contesto" class="form-select form-select-sm">
          <option value="">Seleziona criterio riferito al contesto</option>
          @foreach ($dataView['criterio_contesto'] as $criterio)
            <option value="{{ $criterio->id }}"
              {{ isset($dataView['user']->criteri_contesto) && $dataView['user']->criteri_contesto == $criterio->id ? 'selected' : '' }}>
              {{ $criterio->descrizione }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="row mb-3 align-items-center">
      <label for="criteri_persona" class="col-sm-3 col-form-label">Criteri riferiti alla persona</label>
      <div class="col-sm-9">
        <select id="criteri_persona" name="criteri_persona" class="form-select form-select-sm">
          <option value="">Seleziona criterio riferito alla persona</option>
          @foreach ($dataView['criteri_persona'] as $criterio)
            <option value="{{ $criterio->id }}"
              {{ isset($dataView['user']->criteri_persona) && $dataView['user']->criteri_persona == $criterio->id ? 'selected' : '' }}>
              {{ $criterio->descrizione }}
            </option>
          @endforeach
        </select>
      </div>
    </div>
</div>

    <div class="col-md-12 d-none" id="allegati_minorenne">
        <label class="form-label fw-bold text-danger">Documenti obbligatori per minorenni</label>

        <div class="mb-2">
            <label for="doc3" class="form-label">Copia del primo foglio dell'ISEE *</label>
            <input type="file" id="doc3" name="copia_primo_foglio_ISEE_minorenne" class="form-control form-control-sm">
        </div>

        <div class="mb-2">
            <label for="doc4" class="form-label">Documento del genitore *</label>
            <input type="file" id="doc4" name="documento_genitore" class="form-control form-control-sm">
        </div>
    </div>

    <div class="row mb-3 d-none" id="allegato_isee_container">
        <label for="allegato_isee" class="col-sm-3 col-form-label text-danger">
            Copia del primo foglio dell’ISEE *
        </label>
        <div class="col-sm-9">
            <input type="file" class="form-control form-control-sm" id="allegato_isee" name="copia_primo_foglio_ISEE">
        </div>
    </div>

    <div class="row mb-3 d-none" id="allegato_permesso_container">
        <label for="allegato_permesso" class="col-sm-3 col-form-label text-danger">
            Permesso di soggiorno *
        </label>
        <div class="col-sm-9">
            <input type="file" class="form-control form-control-sm" id="allegato_permesso" name="permesso_soggiorno">
        </div>
    </div>

    <div class="row mb-3 align-items-center">
        <label for="allegato" class="col-sm-3 col-form-label">Documento di identità</label>
        <div class="col-sm-9">
            <input type="file" id="allegato" name="allegato_documento_identita" class="form-control form-control-sm">
        </div>
    </div>

    <div class="row mb-3 align-items-center">
        <label for="allegato" class="col-sm-3 col-form-label">Tessera sanitaria</label>
        <div class="col-sm-9">
          <input type="file" id="allegato" name="allegato_tessera_sanitaria" class="form-control form-control-sm">
        </div>
    </div>

    
    <div class="row mb-3 align-items-center">
        <label for="note" class="col-sm-3 col-form-label">Note</label>
        <div class="col-sm-9">
            <textarea id="note" name="note" rows="3" class="form-control form-control-sm"
                placeholder="Inserisci eventuali note">{{ old('note', $dataView['note'] ?? '') }}</textarea>
        </div>
    </div>
</div>

     <button type="submit" class="btn btn-primary btn-block" id="submitBtn"
        style="display:flex; align-items:center; justify-content:center;" >
        <i class="bi bi-floppy"></i>&nbsp;&nbsp;{{ __('Registrati') }}
    </button>
  </div>
</div>



</div>
    </div>
</form>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    const radioSi = document.getElementById('conferma_si');
    const radioNo = document.getElementById('conferma_no');
    const blocchiVulnerabilita = document.getElementById('blocchi_vulnerabilita');
    const selectContesto = document.getElementById('criteri_contesto');
    const selectPersona = document.getElementById('criteri_persona');
    const hiddenPersona = document.getElementById('criteri_persona_hidden');

    function toggleBlocchi() {
      if (radioSi.checked) {
        blocchiVulnerabilita.style.display = 'block';
      } else {
        blocchiVulnerabilita.style.display = 'none';
        // Quando si seleziona "No", resettiamo i campi
        selectContesto.value = '';
        selectPersona.value = '';
        hiddenPersona.value = '';
        selectPersona.disabled = false;
      }
    }

    function aggiornaSelectPersona() {
      const haValoreContesto = selectContesto.value !== '';

      if (haValoreContesto) {
        const opzioni = selectPersona.options;
        const ultimaOpzione = opzioni[opzioni.length - 1];

        selectPersona.value = ultimaOpzione.value;
        hiddenPersona.value = ultimaOpzione.value;
        selectPersona.disabled = true;
      } else {
        selectPersona.disabled = false;
        hiddenPersona.value = selectPersona.value;
      }
    }

    // Event listeners
    radioSi.addEventListener('change', toggleBlocchi);
    radioNo.addEventListener('change', toggleBlocchi);
    selectContesto.addEventListener('change', aggiornaSelectPersona);
    selectPersona.addEventListener('change', function () {
      if (!selectPersona.disabled) {
        hiddenPersona.value = selectPersona.value;
      }
    });

    // Inizializzazione al caricamento
    toggleBlocchi();
    aggiornaSelectPersona();
  });

     const radioCf = document.getElementById('radio_cf');
    const radioStp = document.getElementById('radio_stp');
    const codiceContainer = document.getElementById('codice_container');
    const codiceLabel = document.getElementById('codice_label');
    const codiceInput = document.getElementById('codice_fiscale');

    function aggiornaCampoCodice() {
        if (radioCf.checked) {
            codiceContainer.classList.remove('d-none');
            codiceLabel.textContent = 'Codice Fiscale *';
            codiceInput.placeholder = 'Inserisci il Codice Fiscale';
            codiceInput.required = true;
        } else if (radioStp.checked) {
            codiceContainer.classList.remove('d-none');
            codiceLabel.textContent = 'Codice STP *';
            codiceInput.placeholder = 'Inserisci il Codice STP';
            codiceInput.required = true;
        } else {
            codiceContainer.classList.add('d-none');
            codiceInput.required = false;
        }
    }

    radioCf.addEventListener('change', aggiornaCampoCodice);
    radioStp.addEventListener('change', aggiornaCampoCodice);

    // Controllo iniziale (in caso di dati precompilati)
    window.addEventList



document.addEventListener('DOMContentLoaded', function () {
    const dataNascitaInput = document.getElementById('data_nascita');
    const allegatiContainer = document.getElementById('allegati_minorenne');

    function calcolaEta(dataString) {
        const oggi = new Date();
        const nascita = new Date(dataString);
        let eta = oggi.getFullYear() - nascita.getFullYear();
        const m = oggi.getMonth() - nascita.getMonth();
        if (m < 0 || (m === 0 && oggi.getDate() < nascita.getDate())) {
            eta--;
        }
        return eta;
    }

    dataNascitaInput.addEventListener('change', function () {
        const dataVal = this.value;
        if (dataVal) {
            const eta = calcolaEta(dataVal);
            if (eta < 18) {
                allegatiContainer.classList.remove('d-none');
            } else {
                allegatiContainer.classList.add('d-none');
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const selectCriteri = document.getElementById('criteri_persona');
    const allegatoIseeContainer = document.getElementById('allegato_isee_container');

    function toggleAllegatoIsee() {
        const selectedValue = selectCriteri.value;
        if (selectedValue === '1') {
            allegatoIseeContainer.classList.remove('d-none');
        } else {
            allegatoIseeContainer.classList.add('d-none');
        }
    }

    // Al cambio del select
    selectCriteri.addEventListener('change', toggleAllegatoIsee);

    // Per inizializzare in caso sia pre-selezionato
    toggleAllegatoIsee();
});

document.addEventListener('DOMContentLoaded', function () {
    const selectCriteri = document.getElementById('criteri_persona');
    const allegatoIseeContainer = document.getElementById('allegato_isee_container');

    const selectVulnerabilita = document.getElementById('categoria_vulnerabilita');
    const allegatoPermessoContainer = document.getElementById('allegato_permesso_container');

    function toggleAllegatoIsee() {
        if (selectCriteri.value === '1') {
            allegatoIseeContainer.classList.remove('d-none');
        } else {
            allegatoIseeContainer.classList.add('d-none');
        }
    }

    function toggleAllegatoPermesso() {
        if (selectVulnerabilita.value === '3') {
            allegatoPermessoContainer.classList.remove('d-none');
        } else {
            allegatoPermessoContainer.classList.add('d-none');
        }
    }

    // Event listeners
    selectCriteri?.addEventListener('change', toggleAllegatoIsee);
    selectVulnerabilita?.addEventListener('change', toggleAllegatoPermesso);

    // Init on page load
    toggleAllegatoIsee();
    toggleAllegatoPermesso();
    });
</script>



@endsection
