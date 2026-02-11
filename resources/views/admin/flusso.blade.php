@extends('bootstrap-italia::page')

@section('content')

<div class="container my-5">

<div class="card-header border-dark mb-3">
    <legend style="text-align:center">{{ __('Esporta il flusso') }}</legend>
</div>

    <!-- Card per selezione date -->
    <div class="card shadow-sm border-light">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.estraiDatiFlusso') }}">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label for="data_da" class="form-label">Data da</label>
                        <input type="date" class="form-control" id="data_da" name="data_da" value="{{ request('data_da') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="data_a" class="form-label">Data a</label>
                        <input type="date" class="form-control" id="data_a" name="data_a" value="{{ request('data_a') }}" required>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <svg class="icon icon-sm me-2"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-search"></use></svg>
                        Estrai Dati
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabella risultati -->
    @if(isset($dataView['utentiDaEsportare']) && count($dataView['utentiDaEsportare']) > 0)
        <div class="mt-5">
            <h2 class="text-success fw-bold mb-3">
                <svg class="icon icon-sm me-2"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-user"></use></svg>
                Utenti da esportare
            </h2>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>Cognome</th>
                            <th>Cellulare</th>
                            <th>Email</th>
                            <th>Codice Fiscale</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataView['utentiDaEsportare'] as $utente)
                            <tr>
                                <td>{{ $utente->nome }}</td>
                                <td>{{ $utente->cognome }}</td>
                                <td>{{ $utente->cellulare }}</td>
                                <td>{{ $utente->email }}</td>
                                <td>{{ $utente->codice_fiscale }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Bottone esporta -->
            <form method="GET" action="{{ route('admin.esportaFlusso') }}" class="mt-3">
                <input type="hidden" name="data_da" value="{{ request('data_da') }}">
                <input type="hidden" name="data_a" value="{{ request('data_a') }}">
                <button type="submit" class="btn btn-success btn-lg">
                    <svg class="icon icon-sm me-2"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-download"></use></svg>
                    Esporta Flusso
                </button>
            </form>
        </div>
    @endif

</div>

{{-- Paginazione --}}
<div class="d-flex justify-content-center mt-4">
    @if(isset($dataView['utentiDaEsportare']))
        {{ $dataView['utentiDaEsportare']->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
    @endif
</div>


@endsection
