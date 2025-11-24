<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
<style>
/* http://meyerweb.com/eric/tools/css/reset/ 
   v2.0 | 20110126
   License: none (public domain)
*/

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed, 
figure, figcaption, footer, header, hgroup, 
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	font: inherit;
	vertical-align: baseline;
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure, 
footer, header, hgroup, menu, nav, section {
	display: block;
}
body {
	line-height: 1;
}
ol, ul {
	list-style: none;
}
blockquote, q {
	quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
	content: '';
	content: none;
}
table {
	border-collapse: collapse;
	border-spacing: 0;
}
a.button {
	padding:20px;
	background:#eee;
	border:1px solid #bbb;
	margin:10px;
	display:block;
	text-align:center;
}
h1 { text-align:center; margin-top:20px; margin-bottom:20px;}
</style>
</head>
<body>
<?php

    require dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php';
    require dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'MTOMClient.php';
    
	try{
		
	
    $client = new MTOMClient(USERNAME_SOGEI, PASSWORD_SOGEI, PIN_SOGEI, ENVIRONMENT_SOGEI);    
    
    if($action == 'invia'){

        $response = $client->inviaFileMtomResponse(PATH_SORGENTE.$nome_file, CODICE_SSA_SOGEI, CODICE_ASL_SOGEI, CODICE_REGIONE_SOGEI, CODICE_FISCALE_SOGEI);
        echo $response->descrizioneEsito . PHP_EOL;

        $protocollo_risposta = "Questo è il protocollo -> ".$response->protocollo;
        
        if($debug){
            echo '<pre>';
            print_r($response);
            echo '</pre>';
        }        
        
        if($response->codiceEsito == '000')
        {
            echo "Protocollo assegnato : #{$response->protocollo}#\n";
            $response = $client->ricevutePdf($response->protocollo,PATH_RICEVUTE);

            if($response->esitoChiamata == 0)
                echo "la risposta si trova in {$response->pdf}\n";
            else
                echo 'errore nel prendere la ricevuta';

        } 
    }
    else if ($action == 'ricevuta'){
        $response = $client->ricevutePdf($protocollo,PATH_RICEVUTE);
        
        if($response->esitoChiamata == 0)
		{
		  echo "<div><h1>La ricevuta pdf si trova in {$response->pdf}</h1><br>";
		  echo "<a class=\"button\" href=\"{$response->pdf}\" target=\"_blank\">APRI RICEVUTA</a>";
		  echo "<a class=\"button\" href=\"" . PATH_RICEVUTE . "\" target=\"_blank\">APRI DIRECTORY</a></div>";
		  //echo '<iframe style="width:100%;height:500px;" src="' .$response->pdf . '"></iframe>';
		  
		}
          
        else
            echo 'errore nel prendere la ricevuta';
        
        if($debug){
            echo '<pre>';
            print_r($response);
            echo '</pre>';
        }
    }
    else if ($action == 'errore'){
        $response = $client->DettaglioErrori730Service($protocollo,PATH_RICEVUTE_ERRORI);
        
        if($response->esitoChiamata == 1)
            echo "Codice Risposta $response->codice descrizione $response->descrizione\n";
        else 
            echo "Il file CSV è stato inserito in: ".$response->csv;
                
        if($debug){
            echo '<pre>';
            print_r($response);
            echo '</pre>';
        }
    }

    }catch(Exception $e){
		 echo '<pre>';
         echo $e->getMessage();
		 echo '</pre>';
	}
?>
</body>
</html>