@extends('bootstrap-italia::page')

@section('bootstrapitalia_css')
    <style>
        body {
            background: #f8fafd;
        }

        .login-box {
            max-width: 960px;
            margin: 3rem auto;
            background-color: #fff;
            border-radius: 1rem;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .app-logo {
            max-width: 140px;
            margin-bottom: 1rem;
        }

        .login-title {
            font-size: 2rem;
            font-weight: 600;
            color: #0b3c5d;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: #5c6c75;
            margin-bottom: 2rem;
        }

        .section-divider {
            margin: 2.5rem 0 1rem;
            text-align: center;
            position: relative;
        }

        .section-divider::before,
        .section-divider::after {
            content: '';
            display: inline-block;
            width: 45%;
            height: 1px;
            background-color: #d9dee3;
            vertical-align: middle;
        }

        .section-divider span {
            padding: 0 1rem;
            font-size: 0.9rem;
            color: #999;
            background: #fff;
            position: relative;
            z-index: 1;
        }

        .custom-btn {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 0.5rem;
            text-align: center;
        }

        .spid-btn {
            background-color: #0066cc;
            color: #fff;
        }

        .cie-btn {
            background-color: #009cde;
            color: #fff;
        }

        .custom-btn:hover {
            opacity: 0.9;
        }

        .login-form input {
            padding: 0.75rem;
        }

        .login-form label {
            font-weight: 500;
        }

        .form-box {
            background-color: #f7f9fc;
            border-radius: 0.5rem;
            padding: 2rem;
        }

        .footer-links {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: #0073e6;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="login-box text-center">
        <h1 class="login-title">Portale Ambulatori</h1>
        <p class="login-subtitle">Accedi alla tua area personale</p>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        @if ($errors->any())
            <div class="alert alert-danger text-start">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Login con email e password --}}
        <div class="form-box mb-4">
            <form method="POST" action="{{ route('standardLoginAuth') }}" class="login-form text-start">
                @csrf
                <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>
                <div class="mb-4">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary custom-btn">Accedi</button>
                </div>
            </form>
        </div>

     <a href="{{ route('registrazione') }}" class="d-block mb-3 text-end">Registrati</a>


        {{-- Divider --}}
        <div class="section-divider"><span>oppure</span></div>

        {{-- SPID / CIE --}}
        <div class="row g-3">
            <div class="col-md-6">
                <a href="https://www2.asp.sr.it:8005/index.php?src={{ md5('150') }}&type={{ md5('_spid_') }}"
                   class="btn spid-btn custom-btn d-flex align-items-center justify-content-center">
                    <img src="/img/spid-ico-circle-bb.svg" alt="SPID" class="me-2" height="24">
                    Entra con SPID
                </a>
            </div>
            <div class="col-md-6">
                <a href="https://www2.asp.sr.it:8005/index.php?src={{ md5('150') }}&type={{ md5('_cie_') }}"
                   class="btn cie-btn custom-btn d-flex align-items-center justify-content-center">
                    <img src="/img/cie-logo.svg" alt="CIE" class="me-2" height="24">
                    Entra con CIE
                </a>
            </div>
        </div>

        <div class="footer-links mt-4">
            <p>
                Non hai SPID o CIE? <br>
                <a href="https://www.spid.gov.it/" target="_blank">Come ottenere SPID</a> |
                <a href="https://www.cartaidentita.interno.gov.it/" target="_blank">Come ottenere CIE</a>
            </p>
        </div>
    </div>
</div>
@endsection
