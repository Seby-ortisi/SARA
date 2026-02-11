@extends('bootstrap-italia::page')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span class="fw-bold"></span> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span class="fw-bold">Errore!</span> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="container container-xxl py-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-primary text-white rounded-top-4">
                <h3 class="mb-0">
                    <i class="bi bi-person-lines-fill me-2"></i> Ricerca Storico Pazienti
                </h3>
            </div>
            <div class="card-body bg-white">
                <form method="GET" action="{{ route('operatore.cercaPaziente') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-9">
                            <label for="query" class="form-label">Cerca paziente</label>
                            <input type="text" class="form-control form-control-lg" id="query" name="query"
                                placeholder="Inserisci nome, cognome o codice fiscale" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-lg btn-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-search me-2"></i> Cerca
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(isset($dataView['storico']) && $dataView['storico']->count() > 0)
        <div class="container container-xxl py-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h4 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i> Storico prenotazioni di
                        <strong>{{ $dataView['paziente']->nome }} {{ $dataView['paziente']->cognome }}</strong>
                    </h4>
                </div>
                <div class="card-body bg-white">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Cognome</th>
                                    <th>Data</th>
                                    <th>Branca</th>
                                    <th>Prestazione</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataView['storico'] as $prenotazione)
                                    <tr>
                                        <td>{{ $prenotazione->nome }}</td>
                                        <td>{{ $prenotazione->cognome }}</td>
                                        <td>{{ \Carbon\Carbon::parse($prenotazione->data_prenotazione)->format('d-m-Y') }}</td>
                                        <td>{{ $prenotazione->nome_branca }}</td>
                                        <td>{{ $prenotazione->nome_prestazione }}</td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

  
@if(isset($dataView['storico']) && $dataView['storico']->isEmpty())
    <div class="container container-xxl py-4">
        <div class="alert alert-warning rounded-4">
            <i class="bi bi-info-circle me-2"></i>
            Il paziente <strong>{{ $dataView['paziente']->nome }} {{ $dataView['paziente']->cognome }}</strong> non ha prenotazioni registrate.
        </div>
    </div>
@endif



@endsection
