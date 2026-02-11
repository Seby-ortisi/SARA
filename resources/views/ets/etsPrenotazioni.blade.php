@extends('bootstrap-italia::page')

@section('content')
    <div class="container my-4">
    <!-- Header Elegante -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <div>
                                <h1 class="display-6 mb-0 fw-bold">Agenda Pazienti</h1>
                                <p class="text-muted mb-0 mt-1">Dashboard di monitoraggio</p>
                            </div>
                        </div>
                    </div>
                    <div class="stats-badge">
                        <div class="text-center">
                            <div class="stats-number">{{ count($dataView['etsAssociati']) }}</div>
                            <div class="stats-label">Pazienti</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigazione Data -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card navigation-card shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="current-date mb-0" id="data_corrente">
                                {{ \Carbon\Carbon::now()->locale('it')->isoFormat('dddd D MMMM YYYY') }}
                            </h3>
                        </div>
                        <div class="btn-group btn-group-lg navigation-buttons">
                            <button type="button" id="btn_prev" class="btn btn-outline-primary">
                                <svg class="icon">
                                    <use href="{{ asset('svg/sprites.svg') }}#it-chevron-left"></use>
                                </svg>
                            </button>
                            <button type="button" id="btn_oggi" class="btn btn-primary px-5">
                                <svg class="icon icon-white me-2">
                                    <use href="{{ asset('svg/sprites.svg') }}#it-calendar"></use>
                                </svg>
                                Oggi
                            </button>
                            <button type="button" id="btn_next" class="btn btn-outline-primary">
                                <svg class="icon">
                                    <use href="{{ asset('svg/sprites.svg') }}#it-chevron-right"></use>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabella Pazienti Elegante -->
    <div class="row">
        <div class="col-12">
            <div class="card patients-card shadow">
                <div class="card-body p-0">
                    <div class="table-responsive" id="lista_pazienti">
                        @if(count($dataView['etsAssociati']) > 0)
                            <table class="table patients-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center col-time">Orario</th>
                                        <th scope="col" class="col-patient">Paziente</th>
                                        <th scope="col" class="col-cf">Codice Fiscale</th>
                                        <th scope="col" class="col-location">Ambulatorio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataView['etsAssociati'] as $index => $prenotazione)
                                        <tr class="patient-row"
                                            data-nome="{{ strtolower($prenotazione->nome) }}"
                                            data-cognome="{{ strtolower($prenotazione->cognome) }}"
                                            data-data="{{ \Carbon\Carbon::parse($prenotazione->data_inizio)->format('Y-m-d') }}"
                                            data-ora="{{ \Carbon\Carbon::parse($prenotazione->data_inizio)->format('H:i') }}">
                                            
                                            <td class="text-center">
                                                <div class="time-badge">
                                                    <svg class="icon icon-white icon-sm mb-1">
                                                        <use href="{{ asset('svg/sprites.svg') }}#it-clock"></use>
                                                    </svg>
                                                    <div class="time-text">{{ \Carbon\Carbon::parse($prenotazione->data_inizio)->format('H:i') }}</div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="patient-info">
                                                    <div class="patient-avatar">
                                                        <svg class="icon icon-white">
                                                            <use href="{{ asset('svg/sprites.svg') }}#it-user"></use>
                                                        </svg>
                                                    </div>
                                                    <div class="patient-name">
                                                        {{ $prenotazione->cognome }} {{ $prenotazione->nome }}
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="cf-code">
                                                    <svg class="icon icon-sm text-primary me-2">
                                                        <use href="{{ asset('svg/sprites.svg') }}#it-card"></use>
                                                    </svg>
                                                    <span class="cf-text">{{ $prenotazione->codice_fiscale ?? 'Non disponibile' }}</span>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="location-info">
                                                    <svg class="icon icon-sm text-muted me-2">
                                                        <use href="{{ asset('svg/sprites.svg') }}#it-pin"></use>
                                                    </svg>
                                                    <span class="location-text">{{ $prenotazione->descrizione }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <svg class="icon">
                                        <use href="{{ asset('svg/sprites.svg') }}#it-info-circle"></use>
                                    </svg>
                                </div>
                                <h3 class="empty-title">Nessun paziente in agenda</h3>
                                <p class="empty-text">Non ci sono prenotazioni per la data selezionata.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Header Section */
    .header-section {
        padding: 1.5rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .icon-wrapper {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #0066CC 0%, #004C99 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }
    
    .icon-wrapper .icon {
        width: 28px;
        height: 28px;
    }
    
    .stats-badge {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 16px;
        padding: 1.5rem 2.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #dee2e6;
    }
    
    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #0066CC;
        line-height: 1;
    }
    
    .stats-label {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 0.25rem;
        font-weight: 600;
    }
    
    /* Navigation Card */
    .navigation-card {
        border: none;
        border-radius: 12px;
        background: #fff;
    }
    
    .current-date {
        color: #2c3e50;
        font-weight: 600;
        font-size: 1.5rem;
    }
    
    .navigation-buttons .btn {
        border-radius: 8px;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .navigation-buttons .btn-primary {
        background: linear-gradient(135deg, #0066CC 0%, #004C99 100%);
        border: none;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }
    
    .navigation-buttons .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 102, 204, 0.4);
    }
    
    .navigation-buttons .btn-outline-primary {
        border: 2px solid #0066CC;
        color: #0066CC;
    }
    
    .navigation-buttons .btn-outline-primary:hover {
        background: #0066CC;
        color: white;
        transform: translateY(-2px);
    }
    
    /* Patients Card */
    .patients-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }
    
    /* Table Styling */
    .patients-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .patients-table thead {
        background: linear-gradient(135deg, #0066CC 0%, #004C99 100%);
    }
    
    .patients-table thead th {
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.813rem;
        letter-spacing: 0.5px;
        padding: 1.25rem 1.5rem;
        border: none;
    }
    
    .patients-table tbody td {
        padding: 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f0f2f5;
    }
    
    .patient-row {
        transition: all 0.3s ease;
        background: white;
    }
    
    .patient-row:hover {
        background: linear-gradient(90deg, #f8f9ff 0%, #ffffff 100%);
        box-shadow: 0 2px 8px rgba(0, 102, 204, 0.08);
        transform: translateX(4px);
    }
    
    .patient-row:last-child td {
        border-bottom: none;
    }
    
    /* Time Badge */
    .time-badge {
        background: linear-gradient(135deg, #0066CC 0%, #004C99 100%);
        border-radius: 10px;
        padding: 0.75rem 1.25rem;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.25);
        min-width: 90px;
    }
    
    .time-text {
        color: white;
        font-size: 1.25rem;
        font-weight: 700;
        font-family: 'Courier New', monospace;
    }
    
    /* Patient Info */
    .patient-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .patient-avatar {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .patient-avatar .icon {
        width: 24px;
        height: 24px;
    }
    
    .patient-name {
        font-weight: 600;
        font-size: 1.063rem;
        color: #2c3e50;
    }
    
    /* CF Code */
    .cf-code {
        display: flex;
        align-items: center;
    }
    
    .cf-text {
        font-family: 'Courier New', monospace;
        font-size: 0.938rem;
        color: #495057;
        background: #f8f9fa;
        padding: 0.5rem 0.875rem;
        border-radius: 6px;
        font-weight: 500;
    }
    
    /* Location Info */
    .location-info {
        display: flex;
        align-items: center;
    }
    
    .location-text {
        color: #6c757d;
        font-size: 0.938rem;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
    }
    
    .empty-icon {
        margin-bottom: 1.5rem;
    }
    
    .empty-icon .icon {
        width: 80px;
        height: 80px;
        color: #dee2e6;
    }
    
    .empty-title {
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .empty-text {
        color: #adb5bd;
        margin: 0;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stats-badge {
            padding: 1rem 1.5rem;
        }
        
        .stats-number {
            font-size: 2rem;
        }
        
        .navigation-buttons .btn {
            padding: 0.5rem 0.75rem;
        }
        
        .navigation-buttons .btn span {
            display: none;
        }
        
        .patients-table thead th,
        .patients-table tbody td {
            padding: 1rem;
            font-size: 0.875rem;
        }
        
        .patient-avatar {
            width: 40px;
            height: 40px;
        }
    }
</style>

@endsection