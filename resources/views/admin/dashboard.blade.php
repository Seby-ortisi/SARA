@extends('bootstrap-italia::page')

@section('content')
<div class="container py-5">

    <div class="card-header border-dark mb-3">
        <legend style="text-align:center">{{ __('Utenti da validare') }}</legend>
    </div>

    {{-- Feedback --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        </div>
    @endif

    {{-- Tabulator Table --}}
    <div id="utenti-table"></div>

</div>



{{-- Script Tabulator --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Dati utenti dal backend
    const utentiData = @json($dataView['utentiDaValidare']);

    // Formatter personalizzato per la colonna espandibile
    const expandFormatter = function(cell, formatterParams, onRendered) {
        return '<i class="bi bi-plus-circle"></i>';
    };

    // Formatter per il pulsante Aggiorna
    const actionFormatter = function(cell, formatterParams, onRendered) {
        const id = cell.getRow().getData().id;
        return `<a href="/admin/valida/${id}" class="btn btn-primary btn-sm">Valida</a>`;
    };

    // Funzione per formattare i dettagli espansi
    function detailFormatter(cell, formatterParams, onRendered) {
        const data = cell.getData();
        
        // Array degli allegati
        const allegati = [
            { label: 'Tessera sanitaria', file: data.allegato_tessera_sanitaria },
            { label: 'ISEE minore', file: data.copia_primo_foglio_ISEE_minorenne },
            { label: 'Documento genitore', file: data.documento_genitore },
            { label: 'ISEE adulto', file: data.copia_primo_foglio_ISEE },
            { label: 'Permesso soggiorno', file: data.permesso_soggiorno },
            { label: 'Documento identità', file: data.allegato_documento_identita }
        ];

        // Altri PDF
        const altriPdf = data.altri_pdf ? data.altri_pdf.split(',') : [];

        let html = '<div class="p-3 bg-light">';
        html += `<p class="mb-2"><strong>Email:</strong> ${data.email || '—'}</p>`;
        html += `<p class="mb-2"><strong>Criteri persona:</strong> ${data.criteri_persona__nome || '—'}</p>`;
        html += `<p class="mb-3"><strong>Criteri contesto:</strong> ${data.criteri_contesto__nome || '—'}</p>`;
        html += `<p class="mb-3"><strong>Note:</strong> ${data.note || '—'}</p>`;
        
        html += '<div class="row">';
        
        // Allegati principali
        allegati.forEach(allegato => {
            html += '<div class="col-md-4 mb-2">';
            html += `<strong>${allegato.label}:</strong>`;
            if (allegato.file) {
                html += ` <a href="/storage/${allegato.file}" target="_blank" class="ms-1">Apri</a>`;
            } else {
                html += ' <span class="text-muted ms-1">—</span>';
            }
            html += '</div>';
        });

        // Altri PDF
        if (altriPdf.length > 0 && altriPdf[0] !== '') {
            html += '<div class="col-12 mt-3 d-flex align-items-center">';
            html += '<strong class="me-2">Altri PDF:</strong>';
            altriPdf.forEach(pdf => {
                html += `<a href="/storage/${pdf.trim()}" target="_blank" class="me-2">Apri</a>`;
            });
            html += '<div style="width:1px; height:24px; background:#ccc; margin: 0 15px;"></div>';
            html += '<strong class="me-2">Note altri pdf:</strong>';
            html += `<span>${data.note_pdf || '—'}</span>`;
            html += '</div>';
        } else {
            html += '<div class="col-12 mt-3">';
            html += '<span class="text-muted">Nessun altro PDF</span>';
            html += '</div>';
        }

        html += '</div></div>';
        
        return html;
    }

    // Inizializza Tabulator
    const table = new Tabulator("#utenti-table", {
        data: utentiData,
        layout: "fitColumns",
        responsiveLayout: "collapse",
        pagination: "local",
        paginationSize: 10,
        paginationSizeSelector: [10, 25, 50, 100],
        columns: [
            {
                formatter: "responsiveCollapse",
                width: 40,
                minWidth: 40,
                hozAlign: "center",
                resizable: false,
                headerSort: false
            },
           {
                title: "Cognome",
                field: "cognome",
                headerFilter: "input"
                ,
                formatter: function(cell, formatterParams, onRendered) {
                    const value = cell.getValue();
                    const row = cell.getRow();
                    return `<span class="expand-trigger" style="cursor:pointer;">
                                <i class="bi bi-plus-circle me-1"> </i>${value}
                            </span>`;
                },
                cellClick: function(e, cell) {
                    cell.getRow().toggleTreeExpanded();
                }
            },
            {
               title: "Nome",
                field: "nome",
                headerFilter: "input"
            },
            
            {
                title: "Codice fiscale",
                field: "codice_fiscale",
                headerFilter: "input"
            },
            {
                title: "Cellulare",
                field: "cellulare",
                headerFilter: "input"
            },
            {
                title: "Categoria vulnerabilità",
                field: "categorie_vulnerabilita__nome",
                headerFilter: "input"
            },
            {
                title: "Azioni",
                field: "id",
                headerSort: false,
                formatter: actionFormatter,
                hozAlign: "center"
            }
        ],
        rowFormatter: function(row) {
            // Crea una riga di dettaglio sotto ogni riga
            const data = row.getData();
            const holderEl = document.createElement("div");
            holderEl.style.display = "none";
            holderEl.classList.add("detail-row");
            
            const detailHtml = detailFormatter(row.getCell("nome"), {}, null);
            holderEl.innerHTML = detailHtml;
            
            row.getElement().appendChild(holderEl);
        }
    });

    // Gestione espansione/collasso righe
    table.on("rowClick", function(e, row) {
        if (e.target.closest('.expand-trigger')) {
            const rowEl = row.getElement();
            const detailRow = rowEl.querySelector('.detail-row');
            const icon = rowEl.querySelector('.bi');
            
            if (detailRow.style.display === "none") {
                detailRow.style.display = "block";
                icon.classList.remove('bi-plus-circle');
                icon.classList.add('bi-dash-circle');
            } else {
                detailRow.style.display = "none";
                icon.classList.remove('bi-dash-circle');
                icon.classList.add('bi-plus-circle');
            }
        }
    });
});
</script>

<style>
.detail-row {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.expand-trigger:hover {
    color: #0066cc;
}

.tabulator {
    border: 1px solid #dee2e6;
}

.tabulator-row {
    min-height: 50px;
}
</style>

@endsection