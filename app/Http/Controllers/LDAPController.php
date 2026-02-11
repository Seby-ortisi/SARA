<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LDAPController extends Controller
{
    private $connection;
    private $ldapBind;

    public function authenticate(Request $request)
    {
       
        // Connessione all'LDAPController
        $this->connection = ldap_connect("ldap://srv-dmn-master.asp8.sr");
        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3); // Imposta la versione del protocollo LDAPController
        ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0); // Disabilita i riferimenti LDAPController

        if ($this->connection) {
            // Autenticazione dell'utente
            $this->ldapBind = ldap_bind($this->connection, "santec_servizi@ASP8", "\$Ant3c2023@123456!");
        }

        $ldapFilter = "(sAMAccountName={$request->ldap_username})"; // Filtro per nome utente

        try {
            // Ricerco l'utente
            $ldapSearch = ldap_search($this->connection, 'OU=UT,DC=asp8,DC=sr', $ldapFilter);
            $ldapEntry = ldap_first_entry($this->connection, $ldapSearch);

            Log::channel('daily')->info('(LDAPController) Login username >' . $request->ldap_username . '<');
        
            // Verifica se l'utente esiste
            if ($ldapEntry) {
             
                // Verifica password
                $ldapUserDN = ldap_get_dn($this->connection, $ldapEntry);
                
                $ldapBindUser = ldap_bind($this->connection, $ldapUserDN, $request->password);
                 //return redirect()->route('login')->with('error', "L'utente non può accedere all'applicazione");
               
                if (!$ldapBindUser) {  
                    Log::channel('daily')->info('(LDAPController) LDAP authentication failed >' . $request->ldap_username . '<');
                    throw new Exception('LDAP authentication failed');
                    
                }
                //$result = $ldapBindUser ? LDAP_USER_AUTHORIZED : LDAP_PASSWORD_ERROR;
                $result = 0;
           
            } else {
      
                Log::channel('daily')->info('(LDAPController) L\'utente non è presente >' . $request->ldap_username . '<');
                //$result = LDAP_NOT_EXISTS_USER_ERROR;
                $result = "L'utente non è presente";
                 return redirect()->route('login')->with('error', "Utente non presente");
            }
        } catch (Exception $exception) {
            //$result = LDAP_NOT_VALID_USER_ERROR;
             return redirect()->route('login')->with('error', "Username o password errati");
            Log::channel('daily')->info('(LDAPController) Autenticazione fallita >' . $request->ldap_username . '<');
            $result = "Autenticazione fallita";
        }

        // Chiusura della connessione LDAPController
        ldap_unbind($this->connection);

        if ($result === 0) {
            // Verifica se l'utente esiste nel database
            $user = User::where('ldap_username', '=', $request->ldap_username)->first();

            if (!$user) {

             return redirect()->route('login')->with('error', "L'utente non esiste");
                // Se l'utente non esiste, lo creiamo
                /*
                $user = new User();
                $user->name = $request->ldap_username;
                $user->ldap_username = $request->ldap_username;
                $user->created_at= Carbon::now();
                $user->save();
*/
                Log::channel('daily')->info('(LDAPController) Utente creato nel database >' . $request->ldap_username . '<');
            }

            // Login dell'utente
            Auth::login($user);
            if ($user->ruolo_id == 3) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->ruolo_id == 2) {
                return redirect()->route('operatore.dashboard');
                } elseif ($user->ruolo_id == 1) {
                return redirect()->route('paziente.dashboard');
            } else {
                // Se vuoi gestire il ruolo 1 o altri casi, aggiungi qui
                // return redirect()->route('qualche.altra.route');
                // Se nessun ruolo corrisponde:
                return redirect()->route('login')->with('error', "L'utente non può accedere all'applicazione");
            }
    
        }
    }
}



