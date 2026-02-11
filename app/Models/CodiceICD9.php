<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodiceICD9 extends Model
{
    use HasFactory;

    protected $table = "codici_icd9";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'codice' ,
        'descrizione',
        'created_at',
        'updated_at',
    ];

}
