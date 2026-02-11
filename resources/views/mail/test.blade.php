<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Promemoria Prenotazione</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
        }
        .header {
            font-weight: bold;
            font-size: 18px;
        }
        .contact {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<p class="header">Ufficio PNES</p>

<p>
    Gentile {{ $dataView['datiEmail']->nome }} {{ $dataView['datiEmail']->cognome }},
</p>

<p>
    La informiamo che la sua visita è stata prenotata con successo.
</p>

<ul>
    <li>📅 Data: {{ \Carbon\Carbon::parse($dataView['datiEmail']->data_inizio)->format('d/m/Y') }}</li>
    <li>⏰ Ora: {{ \Carbon\Carbon::parse($dataView['datiEmail']->data_inizio)->format('H:i') }}</li>
    <li>🏢 Sede: {{ $dataView['datiEmail']->luogo_prestazione }}</li>
</ul>

<div class="contact">
    <p>Per qualsiasi necessità o modifica, può contattarci al:</p>
    <ul>
        <li>0931484849 (Ufficio Amministrativo Siracusa - Lun/Ven 9:00/12:00)</li>
        <li>0931989374 (Ufficio Amministrativo Augusta - Giovedì 9:00/12:00)</li>
        <li>0931484705 (Ufficio Assistente Sociale e Mediatrice Culturale – Lun/Mar/Gio 9:00/11:00)</li>
    </ul>
    <p><strong>NB:</strong> Si prega di chiamare in caso di impossibilità a presentarsi.</p>
</div>

<p>Grazie e Arrivederci</p>

</body>
</html>
