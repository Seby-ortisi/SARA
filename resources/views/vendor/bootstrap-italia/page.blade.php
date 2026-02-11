@extends('bootstrap-italia::master')

@section('bootstrapitalia_css')
@stack('css')
@yield('css')
@stop

@section('body')
<!-- Header -->
<div class="it-header-wrapper">
    <div class="it-header-slim-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="it-header-slim-wrapper-content">
                        <a class="d-none d-lg-block navbar-brand" href="{{ config('bootstrap-italia.owner.link') }}">
                            {!! config('bootstrap-italia.owner.description') !!}
                        </a>

<a class="navbar-text d-flex align-items-center" href="{{ route('notifiche.index') }}">
    <i class="bi bi-bell-fill me-1"></i> Comunicazioni
</a>
                        <div class="nav-mobile">
                            <nav>
                                <a class="it-opener d-lg-none" data-toggle="collapse" href="#menu-principale"
                                    role="button" aria-expanded="false" aria-controls="menu-principale">
                                    <span>{!! config('bootstrap-italia.owner.description') !!}</span>
                                </a>
                            </nav>
                            @if (!Auth::guest())
                                <div class="link-list-wrapper collapse mt-3" role="region"
                                    aria-labelledby="mainNavDropdown0">
                                    <ul class="link-list" style="color: white; text-align: right;">
                                        <li style="list-style: none;">{{ Auth::user()->nome . " " . Auth::user()->cognome . " - " . Auth::user()->ruolo->description }} </li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="header-slim-right-zone">
                            @if (config('bootstrap-italia.auth'))
                                <div class="it-access-top-wrapper">
                                    @guest
                                        @if (config('bootstrap-italia.auth.login'))
                                            <button onclick="event.preventDefault(); window.location=this.getAttribute('href')"
                                                class="btn btn-primary btn-sm"
                                                href="{{ (config('bootstrap-italia.auth.login.type') === 'route') ? route(config('bootstrap-italia.auth.login.route')) : url(config('bootstrap-italia.auth.login.url')) }}"
                                                type="button">{{ trans('bootstrap-italia::bootstrap-italia.login') }}</button>
                                        @endif
                                    @endguest

                                    @auth
                                        @if(strtoupper(config('bootstrap-italia.auth.logout.method')) === 'GET' || !config('bootstrap-italia.auth.logout.method') && version_compare(\Illuminate\Foundation\Application::VERSION, '5.3.0', '<'))
                                            <button onclick="event.preventDefault(); window.location=this.getAttribute('href')"
                                                class="btn btn-primary btn-sm"
                                                href="{{ (config('bootstrap-italia.auth.logout.type') === 'route') ? route(config('bootstrap-italia.auth.logout.route')) : url(config('bootstrap-italia.auth.logout.url')) }}"
                                                type="button">{{ trans('bootstrap-italia::bootstrap-italia.logout') }}</button>
                                        @else
                                            <button class="btn btn-primary btn-sm" href="{{route('logout')}}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                                type="button">{{ trans('bootstrap-italia::bootstrap-italia.logout') }}</button>
                                            <form id="logout-form" action="{{route('logout')}}" method="POST" class="d-none">
                                                @if(config('bootstrap_italia.logout_method'))
                                                    {{ method_field(config('bootstrap_italia.logout_method')) }}
                                                @endif
                                                {{ csrf_field() }}
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="it-nav-wrapper">
        <div class="it-header-center-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="it-header-center-content-wrapper">
                            <div class="it-brand-wrapper">
                                <a href="{{ url(config('bootstrap-italia.routes.home.url')) }}">
                                    @if (config('bootstrap-italia.logo'))
                                        <img alt="logo" class="icon" src="{{ config('bootstrap-italia.logo.url') }}">
                                    @endif
                                    <div class="it-brand-text">
                                        <h2>{!! config('bootstrap-italia.brand-text') !!}</h2>
                                        <h3 class="d-none d-md-block">{!! config('bootstrap-italia.tagline') !!}</h3>
                                    </div>

                                    <div class="col-9 d-flex justify-content-center">
                                        <img src="{{ asset('vendor/bootstrap-italia/dist/assets/Ministero_della_Salute_Logo.png') }}"
                                            alt="Ministero della Salute" class="img-fluid" style="max-height: 90px;">
                                    </div>


                                    <div class="col-2 d-flex justify-content-end">
                                        <img src="{{ asset('vendor/bootstrap-italia/dist/assets/regione_sicilia.png') }}"
                                            alt="Regione Sicilia" class="img-fluid" style="max-height: 90px;">
                                    </div>

                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="it-header-navbar-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="navbar navbar-expand-lg has-megamenu">
                        <button class="custom-navbar-toggler" type="button" aria-controls="nav10" aria-expanded="false"
                            aria-label="{{ trans('bootstrap-italia::bootstrap-italia.toggle_navigation') }}"
                            data-target="#nav10">
                            <svg class="icon">
                                <use xlink:href="{{ asset('vendor/bootstrap-italia/dist/svg/sprite.svg#it-burger') }}">
                                </use>
                            </svg>
                        </button>
                        <div class="navbar-collapsable" id="nav10">
                            <div class="overlay"></div>
                            <div class="close-div sr-only">
                                <button class="btn close-menu" type="button"><span
                                        class="it-close"></span>close</button>
                            </div>
                            <div class="menu-wrapper justify-content-lg-between">
                                @if(Auth::user())
                                  @if(Auth::user()->ruolo()->first()->name == 'operatore')
                                        <ul class="navbar-nav">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('operatore.dashboard') }}">
                                                    <i class="bi bi-person-plus"></i>&nbsp;&nbsp;{{ __('Aggiungi paziente') }}
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('operatore.prenotazioni') }}">
                                                    <i class="bi bi-book"></i>&nbsp;&nbsp;{{ __('Prenotazioni') }}
                                                </a>
                                            </li>

        <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('admin.utentiValidati') }}">
                                                        <i class="bi bi-people"></i>&nbsp;Utenti validati
                                                    </a>
                                                </li>


                                          
  


                   

                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('operatore.storicoPazienti') }}">
                                                    <i class="bi bi-file-medical"></i>&nbsp;&nbsp;{{ __('Storico Pazienti') }}
                                                </a>
                                            </li>

                                        </ul>
                                    @endif


                                    @if(Auth::user()->ruolo()->first()->name == 'paziente')
                                        <ul class="navbar-nav">

                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('paziente.home') }}"><i
                                                        class="bi bi-house-door"></i>&nbsp;&nbsp;{{ __('Index') }}</a>
                                            </li>
                                            @if(Auth::user()->ruolo()->first()->name == 'paziente' && Auth::user()->stato == 1)

                                                    <!--
                                                        <li class="nav-item">
                                                            <a class="nav-link" href="{{ route('paziente.dashboard') }}"><i
                                                                    class="bi bi-journal-x"></i>&nbsp;&nbsp;{{ __('Prenota') }}</a>
                                                        </li>
                                                    -->
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="{{ route('paziente.prenotazioni') }}"><i
                                                                class="bi bi-book"></i>&nbsp;&nbsp;{{ __('Le tue prenotazioni') }}</a>
                                                    </li>

                                                </ul>
                                            @endif
                                    @endif

                                    
                                    @if(Auth::user()->ruolo()->first()->name == 'ets')
                                        <ul class="navbar-nav">

                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('ets.dashboard') }}"><i
                                                        class="bi bi-house-door"></i>&nbsp;&nbsp;{{ __('Home') }}</a>
                                            </li>
                                       

                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('ets.prenotazioni') }}"><i
                                                        class="bi bi-book"></i>&nbsp;&nbsp;{{ __('Le tue prenotazioni') }}</a>
                                            </li>

                                        </ul>
                                    @endif
                                

                                    @if(Auth::user()->ruolo()->first()->name == 'admin')
                                   <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                                        <div class="container-fluid flex-column align-items-start">

                                            {{-- Riga ADMIN --}}
                                            <ul class="navbar-nav mb-2">
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                                        <i class="bi bi-people"></i>&nbsp;Utenti da validare
                                                    </a>
                                                </li>
                                             
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('admin.flussoView') }}">
                                                        <i class="bi bi-arrow-down-up"></i>&nbsp;Esporta flusso
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('admin.etsView') }}">
                                                        <i class="bi bi-file-earmark-spreadsheet"></i>&nbsp;Esporta ETS
                                                    </a>
                                                </li>

                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('operatore.grafici') }}">
                                                        <i class="bi bi-file-earmark-spreadsheet"></i>&nbsp;Grafici
                                                    </a>
                                                </li>

                                                <li class="nav-item dropdown">
                                                    <a class="nav-link dropdown-toggle" href="#" id="dropdownConfigura" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-gear"></i>&nbsp;&nbsp;{{ __('Configura') }}
                                                    </a>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownConfigura">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('operatore.modificaBranche') }}">Branche e prestazioni</a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('operatore.modificaAgenda') }}">Agenda</a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('operatore.modificaFarmaci') }}">Farmaci</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                </ul>
                                            </li>
                                            </ul>

                                            {{-- Riga OPERATORE --}}
                                            <ul class="navbar-nav border-top pt-2">
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('operatore.dashboard') }}">
                                                        <i class="bi bi-person-plus"></i>&nbsp;Aggiungi paziente
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('operatore.prenotazioni') }}">
                                                        <i class="bi bi-calendar-check"></i>&nbsp;Prenotazioni
                                                    </a>
                                                </li>
                                           
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('operatore.storicoPazienti') }}">
                                                        <i class="bi bi-file-medical"></i>&nbsp;Storico Pazienti
                                                    </a>
                                                </li>



                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('admin.utentiValidati') }}">
                                                        <i class="bi bi-people"></i>&nbsp;Utenti validati
                                                    </a>
                                                </li>

                                            </ul>

                                        </div>
                                    </nav>


                                    @endif

                                @endif
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Header -->

<!-- Body -->
   <div class="container my-4" style="max-width: 1600px;">
        @yield('content')
    </div>
<!-- End Body -->

<!-- Footer -->
<footer class="it-footer">
    <div class="it-footer-main">
        <div class="container">
            <section>
                <div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="it-brand-wrapper">
                            <a href="{{ url(config('bootstrap-italia.routes.home.url')) }}"
                                class="d-flex align-items-center justify-content-between w-100">
                                {{-- Branding e testo --}}
                                <div class="d-flex align-items-center">
                                    @if (config('bootstrap-italia.logo'))
                                        <img alt="logo" class="icon me-3" src="{{ config('bootstrap-italia.logo.url') }}">
                                    @endif

                                    <div class="it-brand-text">
                                        <h2 class="mb-0">{!! config('bootstrap-italia.brand-text') !!}</h2>
                                        <h3 class="d-none d-md-block">{!! config('bootstrap-italia.tagline') !!}</h3>
                                    </div>
                                </div>

                                {{-- Loghi Regione e INMP --}}
                                <div class="d-flex align-items-center ms-4 gap-4">
                                    <img src="{{ asset('vendor/bootstrap-italia/dist/assets/cofinanziato_UE.png') }}"
                                        alt="cofinanziato_UE" style="max-height: 50px;">
                                </div>
                                <div class="d-flex align-items-center ms-4 gap-4"></div>
                                <img src="{{ asset('vendor/bootstrap-italia/dist/assets/inmp.png') }}" alt="INMP"
                                    style="max-height: 50px;">
                        </div>
                    </div>
                    </a>
                </div>
        </div>
    </div>
    </section>
    </div>
    </div>
    <div class="it-footer-small-prints clearfix">
        <div class="container">
         <!-- Inline -->

<p class="text-white">S.A.R.A. – Salute Accessibile per le Reti Ambulatoriali</p>

            <ul class="it-footer-small-prints-list list-inline mb-0 d-flex flex-column flex-md-row">
                @each('bootstrap-italia::partials.footer-bar-item', $bootstrapItalia->menu()['footer-bar'], 'item')
            </ul>
        </div>
    </div>
</footer>
<!-- End Footer -->



<a href="#" aria-hidden="true" data-attribute="back-to-top" class="back-to-top">
    <svg class="icon icon-light">
        <use xlink:href="{{ asset('vendor/bootstrap-italia/dist/svg/sprite.svg#it-arrow-up') }}"></use>
    </svg>
</a>
<div class="cookiebar">
    <p>{!! trans('bootstrap-italia::bootstrap-italia.cookiebar.message') !!}</p>
    <div class="cookiebar-buttons">
        <a href="#" class="cookiebar-btn">{!! trans('bootstrap-italia::bootstrap-italia.cookiebar.preferences') !!}</a>
        <button data-accept="cookiebar"
            class="cookiebar-btn cookiebar-confirm">{!! trans('bootstrap-italia::bootstrap-italia.cookiebar.accept') !!}</button>
    </div>
</div>
<script src="{{ asset('vendor/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js') }}"></script>
@stop

@section('bootstrapitalia_js')
@stack('js')
@yield('js')
@stop