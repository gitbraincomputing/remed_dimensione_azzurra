# Infocert API



## Documentazione

Istruzioni su come usare le API

## Configurazione

Il file "digital_doc_sign" contiene le configurazioni per controllare l'uso delle API

```
public static $mode_stage = true;
        
// url stage
public static $url_auth_stage = "https://idpstage.infocert.digital/auth/realms/delivery/protocol/openid-connect/token";
public static $url_api_stage = "https://apistage.infocert.digital/signature/v1/";

// url produzione
public static $url_auth = "https://idpstage.infocert.digital/auth/realms/delivery/protocol/openid-connect/token";
public static $url_api = "https://apistage.infocert.digital/signature/v1/";

public static $username = 'brain-computing';
public static $password = 'b26d265b-6348-494f-8d8b-d31101d67339';
```

## Metodi

- getOauthToken
<br>per recuperare il token
<br><br>
- getCertificates
<br>per recuperare tutti i certificati associati ad un utente
<br><br>
- getCertificate
<br>per recuperere uno specifico certificato di un utente
<br><br>
- challenge
<br>richiesta di avvio transazione e procedura di OTP
<br><br>
- sign_pades_doc
<br>richiesta per la firma del documento PDF in modalità PADES
<br><br>
- sign_cades_doc
<br>richiesta per la firma del documento PDF in modalità CADES

## Test

Per eseguire i test abbiamo due file
- test_api.php
<br>contiene tutte le chiamate per avviare la procedura di firma e ottenere l'OTP<br>
<br><br>
- test_api_2.php
<br>firma il file e restituisce il PDF firmato.
<br>il test contine due procedure PADES e CADES
<br><br>

Paramentri di configurazione da impostare nel secondo test recuperati dal primo test

```
$access_token = "recuperato da test_api.php";
$pin = "14725836";
$otp = "********";
$trasaction_id = "recuperato da test_api.php";
```