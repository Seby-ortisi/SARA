@extends('bootstrap-italia::page')

@section('content')

<div class="it-header-slim-wrapper">
    <div class="container">
        <div class="it-header-slim-wrapper-content">
            <span class="text-white fw-bold">
                {{ __('Benvenuto, ' . Auth::user()->nome . " " . Auth::user()->cognome) }}
            </span>
        </div>
    </div>
</div>

<div class="container mt-4">

    <div class="alert alert-info text-center">
        In questa pagina puoi gestire le <strong>tue prenotazioni</strong> in modo semplice e veloce.
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        </div>
    @endif


<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <strong>Filtra disponibilità</strong>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('agenda.filtra') }}">
            <div class="row g-3 align-items-end">

                {{-- Seleziona una data --}}
                <div class="col-md-4">
                    <label for="data" class="form-label">Seleziona una data</label>
                    <input type="date" id="data" name="data" class="form-control"
                        value="{{ request('data', \Carbon\Carbon::now()->toDateString()) }}">
                </div>

                {{-- Seleziona una branca --}}
                <div class="col-md-4">
                    <label for="branca_id" class="form-label">Seleziona una branca</label>
                    <select id="branca_id" name="branca_id" class="form-select">
                        <option value="">-- Scegli una branca --</option>
                        @foreach ($dataView['branche'] as $branca)
                            <option value="{{ $branca->id }}" {{ request('branca_id') == $branca->id ? 'selected' : '' }}>
                                {{ $branca->nome_branca }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Bottone cerca --}}
                <div class="col-md-4 d-grid">
                    <button type="submit" class="btn btn-primary">
                        Cerca disponibilità
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>


    {{-- Mostra agende e slot disponibili --}}
    @foreach ($dataView['agenda'] as $item)
        <div class="card border-primary shadow-sm mb-4">
            <div class="card-header bg-light d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Ambulatorio: <strong>{{ $item['agenda']->descrizione_ambulatorio }}</strong></h5>
                    <small class="text-muted">Data: <strong>{{ $dataView['data'] }}</strong></small>
                </div>
                <div class="text-end mt-2 mt-md-0">
                    <span class="badge bg-info text-dark">Branca: {{ $dataView['brancaSelezionata']->nome_branca }}</span>
                </div>
            </div>
            <div class="card-body">
                <p class="mb-3"><i class="bi bi-clock"></i> Orario: <strong>{{ $item['agenda']->ora_inizio }} – {{ $item['agenda']->ora_fine }}</strong></p>

                <div class="d-flex flex-wrap gap-2">
                    @foreach ($item['slots'] as $slot)
                        @php
                            $isBooked = in_array($slot, $item['bookedSlots']);
                        @endphp
                        <form method="POST" action="{{ route('agenda.prenotazione') }}">
                            @csrf
                            <input type="hidden" name="orario" value="{{ $slot }}">
                            <input type="hidden" name="data" value="{{ $dataView['data'] }}">
                            <input type="hidden" name="idAmbulatorio" value="{{ $item['agenda']->id_ambulatorio }}">
                            <input type="hidden" name="branca_id" value="{{ 1.1 }}">
                            <button type="submit" class="btn {{ $isBooked ? 'btn-secondary' : 'btn-outline-primary' }}" {{ $isBooked ? 'disabled' : '' }}>
                                {{ $slot }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>

@endsection
