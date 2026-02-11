<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prenotazione extends Model
{
    use HasFactory;

    protected $table = "prenotazioni";

    public $timestamps = false;

    protected $fillable = [
        'id_paziente',
        'data_inizio',
        'data_fine',
        'centro_vaccinale_id',
        'stato_prenotazione',
        'id_prenotazione',
        'branca_id',
        'creato_da',
        'note',
        'created_at',
        'updated_at'
    ];

}
