<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $table = "agenda";

    public $timestamps = false;

    protected $fillable = [
        'id_ambulatorio',
        'giorno' ,
        'ora_inizio',
        'ora_fine',
        'slot',
        'stato',
        'id_user',
        'created_at',
        'updated_at',
    ];

}
