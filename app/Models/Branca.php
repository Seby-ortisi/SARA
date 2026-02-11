<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branca extends Model
{
    use HasFactory;

    protected $table = "branche";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'nome_branca' ,
        'stato_branca',
        'created_at',
        'updated_at',
    ];

}
