<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PadreFiglio extends Model
{
    use HasFactory;

    protected $table = "padre_figlio";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'padre_id',
        'figlio_id',
        'stato',
        'ruolo_referente',
        'created_at',
        'updated_at'
    ];
}
