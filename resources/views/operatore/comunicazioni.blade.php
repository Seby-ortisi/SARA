@extends('bootstrap-italia::page')

@section('content')
<div class="container my-4 my-lg-5">

    <!-- Titolo pagina -->
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <h1 class="mb-3">
                    <i class="bi bi-megaphone-fill me-2"></i>
                    Comunicazioni
                </h1>
                <p class="lead">Tutte le comunicazioni e gli avvisi importanti</p>
            </div>
        </div>
    </div>

    @php
        $comunicazioni = [
            [
                'titolo' => 'Versione 1.0.2',
                'testo' => <<<TEXT
Nella scheda "Criteri di accesso al servizio", il menu
"Sei alla ricerca di lavoro da (durata)"
si attiva solo quando il menu "Condizione professionale attuale"
assume uno dei seguenti valori:

- In cerca di prima occupazione
- Disoccupato alla ricerca di prima occupazione

E' ora possibile gestire le prenotazioni di piu centri aperti
simultaneamente (previa configurazione delle agende).
TEXT,
                'data' => '02/02/2026'
            ],
            [
                'titolo' => 'Versione 1.0.1',
                'testo' => <<<TEXT
- Diminuita la durata degli slot da 15 a 10 minuti
- Resa facoltativa la mail del tutore
- Migliorata la leggibilita' invertendo nome e cognome
  nella sezione "Utenti da validare"
- Inserito il campo "Altro" nell 'area referenti del minore
TEXT,
                'data' => '26/01/2026'
            ],
        ];
    @endphp

    <!-- Lista comunicazioni -->
    <div class="row g-3 g-lg-4">
        @forelse($comunicazioni as $com)
            <div class="col-12 col-lg-6">
                <div class="card-wrapper">
                    <div class="card card-bg card-big no-after">
                        <div class="card-body">

                            <div class="top-icon">
                                <i class="bi bi-file-earmark-text text-primary"></i>
                            </div>

                            <h5 class="card-title h5">{{ $com['titolo'] }}</h5>

                            <p class="card-text">
                                {!! nl2br(e($com['testo'])) !!}
                            </p>

                            <div class="d-flex align-items-center mt-4">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="bi bi-calendar3 me-2"></i>
                                    {{ $com['data'] }}
                                </small>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="callout">
                    <div class="callout-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Nessuna comunicazione disponibile
                    </div>
                    <p>Al momento non ci sono comunicazioni da visualizzare.</p>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection
