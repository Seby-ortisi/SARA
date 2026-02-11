@extends('bootstrap-italia::page')

@section('content')

<div class="card-header border-dark mb-3">
    <legend style="text-align:center">{{ __('Grafici') }}</legend>
</div>

{{-- 🔽 Selettori date "da / a" --}}
<form method="GET" action="{{ route('operatore.grafici') }}" class="row g-3 justify-content-center mb-4">
    <div class="col-auto">
        <label for="data_inizio" class="form-label">Data da:</label>
        <input 
            type="date" 
            id="data_inizio" 
            name="data_inizio" 
            class="form-control"
            value="{{ old('data_inizio', $dataView['data_inizio'] ?? '') }}">
    </div>
    <div class="col-auto">
        <label for="data_fine" class="form-label">Data a:</label>
        <input 
            type="date" 
            id="data_fine" 
            name="data_fine" 
            class="form-control"
            value="{{ old('data_fine', $dataView['data_fine'] ?? '') }}">
    </div>
    <div class="col-auto align-self-end">
        <button type="submit" class="btn btn-primary">Filtra</button>
    </div>
</form>

<!-- Card 1 -->
<div class="it-card-wrapper">
  <div class="it-card rounded shadow border">
    <div class="it-card-header">
      <h5 class="it-card-title p-3 mb-0 text-center">Volume di attività:</h5>
      <hr class="my-0" style="border-top: 1px solid #ddd;">
    </div>
    <div class="it-card-body p-3">
      <canvas id="grafico" width="400" height="200"></canvas>
    </div>
  </div>
</div>

<br>

<!-- Card 2 -->
<div class="it-card-wrapper mt-4">
  <div class="it-card rounded shadow border">
    <div class="it-card-header">
      <h5 class="it-card-title p-3 mb-0 text-center">Totale prestazioni e media per beneficiario</h5>
      <hr class="my-0" style="border-top: 1px solid #ddd;">
    </div>
    <div class="it-card-body p-3">
      <canvas id="graficoDue" width="400" height="200"></canvas>
    </div>
  </div>
</div>

<br>

<!-- Card 3 -->
<div class="it-card-wrapper">
  <div class="it-card rounded shadow border">
    <div class="it-card-header">
      <h5 class="it-card-title p-3 mb-0 text-center">Prestazioni più frequenti</h5>
      <hr class="my-0" style="border-top: 1px solid #ddd;">
    </div>
    <div class="it-card-body p-3 text-center">
      <canvas id="graficoPrestazioni" width="400" height="200"></canvas>
    </div>
  </div>
</div>



<script src="{{ asset('js/chart-js.min.js') }}"></script>

<script>
// Passa i dati PHP a JS (numeri)
const beneficiariUnici = {{ $dataView['beneficiariUnici'] }};
const beneficiariTotali = {{ $dataView['beneficiariTotali'] }};
const mediaPrestazioniPerBeneficiarioUnico = {{ $dataView['mediaPrestazioniPerBeneficiarioUnico'] }};
const prestazioniNomi = @json($dataView['prestazioniNomi']);
const prestazioniTotali = @json($dataView['prestazioniTotali']);

// Configurazione colori uniformi
const coloriPalette = {
    background: [
        'rgba(75, 192, 192, 0.5)',
        'rgba(54, 162, 235, 0.5)',
        'rgba(255, 206, 86, 0.5)',
        'rgba(255, 99, 132, 0.5)',
        'rgba(153, 102, 255, 0.5)'
    ],
    border: [
        'rgba(75, 192, 192, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(153, 102, 255, 1)'
    ]
};

// Opzioni comuni per tutti i grafici
const opzioniComuni = {
    responsive: true,
    plugins: {
        legend: {
            display: false
        },
        title: {
            display: true,
            font: { size: 18 },
            padding: { top: 10, bottom: 30 }
        }
    },
    scales: {
        x: {
            ticks: {
                maxRotation: 45,
                minRotation: 0
            }
        },
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1,
                precision: 0
            }
        }
    }
};

// --- GRAFICO 1: Beneficiari unici vs totali ---
const maxValue1 = Math.max(beneficiariUnici, beneficiariTotali);
const maxY1 = maxValue1 < 5 ? 5 : Math.ceil(maxValue1 * 1.1);

const ctx1 = document.getElementById('grafico').getContext('2d');
const chart1 = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: ['Beneficiari unici', 'Beneficiari totali'],
        datasets: [{
            label: 'Conteggio',
            data: [beneficiariUnici, beneficiariTotali],
            backgroundColor: [coloriPalette.background[0], coloriPalette.background[1]],
            borderColor: [coloriPalette.border[0], coloriPalette.border[1]],
            borderWidth: 1
        }]
    },
    options: {
        ...opzioniComuni,
        plugins: {
            ...opzioniComuni.plugins,
            title: {
                ...opzioniComuni.plugins.title,
                text: 'Rapporto tra beneficiari unici e beneficiari totali'
            }
        },
        scales: {
            ...opzioniComuni.scales,
            y: {
                ...opzioniComuni.scales.y,
                max: maxY1
            }
        }
    }
});

// --- GRAFICO 2: Totale prestazioni e media ---
const maxValue2 = Math.max(beneficiariTotali, mediaPrestazioniPerBeneficiarioUnico);
const maxY2 = maxValue2 < 5 ? 5 : Math.ceil(maxValue2 * 1.1);

const ctx2 = document.getElementById('graficoDue').getContext('2d');
const chart2 = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: ['Totale prestazioni', 'Media prestazioni per beneficiario unico'],
        datasets: [{
            label: 'Valore',
            data: [beneficiariTotali, mediaPrestazioniPerBeneficiarioUnico],
            backgroundColor: [coloriPalette.background[0], coloriPalette.background[1]],
            borderColor: [coloriPalette.border[0], coloriPalette.border[1]],
            borderWidth: 1
        }]
    },
    options: {
        ...opzioniComuni,
        plugins: {
            ...opzioniComuni.plugins,
            title: {
                ...opzioniComuni.plugins.title,
                text: 'Totale prestazioni erogate e media per beneficiario'
            }
        },
        scales: {
            ...opzioniComuni.scales,
            y: {
                ...opzioniComuni.scales.y,
                max: maxY2
            }
        }
    }
});

// --- GRAFICO 3: Top 5 Prestazioni ---
const maxValue3 = Math.max(...prestazioniTotali);
const maxY3 = maxValue3 < 5 ? 5 : Math.ceil(maxValue3 * 1.1);

const ctx3 = document.getElementById('graficoPrestazioni').getContext('2d');
const chart3 = new Chart(ctx3, {
    type: 'bar',
    data: {
        labels: prestazioniNomi,
        datasets: [{
            label: 'Numero di prenotazioni',
            data: prestazioniTotali,
            backgroundColor: prestazioniNomi.map((_, i) => coloriPalette.background[i % coloriPalette.background.length]),
            borderColor: prestazioniNomi.map((_, i) => coloriPalette.border[i % coloriPalette.border.length]),
            borderWidth: 1
        }]
    },
    options: {
        ...opzioniComuni,
        plugins: {
            ...opzioniComuni.plugins,
            title: {
                ...opzioniComuni.plugins.title,
                text: 'Top 5 Prestazioni più frequenti'
            }
        },
        scales: {
            ...opzioniComuni.scales,
            y: {
                ...opzioniComuni.scales.y,
                max: maxY3
            }
        }
    }
});


</script>

@endsection
