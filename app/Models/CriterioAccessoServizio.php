<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriterioAccessoServizio extends Model
{
    use HasFactory;

    protected $table = "criteri_accesso_servizio";

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'titolo_studio' ,
        'condizione_professionale',
        'cerca_lavoro',
        'id_ets',
        'categoria_vulnerabilita',
        'criteri_contesto',
        'criteri_persona',
        'allegato_tessera_sanitaria',
        'copia_primo_foglio_ISEE_minorenne',
        'documento_genitore',
        'copia_primo_foglio_ISEE',
        'permesso_soggiorno',
        'allegato_documento_identita',
        'note',
        'condizione_vulnerabilita_socio_economica',
        'altri_pdf',
        'note_pdf',
        'created_at',
        'updated_at'
    ];

}
