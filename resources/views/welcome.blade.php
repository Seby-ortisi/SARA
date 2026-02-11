@extends('bootstrap-italia::page')

@section('content')

<div class="container my-5 py-5">

    <!-- Hero semplice -->
    <div class="row justify-content-center text-center mb-5">
        <div class="col-12 col-lg-8">
            <h1 class="fw-bold display-4 mb-3">Benvenuto in S.A.R.A.</h1>
            <p class="lead mb-1 text-primary">
                Salute Accessibile per le Reti Ambulatoriali
            </p>
            <p class="text-secondary">
                La piattaforma digitale pensata per facilitare la gestione di pazienti, appuntamenti e documentazione clinica.
            </p>
        </div>
    </div>

    <!-- Cards ingrandite -->
    <div class="row g-5 justify-content-center">
        <!-- Card Paziente -->
        <div class="col-12 col-md-6 col-lg-5">
            <div class="card shadow-lg p-3 card-animate h-100">
                <div class="card-header bg-primary text-white text-center py-4">
                    <i class="bi bi-person-fill display-2"></i>
                </div>

                <div class="card-body text-center">
                    <h3 class="fw-bold mb-3">Accesso Paziente/ETS</h3>
                    <p class="text-secondary fs-5 mb-4">
                        Gestione appuntamenti, documentazione clinica e comunicazioni.
                    </p>

                    <a href="{{ route('standardLogin') }}" class="btn btn-primary btn-lg w-100">
                        Accedi come Paziente
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Operatore -->
        <div class="col-12 col-md-6 col-lg-5">
            <div class="card shadow-lg p-3 card-animate h-100">
                <div class="card-header bg-primary text-white text-center py-4">
                    <i class="bi bi-person-badge-fill display-2"></i>
                </div>

                <div class="card-body text-center">
                    <h3 class="fw-bold mb-3">Accesso Operatore</h3>
                    <p class="text-secondary fs-5 mb-4">
                        Strumenti professionali per la gestione efficace di pazienti e dati clinici.
                    </p>

                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">
                        Accedi come Operatore
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
