<?php
return [

    // credenziali middleware
    'MIDDLEWARE_LOGIN' => 'vaccinazioni@asp.sr.it',
    'MIDDLEWARE_PASSWORD' => 'vaccinazioni2024WOW!!',

    // URL middleware
    'MIDDLEWARE_URL' => 'http://gitlab.asp.sr.it:8001/api/',

    // API middleware
    'MIDDLEWARE_API_LOGIN' => 'login/',
    'MIDDLEWARE_API_IDENTIFICATIVO_TS' => "v1/getIdentificativoTesseraSanitaria/",
    'MIDDLEWARE_API_DATI_TS' => "v1/getDatiTesseraSanitaria/",
    'MIDDLEWARE_API_RICERCA_ANAGRAFICA_CODICEFISCALE' => "v1/getAnagraficheFromCF/",
    'MIDDLEWARE_API_RICERCA_ANAGRAFICA_COGNOME' => "v1/getAnagraficheFromCognome/",

];