@extends('bootstrap-italia::page')

@section('content')

<div class="container my-4">

    {{-- MESSAGGI DI SUCCESSO / ERRORE --}}
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

    {{-- SEZIONE 1: Modifica Prestazioni per Branca --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="bi bi-pencil-square me-2"></i> Modifica Prestazioni per Branca
            </h4>
        </div>
        <div class="card-body">

            {{-- Filtro per Branca --}}
            <form method="GET" action="{{ route('operatore.modificaBranche') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-6">
                    <label for="branca_id" class="form-label fw-semibold">Seleziona Branca</label>
                    <select name="branca_id" id="branca_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Seleziona una branca --</option>
                        @foreach($dataView['brancheSelect'] as $branca)
                            <option value="{{ $branca->id }}" {{ ($dataView['selectedBrancaId'] == $branca->id) ? 'selected' : '' }}>
                                {{ $branca->nome_branca }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            {{-- Tabella prestazioni --}}
            @if($dataView['branche']->isNotEmpty())
                <form method="POST" action="{{ route('operatore.modificaParametriBranche') }}">
                    @csrf
                    <input type="hidden" name="branca_id" value="{{ $dataView['selectedBrancaId'] }}">

                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nome Branca</th>
                                    <th>Denominazione Nomenclatore</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataView['branche'] as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <input type="text" readonly class="form-control-plaintext text-center fw-bold" value="{{ $row->nome_branca }}">
                                    </td>
                                    <td>
                                        <input type="hidden" name="prestazioni[{{ $index }}][id]" value="{{ $row->id_nomenclatore }}">
                                        <input type="text" name="prestazioni[{{ $index }}][denominazione]" class="form-control form-control-sm" value="{{ $row->denominazione_nomenclatore }}">
                                    </td>
                                    <td>
                                        <a href="{{ route('operatore.rimuoviPrestazione', ['id' => $row->id_nomenclatore]) }}" class="btn btn-outline-danger btn-sm">
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
                        <a href="{{ route('operatore.eliminaSingolaBranca', ['id' => $dataView['selectedBrancaId']]) }}" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Elimina Branca
                        </a>
                    </div>
                </form>
            @elseif($dataView['selectedBrancaId'])
                <div class="alert alert-warning mt-4">Nessuna prestazione trovata per questa branca.</div>

                   <a href="{{ route('operatore.eliminaSingolaBranca', ['id' => $dataView['selectedBrancaId']]) }}" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Elimina Branca
                   </a>
            @endif
        </div>
    </div>

    {{-- SEZIONE 2: Aggiungi Prestazione --}}
    @if($dataView['selectedBrancaId'])
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i> Aggiungi Prestazione a Branca "{{ $dataView['brancheSelect']->firstWhere('id', $dataView['selectedBrancaId'])->nome_branca }}"
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('operatore.aggiungiPrestazione') }}">
                    @csrf
                    <input type="hidden" name="branca_id" value="{{ $dataView['selectedBrancaId'] }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="codice_prestazione_esistente" class="form-label">Codice Prestazione</label>
                            <input type="text" name="codice_prestazione" id="codice_prestazione_esistente" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="denominazione_prestazione_esistente" class="form-label">Denominazione Prestazione</label>
                            <input type="text" name="denominazione_prestazione" id="denominazione_prestazione_esistente" class="form-control" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus"></i> Aggiungi Prestazione
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- SEZIONE 3: Nuova Branca --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-success text-white">
        <h4 class="mb-0">
            <input type="checkbox" id="toggleNuovaBranca" class="form-check-input me-2">
            Aggiungi Nuova Branca
        </h4>
    </div>

    <div class="card-body" id="nuovaBrancaForm" style="display: none;">
        <form method="POST" action="{{ route('operatore.aggiungiBrancaPrestazione') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nuova_branca" class="form-label">Nome Branca</label>
                    <input type="text" name="nome_branca" id="nuova_branca" class="form-control" required>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Aggiungi Branca
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


</div>

{{-- Script per mostrare/nascondere form nuova branca --}}
<script>
    document.getElementById('toggleNuovaBranca').addEventListener('change', function() {
        document.getElementById('nuovaBrancaForm').style.display = this.checked ? 'block' : 'none';
    });
</script>

     <style>
        /* CSS per forzare il testo delle label ad essere orizzontale */
        .card-body label.form-label {
            writing-mode: initial !important;
            text-orientation: initial !important;
            white-space: nowrap !important;
            display: block !important;
            width: auto !important;
        }
    </style>

@endsection
