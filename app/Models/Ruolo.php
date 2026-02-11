<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruolo extends Model
{
    use HasFactory;

     protected $table = 'roles';
    protected $fillable = [
        'nome',
        'descrizione',
    ];

public function ldapUsers()
{
    return $this
        ->belongsToMany(User::class)
        ->withTimestamps();
}

}
