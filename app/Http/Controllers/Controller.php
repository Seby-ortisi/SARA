<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use DB;

abstract class Controller
{
    
public function registrazione()
    {

        $dataView['user'] = Auth::user();

        $dataView['nazioni'] = DB::table('nazionalita')
            ->select('id', 'nome_nazione', 'codice_nazione')
            ->get();

        return view('paziente.dashboard')->with('dataView', $dataView);
    }
}
