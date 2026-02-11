@extends('bootstrap-italia::page')

@section('content')

<div class="container mt-4">

    {{-- Intestazione utente --}}
    <div class="card border-dark shadow-sm mb-4">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">
                {{ __('Benvenuto, ' . Auth::user()->nome . ' ' . Auth::user()->cognome) }}
            </h4>
        </div>
    </div>

    {{-- Presentazione piattaforma --}}
    <div class="alert alert-light border-start border-primary border-4 shadow-sm mb-4" role="region">
        <h5 class="fw-semibold">Cos'è Odontoiatria?</h5>
        <p class="mb-1">
            <strong>Portaile odontoiatria</strong> è la piattaforma digitale dedicata alla gestione delle prenotazioni sanitarie.
            Nata per i servizi di <strong>odontoiatria</strong>, oggi supporta anche altre prestazioni mediche e specialistiche.
        </p>
        <p class="mb-0">
            Grazie a un'interfaccia semplice e accessibile, puoi prenotare in pochi clic le tue visite presso ambulatori convenzionati.
        </p>
    </div>

    {{-- Pulsante prenotazione --}}
    @php
        $isDisabled = Auth::user()->stato == 0;
    @endphp

    <div class="text-center mt-4">
        <a href="{{ $isDisabled ? '#' : route('paziente.dashboard') }}"
           class="btn btn-lg {{ $isDisabled ? 'btn-secondary disabled' : 'btn-success' }}"
           {{ $isDisabled ? 'aria-disabled=true tabindex=-1' : '' }}>
            Prenota una prestazione
        </a>

        {{-- Messaggi di stato --}}
        @if (Auth::user()->stato == 0)
            <div class="alert alert-warning mt-3" role="alert">
                Il tuo account è attualmente disabilitato. Contatta l'amministrazione per procedere.
            </div>
        @endif
    </div>

</div>

@endsection
