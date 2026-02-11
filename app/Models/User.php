<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    
    protected $fillable = [
        'nome',
        'cognome',
        'email',
        'sesso',
        'password',
        'cellulare',
        'data_nascita',
        'codice_fiscale',
        'id_assistito',
        'nazionalita',
        'provincia',
        'luogo_nascita',
        'indirizzo_residenza',
        'comune',
        'ldap_username',
        'ruolo_id',
        'email_verified_at',
        'phone_verified_at',
        'stato',
        'modalita_autenticazione',
        'creato_da',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Recupero la relazione con la tabella ruolo
     */
    public function ruolo()
    {
        return $this->belongsTo(Ruolo::class);
    }

       protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email'=> [trans('email o password errati')],
        ]);
    }

}