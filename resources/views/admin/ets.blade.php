@extends('bootstrap-italia::page')

@section('content')

<div class="container my-5">

    <div class="card-header border-dark mb-3">
    <legend style="text-align:center">{{ __('Esporta dati ETS') }}</legend>
</div>

    {{-- Form di selezione date --}}
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.estraiDatiEts') }}">
                <div class="row g-4">
                    <div class="col-md-5">
                        <label for="data_da" class="form-label fw-semibold">Data da</label>
                        <input 
                            type="date" 
                            class="form-control" 
                            id="data_da" 
                            name="data_da" 
                            value="{{ old('data_da', request('data_da')) }}" 
                            required
                            aria-describedby="helpDataDa"
                        >
                        <small id="helpDataDa" class="text-muted">Seleziona la data iniziale</small>
                    </div>

                    <div class="col-md-5">
                        <label for="data_a" class="form-label fw-semibold">Data a</label>
                        <input 
                            type="date" 
                            class="form-control" 
                            id="data_a" 
                            name="data_a" 
                            value="{{ old('data_a', request('data_a')) }}" 
                            required
                            aria-describedby="helpDataA"
                        >
                        <small id="helpDataA" class="text-muted">Seleziona la data finale</small>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="it-download me-2"></i> Estrai dati
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Se ci sono dati da mostrare --}}
    @if(isset($dataView['conteggiPerEts']) && count($dataView['conteggiPerEts']) > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="h5 mb-0">Risultati estrazione ETS</h2>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover m-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Nome ETS</th>
                            <th scope="col" class="text-end">Numero pazienti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataView['conteggiPerEts'] as $ets)
                            <tr>
                                <td>{{ $ets->descrizione_ets }}</td>
                                <td class="text-end fw-semibold">{{ $ets->totale_prenotazioni }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @elseif(request()->has(['data_da', 'data_a']))
        <div class="alert alert-warning" role="alert">
            Nessun dato trovato per l’intervallo selezionato.
        </div>
    @endif

</div>

@endsection
