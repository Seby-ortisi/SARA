<?php

//CIE
//$url="http://127.0.0.1:8000/accesso_istituzionale?sessionId=_915ccff2bd946d2db1dd86a1d6a5d7a9&identityProvider=https%3A%2F%2Fidptest.spidstart.it&spidCode=FAID80D58H501Q&name=Lucrezia&familyName=Borgia&fiscalNumber=TINIT-RTSSST01P03I754J&extendedData%5BauthnRequestId%5D=_beb958efb162791f0e5afe057f9bcb69&extendedData%5BauthnRequestSHA256CkSum%5D=8163EABFE40DBA7564EA8A57AF164E38FE0A640CB5F08460B021BACBC3F66460&extendedData%5BresponseId%5D=id_c85cbd4435380a5bde5aa1110f25ac9dcd545b88&extendedData%5BresponseSHA256CkSum%5D=9204D90851A0E8DA14A6BC1492CE1ABEC43280CB229694FABD8A624C10694BD8&extendedData%5BapiTransactionId%5D=8156&extendedData%5Btimestamp_StartSession%5D=2024-09-04T14%3A28%3A07%2B00%3A00&extendedData%5Btimestamp_Success%5D=2024-09-04T14%3A28%3A16.7693719%2B00%3A00&extendedData%5Btype%5D=cie";


//SPID
$url="http://10.61.28.77:8088/accesso_istituzionale?sessionId=_915ccff2bd946d2db1dd86a1d6a5d7a9&identityProvider=https%3A%2F%2Fidptest.spidstart.it&spidCode=FAID80D58H501Q&email=sebyy.ortisi@gmail.com&dateOfBirth=2014-10-03&domicileProvince=SR&mobilePhone=3331891214&name=Lucrezia&familyName=Borgia&fiscalNumber=TINIT-PRVDUE75T52I754I&extendedData%5BauthnRequestId%5D=_beb958efb162791f0e5afe057f9bcb69&extendedData%5BauthnRequestSHA256CkSum%5D=8163EABFE40DBA7564EA8A57AF164E38FE0A640CB5F08460B021BACBC3F66460&extendedData%5BresponseId%5D=id_c85cbd4435380a5bde5aa1110f25ac9dcd545b88&extendedData%5BresponseSHA256CkSum%5D=9204D90851A0E8DA14A6BC1492CE1ABEC43280CB229694FABD8A624C10694BD8&extendedData%5BapiTransactionId%5D=8156&extendedData%5Btimestamp_StartSession%5D=2024-09-04T14%3A28%3A07%2B00%3A00&extendedData%5Btimestamp_Success%5D=2024-09-04T14%3A28%3A16.7693719%2B00%3A00&extendedData%5Btype%5D=spid";
//$url="http://127.0.0.1:8000/accesso_istituzionale?sessionId=_915ccff2bd946d2db1dd86a1d6a5d7a9&identityProvider=https%3A%2F%2Fidptest.spidstart.it&spidCode=FAID80D58H501Q&email=sebyy.ortisi@gmail.com&mobilePhone=3331891214&name=Lucrezia&familyName=Borgia&fiscalNumber=TINIT-RTSSST01P03I754J&extendedData%5BauthnRequestId%5D=_beb958efb162791f0e5afe057f9bcb69&extendedData%5BauthnRequestSHA256CkSum%5D=8163EABFE40DBA7564EA8A57AF164E38FE0A640CB5F08460B021BACBC3F66460&extendedData%5BresponseId%5D=id_c85cbd4435380a5bde5aa1110f25ac9dcd545b88&extendedData%5BresponseSHA256CkSum%5D=9204D90851A0E8DA14A6BC1492CE1ABEC43280CB229694FABD8A624C10694BD8&extendedData%5BapiTransactionId%5D=8156&extendedData%5Btimestamp_StartSession%5D=2024-09-04T14%3A28%3A07%2B00%3A00&extendedData%5Btimestamp_Success%5D=2024-09-04T14%3A28%3A16.7693719%2B00%3A00&extendedData%5Btype%5D=cie";

header("Location: " . $url);
exit();
return;

    // File per memorizzare i cookie di sessione
    $cookieFile = "/tmp/vaccinazioni_session";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    // Salva il cookie di sessione
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookieFile);
    

    //curl_setopt($curl, CURLOPT_HEADER, true);
    //curl_setopt($curl, CURLOPT_HTTPHEADER, [
    //    'Content-Type: application/json'
    //]);
  
    $result = curl_exec($curl);
  dd($result );
    // Se ricevo un errore, lo visualizzo e termino il processo
    if (curl_errno($curl)) {
        echo "Errore: ";
        var_dump(curl_error($curl));
        die();
    } else {
        // Gestisci la risposta
    }   

    curl_close($curl);
    //$result = json_decode($result, true);
    dd( $result);