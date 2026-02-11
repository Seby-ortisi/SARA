@extends('bootstrap-italia::page')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="text-align:center">
                    <legend>{{ __('Modifica i tuoi dati') }}</legend>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-warning">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    {{ $error }}
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post" action="{{ route('aggiornaProfiloUtente')}}">
                        @csrf
                        <input type="hidden" name="patientID" value="{{$dataView['patient']->id}}">
                        <input type="hidden" name="modalitaAccesso" value="{{$dataView['modalitaAccesso'] }}">

                        <div class="form-group">
                            <label class="active" for="input-text-lg">Cognome</label>
                            <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror"
                                name="surname" value="{{$dataView['patient']->surname}}" required autocomplete="surname"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label class="active" for="input-text-lg">Nome</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{$dataView['patient']->name}}" required autocomplete="name"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label class="active" for="input-text-lg">Codice fiscale</label>
                            <input id="tax_id" type="text" class="form-control @error('tax_id') is-invalid @enderror"
                                name="tax_id" value="{{$dataView['patient']->tax_id}}" required autocomplete="tax_id"
                                readonly>
                        </div>

                        @if($dataView['patient']->id == Auth::user()->id)
                            <div class="form-group">
                                <label class="active" for="input-text-lg">Email</label>

                                @if($dataView['modalitaAccesso'] == 2 || $dataView['patient']->vaccination_center_id == null)
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{$dataView['patient']->email}}" autocomplete="email" required>
                                @else
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{$dataView['patient']->email}}" autocomplete="email" autofocus
                                        readonly>
                                @endif
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="active" for="input-text-lg">Cellulare</label>
                                @if($dataView['modalitaAccesso'] == 2 || $dataView['patient']->vaccination_center_id == null)
                                    <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" value="{{$dataView['patient']->phone}}" autocomplete="phone" required>
                                @else
                                    <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" value="{{$dataView['patient']->phone}}" autocomplete="phone" autofocus
                                        readonly>
                                @endif
                            </div>
                        @endif
                        <br>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-person-check"></i>&nbsp;&nbsp;Modifica</button>
                            
                            @if(Auth::user()->email != null && Auth::user()->phone != null)
                                <form method="get" action="{{ route('home') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-arrow-return-left"></i>&nbsp;&nbsp;Torna indietro
                                    </button>
                                </form>
                            @endif
                        </div>
                    </form>
                </div>

                @if($dataView['patient']->id != Auth::user()->id)
                    <div class="col text-right">
                        <form method="post" action="{{ route('cancellaProfiloUtente') }}">
                            @csrf
                            <input type="hidden" name="patientID" value="{{ $dataView['patient']->id }}">
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Sarà eliminato l\'utente e l\'eventuale prenotazione futura. Sei sicuro di voler eliminare questo utente?');"><i
                                    class="bi bi-person-dash-fill"></i>&nbsp;&nbsp;Cancella</button>
                        </form>
                    </div>
                @endif

            </div>
        </div>
        <br>
    </div>
</div>




<style>
    .card {
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(0, 0, 0, .125);
        border-radius: .4rem;
        box-shadow: 0 20px 27px 0 rgb(0 0 0 / 5%);
    }
</style>
@endsection