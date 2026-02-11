@extends('bootstrap-italia::page')

@section('content')
    <div class="container py-4">
        <div class="card-header border-dark mb-2">
            <legend style="text-align:center">{{ __('Le tue prenotazioni') }}
            </legend>
        </div>

        <br>

        @if($dataView['prenotazioni']->isEmpty())
            <div class="alert alert-info">
                Non hai prenotazioni.
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($dataView['prenotazioni'] as $prenotazione)
                    <div class="col">
                        <div class="card shadow-sm h-100 rounded-4 border border-light">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <h5 class="card-title text-primary fw-bold">
                                    {{ "Ambulatorio di " . ($prenotazione->centro_vaccinale ?? 'Centro vaccinale non specificato') }}
                                </h5>

                                <div class="mb-3">
                                    <p class="mb-1 fs-6"><strong>Nome paziente:</strong> {{ $prenotazione->nome_paziente }}
                                        {{ $prenotazione->cognome_paziente }}</p>
                                    <p class="mb-1 fs-6"><strong>Data:</strong>
                                        {{ \Carbon\Carbon::parse($prenotazione->data_prenotazione)->format('d/m/Y') }}</p>
                                    <p class="mb-1 fs-6"><strong>Orario:</strong>
                                        {{ \Carbon\Carbon::parse($prenotazione->ora_prenotazione)->format('H:i') }}</p>
                                    <p class="mb-1 fs-6"><strong>Branca:</strong>
                                        {{ $prenotazione->branca_prestazione }}</p>
                                </div>

                                <form action="{{ route('prenotazione.elimina', $prenotazione->id) }}" method="POST"
                                    onsubmit="return confirm('Sei sicuro di voler cancellare questa prenotazione?');">
                                    @csrf
                                    <input type="hidden" name="id_prenotazione"
                                        value="{{ $prenotazione->id_prenotazione }}">
                                    <button type="submit" class="btn btn-danger w-100">Cancella prenotazione</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection