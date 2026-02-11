@extends('bootstrap-italia::page')

@section('content')

<div class="container container-xxl py-5">

    <div class="card shadow-sm rounded">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Modifica Agenda</h3>
            <small>Gestisci gli orari e gli ambulatori della tua agenda.</small>
        </div>

        <div class="card-body bg-white">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th style="width: 130px;">Azioni</th>
                            <th>Ambulatorio</th>
                            <th>Giorno</th>
                            <th>Ora Inizio</th>
                            <th>Ora Fine</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataView['agenda'] as $orario)
                        <tr>
                            <td>
                                <div class="d-grid gap-2">
                                    <form id="form-modifica-{{ $orario->id }}" action="{{ route('operatore.modificaParametriAgenda') }}" method="POST" class="mb-1">
                                        @csrf
                                        <input type="hidden" name="agenda_id" value="{{ $orario->id }}">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">Salva</button>
                                    </form>

                                    <form action="{{ route('operatore.eliminaGiornoAgenda') }}" method="POST" onsubmit="return confirm('Eliminare {{ $orario->giorno }} alle {{ $orario->ora_inizio }}?')">
                                        @csrf
                                        <input type="hidden" name="agenda_id" value="{{ $orario->id }}">
                                        <button type="submit" class="btn btn-danger btn-sm w-100">Elimina</button>
                                    </form>
                                </div>
                            </td>

                            <td>
                                <select name="id_ambulatorio" form="form-modifica-{{ $orario->id }}" class="form-select form-select-sm" required>
                                    @foreach ($dataView['ambulatori'] as $ambulatorio)
                                        <option value="{{ $ambulatorio->id }}" {{ $ambulatorio->id == $orario->id_ambulatorio ? 'selected' : '' }}>
                                            {{ $ambulatorio->descrizione }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="text" name="giorno" value="{{ $orario->giorno }}" form="form-modifica-{{ $orario->id }}" class="form-control form-control-sm" required>
                            </td>
                            <td>
                                <input type="time" name="ora_inizio" value="{{ $orario->ora_inizio }}" form="form-modifica-{{ $orario->id }}" class="form-control form-control-sm" required>
                            </td>
                            <td>
                                <input type="time" name="ora_fine" value="{{ $orario->ora_fine }}" form="form-modifica-{{ $orario->id }}" class="form-control form-control-sm" required>
                            </td>
                           
                        </tr>
                        @endforeach
                        
                        <tr class="table-secondary text-center text-uppercase fw-semibold">
                            <td colspan="6">Aggiungi Nuovo Giorno</td>
                        </tr>

                        <tr>
                            <form id="nuovo-giorno-form" action="{{ route('operatore.aggiungiGiornoAgenda') }}" method="POST">
                                @csrf
                                <td>
                                    <button type="submit" class="btn btn-success btn-sm w-100">Aggiungi</button>
                                </td>
                                <td>
                                    <select name="id_ambulatorio" class="form-select form-select-sm" required>
                                        <option value="" disabled selected>Seleziona ambulatorio...</option>
                                        @foreach ($dataView['ambulatori'] as $ambulatorio)
                                            <option value="{{ $ambulatorio->id }}">{{ $ambulatorio->descrizione }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="giorno" class="form-control form-control-sm" placeholder="es. Lunedì" required></td>
                                <td><input type="time" name="ora_inizio" class="form-control form-control-sm" required></td>
                                <td><input type="time" name="ora_fine" class="form-control form-control-sm" required></td>
                      
                            </form>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
