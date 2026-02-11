<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedaPaziente extends Model
{
    use HasFactory;

    protected $table = "scheda_paziente";

    public $timestamps = false;

    protected $fillable = [
        'paziente_id',
        'id_prenotazione',
        'diagnosi',
        'prestazione_erogata',
        'luogo_prestazione',
        'codici_icd9',
        'erogazione_farmaci',
        'tipologia_farmaco',
        'erogazione_dispositivo_medico',
        'tipologia_dispositivo_medico',
        'scheda_amnestica',
        'created_at',
        'updated_at'
    ];
}
