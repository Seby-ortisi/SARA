@extends('bootstrap-italia::page')

@section('content')

<div class="container my-4">

    {{-- MESSAGGI SUCCESSO / ERRORE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span class="fw-bold">Successo!</span> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span class="fw-bold">Errore!</span> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        </div>
    @endif

    {{-- SEZIONE 1: Filtra per Tipologia Farmaco --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="bi bi-funnel me-2"></i> Filtra Farmaci per Tipologia
            </h4>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('operatore.modificaFarmaci') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-6">
                    <label for="tipologia_id" class="form-label fw-semibold">Seleziona Tipologia</label>
                    <select name="tipologia_id" id="tipologia_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Seleziona una tipologia --</option>
                        @foreach($dataView['tipologieFarmaco'] as $tipologia)
                            <option value="{{ $tipologia->id }}" {{ ($dataView['selectedTipologiaId'] == $tipologia->id) ? 'selected' : '' }}>
                                {{ $tipologia->descrizione_tipologia_farmaco }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            {{-- Tabella Farmaci --}}
            @if($dataView['farmaci']->isNotEmpty())
                <form method="POST" action="{{ route('operatore.modificaParametriFarmaci') }}">
                    @csrf
                    <input type="hidden" name="tipologia_id" value="{{ $dataView['selectedTipologiaId'] }}">

                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Descrizione</th>
                                    <th>Codice Farmaco</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataView['farmaci'] as $index => $farmaco)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <input type="hidden" name="farmaci[{{ $index }}][id]" value="{{ $farmaco->id }}">
                                            <input type="text" name="farmaci[{{ $index }}][descrizione]" class="form-control form-control-sm" value="{{ $farmaco->descrizione }}" required>
                                        </td>
                                        <td>
                                            <input type="text" name="farmaci[{{ $index }}][codice_farmaco]" class="form-control form-control-sm" value="{{ $farmaco->codice_farmaco }}" required>
                                        </td>
                                        <td>
                                            <a href="{{ route('operatore.rimuoviFarmaco', ['id' => $farmaco->id]) }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('Sei sicuro di voler eliminare questo farmaco?')">
                                                <i class="bi bi-trash"></i> Elimina
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salva Modifiche
                        </button>
                        @if($dataView['selectedTipologiaId'])
                            <a href="{{ route('operatore.eliminaSingolaTipologiaFarmaco', ['id' => $dataView['selectedTipologiaId']]) }}" class="btn btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questa tipologia?')">
                                <i class="bi bi-trash"></i> Elimina Tipologia
                            </a>
                        @endif
                    </div>
                </form>
            @elseif($dataView['selectedTipologiaId'])
                <div class="alert alert-warning mt-4">Nessun farmaco trovato per questa tipologia.</div>

                <a href="{{ route('operatore.eliminaSingolaTipologiaFarmaco', ['id' => $dataView['selectedTipologiaId']]) }}" class="btn btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questa tipologia?')">
                    <i class="bi bi-trash"></i> Elimina Tipologia
                </a>
            @endif
        </div>
    </div>

    {{-- SEZIONE 2: Aggiungi Farmaco --}}
    @if($dataView['selectedTipologiaId'])
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i> Aggiungi Farmaco a Tipologia "{{ $dataView['tipologieFarmaco']->firstWhere('id', $dataView['selectedTipologiaId'])->descrizione_tipologia_farmaco }}"
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('operatore.aggiungiFarmaco') }}">
                    @csrf
                    <input type="hidden" name="tipologia_id" value="{{ $dataView['selectedTipologiaId'] }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="descrizione_farmaco" class="form-label">Descrizione Farmaco</label>
                            <input type="text" name="descrizione_farmaco" id="descrizione_farmaco" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="codice_farmaco" class="form-label">Codice Farmaco</label>
                            <input type="text" name="codice_farmaco" id="codice_farmaco" class="form-control" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus"></i> Aggiungi Farmaco
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- SEZIONE 3: Aggiungi Nuova Tipologia Farmaco --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">
                <input type="checkbox" id="toggleNuovaTipologia" class="form-check-input me-2">
                Aggiungi Nuova Tipologia Farmaco
            </h4>
        </div>
        <div class="card-body" id="nuovaTipologiaForm" style="display: none;">
            <form method="POST" action="{{ route('operatore.aggiungiTipologiaFarmaco') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="descrizione_tipologia" class="form-label">Descrizione Tipologia</label>
                        <input type="text" name="descrizione_tipologia" id="descrizione_tipologia" class="form-control" required>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Aggiungi Tipologia
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- Script per mostrare/nascondere form nuova tipologia --}}
<script>
    document.getElementById('toggleNuovaTipologia').addEventListener('change', function() {
        document.getElementById('nuovaTipologiaForm').style.display = this.checked ? 'block' : 'none';
    });
</script>

<style>
    /* Forza label orizzontali */
    .card-body label.form-label {
        writing-mode: initial !important;
        text-orientation: initial !important;
        white-space: nowrap !important;
        display: block !important;
        width: auto !important;
    }
</style>

@endsection
