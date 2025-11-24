<?

// la funzione selectdomains fa un elenco di tutti i domini in tb_domini con il flag 
// visible == 1. Inoltre, dove non esiste, crea un percorso base in /var/www/html/new/domini. 
// Infine, se non esiste, crea una cartella per i template.


    mysql_query("truncate table tb_camplinks;");

    $lang_query = "select id_lingua,iso_lingua from tb_lingue_supportate";
    $lang_result = mysql_query($lang_query);
    while ($lang_array = mysql_fetch_array($lang_result)){

      $lngid = $lang_array[0];
      $lngiso = $lang_array[1];
      $langnames[$lngid] = $lngiso;
    }

      $sql = "select distinct indirizzo from tb_pagine_generiche where id_lingua=" . $lang[0] ." or id_lingua=1;";
      $result = mysql_query($sql);
      while ($row = mysql_fetch_row($result))buildgenericpage($row[0],$lang[0]);
	  
	   function ordinamento_strutture($iddominio)
		{
		
		  $queryordinamento="select query from tb_strutture_ordinamento where iddominio=$iddominio and attivo=1";
		$order_result=mysql_query($queryordinamento);
		if ($order_result=mysql_fetch_array($order_result))
			$orderby=$order_result[0];
		else
			$orderby="fg_cliente desc,provincia_struttura desc";
			echo($queryordinamento.$orderby);
			return $orderby;
		
		}


  function selectdomains(){

    echo "lettura dal database domini da creare... ";
    flush();

    $makedomainquery = "select nome_dominio,id_dominio from tb_domini where fg_visible=1;";
    $makedomainresult = mysql_query($makedomainquery);
    $domainarray = array();
    echo "fatto\ncreazione percorsi mancanti... ";
    flush();

    while ($arr = mysql_fetch_array($makedomainresult)){

      $domain = $arr[0];
      $domain_id = $arr[1];
      $path = "/var/www/html/new/domini/" . $domain;
      if (!(is_dir($path)))mkdir($path,0755);
      $path = "/var/www/html/new/images/" . $domain;
      if (!(is_dir($path))){mkdir($path,0755);
        `cp -R /var/www/html/network/campeggi/images/* $path`;
      }
        `cp -R /var/www/html/network/common/images/* $path`;
      $path = "/var/www/html/new/templates/" . $domain;
      if (!(is_dir($path)))mkdir($path,0755);

      array_push($domainarray,$domain);

    } //DEBUG
    $domainarray = array("villaggi-italia.com","campeggievillaggi.it");

    echo "fatto\n";
    flush();

    return $domainarray;

  }


// la funzione makehomepage crea la pagina index.html per il dominio in questione. NB su questo 
// server abbiamo configurato .HTML come script PHP. Cerca il template tmpl_homepage.html nel 
// percorso /var/www/html/new/templates/[DOMINIO]. In mancanza di questo file usa il template di 
// default.

  function makehomepage($domain){

    global $settings, $lingua, $linguatext;

      echo "  LINGUA: ".$linguatext[$lingua] ."\n";
      echo "  creazione home page... ";
      flush();

      $percorsi_query = "select percorso_foto_strutture,percorso_loghi_strutture,percorso_immagini_generiche from tb_domini_impostazioni where id_dominio=" . $domain_id ." and id_lingua=$lingua;";
      $percorsi_result = mysql_query($percorsi_query);
      if (mysql_num_rows($percorsi_result) == 0){
       $percorsi_query = "select percorso_foto_strutture,percorso_loghi_strutture,percorso_immagini_generiche from tb_domini_impostazioni where id_dominio=0 and id_lingua=$lingua;";
       $percorsi_result = mysql_query($percorsi_query);
      }
      $percorsi = mysql_fetch_array($percorsi_result);
      $fotopath = ereg_replace("//","/","/var/www/html/new/domini/$domain/" . $percorsi[0]);
      $logopath = ereg_replace("//","/","/var/www/html/new/domini/$domain/" . $percorsi[1]);
      $imgpath = ereg_replace("//","/","/var/www/html/new/domini/$domain/" . $percorsi[2]);

      if (!(is_dir($fotopath))){
       `mkdir -p $fotopath`;
       `rmdir $fotopath`;
       `ln -s /var/www/html/tcev/photos_struct $fotopath`;
      }
      if (!(is_dir($logopath))){
       `mkdir -p $logopath`;
       `rmdir $logopath`;
       `ln -s /var/www/html/tcev/logos_struct/toexplode/ $logopath`;
      } //DEBUG E DEMO
      if (!(is_dir($imgpath))&& 23 == 234238476){
       `mkdir -p $imgpath`;
       `rmdir $imgpath`; while (substr($imgpath,strlen($imgpath)-1,1) == "/")$imgpath = substr($imgpath,0,strlen($imgpath)-1);
       `ln -s /var/www/html/new/images/$domain/ $imgpath`;
       `ln -s /var/www/html/new/images/$domain/ $imgpath`;

      }



    $path = $settings['percorso_base'];
    $imgpath = $settings['percorso_immagini_generiche'];
    $imglangpath = $settings['percorso_immagini_lingua'];
    $fotopath = $settings['percorso_foto_strutture'];
    $logopath = $settings['percorso_loghi_strutture'];


    // carichiamo il template

//    $template = "/var/www/html/new/templates/" . $domain . "/tmpl_homepage.html";
//    if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_homepage.html";
    $template = "/var/www/html/new/templates/" . $domain . "/index_ita.html";
//    if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_homepage.html";

    $fh = fopen($template,"r");
    $content = fread($fh,filesize($template));
    fclose($fh);

    $template = "/var/www/html/new/templates/" . $domain . "/tmpl_homepage_corpo.html";
    if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_homepage_corpo.html";

    $fh = fopen($template,"r");
    $content = ereg_replace("###-CORPO_PAGINA-###",fread($fh,filesize($template)),$content);
    fclose($fh);

    $linkorder = split("\|",$settings['ordine_link_lingue']);
    $langlinks = "";

    $dom_id_query = "select id_dominio from tb_domini where nome_dominio like \"$domain\";";
    $dom_id_result = mysql_query($dom_id_query);
    $dom_id_arr = mysql_fetch_array($dom_id_result);
    $dom = $dom_id_arr[0];

    $lang_query = "select id_lingua,iso_lingua from tb_lingue_supportate";
    $lang_result = mysql_query($lang_query);
    while ($lang_array = mysql_fetch_array($lang_result)){

      $lngid = $lang_array[0];
      $lngiso = $lang_array[1];
      $lingue[$lngid] = $lngiso;
    }


    foreach ($linkorder as $link){
      $linkpath_query = "select percorso_base from tb_domini_impostazioni where id_dominio=$dom and id_lingua=$link;";
      $linkpath_result = mysql_query($linkpath_query);
      if (mysql_num_rows($linkpath_result) == 0){
        $linkpath_query = "select percorso_base from tb_domini_impostazioni where id_dominio=0 and id_lingua=$link;";
        $linkpath_result = mysql_query($linkpath_query);
      }
      $linkpath_array = mysql_fetch_array($linkpath_result);
      $linkpath = $linkpath_array[0];

      $langlink = $settings['html_language_link'];
      $langlink = ereg_replace("###-LANGLINK-###",$linkpath,$langlink);
      $langlink = ereg_replace("###-LANG-3-CAR-###",$lingue[$link],$langlink);
      if ($linkpath == $path){

         $langlink = preg_replace('/<a[^>]+href[^>]+>/',"",$langlink);
         $langlink = eregi_replace('</a>',"",$langlink);

      }

      $langlinks .= $langlink;

    }

    $imgpath = $settings['percorso_immagini_generiche'];


    $content = ereg_replace("###-PERCORSO_BASE-###/","###-PERCORSO_BASE-###",$content);
    $content = ereg_replace("/###-PERCORSO_BASE-###","###-PERCORSO_BASE-###",$content);
    $content = ereg_replace("###-PERCORSO_REGIONI-###/","###-PERCORSO_REGIONI-###",$content);
    $content = ereg_replace("/###-PERCORSO_REGIONI-###","###-PERCORSO_REGIONI-###",$content);
    $content = ereg_replace("###-PERCORSO_BASE_UE-###/","###-PERCORSO_BASE_UE-###",$content);
    $content = ereg_replace("/###-PERCORSO_BASE_UE-###","###-PERCORSO_BASE_UE-###",$content);
    $content = ereg_replace("###-TITOLO_PAGINA-###",$settings['homepage_titolo'],$content);
    $content = ereg_replace("###-META_DESCRIPTION-###",$settings['homepage_description'],$content);
    $content = ereg_replace("###-META_KEYWORDS-###",$settings['homepage_keywords'],$content);
    $content = ereg_replace("###-NAVBAR-###",$settings['homepage_navigation_bar'],$content);
    $content = ereg_replace("###-PERCORSO_REGIONI-###",$settings['percorso_regioni'],$content);
    $content = ereg_replace("###-PERCORSO_BASE-###",$settings['percorso_base'],$content);
    $content = ereg_replace("###-PERCORSO_BASE_UE-###",$settings['percorso_base_ue'],$content);
    $content = ereg_replace("###-LANGUAGES-###",$langlinks,$content);
    $content = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###/",$imgpath,$content);
    $content = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###/",$imglangpath,$content);
    $content = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###",$imgpath,$content);
    $content = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###",$imglangpath,$content);
    $fotopath = $settings['percorso_foto_strutture'];
    $logopath = $settings['percorso_loghi_strutture'];
    $content = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###/",$imgpath,$content);
    $content = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###/",$imglangpath,$content);
    $content = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###",$imgpath,$content);
    $content = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###",$imglangpath,$content);
    $content = ereg_replace("###-HOMELINK-###",$settings['homelink'],$content);
    $content = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$content);


if ($lingua == 2)
{
$content = ereg_replace("campeggi-","camping-",$content);
}
if ($lingua == 3)
{

$content = ereg_replace("campeggi-","camping-",$content);

   $trans_query = "select id_regione,nome_regione from tb_regioni where id_lingua=1";
   
    $trans_result = mysql_query($trans_query);
    while ($trans = mysql_fetch_array($trans_result))
	{
	
	$idregg = $trans['id_regione'];
	$nomeitareg=$trans['nome_regione'];
	$nomeitareg=str_replace(" ","_",$nomeitareg);
	
	
	$lingua4=3;
	$trans_query2 = "select nome_regione from tb_regioni where rel_id=$idregg and  id_lingua=\"$lingua4\";";
	$trans_result2 = mysql_query($trans_query2);
	
	if($trans2 = mysql_fetch_array($trans_result2))
	{
	
	
	$nomereg = $trans2['nome_regione'];
	$nomereg=str_replace(" ","_",$nomereg);
	$content = str_replace($nomeitareg,$nomereg,$content);
	
	  
	}
	echo($content);
	}
	



}

    $trans_query = "select nome_vocabolo,valore_vocabolo from tb_traduzioni where id_lingua=1;";
    $trans_result = mysql_query($trans_query);
    while ($trans = mysql_fetch_array($trans_result)){
    
      $nome = $trans['nome_vocabolo'];
      $value = $trans['valore_vocabolo'];
                  
      $translate[$nome] = $value;
                  
    }
    $trans_query = "select nome_vocabolo,valore_vocabolo from tb_traduzioni where id_lingua=\"$lingua\";";
    $trans_result = mysql_query($trans_query);
    while ($trans = mysql_fetch_array($trans_result)){
    
      $nome = $trans['nome_vocabolo'];
      $value = $trans['valore_vocabolo'];
                  
      if (strlen($value) > 0)$translate[$nome] = $value;
                  
    }

    $keys = array_keys($translate);
                  
    foreach($keys as $key){

      $string = "###-" . $key . "-###";
      $content = str_replace($string,$translate[$key],$content);
                
    }

    $writepath = "/var/www/html/new/domini/$domain/" . $path; // if ($newpath) $writepath .= "/var/www/html/new/domain/$path/$newpath";
    $outfile = $writepath . "/index.html";
    $outfile = ereg_replace("//","/",$outfile);
    $outfile = ereg_replace("//","/",$outfile);

    $content = banners($content,$outfile);
    $content = unwhite($content);
    $content = ereg_replace("###-ID_STRUTTURA-###",$strucno,$content);

    $fh = fopen($outfile,"w");
	
	
	echo ("scrivo il file ".$outfile);
	
    fputs($fh,$content);
    fclose($fh);
    echo "fatto\n";
    flush();

  }

// la funzione makeuepage è molto simile alla funzione makehomepage, ma crea il percorso 
// specificato in percorso_base ue e usa il template tmpl_homepage_corpo_ue.html invece di 
// tmpl_homepage_corpo.html

  function makeuepage($domain){
    global $settings, $lingua;

    echo "  creazione homepage UE... ";
    flush();

    $path = $settings['percorso_base_ue'];
    $longpath = "/var/www/html/new/domini/$domain/$path";
    if (!(is_dir($longpath)))`mkdir -p $longpath`;
    $imgpath = $settings['percorso_immagini_generiche'];
    $imglangpath = $settings['percorso_immagini_lingua'];
    $fotopath = $settings['percorso_foto_strutture'];
    $logopath = $settings['percorso_loghi_strutture'];

    // carichiamo il template

//    $template = "/var/www/html/new/templates/" . $domain . "/tmpl_homepage.html";
//    if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_homepage.html";
    $template = "/var/www/html/new/templates/" . $domain . "/index_eu.html";

    $fh = fopen($template,"r");
    $content = fread($fh,filesize($template));
    fclose($fh);

    $template = "/var/www/html/new/templates/" . $domain . "/tmpl_homepage_corpo_ue.html";
    if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_homepage_corpo_ue.html";

    $fh = fopen($template,"r");
    $content = ereg_replace("###-CORPO_PAGINA-###",fread($fh,filesize($template)),$content);
    fclose($fh);

    $linkorder = split("\|",$settings['ordine_link_lingue']);
    $langlinks = "";

    $dom_id_query = "select id_dominio from tb_domini where nome_dominio like \"$domain\";";
    $dom_id_result = mysql_query($dom_id_query);
    $dom_id_arr = mysql_fetch_array($dom_id_result);
    $dom = $dom_id_arr[0];

    $lang_query = "select id_lingua,iso from tb_lingue_supportate";
    $lang_result = mysql_query($lang_query);
    while ($lang_array = mysql_fetch_array($lang_result)){

      $lngid = $lang_array[0];
      $lngiso = $lang_array[1];
      $lingue[$lngid] = $lngiso;

    }

    foreach ($linkorder as $link){

      $linkpath_query = "select percorso_base_ue from tb_domini_impostazioni where id_dominio=$dom and id_lingua=$link;";
      $linkpath_result = mysql_query($linkpath_query);
      if (mysql_num_rows($linkpath_result) == 0){
        $linkpath_query = "select percorso_base_ue from tb_domini_impostazioni where id_dominio=0 and id_lingua=$link;";
        $linkpath_result = mysql_query($linkpath_query);
      }
      $linkpath_array = mysql_fetch_array($linkpath_result);
      $linkpath = $linkpath_array[0];

      $langlink = $settings['html_language_link'];
      $langlink = ereg_replace("###-LANGLINK-###",$linkpath,$langlink);
      $langlink = ereg_replace("###-LANG-3-CAR-###",$lingue[$link],$langlink);

      if ($linkpath == $path){

         $langlink = preg_replace('/<a[^>]+href[^>]+>/',"",$langlink);
         $langlink = eregi_replace('</a>',"",$langlink);

      }

      $langlinks .= $langlink;

    }

    $content = ereg_replace("###-PERCORSO_BASE-###/","###-PERCORSO_BASE-###",$content);
    $content = ereg_replace("/###-PERCORSO_BASE-###","###-PERCORSO_BASE-###",$content);
    $content = ereg_replace("###-PERCORSO_BASE_UE-###/","###-PERCORSO_BASE_UE-###",$content);
    $content = ereg_replace("/###-PERCORSO_BASE_UE-###","###-PERCORSO_BASE_UE-###",$content);
    $content = ereg_replace("###-TITOLO_PAGINA-###",$settings['homepage_titolo'],$content);
    $content = ereg_replace("###-META_DESCRIPTION-###",$settings['homepage_description'],$content);
    $content = ereg_replace("###-META_KEYWORDS-###",$settings['homepage_keywords'],$content);
    $content = ereg_replace("###-NAVBAR-###",$settings['homepage_navigation_bar'],$content);
    $content = ereg_replace("###-PERCORSO_BASE-###",$settings['percorso_base'],$content);
    $content = ereg_replace("###-PERCORSO_BASE_UE-###",$settings['percorso_base_ue'],$content);
    $content = ereg_replace("###-LANGUAGES-###",$langlinks,$content);
    $content = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###/",$imgpath,$content);
    $content = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###/",$imglangpath,$content);
    $content = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###",$imgpath,$content);
    $content = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###",$imglangpath,$content);
    $fotopath = $settings['percorso_foto_strutture'];
    $logopath = $settings['percorso_loghi_strutture'];
    $content = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###/",$imgpath,$content);
    $content = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###/",$imglangpath,$content);
    $content = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###",$imgpath,$content);
    $content = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###",$imglangpath,$content);
    $content = ereg_replace("###-HOMELINK-###",$settings['homelink'],$content);

if ($lingua == 2)$content = ereg_replace("campeggi-","camping-",$content);
if ($lingua == 3)$content = ereg_replace("campeggi-","camping-",$content);

    $trans_query = "select nome_vocabolo,valore_vocabolo from tb_traduzioni where id_lingua=\"1\";";
    $trans_result = mysql_query($trans_query);
    while ($trans = mysql_fetch_array($trans_result)){
    
      $nome = $trans['nome_vocabolo'];
      $value = $trans['valore_vocabolo'];
                  
      $translate[$nome] = $value;

                  
    }
    $trans_query = "select nome_vocabolo,valore_vocabolo from tb_traduzioni where id_lingua=\"$lingua\";";
    $trans_result = mysql_query($trans_query);
    while ($trans = mysql_fetch_array($trans_result)){
    
      $nome = $trans['nome_vocabolo'];
      $value = $trans['valore_vocabolo'];
                  
      $translate[$nome] = $value;
      if (strlen($value) > 0)$translate[$nome] = $value;
                  
    }

    $keys = array_keys($translate);
                  
    foreach($keys as $key){

      $string = "###-" . $key . "-###";
      $content = str_replace($string,$translate[$key],$content);
                
    }

    $writepath = "/var/www/html/new/domini/$domain/" . $path; 
// if ($newpath) $writepath .= "/$newpath";
    $outfile = $writepath . "/index.html";
    $outfile = ereg_replace("//","/",$outfile);
    $outfile = ereg_replace("//","/",$outfile); 
// $outlinkfile = ereg_replace("/var/www/html/new/domini/$domain","",$outfile);

    $content = banners($content,$outfile,"Europa");
    $content = unwhite($content);
    $content = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$content);
    $content = ereg_replace("###-ID_STRUTTURA-###",$strucno,$content);

    $fh = fopen($outfile,"w");
    fputs($fh,$content);
    fclose($fh);
    echo "fatto\n";
    flush();

  }

// la funzione loadsettings legge le configurazioni della combinazione dominio/lingua

  function loadsettings($domain,$lingua=1){

    $domain_id_query = "select id_dominio from tb_domini where nome_dominio like \"$domain\";";
    $domain_id_result = mysql_query($domain_id_query);
    $domain_id_array = mysql_fetch_array($domain_id_result);

    $id = $domain_id_array[0];

    $settings_query = "select * from tb_domini_impostazioni where id_dominio=$id and id_lingua=$lingua order by id_lingua;";
    $settings_result = mysql_query($settings_query);
    if (mysql_num_rows($settings_result) == 0){
      $settings_query = "select * from tb_domini_impostazioni where id_dominio=0 and id_lingua=$lingua order by id_lingua;";
      $settings_result = mysql_query($settings_query);
    }
    $settings_array = mysql_fetch_array($settings_result);

    return $settings_array;

  }

// la funzione selectlanguages restituisce una tabella di tutti le lingue 
// NB: per lo scope di questo progetto si definisce una lingua persino il crucco (invece di un difetto)

  function selectlanguages($domain){

    global $linguatext;

    echo "lettura elenco lingue... ";
    flush();

    if ($domain == "campinginitaly.de")$where = " where id_lingua=3";
    elseif ($domain != "campeggievillaggi.it")$where = " where id_lingua=1";
    else $where="";
	
	
	

    $language_query = "select id_lingua,nome_lingua from tb_lingue_supportate$where order by id_lingua;";
    $language_result = mysql_query($language_query);
    $lang = array();
    while ($arr = mysql_fetch_array($language_result)){

      array_push($lang,$arr[0]);
      $num = $arr[0];
      $linguatext[$num] = $arr[1];

    }

    echo "fatto\n";
    flush();

    return $lang;

  }

// La funzione buildregions costruisce gli elenchi regionali (tutti i campeggi di una determinata // regione.

  function buildregions($domain,$regsnum="99999999"){

    mysql_query("update tb_strutture set provincia_struttura=146 where regione_struttura=20;");

    $searchpagedone = 0;

    global $settings, $lingua, $langnames;
    $path="";
    $imglangpath="";
    $field = array();
    $field2 = array();
    $provpage = array();
    $ombreggiatura = array();
    $strucservizi = array();
    $creditcard = array();
    $ristorazione = array();

// load tabelle valori


   $querya = "select id_location,nome_location from tb_location where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $location[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_location from tb_location where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
        if (strlen($arr[1]) > 0)$location[$arrh] = $arr[1];
      }
   }
   $querya = "select id_carta_credito,nome_carta_credito from tb_carte_credito where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $creditcard[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_carta_credito from tb_carte_credito where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
        $creditcard[$arrh] = $arr[1];
      }
   }
   $querya = "select id_carta_credito,nome_carta_credito from tb_carte_credito where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $creditcard[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_carta_credito from tb_carte_credito where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
        $creditcard[$arrh] = $arr[1];
      }
   }
   $querya = "select id_ristorazione,nome_ristorazione from tb_ristorazione where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $ristorazione[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_ristorazione from tb_ristorazione where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
        if (strlen($arr[1]) > 0)$ristorazione[$arrh] = $arr[1];
      }
   }
   $querya = "select id_posteggio,nome_posteggio from tb_posteggi_auto where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $parcheggi[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_posteggio from tb_posteggi_auto where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
        if (strlen($arr[1]) > 0)$parcheggi[$arrh] = $arr[1];
      }
   }
   $querya = "select id_servizio,nome_servizio from tb_servizi_strutture where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $strucservizi[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_servizio from tb_servizi_strutture where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
        if (strlen($arr[1]) > 0)$strucservizi[$arrh] = $arr[1];
      }
   }
   $querya = "select id_ombreggiatura,nome_ombreggiatura from tb_ombreggiatura where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $ombreggiatura[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_ombreggiatura from tb_ombreggiatura where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
        if (strlen($arr[1]) > 0)$ombreggiatura[$arrh] = $arr[1];
      }
   }

// fine load tabelle valori

    $tipo_query = "select * from tb_tipologie_camping where id_lingua=$lingua;";
    $tipo_result = mysql_query($tipo_query);
    $campingtype = array("");
    while ($tipo_arr = mysql_fetch_array($tipo_result)){
      $id = $tipo_arr['rel_id'];
      if ($id == 0)$id = $tipo_arr['id_tipologia'];
      $value = $tipo_arr['nome_tipologia'];
      $campingtype[$id] = $value;
    }

    // restituiamo un elenco di regioni con il quale possiamo lavorare
    // in mancanza del percorso questo viene creato

    $regions_query = "select distinct nome_regione,id_regione,rel_id from tb_regioni where id_lingua=$lingua;";
 if ($regsnum != 99999999)  $regions_query = "select distinct nome_regione,id_regione,rel_id from tb_regioni where id_regione=$regsnum and id_lingua=$lingua;";
    $regions_result = mysql_query($regions_query);
    if (mysql_num_rows($regions_result) == 0){
     $regions_query = "select distinct nome_regione,id_regione from tb_regioni where id_lingua=1;";
 if ($regsnum != 99999999)  $regions_query = "select distinct nome_regione,id_regione from tb_regioni where id_regione=$regsnum and id_lingua=1;";
    $regions_result = mysql_query($regions_query);
}
    if (mysql_num_rows($regions_result) == 0){

      $regions_query = "select distinct nome_regione,id_regione from tb_regioni where id_lingua=1;";
      $regions_result = mysql_query($regions_query);

    }
        echo "  controllo esistenza percorso base per elenchi regioni";
        echo "... ";
        $file = "/var/www/html/new/domini/$domain/" . $settings['percorso_regioni'] . "/";
        $searchfile = "/var/www/html/new/domini/$domain/" . $settings['percorso_base'] . "/search.php";
        $feedbackfile = "/var/www/html/new/domini/$domain/" . $settings['percorso_base'] . "/feedback.php";
        $regionspath = $file;
        if (is_dir($file))echo "esiste";
        else {
          $file = ereg_replace("'","\\'",$file);
          echo "non esiste... ";
          `rm -fR $file`;
          `mkdir -p $file`;
          echo "creato";
          flush();
        }
        echo "\n";

    while ($regions_arr = mysql_fetch_array($regions_result)){

$newregion = ""; $newpath = "";

      if (eregi("/",$regions_arr[0])){

         $pathel = split("/",$regions_arr[0]);
         $pathelcount = count($pathel) -1;
         $newregion = array_pop($pathel);
         $newpath = join("/",$pathel);
        $file = "/var/www/html/new/domini/$domain/" . $settings['percorso_regioni'] . "/$newpath"; // $regionspath = $file;
        if (is_dir($file))echo "esiste";
        else {
          $file = ereg_replace("'","\\'",$file);
          `rm -fR $file`;
          `mkdir -p $file`;
          flush();
        }

      }

      if ($regions_arr[0]){

        echo "  ".$regions_arr[0].":\n";
        echo "  conteggio numero di strutture in questa regione... ";
        $regionis = $regions_arr[1];
        $regionis = $regions_arr[1];
        if ($lingua > 1)$regionis = $regions_arr[2];
//        $region_query = "select * from tb_strutture where regione_struttura = ". $regions_arr[1] ." and fg_visible=1 order by fg_cliente desc, posizione_inelenco, provincia_struttura, localita_struttura, nome_struttura;";
        $region_query = "select t1.*, t2.nome_provincia from tb_strutture as t1, tb_province as t2 where t1.provincia_struttura = t2.id_provincia 
and t1.regione_struttura 
= ". $regionis ." and t1.fg_visible=1 order by t1.fg_cliente desc, t1.posizione_inelenco, t2.nome_provincia, t1.localita_struttura, t1.nome_struttura;";
        $region_result = mysql_query($region_query);
        $region_count = mysql_num_rows($region_result);
        echo $region_count;

        echo "\n";
        echo "  calcolo numero di pagine...";
        $donostructure = 0;
        $pages = round($region_count/$settings['strutture_negli_elenchi']+.5,0);
        if ($region_count % $settings['strutture_negli_elenchi'] == 0)$pages = $region_count/$settings['strutture_negli_elenchi'];
        if ($pages == 0){
          $pages = 1;
          $donostructure = 1;
        }
        echo $pages;

          $template = "/var/www/html/new/templates/" . $domain . "/tmpl_elenco.html";
          if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_elenco.html";
      
          $fh = fopen($template,"r");
          $content = fread($fh,filesize($template));
/*
echo $content;
exit;
*/
          fclose($fh);

        $regione = $regions_arr['nome_regione'];
        if (ereg("/",$regione)) $regione = $newregion;
        $regionid = $regions_arr['id_regione'];
if ($lingua > 1)$regionid = $regions_arr['rel_id'];

#########DA ADATTARE

        $province_query = "select nome_provincia,id_provincia,sigla_provincia from tb_province where id_regione=$regionid and id_lingua=1;";
        $province_result = mysql_query($province_query);
if (mysql_num_rows($province_result) == 0)$content = eregi_replace("province ###-REGIONE-###","",$content);


$provinx = $settings['link_sx_provincia']; $provincelink = ereg_replace("//","/",$settings['percorso_province']."/". $settings['nome_elenchi_provinciali']);
    
        $provincelist = "";
        $provincelistx = "";
        $provinceoptions = "<select name=\"goprov\"><option value=\"\">###-TUTTE LE PROVINCE-###</option>";



        while ($prov = mysql_fetch_array($province_result)){

          $provno = $prov[1];

          if ($provincelist) $provincelist .= ", ";
          $provincelist .= $prov[0];

$provincelink2 = ereg_replace("###-NOME_PROVINCIA-###",urlencode(ereg_replace(" ","_",$prov[0])),$provincelink); $provincelink2 = 
ereg_replace("###-REGIONE-###",urlencode(ereg_replace(" ","_",$regione)),$provincelink2); $provincelink2 = ereg_replace("###-NUMERO_PAGINA-###","",$provincelink2);

$provincelistx .= ereg_replace("###-NOME_PROVINCIA-###",$prov[0],(ereg_replace("###-PROVINCELINK-###",$provincelink2,$provinx)));

          $provno = $prov[1];
          $provsig = $prov[2];
          if ($provsig == "")$provsig = $prov[0];
          $sigle[$provno]=$provsig;
          $provincename[$provno] = $prov[0];

        }

        for ($i=1;$i<=$pages;$i++){
          $page = "$i";

          echo "\n costruzione pagina ". $i . "/$pages... ";
          flush();

          $regionsfile = $settings['nome_elenchi_regionali']; // if ($newpath)$regionsfile .= "/$newregion";
          if ($i > 1)$regionsfile = ereg_replace("###-NUMERO_PAGINA-###","$i",$regionsfile);
          else $regionsfile = ereg_replace("###-NUMERO_PAGINA-###","",$regionsfile);
          if ($newregion)$regionsfile = "/$newpath/" . ereg_replace("###-NOME_REGIONE-###",ereg_replace(" ","_",$newregion),$regionsfile);
          $regionsfile = ereg_replace("###-NOME_REGIONE-###",ereg_replace(" ","_",$regione),$regionsfile);
          $regionsfile = ereg_replace("###-NOME_PROVINCIA-###",ereg_replace(" ","_",$prov[0]),$regionsfile);
          $regionsfile = ereg_replace("###-PROVINCIA-###",ereg_replace(" ","_",$prov[2]),$regionsfile);
          $outfile = $regionspath ."/" .$regionsfile;
    $outfile = ereg_replace("//","/",$outfile);
    $outfile = ereg_replace("//","/",$outfile);
          $outlinkfile = ereg_replace("/var/www/html/new/domini/$domain","",$outfile);


    $linkorder = split("\|",$settings['ordine_link_lingue']);

    $imgpath = $settings['percorso_immagini_generiche'];
        $provinceform = $provinceoptions . "</select>&nbsp;&nbsp;<input type=\"submit\" value=\"###-SCEGLI-###\">";

    $content = ereg_replace("###-PERCORSO_BASE-###/","###-PERCORSO_BASE-###",$content);
    $content = ereg_replace("/###-PERCORSO_BASE-###","###-PERCORSO_BASE-###",$content);
    $content = ereg_replace("###-PERCORSO_BASE_UE-###/","###-PERCORSO_BASE_UE-###",$content);
    $content = ereg_replace("/###-PERCORSO_BASE_UE-###","###-PERCORSO_BASE_UE-###",$content);
    $content = ereg_replace("###-NAVBAR-###",$settings['regione_navigation_bar'],$content);
    $content = ereg_replace("###-PERCORSO_BASE-###",$settings['percorso_base'],$content);
    $content = ereg_replace("###-PERCORSO_BASE_UE-###",$settings['percorso_base_ue'],$content);
    $content = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###/",$imgpath,$content);
    $content = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###/",$imglangpath,$content);
    $content = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###",$imgpath,$content);
    $content = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###",$imglangpath,$content);
    $fotopath = $settings['percorso_foto_strutture'];
    $logopath = $settings['percorso_loghi_strutture'];
    $content = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###/",$imgpath,$content);
    $content = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###/",$imglangpath,$content);
    $content = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###",$imgpath,$content);
    $content = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###",$imglangpath,$content);
    $content = ereg_replace("###-HOMELINK-###",$settings['homelink'],$content);
    $content = ereg_replace("###-TITOLO_ELENCO_REGIONE-###",$settings['regione_titolo_elenco'],$content);
    $content = ereg_replace("###-REGIONE-###","$regione",$content);
    $content = ereg_replace("###-SELEZIONA_PROVINCIA-###","$provinceform",$content);
    $content = ereg_replace("//Toscana","/Toscana",$content);



    $trans_query = "select nome_vocabolo,valore_vocabolo from tb_traduzioni where id_lingua=\"1\";";
    $trans_result = mysql_query($trans_query);
    while ($trans = mysql_fetch_array($trans_result)){
    
      $nome = $trans['nome_vocabolo'];
      $value = $trans['valore_vocabolo'];
                  
      $translate[$nome] = $value; }
    $trans_query = "select nome_vocabolo,valore_vocabolo from tb_traduzioni where id_lingua=\"$lingua\";";
    $trans_result = mysql_query($trans_query);
    while ($trans = mysql_fetch_array($trans_result)){
    
      $nome = $trans['nome_vocabolo'];
      $value = $trans['valore_vocabolo'];
                  
      if (strlen($value) > 0)$translate[$nome] = $value;

}

if ($searchpagedone == 0){
    $searchpage = $content;
    $searchpage = ereg_replace("###-ELENCO_PROVINCE-###","",$searchpage);
    $searchpage = ereg_replace("###-LINK_PROVINCE-###","",$searchpage);
    $searchpage = ereg_replace("###-SITENAV-###","<a href=\"" . $domvars['percorso_base'] . "/\">HOME</a>&nbsp;»&nbsp;###-RICERCA-###",$searchpage);
    $searchpage = banners($searchpage,$searchfile);
    $searchpage = ereg_replace("###-TITOLO_PAGINA-###","###-RISULTATI RICERCA-###",$searchpage);
    $searchpage = ereg_replace("###-META_DESCRIPTION-###","",$searchpage);
    $searchpage = ereg_replace("###-META_KEYWORDS-###","",$searchpage);
    $searchpage = ereg_replace("###-PAGING-###","<?=\$pagestring2?>",$searchpage);
    $searchpage = ereg_replace("background=\"/images/sfondo_suphome.jpg\"","",$searchpage);
    $searchpage = ereg_replace("Campeggi in ###-PROVINCIAREGIONE-###","###-RISULTATI RICERCA-###",$searchpage);
    $searchpage = ereg_replace("###-SCRIVI AI CAMPEGGI IN-### ###-PROVINCIAREGIONE-###","###-SCRIVI_TUTTI_RICERCA-###",$searchpage);
    $searchpage = ereg_replace("###-MAILFORMLINK-###",$settings['percorso_base']."feedback.php?c=<? if (\$c\)echo \"\$c\" . \"&\"; if (\$qid)\$caso = \$qid; else \$caso = 
md5(\$_SERVER['REMOTE_ADDR'] . microtime()); \$qid = \$caso; echo \$qid;?>",$searchpage);




    $searchpage = ereg_replace('<tr height="1">
														<td align="right" valign="middle" bgcolor="black" height="1"></td>
													</tr>
													<tr>
														<td align="right" valign="middle" bgcolor="#005fae" height="30">
															<table border="0" cellspacing="0" cellpadding="0">
																<tr>
																	<td align="left" valign="middle" width="170" height="30"><span class="grandebianco">Province Abruzzo</span></td>
																	<td align="left" valign="middle" width="20" height="30"><img src="/images/ombra_blu.jpg" alt="" width="20" height="30" border="0"></td>
																</tr>
															</table>
														</td>
													</tr>
													',"",$searchpage);


    $keys = array_keys($translate);
    
    foreach($keys as $key){
    
      $string = "###-" . $key . "-###";
      $searchpage = str_replace($string,$translate[$key],$searchpage);
      
    }

    $langlinkssr = "";
    $langlinksml = "";

    $dom_id_query = "select id_dominio from tb_domini where nome_dominio like \"$domain\";";
    $dom_id_result = mysql_query($dom_id_query);
    $dom_id_arr = mysql_fetch_array($dom_id_result);
    $dom = $dom_id_arr[0];

    $lang_query = "select id_lingua,iso_lingua from tb_lingue_supportate";
    $lang_result = mysql_query($lang_query);
    while ($lang_array = mysql_fetch_array($lang_result)){

      $lngid = $lang_array[0];
      $lngiso = $lang_array[1];
      $lingue[$lngid] = $lngiso;

    }

    foreach ($linkorder as $link){
      $linkpath_query = "select percorso_base from tb_domini_impostazioni where id_dominio=$dom and id_lingua=$link;";
      $linkpath_result = mysql_query($linkpath_query);
      if (mysql_num_rows($linkpath_result) == 0){
        $linkpath_query = "select percorso_base from tb_domini_impostazioni where id_dominio=0 and id_lingua=$link;";
        $linkpath_result = mysql_query($linkpath_query);
      }
      $linkpath_array = mysql_fetch_array($linkpath_result);
      $linkpathsr = "/".$linkpath_array[0]. "/search.php?" ."<?=\$_SERVER['QUERY_STRING']?>";
      $linkpathml = "/".$linkpath_array[0]. "/feedback.php?" ."<?=\$_SERVER['QUERY_STRING']?>";

      $linkpathml = ereg_replace("//","/",$linkpathml);
      $linkpathml = ereg_replace("//","/",$linkpathml);
      $linkpathml = ereg_replace("//","/",$linkpathml);
      $linkpathsr = ereg_replace("//","/",$linkpathsr);
      $linkpathsr = ereg_replace("//","/",$linkpathsr);
      $linkpathsr = ereg_replace("//","/",$linkpathsr);


      $langlinksr = $settings['html_language_link'];
      $langlinksr = ereg_replace("###-LANGLINK-###",$linkpathsr,$langlinksr);
      $langlinksr = ereg_replace("###-LANG-3-CAR-###",$langnames[$link],$langlinksr);
      $langlinkml = $settings['html_language_link'];
      $langlinkml = ereg_replace("###-LANGLINK-###",$linkpathml,$langlinkml);
      $langlinkml = ereg_replace("###-LANG-3-CAR-###",$langnames[$link],$langlinkml);


      $langlinkssr .= $langlinksr;
      $langlinksml .= $langlinkml;

    }

    $mailpage = "<?include_once(\"/var/www/html/tcev/feedback.php\")?>";
    $searchpage = ereg_replace("###-LISTA_STRUTTURE-###","<?include_once(\"/var/www/html/tcev/search.php\")?>",$searchpage);


    $searchpage = ereg_replace("###-LANGUAGES-###",$langlinkssr,$searchpage);
    $mailpage = ereg_replace("###-LANGUAGES-###",$langlinksml,$mailpage);


    $searchpage = unwhite($searchpage);
    $mailpage = unwhite($mailpage);
    $searchpage = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$searchpage);
    $mailpage = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$mailpage);

    $fh7 = fopen("$searchfile","w");
    fputs($fh7,$searchpage);
    fclose($fh7);

    $fh7 = fopen("$feedbackfile","w");
    fputs($fh7,$mailpage);
    fclose($fh7);
    $searchpagedone = 1;



















}

    $content = ereg_replace("###-ELENCO_PROVINCE-###","$provincelist",$content);
    $langlinks = "";

    $dom_id_query = "select id_dominio from tb_domini where nome_dominio like \"$domain\";";
    $dom_id_result = mysql_query($dom_id_query);
    $dom_id_arr = mysql_fetch_array($dom_id_result);
    $dom = $dom_id_arr[0];

    $lang_query = "select id_lingua,iso_lingua from tb_lingue_supportate";
    $lang_result = mysql_query($lang_query);
    while ($lang_array = mysql_fetch_array($lang_result)){

      $lngid = $lang_array[0];
      $lngiso = $lang_array[1];
      $lingue[$lngid] = $lngiso;

    }

    foreach ($linkorder as $link){
      $linkpath_query = "select percorso_base,percorso_regioni,nome_elenchi_regionali from tb_domini_impostazioni where id_dominio=$dom and id_lingua=$link;";
      $linkpath_result = mysql_query($linkpath_query);
      if (mysql_num_rows($linkpath_result) == 0){
        $linkpath_query = "select percorso_base,percorso_regioni,nome_elenchi_regionali from tb_domini_impostazioni where id_dominio=0 and id_lingua=$link;";
        $linkpath_result = mysql_query($linkpath_query);
      }
      $linkpath_array = mysql_fetch_array($linkpath_result);
      $linkpath = "/".$linkpath_array[1]."/".$linkpath_array[2];
/*
RIPARAZIONE TEDESCO
*/
      $sql = "select nome_regione from tb_regioni where rel_id=$regionid and id_lingua=$link;";
      $hresult = mysql_query($sql);
      if (mysql_num_rows($hresult) == 0){
       $sql = "select nome_regione from tb_regioni where id_regione=$regionid and id_lingua=1;";
       $hresult = mysql_query($sql);
      }
      $rw = mysql_fetch_row($hresult);
      $rwh = ereg_replace(" ","_",ucwords(strtolower($rw[0])));
      $linkpath = ereg_replace("###-REGIONE-###",$rwh,$linkpath);
      $linkpath = ereg_replace("###-NOME_REGIONE-###",$rwh,$linkpath);
      $linkpath = ereg_replace("###-NUMERO_PAGINA-###","",$linkpath);
      $linkpath = ereg_replace(" ","_",$linkpath);
      $linkpath = ereg_replace("//","/",$linkpath);
      $linkpath = ereg_replace("//","/",$linkpath);
      $linkpath = ereg_replace("//","/",$linkpath);

      $langlink = $settings['html_language_link'];
      $langlink = ereg_replace("###-LANGLINK-###",$linkpath,$langlink);
      $langlink = ereg_replace("###-LANG-3-CAR-###",$langnames[$link],$langlink);

      if ($linkpath == $path){

         $langlink = preg_replace('/<a[^>]+href[^>]+>/',"",$langlink);
         $langlink = eregi_replace('</a>',"",$langlink);

      }

      $langlinks .= $langlink;

    }


    $content = ereg_replace("###-MAILFORMLINK-###",$settings['percorso_base']."feedback.php?regione=$regionid",$content);
    if ($regionis == 20)$content = ereg_replace("###-LINK_PROVINCE-###","",$content);
    $content = ereg_replace("###-LINK_PROVINCE-###",$provincelistx,$content);
    $content = ereg_replace("###-TITOLO_PAGINA-###",$settings['regione_titolo'],$content);
    $content = ereg_replace("###-META_DESCRIPTION-###",$settings['regione_description'],$content);
    $content = ereg_replace("###-META_KEYWORDS-###",$settings['regione_keywords'],$content);
    $content = ereg_replace("###-LANGUAGES-###",$langlinks,$content);

// $content = banners($content);




$pagestring = $settings['paging_html']; 
$pagestring = ereg_replace("###-PAGINA_ATTUALE-###","$i",$pagestring); 
$pagestring = ereg_replace("###-NUMERO_PAGINE-###","$pages",$pagestring);

$pagestring2 = ""; 
$plink = $settings['paging_link_pagina']; 
$plink = ereg_replace("###-REGIONE-###",ereg_replace(" ","_",$regione),$plink);
$alink = $settings['paging_pagina_attiva'];
$pagestring = ereg_replace(" ","_",$pagestring);
$pagestring2 = ereg_replace(" ","_",$pagestring2);

  $lastpage = $i-1; if ($lastpage == 1)$lastpage = "";
  $nextpage = $i+1;

if ($i > 2) $pagestring2 .= ereg_replace("###-PAGINA-###","",ereg_replace("###-SEGNO-###","«",$plink)); 
if ($i > 1) $pagestring2 .= ereg_replace("###-PAGINA-###","$lastpage",ereg_replace("###-SEGNO-###","‹",$plink)); 
if ($i == 4)$pagestring2 .= ereg_replace("###-PAGINA-###","",ereg_replace("###-SEGNO-###","1",$plink)); 
//if ($i > 4)$pagestring2 .= ereg_replace("###-ATTIVA-###","...",$alink);
  for($j=1;$j<=$pages;$j++){

  
    if ($j == $i)$pagestring2 .= ereg_replace("###-ATTIVA-###","$j",$alink);
    elseif ($j + 3 > $i && $j-3 < $i && $j == 1)$pagestring2 .= ereg_replace("###-PAGINA-###","",ereg_replace("###-SEGNO-###","$j",$plink));
    elseif ($j + 3 > $i && $j-3 < $i)$pagestring2 .= ereg_replace("###-PAGINA-###","$j",ereg_replace("###-SEGNO-###","$j",$plink));

  }
   
//if ($i < $pages-3)$pagestring2 .= ereg_replace("###-ATTIVA-###","...",$alink);
 if ($i == $pages-3)$pagestring2 .= ereg_replace("###-PAGINA-###","$pages",ereg_replace("###-SEGNO-###","$pages",$plink));
 if ($i < $pages)$pagestring2 .= ereg_replace("###-PAGINA-###","$nextpage",ereg_replace("###-SEGNO-###","›",$plink));
 if ($i + 1 < $pages)$pagestring2 .= ereg_replace("###-PAGINA-###","$pages",ereg_replace("###-SEGNO-###","»",$plink));
 if ($pages <= 1)$pagestring = ereg_replace("###-PAGING_LINKS-###","",$pagestring);
 else $pagestring = ereg_replace("###-PAGING_LINKS-###",$pagestring2,$pagestring);

//$pagestring2 = ereg_replace("###-REGIONE-###",ereg_replace(" ","_",$regione),$pagestring2);
#$pagestring = ereg_replace(" ","_",$pagestring);
#$pagestring2 = ereg_replace(" ","_",$pagestring2);

    $navpath = "<a href=\"/" . $settings['percorso_base'] . "\">HOME</a>&nbsp;»&nbsp;" . $settings['toplink_regione'];
    $navpath = ereg_replace("//","/",$navpath);
    $navpath = ereg_replace("//","/",$navpath);
    $navpath = ereg_replace("http:/www.","http://www.",$navpath);
    $navpath = ereg_replace("###-REGIONE-###","$regione",$navpath);

    $content = ereg_replace("###-SITENAV-###",$navpath,$content);

    $listcontent = "";

    $template = "/var/www/html/new/templates/" . $domain . "/tmpl_scheda_base.html";
    if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_scheda_base.html";

    $fh = fopen($template,"r");
    $schedabase = fread($fh,filesize($template));
    fclose($fh);

    $template = "/var/www/html/new/templates/" . $domain . "/tmpl_viplisting.html";
    if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_viplisting.html";

    $fh = fopen($template,"r");
    $tempvip = fread($fh,filesize($template));
    fclose($fh);

    $template = "/var/www/html/new/templates/" . $domain . "/tmpl_noviplisting.html";
    if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_noviplisting.html";

    $fh = fopen($template,"r");
    $tempnovip = fread($fh,filesize($template));
    fclose($fh);


// QUI CODICE PER LIMITARE IL NUMERO DI RISULTATI A PAGINA

$lima = $settings['strutture_negli_elenchi']; $limb = ($i - 1)*$lima;

//*******MODIFICA BRAIN COMPUTING**************

	$orderby=ordinamento_strutture($dom);
	
	
	
	$region_query = "select t1.*, t2.nome_provincia from tb_strutture as t1, tb_province as t2 where t1.provincia_struttura = t2.id_provincia and t1.regione_struttura = ". $regionis ." and t1.fg_visible=1 order by ".$orderby." limit $limb,$lima;";	
	
//*********** FINE MODIFICA ********************


        $region_result = mysql_query($region_query); $fields2 = array();

    while ($jk < mysql_num_fields($region_result)){
      $meta = mysql_fetch_field($region_result, $jk);
      array_push($fields2,$meta->name);
      $jk++;
    }

        $region_result = mysql_query($region_query);
    while ($camplisting = mysql_fetch_array($region_result)){

       if ($camplisting['fg_cliente']>0){$temp = $tempvip;$clientflag = "1";}
       else {$temp = $tempnovip;$clientflag="";}
       $temp = ereg_replace("###-NOME_STRUTTURA-###",eregi_replace("!!!+[[:alnum:]|_]+!!!","",$camplisting['nome_struttura']),$temp);
       $f2 = "/var/www/html/new/domini/$domain/links/" .$camplisting['id_struttura'].".LISTLNK." . $langnames[$lingua];
       $temp = ereg_replace("###-LINK_OFFERTE-###","<?if (is_file(\"$f2\"))include(\"$f2\");?>",$temp);
       $temp = ereg_replace("###-CAP-###",$camplisting['cap_struttura'],$temp);
       $temp = ereg_replace("###-LOCALITA-###",ucwords(strtolower($camplisting['localita_struttura'])),$temp);
       $f2 = "/var/www/html/new/domini/$domain/links/" .$camplisting['id_struttura'].".LISTLNK." . $langnames[$lingua];
       $temp = ereg_replace("###-LINK_OFFERTE-###","<?if (is_file(\"$f2\"))include(\"$f2\");?>",$temp);
       $pn = $camplisting['provincia_struttura'];
       if ($pn > 0 && array_key_exists($pn,$sigle))$temp = ereg_replace("###-PROVINCIA-###","(".$sigle[$pn].")",$temp);
       else $temp = ereg_replace("###-PROVINCIA-###","",$temp);
       $temp = ereg_replace("###-INDIRIZZO-###",$camplisting['indirizzo_struttura'],$temp);
       $temp = ereg_replace("###-TEL_ESTIVO_PREF-###",$camplisting['tel_estivo_pref'],$temp);
       $temp = ereg_replace("###-TEL_ESTIVO-###",$camplisting['tel_estivo'],$temp);
       if ($camplisting['fax_estivo']){




         if ($camplisting['fax_estivo_pref'])$temp = ereg_replace("###-FAX_ESTIVO_PREF-###",$camplisting['fax_estivo_pref'],$temp);
         else $temp = ereg_replace("###-FAX_ESTIVO_PREF-###",$camplisting['tel_estivo_pref'],$temp);
         $temp = ereg_replace("###-FAX_ESTIVO-###",$camplisting['fax_estivo'],$temp);
       }
       else{
         $temp = ereg_replace("###-FAX_ESTIVO_PREF-###","",$temp);
         $temp = ereg_replace("###-FAX_ESTIVO-###","",$temp);
         $temp = ereg_replace("###-FAX-###","",$temp);
       }
	   
	     $sitointernet=$camplisting['sito_web'];
	  
	  if ($sitointernet!="" and $sitointernt!="null")	  
	  $temp = str_replace("###-SITOWEBLINK-###","http://".$camplisting['sito_web'],$temp);
	  else
	  $temp = str_replace("###-SITOWEBLINK-###","#",$temp);
	  $temp = str_replace("###-SITOWEB-###","SITO WEB",$temp);
	   
       $schedapath = "/" . $settings['percorso_base'] . "/" . $settings['percorso_schede'];
       $schedapath = ereg_replace("//","/",$schedapath);
       $schedaname = ereg_replace("###-REGIONE-###","$regione",$settings['nome_schede']);
       $schedapricename = ereg_replace("###-REGIONE-###","$regione",$settings['nome_schede_tariffe']);
       $schedamailname = ereg_replace("###-REGIONE-###","$regione",$settings['nome_schede_mail']);
       $schedaoffertename = ereg_replace("###-REGIONE-###","$regione",$settings['nome_schede_offerte']);
       $schedaofferte2name = ereg_replace("###-REGIONE-###","$regione",$settings['nome_dettagli_offerte']);
       $schedapath = ereg_replace("###-REGIONE-###","$regione",$schedapath);
       $pn = $camplisting['provincia_struttura']; if(!array_key_exists($pn,$provpage))$provpage[$pn] = 0; $provpage[$pn] = strval($provpage[$pn]) + 1;
       if ($pn>0 && array_key_exists($pn,$sigle))$schedaname = ereg_replace("###-PROVINCIA-###",$sigle[$pn],$schedaname);
       else $schedaname = ereg_replace("###-PROVINCIA-###","",$schedaname);
       if ($pn>0 && array_key_exists($pn,$sigle))$schedapricename = ereg_replace("###-PROVINCIA-###",$sigle[$pn],$schedapricename);
       else $schedapricename = ereg_replace("###-PROVINCIA-###","",$schedapricename);
       if ($pn>0 && array_key_exists($pn,$sigle))$schedamailname = ereg_replace("###-PROVINCIA-###",$sigle[$pn],$schedamailname);
       else $schedamailname = ereg_replace("###-PROVINCIA-###","",$schedamailname);
       if ($pn>0 && array_key_exists($pn,$sigle))$schedaoffertename = ereg_replace("###-PROVINCIA-###",$sigle[$pn],$schedaoffertename);
       else $schedaoffertename = ereg_replace("###-PROVINCIA-###","",$schedaoffertename);
       if ($pn>0 && array_key_exists($pn,$sigle))$schedaofferte2name = ereg_replace("###-PROVINCIA-###",$sigle[$pn],$schedaofferte2name);
       else $schedaoffert2ename = ereg_replace("###-PROVINCIA-###","",$schedaofferte2name);
       if ($pn>0 && array_key_exists($pn,$sigle))$schedapath = ereg_replace("###-PROVINCIA-###",$sigle[$pn],$schedapath);
       else $schedapath = ereg_replace("###-PROVINCIA-###","",$schedapath); 
       /* $schedaname = ereg_replace("###-NOME-###",eregi_replace("!!!+[[:alnum:]|_]+!!!","",$camplisting['nome_struttura']),$schedaname);
       $schedapricename = ereg_replace("###-NOME-###",eregi_replace("!!!+[[:alnum:]|_]+!!!","",$camplisting['nome_struttura']),$schedapricename);
       $schedaoffertename = ereg_replace("###-NOME-###",eregi_replace("!!!+[[:alnum:]|_]+!!!","",$camplisting['nome_struttura']),$schedaoffertename);
       $schedaofferte2name = ereg_replace("###-NOME-###",eregi_replace("!!!+[[:alnum:]|_]+!!!","",$camplisting['nome_struttura']),$schedaofferte2name);
       $schedamailname = ereg_replace("###-NOME-###",eregi_replace("!!!+[[:alnum:]|_]+!!!","",$camplisting['nome_struttura']),$schedamailname); */




       $schedaname = ereg_replace("###-NOME-###",eregi_replace("_.htm",".htm",eregi_replace("!!!","_",$camplisting['nome_struttura'])),$schedaname);
       $schedapricename = ereg_replace("###-NOME-###",eregi_replace("_.htm",".htm",eregi_replace("!!!","_",$camplisting['nome_struttura'])),$schedapricename);
       $schedamailname = ereg_replace("###-NOME-###",eregi_replace("_.htm",".htm",eregi_replace("!!!","_",$camplisting['nome_struttura'])),$schedamailname);
       $schedaoffertename = ereg_replace("###-NOME-###",eregi_replace("_.htm",".htm",eregi_replace("!!!","_",$camplisting['nome_struttura'])),$schedaoffertename);
       $schedaofferte2name = ereg_replace("###-NOME-###",eregi_replace("_.htm",".htm",eregi_replace("!!!","_",$camplisting['nome_struttura'])),$schedaofferte2name);
       $schedapath = ereg_replace("###-NOME-###",eregi_replace("!!!+[[:alnum:]|_]+!!!","",$camplisting['nome_struttura']),$schedapath);
       $schedapath=ereg_replace("!!!","_",$schedapath);
       $schedapath=ereg_replace("_.htm",".htm",$schedapath);
       $schedaname = rawurlencode(ereg_replace(" ","_",$schedaname));
       $schedapricename = rawurlencode(ereg_replace(" ","_",$schedapricename));
       $schedamailname = rawurlencode(ereg_replace(" ","_",$schedamailname));
       $schedaoffertename = rawurlencode(ereg_replace(" ","_",$schedaoffertename));
       $schedaofferte2name = rawurlencode(ereg_replace(" ","_",$schedaofferte2name));
       $schedapath = ereg_replace(" ","_",$schedapath);
       $schedapricepath = ereg_replace(" ","_",$schedapath);
       $schedamailpath = ereg_replace(" ","_",$schedapath);
       $schedaoffertepath = ereg_replace(" ","_",$schedapath);
       $schedaofferte2path = ereg_replace(" ","_",$schedapath);
       $schedapath2 = $schedapath;
       $schedapath3 = ereg_replace("'","\'",$schedapath);
       `mkdir -p /var/www/html/new/domini/$domain$schedapath3`; // `touch /var/www/html/new/domini/$domain$schedapath3/index.html`;
       $schedaname=ereg_replace("_.htm",".htm",$schedaname);
       $schedapricename=ereg_replace("_.htm",".htm",$schedapricename);
       $schedamailname=ereg_replace("_.htm",".htm",$schedamailname);
       $schedaoffertename=ereg_replace("_.htm",".htm",$schedaoffertename);
       $schedaofferte2name=ereg_replace("_.htm",".htm",$schedaofferte2name);
       $schedaname=ereg_replace("_-","-",$schedaname);
       $schedapricename=ereg_replace("_-","-",$schedapricename);
       $schedamailname=ereg_replace("_-","-",$schedamailname);
       $schedaoffertename=ereg_replace("_-","-",$schedaoffertename);
       $schedaofferte2name=ereg_replace("_-","-",$schedaofferte2name);



       $myoutlink1 = $schedapath. "/" . $schedaname;
       $myoutlink2 = $schedapath. "/" . $schedapricename;
       $myoutlink3 = $schedapath. "/" . $schedamailname;
       $myoutlink4 = $schedapath. "/" . $schedaoffertename;
       $myoutlink5 = $schedapath. "/" . $schedaofferte2name;
       $myoutlink1 = ereg_replace("//","/",$myoutlink1);
       $myoutlink2 = ereg_replace("//","/",$myoutlink2);
       $myoutlink3 = ereg_replace("//","/",$myoutlink3);
       $myoutlink4 = ereg_replace("//","/",$myoutlink4);
       $myoutlink5 = ereg_replace("//","/",$myoutlink5);
       $schedapath .= "/" . $schedaname;
       $schedapath = ereg_replace("//","/",$schedapath);
       $schedapricepath .= "/" . $schedapricename;
       $schedapricepath = ereg_replace("//","/",$schedapricepath);
       $schedamailpath .= "/" . $schedamailname;
       $schedamailpath = ereg_replace("//","/",$schedamailpath);
       $schedaoffertepath .= "/" . $schedaoffertename;
       $schedaoffertepath = ereg_replace("//","/",$schedaoffertepath);
       $schedaofferte2path .= "/" . $schedaofferte2name;
       $schedaofferte2path = ereg_replace("//","/",$schedaofferte2path);
       $schedapath2 .= "/" . $schedaname;
       $schedapath2 = ereg_replace("//","/",$schedapath2);
       $schedapath = ereg_replace("_-","-",$schedapath);
       $schedapricepath = ereg_replace("_-","-",$schedapricepath);
       $schedamailpath = ereg_replace("_-","-",$schedamailpath);
       $schedaoffertepath = ereg_replace("_-","-",$schedaoffertepath);
       $schedaofferte2path = ereg_replace("_-","-",$schedaofferte2path);
       $schedapath = ereg_replace("_-","-",$schedapath);
       $schedapricepath = ereg_replace("_-","-",$schedapricepath);
       $schedamailpath = ereg_replace("_-","-",$schedamailpath);
       $schedaoffertepath = ereg_replace("_-","-",$schedaoffertepath);
       $schedaofferte2path = ereg_replace("_-","-",$schedaofferte2path);
       $temp = ereg_replace("###-SCHEDALINK-###",$schedapath,$temp);
       $temp = ereg_replace("###-SCHEDAPRICELINK-###",$schedapricepath,$temp);
       $temp = ereg_replace("###-SCHEDAMAILLINK-###",$schedamailpath,$temp);
       $temp = ereg_replace("###-SCHEDAOFFERTELINK-###",$schedaoffertepath,$temp);
       $temp = ereg_replace("###-SCHEDAOFFERTEDETAILSLINK-###",$schedaofferte2path,$temp);


############################ LANGLINKS DEBUG
      
      
    $langlinks_1 = "";
    $langlinks_2 = "";
    $langlinks_3 = "";
    $langlinks_4 = "";
    $langlinks_5 = "";
        
    $dom_id_query = "select id_dominio from tb_domini where nome_dominio like \"$domain\";";
    $dom_id_result = mysql_query($dom_id_query);
    $dom_id_arr = mysql_fetch_array($dom_id_result);
    $dom = $dom_id_arr[0];
      
    $lang_query = "select id_lingua,iso_lingua from tb_lingue_supportate";
    $lang_result = mysql_query($lang_query);
    while ($lang_array = mysql_fetch_array($lang_result)){
       
      $lngid = $lang_array[0];
      $lngiso = $lang_array[1];
      $lingue[$lngid] = $lngiso;
      
    }
      
    foreach ($linkorder as $link){
      $linkpath_query = "select percorso_base,percorso_schede,nome_schede,nome_schede_tariffe,nome_schede_mail,nome_schede_offerte,nome_dettagli_offerte from 
tb_domini_impostazioni where id_dominio=$dom and id_lingua=$link;";
      $linkpath_result = mysql_query($linkpath_query);
      if (mysql_num_rows($linkpath_result) == 0){ 
// $linkpath_query = "select percorso_base,percorso_schede,nome_elenchi_regionali from tb_domini_impostazioni where id_dominio=0 and id_lingua=$link;";
        $linkpath_query = "select percorso_base,percorso_schede,nome_schede,nome_schede_tariffe,nome_schede_mail,nome_schede_offerte,nome_dettagli_offerte from tb_domini_impostazioni where id_dominio=0 and id_lingua=$link;";
        $linkpath_result = mysql_query($linkpath_query);
      }
      $linkpath_array2 = mysql_fetch_array($linkpath_result); print mysql_error();

      $linkpath_1 = "/".$linkpath_array2[0]."/".$linkpath_array2[1]."/".$linkpath_array2[2];
      $linkpath_2 = "/".$linkpath_array2[0]."/".$linkpath_array2[1]."/".$linkpath_array2[3];
      $linkpath_3 = "/".$linkpath_array2[0]."/".$linkpath_array2[1]."/".$linkpath_array2[4];
      $linkpath_4 = "/".$linkpath_array2[0]."/".$linkpath_array2[1]."/".$linkpath_array2[5];
      $linkpath_5 = "/".$linkpath_array2[0]."/".$linkpath_array2[1]."/".$linkpath_array2[6];
/*
RIPARAZIONE TEDESCO 1000 era $link
*/

      $sql = "select nome_regione from tb_regioni where rel_id=$regionid and id_lingua=$link;";
      $hresult = mysql_query($sql);
      if (mysql_num_rows($hresult) == 0){
       $sql = "select nome_regione from tb_regioni where id_regione=$regionid and id_lingua=1;";
       $hresult = mysql_query($sql);
      }
      $rw = mysql_fetch_row($hresult);
      $rwh = ereg_replace(" ","_",ucwords(strtolower($rw[0])));
      $linkpath_1 = ereg_replace("###-REGIONE-###",$rwh,$linkpath_1);
      $linkpath_1 = ereg_replace("###-PROVINCIA-###",$sigle[$pn],$linkpath_1);
      $linkpath_1 = ereg_replace("###-NOME_REGIONE-###",$rwh,$linkpath_1);
      $linkpath_1 = ereg_replace("###-NUMERO_PAGINA-###","",$linkpath_1);
      $linkpath_1 = ereg_replace("###-NOME-###",rawurlencode(ereg_replace(" ","_",eregi_replace("!!!","_",eregi_replace("_.htm",".htm",$camplisting['nome_struttura'])))),$linkpath_1);
      $linkpath_1 = eregi_replace("_.htm",".htm",$linkpath_1);
      $linkpath_1 = eregi_replace("_-","-",$linkpath_1);

      $linkpath_1 = ereg_replace("//","/",$linkpath_1);
      $linkpath_1 = ereg_replace("//","/",$linkpath_1);
      $linkpath_1 = ereg_replace("//","/",$linkpath_1);

      $langlink_1 = $settings['html_language_link'];
      $langlink_1 = ereg_replace("###-LANGLINK-###",$linkpath_1,$langlink_1);
      $langlink_1 = ereg_replace("###-LANG-3-CAR-###",$langnames[$link],$langlink_1);
      
      $langlinks_1 .= $langlink_1;

      $linkpath_2 = ereg_replace("###-REGIONE-###",$rwh,$linkpath_2);
      $linkpath_2 = ereg_replace("###-PROVINCIA-###",$sigle[$pn],$linkpath_2);
      $linkpath_2 = ereg_replace("###-NOME_REGIONE-###",$rwh,$linkpath_2);
      $linkpath_2 = ereg_replace("###-NUMERO_PAGINA-###","",$linkpath_2); 
// $linkpath_2 = ereg_replace("###-NOME-###",rawurlencode(ereg_replace(" ","_",$camplisting['nome_struttura'])),$linkpath_2);
      $linkpath_2 = ereg_replace("###-NOME-###",rawurlencode(ereg_replace(" ","_",eregi_replace("!!!","_",eregi_replace("_.htm",".htm",$camplisting['nome_struttura'])))),$linkpath_2);
      $linkpath_2 = eregi_replace("_.htm",".htm",$linkpath_2);
      $linkpath_2 = eregi_replace("_-","-",$linkpath_2);
      $linkpath_2 = ereg_replace("//","/",$linkpath_2);
      $linkpath_2 = ereg_replace("//","/",$linkpath_2);
      $linkpath_2 = ereg_replace("//","/",$linkpath_2);

      $langlink_2 = $settings['html_language_link'];
      $langlink_2 = ereg_replace("###-LANGLINK-###",$linkpath_2,$langlink_2);
      $langlink_2 = ereg_replace("###-LANG-3-CAR-###",$langnames[$link],$langlink_2);
      
      $langlinks_2 .= $langlink_2;
      
      $linkpath_3 = ereg_replace("###-REGIONE-###",$rwh,$linkpath_3);
      $linkpath_3 = ereg_replace("###-PROVINCIA-###",$sigle[$pn],$linkpath_3);
      $linkpath_3 = ereg_replace("###-NOME_REGIONE-###",$rwh,$linkpath_3);
      $linkpath_3 = ereg_replace("###-NUMERO_PAGINA-###","",$linkpath_3); 
// $linkpath_3 = ereg_replace("###-NOME-###",rawurlencode(ereg_replace(" ","_",$camplisting['nome_struttura'])),$linkpath_3);
      $linkpath_3 = ereg_replace("###-NOME-###",rawurlencode(ereg_replace(" ","_",eregi_replace("!!!","_",eregi_replace("_.htm",".htm",$camplisting['nome_struttura'])))),$linkpath_3);
      $linkpath_3 = eregi_replace("_.htm",".htm",$linkpath_3);
      $linkpath_3 = eregi_replace("_-","-",$linkpath_3);
      $linkpath_3 = ereg_replace("//","/",$linkpath_3);
      $linkpath_3 = ereg_replace("//","/",$linkpath_3);
      $linkpath_3 = ereg_replace("//","/",$linkpath_3);

      $langlink_3 = $settings['html_language_link'];
      $langlink_3 = ereg_replace("###-LANGLINK-###",$linkpath_3,$langlink_3);
      $langlink_3 = ereg_replace("###-LANG-3-CAR-###",$langnames[$link],$langlink_3);
      
      $langlinks_3 .= $langlink_3;
      
      $linkpath_4 = ereg_replace("###-REGIONE-###",$rwh,$linkpath_4);
      $linkpath_4 = ereg_replace("###-PROVINCIA-###",$sigle[$pn],$linkpath_4);
      $linkpath_4 = ereg_replace("###-NOME_REGIONE-###",$rwh,$linkpath_4);
      $linkpath_4 = ereg_replace("###-NUMERO_PAGINA-###","",$linkpath_4); 
// $linkpath_4 = ereg_replace("###-NOME-###",rawurlencode(ereg_replace(" ","_",$camplisting['nome_struttura'])),$linkpath_4);
      $linkpath_4 = ereg_replace("###-NOME-###",rawurlencode(ereg_replace(" ","_",eregi_replace("!!!","_",eregi_replace("_.htm",".htm",$camplisting['nome_struttura'])))),$linkpath_4);
      $linkpath_4 = eregi_replace("_.htm",".htm",$linkpath_4);
      $linkpath_4 = eregi_replace("_-","-",$linkpath_4);
      $linkpath_4 = ereg_replace("//","/",$linkpath_4);
      $linkpath_4 = ereg_replace("//","/",$linkpath_4);
      $linkpath_4 = ereg_replace("//","/",$linkpath_4);

      $langlink_4 = $settings['html_language_link'];
      $langlink_4 = ereg_replace("###-LANGLINK-###",$linkpath_4,$langlink_4);
      $langlink_4 = ereg_replace("###-LANG-3-CAR-###",$langnames[$link],$langlink_4);
      
      $langlinks_4 .= $langlink_4;

      $linkpath_5 = ereg_replace("###-REGIONE-###",$rwh,$linkpath_5);
      $linkpath_5 = ereg_replace("###-PROVINCIA-###",$sigle[$pn],$linkpath_5);
      $linkpath_5 = ereg_replace("###-NOME_REGIONE-###",$rwh,$linkpath_5);
      $linkpath_5 = ereg_replace("###-NUMERO_PAGINA-###","",$linkpath_5); 
// $linkpath_5 = ereg_replace("###-NOME-###",rawurlencode(ereg_replace(" ","_",$camplisting['nome_struttura'])),$linkpath_5);
      $linkpath_5 = ereg_replace("###-NOME-###",rawurlencode(ereg_replace(" ","_",eregi_replace("!!!","_",eregi_replace("_.htm",".htm",$camplisting['nome_struttura'])))),$linkpath_5);
      $linkpath_5 = eregi_replace("_.htm",".htm",$linkpath_5);
      $linkpath_5 = eregi_replace("_-","-",$linkpath_5);
      $linkpath_5 = ereg_replace("//","/",$linkpath_5);
      $linkpath_5 = ereg_replace("//","/",$linkpath_5);
      $linkpath_5 = ereg_replace("//","/",$linkpath_5);

      $langlink_5 = $settings['html_language_link'];
      $langlink_5 = ereg_replace("###-LANGLINK-###",$linkpath_5,$langlink_5);
      $langlink_5 = ereg_replace("###-LANG-3-CAR-###",$langnames[$link],$langlink_5);
      
      $langlinks_5 .= $langlink_5;
      

    }
      
      
      
############################ LANGLINKS DEBUG (fine)


       $stars = $camplisting['stelle_n'];
       $starhtml = "";
       for ($k=0;$k<$stars;$k++)$starhtml .= $settings['html_stella'];
       $temp = ereg_replace("###-STELLE-###",$starhtml,$temp);



      $strucno = $camplisting['id_struttura'];

      $abstract_query = "select abstract_struttura,presentazione_struttura,listino_prezzi from tb_strutture_specifiche where id_struttura=$strucno and id_lingua=$lingua;";
      $abstract_result = mysql_query($abstract_query);
      $row = mysql_fetch_row($abstract_result);
//      if (mysql_num_rows($abstract_result) == 0){
        $abstract_query = "select abstract_struttura,presentazione_struttura,listino_prezzi from tb_strutture_specifiche where id_struttura=$strucno and id_lingua=1;";
        $abstract_result = mysql_query($abstract_query);
//      }
      $row2 = mysql_fetch_row($abstract_result);
      if ($row[0] == "")$row[0] = $row2[0];
      if ($row[1] == "")$row[1] = $row2[1];
      if ($row[2] == "")$row[2] = $row2[2];
      $abstract = $row[0];
      $listino = $row[2];
      if (strlen($abstract) == 0){

          $maxablen = 260;
          $abstract = substr($row[1],0,$maxablen);
          $len = strlen($abstract) -1;
          $lchar =substr($abstract,$len,1);
          if (!ereg("\.",$abstract))$abstract .= "...";
          else {while ($lchar <> '.' && $maxablen > 0){
            $maxablen -= 1;
            $abstract = substr($row[1],0,$maxablen);
            $len = strlen($abstract) -1;
            $lchar =substr($abstract,$len,1); // $abstract=substr($row[0],0,$maxablen);
             }
          }
      }

       $temp = ereg_replace("###-ABSTRACT-###",$abstract,$temp);

      $logo_query = "select nome_foto,id_foto from tb_foto where id_struttura=$strucno and fg_logo>0;";
      $logo_result = mysql_query($logo_query);
      $logo_arr = mysql_fetch_array($logo_result);
      $logoname = rawurlencode($logo_arr[0]);
      $fotid = $logo_arr[1];

// codice importazione vecchio sistema

//      if ((!$logoname)){

//      $logohelp_query = "select tag,tipo,titolo from ge_images where subgruppo=\"IMG_LOGO_P$strucno\";";
//      $logohelp_result = mysql_query($logohelp_query);
//      $logohelp_arr = mysql_fetch_array($logohelp_result);
//      $lhtag = $logohelp_arr['tag'];
//      $lhtipo = $logohelp_arr['tipo'];
//      $lhnome = $logohelp_arr['titolo']; //if ($lhnome && $lhtag && $lhtipo){
/*
      $provatt = $camplisting['provincia_struttura'];
      if (array_key_exists($provatt,$sigle))$provnameatt = $sigle[$provatt];
      else $provnameatt = "";
      $newname = ereg_replace(" ","_","$regione") ."_" . $provnameatt ."_".ereg_replace(" ","_",$lhnome) . ".$lhtipo";
      $newname = ereg_replace("'","",$newname);
      $newname = ereg_replace("/","_",$newname);
      $oldname = $lhtag . "_95x95.". $lhtipo; if (is_file("/var/www/html/network/campeggi/images/resized/$oldname")){
      `cp /var/www/html/network/campeggi/images/resized/$oldname /var/www/html/tcev/logos_struct/toexplode/$newname`;
      `chown apache:apache /var/www/html/tcev/logos_struct/toexplode/$newname`; 
*/

/* echo "  `cp /var/www/html/network/campeggi/images/resized/$oldname /var/www/html/tcev/logos_struct/toexplode/$newname`;      `chown apache:apache /var/www/html/tcev/logos_struct/toexplode/$newname`; "; */

/*
      mysql_query("insert into tb_foto (nome_foto,path_foto,id_struttura,fg_logo) values (\"$newname\",\"/logos_struct/\",$strucno,1);");
      $fotid = mysql_insert_id();
      mysql_query("insert into tb_foto_tag (id_foto,tag_title,tag_alt,id_lingua) values ($fotid,\"$lhnome\",\"$lhnome\",1);");
      mysql_query("insert into tb_foto_tag (id_foto,tag_title,tag_alt,id_lingua) values ($fotid,\"$lhnome\",\"$lhnome\",2);");
      mysql_query("insert into tb_foto_tag (id_foto,tag_title,tag_alt,id_lingua) values ($fotid,\"$lhnome\",\"$lhnome\",3);"); echo mysql_error();
      mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,2);");
      mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,8);");
      mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,10);");
      mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,11);");
      mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,12);");
      mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,14);");
      mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,15);");
      mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,17);");
//
      }
      }
*/



      $foto_query = "select nome_foto,id_foto from tb_foto where id_struttura=$strucno and (fg_logo=0 or fg_logo is NULL);";
      $foto_result = mysql_query($foto_query);
      $foto_arr = mysql_fetch_array($foto_result);
      $fotoname = rawurlencode($foto_arr[0]);
      $fotid = $foto_arr[1];


// codice importazione vecchio sistema
      if ((!$fotoname)){

      $fotohelp_query = "select tag,tipo,titolo,zorder from ge_images where subgruppo=\"STRTR_FOTO_P$strucno\";";
      $fotohelp_result = mysql_query($fotohelp_query);

      while ($fotohelp_arr = mysql_fetch_array($fotohelp_result)){
        $fhtag = $fotohelp_arr['tag'];
        $fhtipo = $fotohelp_arr['tipo'];
        $num = $fotohelp_arr['zorder'];
        $fhnome = $fotohelp_arr['titolo'].$num; //if ($lhnome && $lhtag && $lhtipo){
        $provatt = $camplisting['provincia_struttura'];
        $provnameatt = $sigle[$provatt];
        $newname = ereg_replace(" ","_","$regione") ."_" . $provnameatt ."_".ereg_replace(" ","_",$fhnome) . ".$fhtipo";
        $newname = ereg_replace("'","",$newname);
        $newname = ereg_replace("/","_",$newname);
        $newname = ereg_replace(":","",$newname);
        $oldname = $fhtag . "_550x.". $fhtipo; if (is_file("/var/www/html/network/campeggi/images/resized/$oldname")){
        `cp /var/www/html/network/campeggi/images/resized/$oldname /var/www/html/tcev/photos_struct/$newname`;
        `chown apache:apache /var/www/html/tcev/photos_struct/$newname`; }
        $oldname = $fhtag . "_180x120.". $fhtipo; if (is_file("/var/www/html/network/campeggi/images/resized/$oldname")){
        `cp /var/www/html/network/campeggi/images/resized/$oldname /var/www/html/tcev/photos_struct/toexplode/$newname`;
        `chown apache:apache /var/www/html/tcev/photos_struct/toexplode/$newname`; } 
// echo " `cp /var/www/html/network/campeggi/images/resized/$oldname /var/www/html/tcev/photos_struct/toexplode/$newname`; 
// `chown apache:apache /var/www/html/tcev/photos_struct/toexplode/$newname`; ";

//$num = $num+1; //if ($num == 1)$num = 2;

        mysql_query("insert into tb_foto (nome_foto,path_foto,id_struttura,fg_logo) values (\"$newname\",\"/photos_struct/\",$strucno,0);"); $fotid = mysql_insert_id();
        mysql_query("insert into tb_foto_tag (id_foto,tag_title,tag_alt,id_lingua) values ($fotid,\"$fhnome\",\"$fhnome\",1);");
        mysql_query("insert into tb_foto_tag (id_foto,tag_title,tag_alt,id_lingua) values ($fotid,\"$fhnome\",\"$fhnome\",2);");
        mysql_query("insert into tb_foto_tag (id_foto,tag_title,tag_alt,id_lingua) values ($fotid,\"$fhnome\",\"$fhnome\",3);"); echo mysql_error();
        mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,2);");
        mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,8);");
        mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,10);");
        mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,11);");
        mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,12);");
        mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,14);");
        mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,15);");
        mysql_query("insert into rel_foto_domini (id_foto,id_dominio) values ($fotid,17);");
      } //

      $foto_query = "select nome_foto,id_foto from tb_foto where id_struttura=$strucno and fg_logo=0;";
      $foto_result = mysql_query($foto_query);
      for ($rt=0;$rt<=20;$rt++){
       $fotarr[$rt]=array();
       $fotohtml[$rt] = "";
      }

//}
      } // fine import
      if (!$logoname)$logoname = "no_logo_camping.gif";
      $logoto = $settings['percorso_loghi_strutture'] . "/" . $logoname;
      $logoto = ereg_replace("//","/",$logoto);
      $logoto = ereg_replace("//","/",$logoto);

      $logoalt_query = "select tag_title from tb_foto_tag where id_foto=$fotid;";
      if ($fotid>0)$logoalt_result = mysql_query($logoalt_query);
      if ($fotid>0)$logoalt_arr = mysql_fetch_array($logoalt_result);
      if ($fotid>0)$logoalt = $logoalt_arr[0];

      $temp = ereg_replace("###-ANCHOR-###","s".$camplisting['id_struttura'],$temp);
      $temp = ereg_replace("###-LOGO-###","$logoto",$temp);
      $temp = ereg_replace("###-LOGO_ALT-###","$logoalt",$temp);

    foreach($keys as $key){
      $string = "###-" . $key . "-###";
      $temp = str_replace($string,$translate[$key],$temp);
      $temp = ereg_replace("###-STELLE-###",$starhtml,$temp);
                
    }


      $numo = $camplisting['id_struttura'];
      $listingfile = "/var/www/html/new/domini/$domain/listings/" . $langnames[$lingua] . "/$numo";
      $fh4 = fopen($listingfile,"w");
    $temp = ereg_replace("###-ID_STRUTTURA-###",$strucno,$temp);

    $temp = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$temp);

      fputs ($fh4,$temp);
      fclose($fh4);



     //*********** MODIFICA BRAIN ****************//
/*nella pagina elenco regionale compare il link al sito ufficiale della struttura se:
1. e' cliente
2. esiste il sito
sono state introdotte 2 variabili nella pagina tmpl_viplisting che consentono di visualizzare il sito
*/
	  $sitointernet=$camplisting['sito_web'];
	  
	  if ($sitointernet!="" and $sitointernt!="null")	  
	  $temp = str_replace("###-SITOWEBLINK-###","http://".$camplisting['sito_web'],$temp);
	  else
	  $temp = str_replace("###-SITOWEBLINK-###","#",$temp);
	  $temp = str_replace("###-SITOWEB-###","SITO WEB",$temp);
    
	  $listcontent .= $temp;
//*********** FINE MODIFICA ****************// 

   

// echo "--->$clientflag\n";
                  
    $keys = array_keys($translate);

    $campid = $camplisting['id_struttura'];
    if ($lingua == 1)mysql_query("update tb_strutture set percorso_scheda_principale=\"\" where id_struttura=$campid;");

                  
if ($clientflag == "1"){
    // build scheda
    if (strlen($schedapath2) > 3){
    
     echo "  \n costruzione $schedapath2 ";
     if ($lingua == 1)mysql_query("update tb_strutture set percorso_scheda_principale=\"$schedapath2\" where id_struttura=$campid;");
     echo "  \n costruzione $schedapricepath ";
     echo "  \n costruzione $schedamailpath ";
     echo "  \n costruzione $schedaoffertepath ";
     echo "  \n costruzione $schedaofferte2path ";
     $schedapath2 = ereg_replace("\\'","'",rawurldecode($schedapath2));
     $schedapricepath2 = ereg_replace("\\'","'",rawurldecode($schedapricepath));
     $schedamailpath2 = ereg_replace("\\'","'",rawurldecode($schedamailpath));
     $schedaoffertepath2 = ereg_replace("\\'","'",rawurldecode($schedaoffertepath));
     $schedaofferte2path2 = ereg_replace("\\'","'",rawurldecode($schedaofferte2path));
     $schedalink12 = ereg_replace("\\'","'",rawurldecode($schedapath2));
     $myoutfile1 = "/var/www/html/new/domini/$domain/$schedapath2";
     $myoutfile2 = "/var/www/html/new/domini/$domain/$schedapricepath2";
     $myoutfile3 = "/var/www/html/new/domini/$domain/$schedamailpath2";
     $myoutfile4 = "/var/www/html/new/domini/$domain/$schedaoffertepath2";
     $myoutfile5 = "/var/www/html/new/domini/$domain/$schedaofferte2path2";
echo "\n$myoutfile1\n";
     $myoutfile1 = ereg_replace("!!!","_",$myoutfile1);
     $myoutfile1 = ereg_replace("_.htm",".htm",$myoutfile1);
     $myoutfile2 = ereg_replace("!!!","_",$myoutfile2);
     $myoutfile2 = ereg_replace("_.htm",".htm",$myoutfile2);
     $myoutfile3 = ereg_replace("!!!","_",$myoutfile3);
     $myoutfile3 = ereg_replace("_.htm",".htm",$myoutfile3);
     $myoutfile4 = ereg_replace("!!!","_",$myoutfile4);
     $myoutfile4 = ereg_replace("_.htm",".htm",$myoutfile4);
     $myoutfile5 = ereg_replace("!!!","_",$myoutfile5);
     $myoutfile5 = ereg_replace("_.htm",".htm",$myoutfile5);
     $myoutfile1 = ereg_replace("_-","-",$myoutfile1);
     $myoutfile2 = ereg_replace("_-","-",$myoutfile2);
     $myoutfile3 = ereg_replace("_-","-",$myoutfile3);
     $myoutfile4 = ereg_replace("_-","-",$myoutfile4);
     $myoutfile5 = ereg_replace("_-","-",$myoutfile5);

     $schedabase2 = $schedabase;
     $schedabase2 = ereg_replace("###-LOGO-###","$logoto",$schedabase2);
     $schedabase2 = ereg_replace("###-LOGO_ALT-###","$logoalt",$schedabase2);
     $schedabase2 = ereg_replace("###-NOME_STRUTTURA-###",eregi_replace("!!!+[[:alnum:]|_]+!!!","",$camplisting['nome_struttura']),$schedabase2);
     $ct = $camplisting['titolo_struttura'];
     $schedabase2 = ereg_replace("###-TITOLO_STRUTTURA-###",$campingtype[$ct],$schedabase2);
     $tito = $campingtype[$ct];
     $schedabase2 = ereg_replace("###-INDIRIZZO-###",$camplisting['indirizzo_struttura'],$schedabase2);
     $schedabase2 = ereg_replace("###-CAP-###",$camplisting['cap_struttura'],$schedabase2);
     $schedabase2 = ereg_replace("###-LOCALITA-###",ucwords(strtolower($camplisting['localita_struttura'])),$schedabase2);
    $template = "/var/www/html/new/templates/" . $domain . "/tmpl_scheda_5.html";
    if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_scheda_5.html";

    $fh = fopen($template,"r");
    $scheda5 = fread($fh,filesize($template));
    fclose($fh);

     $scheda5 = ereg_replace("###-LOGO-###","$logoto",$scheda5);
     $scheda5 = ereg_replace("###-LOGO_ALT-###","$logoalt",$scheda5);
     $scheda5 = ereg_replace("###-NOME_STRUTTURA-###",eregi_replace("!!!+[[:alnum:]|_]+!!!","",$camplisting['nome_struttura']),$scheda5);
     $scheda5 = ereg_replace("###-TITOLO_STRUTTURA-###",$campingtype[$ct],$scheda5);
     $scheda5 = ereg_replace("###-INDIRIZZO-###",$camplisting['indirizzo_struttura'],$scheda5);
     $scheda5 = ereg_replace("###-CAP-###",$camplisting['cap_struttura'],$scheda5);
     $scheda5 = ereg_replace("###-LOCALITA-###",ucwords(strtolower($camplisting['localita_struttura'])),$scheda5);

     $pn = $camplisting['provincia_struttura'];
     $provincia = $provincename[$pn];
     $sigla = $sigle[$pn];
     if ($pn > 0)$schedabase2 = ereg_replace("###-NOME_PROVINCIA-###"," - ".$provincename[$pn]."",$schedabase2);
     else $schedabase2 = ereg_replace("###-NOME_PROVINCIA-###","",$schedabase2);
     if ($pn > 0)$scheda5 = ereg_replace("###-NOME_PROVINCIA-###"," - ".$provincename[$pn]."",$scheda5);
     else $scheda5 = ereg_replace("###-NOME_PROVINCIA-###","",$scheda5);
     if ($camplisting['frazione_struttura'] && $camplisting['indirizzo_struttura'])$schedabase2 = ereg_replace("###-FRAZIONE-###",$camplisting['frazione_struttura']." - 
",$schedabase2);
     elseif ($camplisting['frazione_struttura'])$schedabase2 = ereg_replace("###-FRAZIONE-###",$camplisting['frazione_struttura'],$schedabase2);
     else $schedabase2 = ereg_replace("###-FRAZIONE-###","",$schedabase2);
     if ($camplisting['frazione_struttura'] && $camplisting['indirizzo_struttura'])$scheda5 = ereg_replace("###-FRAZIONE-###",$camplisting['frazione_struttura']." - 
",$scheda5);
     elseif ($camplisting['frazione_struttura'])$scheda5 = ereg_replace("###-FRAZIONE-###",$camplisting['frazione_struttura'],$scheda5);
     else $scheda5 = ereg_replace("###-FRAZIONE-###","",$scheda5);
       if ($camplisting['fax_estivo']){
         if ($camplisting['tel_estivo'])$schedabase2 = ereg_replace("###-FAX_ESTIVO-###"," - ###-FAX_ESTIVO-###",$schedabase2);
         if ($camplisting['fax_estivo_pref'])$schedabase2 = ereg_replace("###-FAX_ESTIVO_PREF-###",$camplisting['fax_estivo_pref'],$schedabase2);
         else $schedabase2 = ereg_replace("###-FAX_ESTIVO_PREF-###",$camplisting['tel_estivo_pref'],$schedabase2);
         $schedabase2 = ereg_replace("###-FAX_ESTIVO_NUMERO-###",$camplisting['fax_estivo'],$schedabase2);
         if ($camplisting['tel_estivo'])$scheda5 = ereg_replace("###-FAX_ESTIVO-###"," - ###-FAX_ESTIVO-###",$scheda5);
         if ($camplisting['fax_estivo_pref'])$scheda5 = ereg_replace("###-FAX_ESTIVO_PREF-###",$camplisting['fax_estivo_pref'],$scheda5);
         else $scheda5 = ereg_replace("###-FAX_ESTIVO_PREF-###",$camplisting['tel_estivo_pref'],$scheda5);
         $scheda5 = ereg_replace("###-FAX_ESTIVO_NUMERO-###",$camplisting['fax_estivo'],$scheda5);
       }
       else{
         $schedabase2 = ereg_replace("###-FAX_ESTIVO_PREF-###","",$schedabase2);
         $schedabase2 = ereg_replace("###-FAX_ESTIVO_NUMERO-###","",$schedabase2);
         $schedabase2 = ereg_replace("###-FAX-###","",$schedabase2);
         $scheda5 = ereg_replace("###-FAX_ESTIVO_PREF-###","",$scheda5);
         $scheda5 = ereg_replace("###-FAX_ESTIVO_NUMERO-###","",$scheda5);
         $scheda5 = ereg_replace("###-FAX-###","",$scheda5);
       }
       if ($camplisting['fax_invernale']){
         if ($camplisting['tel_invernale'])$schedabase2 = ereg_replace("###-FAX_INVERNALE-###"," - ###-FAX_INVERNALE-###",$schedabase2);
         if ($camplisting['fax_invernale_pref'])$schedabase2 = ereg_replace("###-FAX_INVERNALE_PREF-###",$camplisting['fax_invernale_pref'],$schedabase2);
         $schedabase2 = ereg_replace("###-FAX_INVERNALE_PREF-###",$camplisting['fax_invernale_pref'],$schedabase2);
         $schedabase2 = ereg_replace("###-FAX_INVERNALE_NUMERO-###",$camplisting['fax_invernale'],$schedabase2);
         if ($camplisting['tel_invernale'])$scheda5 = ereg_replace("###-FAX_INVERNALE-###"," - ###-FAX_INVERNALE-###",$scheda5);
         if ($camplisting['fax_invernale_pref'])$scheda5 = ereg_replace("###-FAX_INVERNALE_PREF-###",$camplisting['fax_invernale_pref'],$scheda5);
         $scheda5 = ereg_replace("###-FAX_INVERNALE_PREF-###",$camplisting['fax_invernale_pref'],$scheda5);
         $scheda5 = ereg_replace("###-FAX_INVERNALE_NUMERO-###",$camplisting['fax_invernale'],$scheda5);
       }
       else{
         $schedabase2 = ereg_replace("###-FAX_INVERNALE_PREF-###","",$schedabase2);
         $schedabase2 = ereg_replace("###-FAX_INVERNALE_NUMERO-###","",$schedabase2);
         $schedabase2 = ereg_replace("###-FAX_INVERNALE-###","",$schedabase2);
         $scheda5 = ereg_replace("###-FAX_INVERNALE_PREF-###","",$scheda5);
         $scheda5 = ereg_replace("###-FAX_INVERNALE_NUMERO-###","",$scheda5);
         $scheda5 = ereg_replace("###-FAX_INVERNALE-###","",$scheda5);
       }
       if ($camplisting['tel_estivo']){
         $schedabase2 = ereg_replace("###-TEL_ESTIVO_PREF-###",$camplisting['tel_estivo_pref'],$schedabase2);
         $schedabase2 = ereg_replace("###-TEL_ESTIVO_NUMERO-###",$camplisting['tel_estivo'],$schedabase2);
         $scheda5 = ereg_replace("###-TEL_ESTIVO_PREF-###",$camplisting['tel_estivo_pref'],$scheda5);
         $scheda5 = ereg_replace("###-TEL_ESTIVO_NUMERO-###",$camplisting['tel_estivo'],$scheda5);
       }
       else{
         $schedabase2 = ereg_replace("###-TEL_ESTIVO_PREF-###","",$schedabase2);
         $schedabase2 = ereg_replace("###-TEL_ESTIVO_NUMERO-###","",$schedabase2);
         $schedabase2 = ereg_replace("###-TEL_ESTIVO-###","",$schedabase2);
         $scheda5 = ereg_replace("###-TEL_ESTIVO_PREF-###","",$scheda5);
         $scheda5 = ereg_replace("###-TEL_ESTIVO_NUMERO-###","",$scheda5);
         $scheda5 = ereg_replace("###-TEL_ESTIVO-###","",$scheda5);
       }
       if (strlen($camplisting['tel_invernale'])>3){
         $schedabase2 = ereg_replace("###-TEL_INVERNALE_PREF-###",$camplisting['tel_invernale_pref'],$schedabase2);
         $schedabase2 = ereg_replace("###-TEL_INVERNALE_NUMERO-###",$camplisting['tel_invernale'],$schedabase2);
         $scheda5 = ereg_replace("###-TEL_INVERNALE_PREF-###",$camplisting['tel_invernale_pref'],$scheda5);
         $scheda5 = ereg_replace("###-TEL_INVERNALE_NUMERO-###",$camplisting['tel_invernale'],$scheda5);
       }
       else{
         $schedabase2 = ereg_replace("###-TEL_INVERNALE_PREF-###","",$schedabase2);
         $schedabase2 = ereg_replace("###-TEL_INVERNALE_NUMERO-###","",$schedabase2);
         $schedabase2 = ereg_replace("###-TELEFONO_INVERNALE-###","",$schedabase2);
         $scheda5 = ereg_replace("###-TEL_INVERNALE_PREF-###","",$scheda5);
         $scheda5 = ereg_replace("###-TEL_INVERNALE_NUMERO-###","",$scheda5);
         $scheda5 = ereg_replace("###-TELEFONO_INVERNALE-###","",$scheda5);
       }
       if ($camplisting['sito_web']){
         $sitolink = ereg_replace("###-URL_SITO-###",$camplisting['sito_web'],$settings['html_sito_link']);
         $schedabase2 = ereg_replace("###-SITO_WEB-###",$sitolink,$schedabase2);
         $scheda5 = ereg_replace("###-SITO_WEB-###",$sitolink,$scheda5);
       }
       else {
         $schedabase2 = ereg_replace("###-SITO_WEB-###","",$schedabase2);
         $scheda5 = ereg_replace("###-SITO_WEB-###","",$scheda5);
       }
       if ($camplisting['email_struttura']){
         $sitolink2 = ereg_replace("###-INDIRIZZO_EMAIL-###",$camplisting['email_struttura'],$settings['html_email_link']);
         $sitolink2 = ereg_replace("###-MAILFORM-###","$myoutlink3",$sitolink2);
         $schedabase2 = ereg_replace("###-EMAIL-###",$sitolink2,$schedabase2);
         $scheda5 = ereg_replace("###-EMAIL-###",$sitolink2,$scheda5);
       }
       else {
         $schedabase2 = ereg_replace("###-EMAIL-###","",$schedabase2);
         $scheda5 = ereg_replace("###-EMAIL-###","",$scheda5);
       }
       $scheda5 = ereg_replace("###-ID_STRUTTURA-###",$camplisting['id_struttura'],$scheda5);
       $scheda5 = ereg_replace("###-ISO_LINGUA-###",$langnames[$lingua],$scheda5);
        
/*
       $schedabase2 = ereg_replace("###-URL-###",$myoutlink1,ereg_replace("###-SCHEDA_STRUTTURA-###",$schedalink,$schedabase2));
       $schedabase2 = ereg_replace("###-URL-###",$myoutlink2,ereg_replace("###-SCHEDA_TARIFFE-###",$schedapricelink,$schedabase2));
       $schedabase2 = ereg_replace("###-URL-###",$myoutlink3,ereg_replace("###-CONTATTA_STRUTTURA-###",$schedamaillink,$schedabase2));
*/
       $schedabase2 = ereg_replace("###-URL-###",$myoutlink1,ereg_replace("###-SCHEDA_STRUTTURA-###",$settings['link_dettagli'],$schedabase2));
       $schedabase2 = ereg_replace("###-URL-###",$myoutlink2,ereg_replace("###-SCHEDA_TARIFFE-###",$settings['link_tariffe'],$schedabase2));
       $schedabase2 = ereg_replace("###-URL-###",$myoutlink3,ereg_replace("###-CONTATTA_STRUTTURA-###",$settings['link_contatta'],$schedabase2));
       $schedabase2 = ereg_replace("###-HOMELINK-###",$settings['homelink'],$schedabase2);
       $schedabase2 = ereg_replace("###-BACK_HOME-###",$settings['link_sx_home'],$schedabase2);
       $schedabase2 = ereg_replace("###-BACK_REGIONE-###",$settings['link_sx_regione'],$schedabase2);
       $schedabase2 = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$schedabase2);
       $schedabase2 = ereg_replace("###-REGIONLINK-###","$outlinkfile#s" . $camplisting['id_struttura'],$schedabase2);
       $schedabase2 = ereg_replace("###-REGIONE-###","$regione",$schedabase2);
/*
       $scheda5 = ereg_replace("###-URL-###",$myoutlink1,ereg_replace("###-SCHEDA_STRUTTURA-###",$schedalink,$scheda5));
       $scheda5 = ereg_replace("###-URL-###",$myoutlink2,ereg_replace("###-SCHEDA_TARIFFE-###",$schedapricelink,$scheda5));
       $scheda5 = ereg_replace("###-URL-###",$myoutlink3,ereg_replace("###-CONTATTA_STRUTTURA-###",$schedamaillink,$scheda5));
*/
       $scheda5 = ereg_replace("###-URL-###",$myoutlink1,ereg_replace("###-SCHEDA_STRUTTURA-###",$settings['link_dettagli'],$scheda5));
       $scheda5 = ereg_replace("###-URL-###",$myoutlink2,ereg_replace("###-SCHEDA_TARIFFE-###",$settings['link_tariffe'],$scheda5));
       $scheda5 = ereg_replace("###-URL-###",$myoutlink3,ereg_replace("###-CONTATTA_STRUTTURA-###",$settings['link_contatta'],$scheda5));
       $scheda5 = ereg_replace("###-HOMELINK-###",$settings['homelink'],$scheda5);
       $scheda5 = ereg_replace("###-BACK_HOME-###",$settings['link_sx_home'],$scheda5);
       $scheda5 = ereg_replace("###-BACK_REGIONE-###",$settings['link_sx_regione'],$scheda5);
       $scheda5 = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$scheda5);
       $scheda5 = ereg_replace("###-REGIONLINK-###","$outlinkfile#s" . $camplisting['id_struttura'],$scheda5);
       $scheda5 = ereg_replace("###-REGIONE-###","$regione",$scheda5);
    $navpathx = "<a href=\"/" . $settings['percorso_base'] . "\">HOME</a>&nbsp;»&nbsp;" ; if ($camplisting['regione_struttura'] == 6)  $navpathx2 = 
$settings['toplink_scheda_corsica']; else $navpathx2 = $settings['toplink_scheda'];
    $navpathx2 = ereg_replace("###-REGIONLINK-###","$outlinkfile#s" . $camplisting['id_struttura'],$navpathx2);
    $navpathx2 = ereg_replace("###-PROVLINK-###","/".$settings['percorso_base']."/".$settings['percorso_province']."/".$settings['nome_elenchi_provinciali'],$navpathx2);
    $navpathx .= $navpathx2;
    $navpathx = ereg_replace("//","/",$navpathx);
    $navpathx = ereg_replace("//","/",$navpathx);
    $navpathx = ereg_replace("http:/www.","http://www.",$navpathx);
    $navpathx = ereg_replace("###-REGIONE-###","$regione",$navpathx);
    $navpathx = ereg_replace("###-NOME_PROVINCIA-###","$provincia",$navpathx);
    $navpathx = ereg_replace("###-PROVINCIA-###","$provincia",$navpathx);
    $navpathx = ereg_replace("###-NUMERO_PAGINA-###","",$navpathx);
       $navpathx = ereg_replace("###-NOME_STRUTTURA-###",eregi_replace("!!!+[[:alnum:]|_]+!!!","",$camplisting['nome_struttura']),$navpathx);

    $schedabase2 = ereg_replace("###-SITENAV-###",$navpathx,$schedabase2);
    $scheda5 = ereg_replace("###-SITENAV-###",$navpathx,$scheda5);

//print_r($provpage);

    foreach($keys as $key){

      $string = "###-" . $key . "-###";
      $schedabase2 = str_replace($string,$translate[$key],$schedabase2);
      $scheda5 = str_replace($string,$translate[$key],$scheda5); // $listcontent = str_replace($string,$translate[$key],$listcontent);
       $schedabase2 = ereg_replace("###-STELLE-###",$starhtml,$schedabase2);
       $scheda5 = ereg_replace("###-STELLE-###",$starhtml,$scheda5);
                
    } // /var/www/html/new/templates/DEFAULT/tmpl_scheda_2.html

    $template = "/var/www/html/new/templates/" . $domain . "/tmpl_scheda_1.html";
    if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_scheda_1.html";

    $fh = fopen($template,"r");
    $scheda1 = fread($fh,filesize($template));
    fclose($fh);

    $template = "/var/www/html/new/templates/" . $domain . "/tmpl_scheda_2.html";
    if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_scheda_2.html";

    $fh = fopen($template,"r");
    $scheda2 = fread($fh,filesize($template));
    fclose($fh);

    $template = "/var/www/html/new/templates/" . $domain . "/tmpl_scheda_3.html";
    if (!(is_file($template)))$template = "/var/www/html/new/templates/DEFAULT/tmpl_scheda_3.html";

    $fh = fopen($template,"r");
    $scheda3 = fread($fh,filesize($template));
    fclose($fh);

    $struc_query = "select * from tb_strutture_specifiche limit 0,1;";
    $struc_result = mysql_query($struc_query);
    $field = array();
    $jk=0;
    while ($jk < mysql_num_fields($struc_result)){
      $meta = mysql_fetch_field($struc_result, $jk);
      //echo $meta->name;
      array_push($field,$meta->name);
      $jk++;
    }
    $struc_query = "select * from tb_strutture_specifiche where id_struttura=$strucno and id_lingua=1;";
    $struc_result = mysql_query($struc_query);
    $strucdet2 = mysql_fetch_array($struc_result);
    $strucdet = mysql_fetch_array($struc_result);
    $struc_query = "select * from tb_strutture_specifiche where id_struttura=$strucno and id_lingua=$lingua;";
    $struc_result = mysql_query($struc_query);
    $strucdet = mysql_fetch_array($struc_result);
    if ($strucdet['presentazione_struttura'] == ""){

$strucdet['presentazione_struttura'] = $strucdet2['presentazione_struttura'];
}

//print_r($field);

    $camplisting['location_struttura'] = ereg_replace(" ","",$camplisting['location_struttura']);
    $locarr = split('\*',$camplisting['location_struttura']);
    $doloc = "";
    foreach ($locarr as $locval){
      if ($doloc != "" && strval($locval) > 0)$doloc .= ", ";
      if ($locval .= "")$doloc .= $location[$locval];

    }
    $scheda1 = ereg_replace("###-LOCATION-###",$doloc,$scheda1);

    $locarr = split('\*',$camplisting['ombreggiatura_struttura']);
    $doloc = "";
    foreach ($locarr as $locval){
      if ($doloc != "" && strval($locval) > 0)$doloc .= ", ";
      if (array_key_exists($locval,$ombreggiatura))$doloc .= $ombreggiatura[$locval];

    }
    $scheda1 = ereg_replace("###-OMBREGGIATURA-###",$doloc,$scheda1);

    $locarr = split('\*',$camplisting['altri_servizi']);
    $doloc = "";
    foreach ($locarr as $locval){
      if ($doloc != "" && strval($locval) > 0)$doloc .= ", ";
      if (array_key_exists($locval,$strucservizi))$doloc .= $strucservizi[$locval];
    }
    $scheda1 = ereg_replace("###-ALTRI_SERVIZI-###",$doloc,$scheda1);
    $locarr = split('\*',$camplisting['altri_servizi']);
    $doloc = "";
    foreach ($locarr as $locval){
      if ($doloc != "" && strval($locval) > 0)$doloc .= ", ";
      if (array_key_exists($locval,$strucservizi))$doloc .= $strucservizi[$locval];
    }
    $scheda1 = ereg_replace("###-ALTRI_SERVIZI-###",$doloc,$scheda1);

    $locarr = split('\*',$camplisting['carte_credito_supportate']);
    $doloc = "";
    foreach ($locarr as $locval){
      if ($doloc != "" && strval($locval) > 0)$doloc .= ", ";
      if (array_key_exists($locval,$creditcard))$doloc .= $creditcard[$locval];
    }
    $scheda1 = ereg_replace("###-CARTE_CREDITO-###",$doloc,$scheda1);
    $locarr = split('\*',$camplisting['ristorazione_struttura']);
    $doloc = "";
    foreach ($locarr as $locval){
      if ($doloc != "" && strval($locval) > 0)$doloc .= ", ";
      if (array_key_exists($locval,$ristorazione))$doloc .= $ristorazione[$locval];
    }
    $scheda1 = ereg_replace("###-RISTORAZIONE-###",$doloc,$scheda1);
    //$locarr = split('\*',$camplisting['posteggio_auto1']);
    $locarr = array();
    if ($camplisting['posteggio_auto1'])array_push($locarr,$camplisting['posteggio_auto1']);
    if ($camplisting['posteggio_auto2'])array_push($locarr,$camplisting['posteggio_auto2']);
    $doloc = "";
    foreach ($locarr as $locval){
      if ($doloc != "" && strval($locval) > 0)$doloc .= ", ";
      $doloc .= $parcheggi[$locval];

    }
    $scheda1 = ereg_replace("###-PARCHEGGI-###",$doloc,$scheda1);

// valori boolean
    if ($lingua == 1)$si = "S&igrave";
    if ($lingua == 2)$si = "Yes";
    if ($lingua == 3)$si = "Ja";
    if ($camplisting['fg_servizi_disabili'])$scheda1 = ereg_replace("###-SERVIZI_DISABILI-###",$si,$scheda1);
    if ($camplisting['fg_pronto_soccorso'])$scheda1 = ereg_replace("###-PRONTO_SOCCORSO-###",$si,$scheda1);
    if ($camplisting['fg_tel_pubblico'])$scheda1 = ereg_replace("###-TEL_PUBBLICO-###",$si,$scheda1);
    if ($camplisting['fg_sconto_eurocamp'])$scheda1 = ereg_replace("###-SCONTO_EUROCAMP-###",$si,$scheda1);
    if ($camplisting['fg_camper_service'])$scheda1 = ereg_replace("###-CAMPER_SERVICE-###",$si,$scheda1);
    if ($camplisting['fg_rimessaggio'])$scheda1 = ereg_replace("###-RIMESSAGGIO-###",$si,$scheda1);
    if ($camplisting['fg_market'])$scheda1 = ereg_replace("###-MARKET-###",$si,$scheda1);
    if ($camplisting['fg_bar'])$scheda1 = ereg_replace("###-BAR-###",$si,$scheda1);
    if ($camplisting['fg_wc'])$scheda1 = ereg_replace("###-WC-###",$si,$scheda1);
    if ($camplisting['fg_svuotatoi'])$scheda1 = ereg_replace("###-SVUOTATOI-###",$si,$scheda1);
    if ($camplisting['fg_ville'])$scheda1 = ereg_replace("###-VILLE-###",$si,$scheda1);
    if ($camplisting['ville_n'])$scheda1 = ereg_replace("###-VILLE_N-###",$si,$scheda1);
    if ($camplisting['fg_docce'])$scheda1 = ereg_replace("###-DOCCE_N-###",$si,$scheda1);
    if ($camplisting['fg_docce_calde'])$scheda1 = ereg_replace("###-DOCCE_CALDE-###",$si,$scheda1);
    if ($camplisting['fg_docce_gettone'])$scheda1 = ereg_replace("###-DOCCE_GETTONE-###",$si,$scheda1);

    if (!$camplisting['fg_servizi_disabili'])$scheda1 = ereg_replace("###-SERVIZI_DISABILI-###","",$scheda1);
    if (!$camplisting['fg_pronto_soccorso'])$scheda1 = ereg_replace("###-PRONTO_SOCCORSO-###","",$scheda1);
    if (!$camplisting['fg_tel_pubblico'])$scheda1 = ereg_replace("###-TEL_PUBBLICO-###","",$scheda1);
    if (!$camplisting['fg_sconto_eurocamp'])$scheda1 = ereg_replace("###-SCONTO_EUROCAMP-###","",$scheda1);
    if (!$camplisting['fg_camper_service'])$scheda1 = ereg_replace("###-CAMPER_SERVICE-###","",$scheda1);
    if (!$camplisting['fg_rimessaggio'])$scheda1 = ereg_replace("###-RIMESSAGGIO-###","",$scheda1);
    if (!$camplisting['fg_market'])$scheda1 = ereg_replace("###-MARKET-###","",$scheda1);
    if (!$camplisting['fg_bar'])$scheda1 = ereg_replace("###-BAR-###","",$scheda1);
    if (!$camplisting['fg_wc'])$scheda1 = ereg_replace("###-WC-###","",$scheda1);
    if (!$camplisting['fg_svuotatoi'])$scheda1 = ereg_replace("###-SVUOTATOI-###","",$scheda1);
    if (!$camplisting['ville_n'])$scheda1 = ereg_replace("###-VILLE_N-###","",$scheda1);
    if (!$camplisting['fg_docce'])$scheda1 = ereg_replace("###-DOCCE_N-###","",$scheda1);
    if (!$camplisting['fg_docce_calde'])$scheda1 = ereg_replace("###-DOCCE_CALDE-###","",$scheda1);
    if (!$camplisting['fg_docce_gettone'])$scheda1 = ereg_replace("###-DOCCE_GETTONE-###","",$scheda1);

    $scheda1 = ereg_replace("###-LISTINO-###",$listino,$scheda1);
    $scheda2 = ereg_replace("###-LISTINO-###",$listino,$scheda2);
    $scheda3 = ereg_replace("###-LISTINO-###",$listino,$scheda3);


    $scheda1 = ereg_replace("###-DESCRIZIONE_STRUTTURA-###",$strucdet['presentazione_struttura'],$scheda1);
    $scheda1 = ereg_replace("###-SUPERFICIE_MQ-###",$camplisting['superficie_mq'],$scheda1);
    $scheda1 = ereg_replace("###-PIAZZOLE_N-###",$camplisting['piazzole_n'],$scheda1);
    $scheda1 = ereg_replace("###-APPARTAMENTI_N-###",$camplisting['appartamenti_n'],$scheda1);
    $scheda1 = ereg_replace("###-CASEMOBILI_N-###",$camplisting['casemobili_n'],$scheda1);
    $scheda1 = ereg_replace("###-BUNGALOWS_N-###",$camplisting['bungalows_n'],$scheda1); 
    $scheda1 = ereg_replace("###-VILLE_N-###",$camplisting['ville_n'],$scheda1);
    $scheda1 = ereg_replace("###-DOCCE_N-###",$camplisting['docce_n'],$scheda1);
    foreach($field as $fieldname){
      $findstring = "###-" . strtoupper($fieldname) . "-###";
      if ($strucdet[$fieldname] != "")$scheda1 = eregi_replace("$findstring",$strucdet[$fieldname],$scheda1);
      else $scheda1 = eregi_replace("$findstring",$strucdet2[$fieldname],$scheda1);
    }

    $longid = $camplisting['id_struttura'];
    while (strlen($longid) < 4)$longid = "0" . $longid;
    $langiso = $langnames[$lingua];
    $scheda4path = "/var/www/indexofferte2/index_" . $longid . "_" . strtoupper($langiso) . ".html"; // if (is_file($scheda4path)){
      $scheda4 = "<?include(\"$scheda4path\");?>"; // } // else $scheda4 = "";

    $outcont1 = ereg_replace("###-CORPO_PAGINA-###",$scheda1,$schedabase2);
    $outcont2 = ereg_replace("###-CORPO_PAGINA-###",$scheda2,$schedabase2);
    $outcont3 = ereg_replace("###-CORPO_PAGINA-###",$scheda3,$schedabase2);
    $outcont4 = ereg_replace("###-CORPO_PAGINA-###",$scheda4,$schedabase2);

    foreach($fields2 as $fieldname){
      $findstring = "###-" . strtoupper($fieldname) . "-###";
      $scheda1 = eregi_replace("$findstring",$camplisting[$fieldname],$scheda1); //echo $findstring ."\n" . $camlisting[$fieldname]. "\n" . $fieldname;
    }

    foreach($keys as $key){

      $string = "###-" . $key . "-###";
      $outcont1 = str_replace($string,$translate[$key],$outcont1);
      $outcont2 = str_replace($string,$translate[$key],$outcont2);
      $outcont3 = str_replace($string,$translate[$key],$outcont3);
                
    }

    $imgpath = $settings['percorso_immagini_generiche'];
    
    
      $foto_query = "select nome_foto,id_foto from tb_foto where id_struttura=$strucno and fg_logo=0;";
      $foto_result = mysql_query($foto_query);
      for ($rt=0;$rt<=20;$rt++){
       $fotarr[$rt]=array();
       $fotohtml[$rt] = "";
      }

$rt = 0; $dolente = mysql_num_rows($foto_result); while ($foto_arr = mysql_fetch_array($foto_result)){
        $rt++;
        $fot2id = $foto_arr[1];
        $fotoname = rawurlencode($foto_arr[0]);
        $fotoalt_query = "select tag_title from tb_foto_tag where id_foto=$fot2id;";
        $fotoalt_result = mysql_query($fotoalt_query);
        $fotoalt_arr = mysql_fetch_array($fotoalt_result);
        $fotoalt = $fotoalt_arr[0];
        $fotohtml[$rt] = "<a href=\"" . $settings['percorso_foto_strutture'] . "/" . $fotoname . "\" target=\"_blank\"><img src=\"" . $settings['percorso_foto_strutture'] . 
"/toexplode/$fotoname\" border=\"0\"></a>";
        $fotohtml[$rt] = ereg_replace("//","/",$fotohtml[$rt]);
      }
    for ($rti = 1;$rti<=20;$rti++){
    $outcont1 = ereg_replace("###-FOTO_$rti-###",$fotohtml[$rti],$outcont1);
    $outcont2 = ereg_replace("###-FOTO_$rti-###",$fotohtml[$rti],$outcont2);
    $outcont3 = ereg_replace("###-FOTO_$rti-###",$fotohtml[$rti],$outcont3);
    }
    $outcont1 = ereg_replace("###-PERCORSO_BASE-###/","###-PERCORSO_BASE-###",$outcont1);
    $outcont1 = ereg_replace("/###-PERCORSO_BASE-###","###-PERCORSO_BASE-###",$outcont1);
    $outcont1 = ereg_replace("###-PERCORSO_REGIONI-###/","###-PERCORSO_REGIONI-###",$outcont1);
    $outcont1 = ereg_replace("/###-PERCORSO_REGIONI-###","###-PERCORSO_REGIONI-###",$outcont1);
    $outcont1 = ereg_replace("###-PERCORSO_BASE_UE-###/","###-PERCORSO_BASE_UE-###",$outcont1);
    $outcont1 = ereg_replace("/###-PERCORSO_BASE_UE-###","###-PERCORSO_BASE_UE-###",$outcont1);
    $outcont1 = ereg_replace("###-TITOLO_PAGINA-###",$settings['homepage_titolo'],$outcont1);
    $outcont1 = ereg_replace("###-META_DESCRIPTION-###",$settings['homepage_description'],$outcont1);
    $outcont1 = ereg_replace("###-META_KEYWORDS-###",$settings['homepage_keywords'],$outcont1);
    $outcont1 = ereg_replace("###-NAVBAR-###",$settings['homepage_navigation_bar'],$outcont1);
    $outcont1 = ereg_replace("###-PERCORSO_REGIONI-###",$settings['percorso_regioni'],$outcont1);
    $outcont1 = ereg_replace("###-PERCORSO_BASE-###",$settings['percorso_base'],$outcont1);
    $outcont1 = ereg_replace("###-PERCORSO_BASE_UE-###",$settings['percorso_base_ue'],$outcont1);
    $outcont1 = ereg_replace("###-LANGUAGES-###",$langlinks,$outcont1);
    $outcont1 = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###/",$imgpath,$outcont1);
    $outcont1 = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###/",$imglangpath,$outcont1);
    $outcont1 = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###",$imgpath,$outcont1);
    $outcont1 = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###",$imglangpath,$outcont1);
    if ($dolente >0)$outcont1 = ereg_replace("###-LENTE-###",$settings['lente'],$outcont1);
    else $outcont1 = ereg_replace("###-LENTE-###",$settings['lente'],$outcont1);
    $fotopath = $settings['percorso_foto_strutture'];
    $logopath = $settings['percorso_loghi_strutture'];
    $outcont1 = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###/",$imgpath,$outcont1);
    $outcont1 = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###/",$imglangpath,$outcont1);
    $outcont1 = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###",$imgpath,$outcont1);
    $outcont1 = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###",$imglangpath,$outcont1);
    $outcont1 = ereg_replace("###-HOMELINK-###",$settings['homelink'],$outcont1);
    $outcont2 = ereg_replace("###-PERCORSO_BASE-###/","###-PERCORSO_BASE-###",$outcont2);
    $outcont2 = ereg_replace("/###-PERCORSO_BASE-###","###-PERCORSO_BASE-###",$outcont2);
    $outcont2 = ereg_replace("###-PERCORSO_REGIONI-###/","###-PERCORSO_REGIONI-###",$outcont2);
    $outcont2 = ereg_replace("/###-PERCORSO_REGIONI-###","###-PERCORSO_REGIONI-###",$outcont2);
    $outcont2 = ereg_replace("###-PERCORSO_BASE_UE-###/","###-PERCORSO_BASE_UE-###",$outcont2);
    $outcont2 = ereg_replace("/###-PERCORSO_BASE_UE-###","###-PERCORSO_BASE_UE-###",$outcont2);
    $outcont2 = ereg_replace("###-TITOLO_PAGINA-###",$settings['homepage_titolo'],$outcont2);
    $outcont2 = ereg_replace("###-META_DESCRIPTION-###",$settings['homepage_description'],$outcont2);
    $outcont2 = ereg_replace("###-META_KEYWORDS-###",$settings['homepage_keywords'],$outcont2);
    $outcont2 = ereg_replace("###-NAVBAR-###",$settings['homepage_navigation_bar'],$outcont2);
    $outcont2 = ereg_replace("###-PERCORSO_REGIONI-###",$settings['percorso_regioni'],$outcont2);
    $outcont2 = ereg_replace("###-PERCORSO_BASE-###",$settings['percorso_base'],$outcont2);
    $outcont2 = ereg_replace("###-PERCORSO_BASE_UE-###",$settings['percorso_base_ue'],$outcont2);
    $outcont2 = ereg_replace("###-LANGUAGES-###",$langlinks,$outcont2);
    $outcont2 = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###/",$imgpath,$outcont2);
    $outcont2 = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###/",$imglangpath,$outcont2);
    $outcont2 = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###",$imgpath,$outcont2);
    $outcont2 = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###",$imglangpath,$outcont2);
    $fotopath = $settings['percorso_foto_strutture'];
    $logopath = $settings['percorso_loghi_strutture'];
    $outcont2 = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###/",$imgpath,$outcont2);
    $outcont2 = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###/",$imglangpath,$outcont2);
    $outcont2 = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###",$imgpath,$outcont2);
    $outcont2 = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###",$imglangpath,$outcont2);
    $outcont2 = ereg_replace("###-HOMELINK-###",$settings['homelink'],$outcont2);
    $outcont3 = ereg_replace("###-PERCORSO_BASE-###/","###-PERCORSO_BASE-###",$outcont3);
    $outcont3 = ereg_replace("/###-PERCORSO_BASE-###","###-PERCORSO_BASE-###",$outcont3);
    $outcont3 = ereg_replace("###-PERCORSO_REGIONI-###/","###-PERCORSO_REGIONI-###",$outcont3);
    $outcont3 = ereg_replace("/###-PERCORSO_REGIONI-###","###-PERCORSO_REGIONI-###",$outcont3);
    $outcont3 = ereg_replace("###-PERCORSO_BASE_UE-###/","###-PERCORSO_BASE_UE-###",$outcont3);
    $outcont3 = ereg_replace("/###-PERCORSO_BASE_UE-###","###-PERCORSO_BASE_UE-###",$outcont3);
    $outcont3 = ereg_replace("###-TITOLO_PAGINA-###",$settings['homepage_titolo'],$outcont3);
    $outcont3 = ereg_replace("###-META_DESCRIPTION-###",$settings['homepage_description'],$outcont3);
    $outcont3 = ereg_replace("###-META_KEYWORDS-###",$settings['homepage_keywords'],$outcont3);
    $outcont3 = ereg_replace("###-NAVBAR-###",$settings['homepage_navigation_bar'],$outcont3);
    $outcont3 = ereg_replace("###-PERCORSO_REGIONI-###",$settings['percorso_regioni'],$outcont3);
    $outcont3 = ereg_replace("###-PERCORSO_BASE-###",$settings['percorso_base'],$outcont3);
    $outcont3 = ereg_replace("###-PERCORSO_BASE_UE-###",$settings['percorso_base_ue'],$outcont3);
    $outcont3 = ereg_replace("###-LANGUAGES-###",$langlinks,$outcont3);
    $outcont3 = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###/",$imgpath,$outcont3);
    $outcont3 = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###/",$imglangpath,$outcont3);
    $outcont3 = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###",$imgpath,$outcont3);
    $outcont3 = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###",$imglangpath,$outcont3);
    $fotopath = $settings['percorso_foto_strutture'];
    $logopath = $settings['percorso_loghi_strutture'];
    $outcont3 = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###/",$imgpath,$outcont3);
    $outcont3 = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###/",$imglangpath,$outcont3);
    $outcont3 = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###",$imgpath,$outcont3);
    $outcont3 = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###",$imglangpath,$outcont3);
    $outcont3 = ereg_replace("###-HOMELINK-###",$settings['homelink'],$outcont3);


    $outcont4 = ereg_replace("###-PERCORSO_BASE-###/","###-PERCORSO_BASE-###",$outcont4);
    $outcont4 = ereg_replace("/###-PERCORSO_BASE-###","###-PERCORSO_BASE-###",$outcont4);
    $outcont4 = ereg_replace("###-PERCORSO_REGIONI-###/","###-PERCORSO_REGIONI-###",$outcont4);
    $outcont4 = ereg_replace("/###-PERCORSO_REGIONI-###","###-PERCORSO_REGIONI-###",$outcont4);
    $outcont4 = ereg_replace("###-PERCORSO_BASE_UE-###/","###-PERCORSO_BASE_UE-###",$outcont4);
    $outcont4 = ereg_replace("/###-PERCORSO_BASE_UE-###","###-PERCORSO_BASE_UE-###",$outcont4);
    $outcont4 = ereg_replace("###-TITOLO_PAGINA-###",$settings['homepage_titolo'],$outcont4);
    $outcont4 = ereg_replace("###-META_DESCRIPTION-###",$settings['homepage_description'],$outcont4);
    $outcont4 = ereg_replace("###-META_KEYWORDS-###",$settings['homepage_keywords'],$outcont4);
    $outcont4 = ereg_replace("###-NAVBAR-###",$settings['homepage_navigation_bar'],$outcont4);
    $outcont4 = ereg_replace("###-PERCORSO_REGIONI-###",$settings['percorso_regioni'],$outcont4);
    $outcont4 = ereg_replace("###-PERCORSO_BASE-###",$settings['percorso_base'],$outcont4);
    $outcont4 = ereg_replace("###-PERCORSO_BASE_UE-###",$settings['percorso_base_ue'],$outcont4);
    $outcont4 = ereg_replace("###-LANGUAGES-###",$langlinks,$outcont4);
    $outcont4 = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###/",$imgpath,$outcont4);
    $outcont4 = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###/",$imglangpath,$outcont4);
    $outcont4 = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###",$imgpath,$outcont4);
    $outcont4 = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###",$imglangpath,$outcont4);
    $fotopath = $settings['percorso_foto_strutture'];
    $logopath = $settings['percorso_loghi_strutture'];
    $outcont4 = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###/",$imgpath,$outcont4);
    $outcont4 = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###/",$imglangpath,$outcont4);
    $outcont4 = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###",$imgpath,$outcont4);
    $outcont4 = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###",$imglangpath,$outcont4);
    $outcont4 = ereg_replace("###-HOMELINK-###",$settings['homelink'],$outcont4);


    $outcont5 = $scheda5;
    $outcont5 = ereg_replace("###-PERCORSO_BASE-###/","###-PERCORSO_BASE-###",$outcont5);
    $outcont5 = ereg_replace("/###-PERCORSO_BASE-###","###-PERCORSO_BASE-###",$outcont5);
    $outcont5 = ereg_replace("###-PERCORSO_REGIONI-###/","###-PERCORSO_REGIONI-###",$outcont5);
    $outcont5 = ereg_replace("/###-PERCORSO_REGIONI-###","###-PERCORSO_REGIONI-###",$outcont5);
    $outcont5 = ereg_replace("###-PERCORSO_BASE_UE-###/","###-PERCORSO_BASE_UE-###",$outcont5);
    $outcont5 = ereg_replace("/###-PERCORSO_BASE_UE-###","###-PERCORSO_BASE_UE-###",$outcont5);
    $outcont5 = ereg_replace("###-TITOLO_PAGINA-###",$settings['homepage_titolo'],$outcont5);
    $outcont5 = ereg_replace("###-META_DESCRIPTION-###",$settings['homepage_description'],$outcont5);
    $outcont5 = ereg_replace("###-META_KEYWORDS-###",$settings['homepage_keywords'],$outcont5);
    $outcont5 = ereg_replace("###-NAVBAR-###",$settings['homepage_navigation_bar'],$outcont5);
    $outcont5 = ereg_replace("###-PERCORSO_REGIONI-###",$settings['percorso_regioni'],$outcont5);
    $outcont5 = ereg_replace("###-PERCORSO_BASE-###",$settings['percorso_base'],$outcont5);
    $outcont5 = ereg_replace("###-PERCORSO_BASE_UE-###",$settings['percorso_base_ue'],$outcont5);
    $outcont5 = ereg_replace("###-LANGUAGES-###",$langlinks,$outcont5);
    $outcont5 = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###/",$imgpath,$outcont5);
    $outcont5 = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###/",$imglangpath,$outcont5);
    $outcont5 = ereg_replace("###-PERCORSO_IMMAGINI_GENERICHE-###",$imgpath,$outcont5);
    $outcont5 = ereg_replace("###-PERCORSO_IMMAGINI_LINGUA-###",$imglangpath,$outcont5);
    $fotopath = $settings['percorso_foto_strutture'];
    $logopath = $settings['percorso_loghi_strutture'];
    $outcont5 = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###/",$imgpath,$outcont5);
    $outcont5 = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###/",$imglangpath,$outcont5);
    $outcont5 = ereg_replace("###-PERCORSO_FOTO-STRUTTURE-###",$imgpath,$outcont5);
    $outcont5 = ereg_replace("###-PERCORSO_LOGHI-STRUTTURE-###",$imglangpath,$outcont5);
    $outcont5 = ereg_replace("###-HOMELINK-###",$settings['homelink'],$outcont5);



    $outcont1 = ereg_replace("###-URL-###",$myoutlink1,ereg_replace("###-SCHEDA_STRUTTURA-###",$schedalink,$outcont1));
    $outcont1 = ereg_replace("###-URL-###",$myoutlink2,ereg_replace("###-SCHEDA_TARIFFE-###",$schedapricelink,$outcont1));
    $outcont1 = ereg_replace("###-URL-###",$myoutlink3,ereg_replace("###-CONTATTA_STRUTTURA-###",$schedamaillink,$outcont1));
    $outcont2 = ereg_replace("###-URL-###",$myoutlink1,ereg_replace("###-SCHEDA_STRUTTURA-###",$schedalink,$outcont2));
    $outcont2 = ereg_replace("###-URL-###",$myoutlink2,ereg_replace("###-SCHEDA_TARIFFE-###",$schedapricelink,$outcont2));
    $outcont2 = ereg_replace("###-URL-###",$myoutlink3,ereg_replace("###-CONTATTA_STRUTTURA-###",$schedamaillink,$outcont2));
    $outcont3 = ereg_replace("###-URL-###",$myoutlink1,ereg_replace("###-SCHEDA_STRUTTURA-###",$schedalink,$outcont3));
    $outcont3 = ereg_replace("###-URL-###",$myoutlink2,ereg_replace("###-SCHEDA_TARIFFE-###",$schedapricelink,$outcont3));
    $outcont3 = ereg_replace("###-URL-###",$myoutlink3,ereg_replace("###-CONTATTA_STRUTTURA-###",$schedamaillink,$outcont3));
    $outcont4 = ereg_replace("###-URL-###",$myoutlink1,ereg_replace("###-SCHEDA_STRUTTURA-###",$schedalink,$outcont4));
    $outcont4 = ereg_replace("###-URL-###",$myoutlink2,ereg_replace("###-SCHEDA_TARIFFE-###",$schedapricelink,$outcont4));
    $outcont4 = ereg_replace("###-URL-###",$myoutlink3,ereg_replace("###-CONTATTA_STRUTTURA-###",$schedamaillink,$outcont4));
    $outcont5 = ereg_replace("###-URL-###",$myoutlink1,ereg_replace("###-SCHEDA_STRUTTURA-###",$schedalink,$outcont5));
    $outcont5 = ereg_replace("###-URL-###",$myoutlink2,ereg_replace("###-SCHEDA_TARIFFE-###",$schedapricelink,$outcont5));
    $outcont5 = ereg_replace("###-URL-###",$myoutlink3,ereg_replace("###-CONTATTA_STRUTTURA-###",$schedamaillink,$outcont5));

    $outcont1 = ereg_replace("###-ID_STRUTTURA-###",$camplisting['id_struttura'],$outcont1);
    $outcont2 = ereg_replace("###-ID_STRUTTURA-###",$camplisting['id_struttura'],$outcont2);
    $outcont3 = ereg_replace("###-ID_STRUTTURA-###",$camplisting['id_struttura'],$outcont3);
    $outcont4 = ereg_replace("###-ID_STRUTTURA-###",$camplisting['id_struttura'],$outcont4);
    $outcont5 = ereg_replace("###-ID_STRUTTURA-###",$camplisting['id_struttura'],$outcont5);

    $outcont1 = banners($outcont1,$myoutfile1,$regione);
    $outcont2 = banners($outcont2,$myoutfile2,$regione);
    $outcont3 = banners($outcont3,$myoutfile3,$regione);
    $outcont4 = banners($outcont4,$myoutfile4,$regione);
    $outcont5 = banners($outcont5,$myoutfile5,$regione);
    $outcont1 = ereg_replace("###-LANGLINKS-###",$langlinks_1,$outcont1);
    $outcont2 = ereg_replace("###-LANGLINKS-###",$langlinks_2,$outcont2);
    $outcont3 = ereg_replace("###-LANGLINKS-###",$langlinks_3,$outcont3);
    $outcont4 = ereg_replace("###-LANGLINKS-###",$langlinks_4,$outcont4);
    $outcont5 = ereg_replace("###-LANGLINKS-###",$langlinks_5,$outcont5);
    $outcont1 = ereg_replace("###-SITENAV-###",$navpath,$outcont1);
    $outcont2 = ereg_replace("###-SITENAV-###",$navpath,$outcont2);
    $outcont3 = ereg_replace("###-SITENAV-###",$navpath,$outcont3);
    $outcont4 = ereg_replace("###-SITENAV-###",$navpath,$outcont4);
    $outcont5 = ereg_replace("###-SITENAV-###",$navpath,$outcont5);

    $f2 = "/var/www/html/new/domini/$domain/links/" .$camplisting['id_struttura'].".LNK." . $langnames[$lingua];
    $outcont1 = ereg_replace("###-LINK_OFFERTE-###","<?if (is_file(\"$f2\"))include(\"$f2\");?>",$outcont1);
    $outcont2 = ereg_replace("###-LINK_OFFERTE-###","<?if (is_file(\"$f2\"))include(\"$f2\");?>",$outcont2);
    $outcont3 = ereg_replace("###-LINK_OFFERTE-###","<?if (is_file(\"$f2\"))include(\"$f2\");?>",$outcont3);
    $outcont4 = ereg_replace("###-LINK_OFFERTE-###","<?if (is_file(\"$f2\"))include(\"$f2\");?>",$outcont4);
    $outcont5 = ereg_replace("###-LINK_OFFERTE-###","<?if (is_file(\"$f2\"))include(\"$f2\");?>",$outcont5);
    $outcont1 = ereg_replace('//feedback',"/feedback",$outcont1);
    $outcont2 = ereg_replace('//feedback',"/feedback",$outcont2);
    $outcont3 = ereg_replace('//feedback',"/feedback",$outcont3);
    $outcont4 = ereg_replace('//feedback',"/feedback",$outcont4);
    $outcont5 = ereg_replace('//feedback',"/feedback",$outcont5);


    }

    $outcont1 .= "<?
    \$page=1;
    \$id=" . $camplisting['id_struttura'] . ";
    include_once (\"/var/www/html/new/general/contatore\");?>";
    $outcont2 .= "<?
    \$page=2;
    \$id=" . $camplisting['id_struttura'] . ";
    include_once (\"/var/www/html/new/general/contatore\");?>";
    $outcont3 .= "<?
    \$page=3;
    \$id=" . $camplisting['id_struttura'] . ";
    include_once (\"/var/www/html/new/general/contatore\");?>";
    $outcont4 .= "<?
    \$page=4;
    \$id=" . $camplisting['id_struttura'] . ";
    include_once (\"/var/www/html/new/general/contatore\");?>";
    $outcont5 .= "<?
    \$page=5;
    \$id=" . $camplisting['id_struttura'] . ";
    include_once (\"/var/www/html/new/general/contatore\");?>";

    $outcont1 = unwhite($outcont1);
    $outcont2 = unwhite($outcont2);
    $outcont3 = unwhite($outcont3);
    $outcont4 = unwhite($outcont4);
    $outcont5 = unwhite($outcont5);

    $outcont1 = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$outcont1);
    $outcont2 = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$outcont2);
    $outcont3 = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$outcont3);
    $outcont4 = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$outcont4);
    $outcont4 = ereg_replace("$sitolink","",$outcont4);
    $outcont5 = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$outcont5);

    $outcont1 = ereg_replace("###-STELLE-###",$starhtml,$outcont1);
    $outcont2 = ereg_replace("###-STELLE-###",$starhtml,$outcont2);
    $outcont3 = ereg_replace("###-STELLE-###",$starhtml,$outcont3);
    $outcont4 = ereg_replace("###-STELLE-###",$starhtml,$outcont4);
    $outcont5 = ereg_replace("###-STELLE-###",$starhtml,$outcont5);
    

    $myurl = addslashes(ereg_replace ("/var/www/html/new/domini/","http://",$myoutfile1));
    $myname = addslashes($tito . " " .$camplisting['nome_struttura']);
    $myplace = addslashes($camplisting['localita_struttura']);

          $linxql = "insert into tb_camplinks (regione,provincia,localita,nome_struttura,lingua,url) values (\"$regione\",\"$sigla\",\"$myplace\",\"$myname\",\"$lingua\",\"$myurl\");";
          mysql_query($linxql);


          $fh2 =fopen($myoutfile1,"w");
          $outcont1 = ereg_replace("###-ID_STRUTTURA-###",$strucno,$outcont1);
          fputs($fh2,$outcont1);
          fclose($fh2);

          $fh2 =fopen($myoutfile2,"w");
          $outcont2 = ereg_replace("###-ID_STRUTTURA-###",$strucno,$outcont2);
          fputs($fh2,$outcont2);
          fclose($fh2);

          $fh2 =fopen($myoutfile3,"w");
          $outcont3 = ereg_replace("###-ID_STRUTTURA-###",$strucno,$outcont3);
          fputs($fh2,$outcont3);
          fclose($fh2);

          $fh2 =fopen($myoutfile4,"w");
          $outcont4 = ereg_replace("###-ID_STRUTTURA-###",$strucno,$outcont4);
          fputs($fh2,$outcont4);
          fclose($fh2);

          $fh2 =fopen($myoutfile5,"w");
          $outcont5 = ereg_replace("###-ID_STRUTTURA-###",$strucno,$outcont5);
          fputs($fh2,$outcont5);
          fclose($fh2);
    }

    }

    foreach($keys as $key){

      $string = "###-" . $key . "-###";
      $content = str_replace($string,$translate[$key],$content);
      $listcontent = str_replace($string,$translate[$key],$listcontent);
                
    }
      $contentpage = str_replace("###-LISTA_STRUTTURE-###",$listcontent,$content);
if ($lingua != 1 && $lingua != "ita")$pagestring = ereg_replace("campeggi-","camping-",$pagestring);
      $contentpage = ereg_replace("###-PAGING-###","$pagestring",$contentpage);
      $contentpage = ereg_replace("###-REGIONE-###","$regione",$contentpage);
      $contentpage = ereg_replace("###-PROVINCIAREGIONE-###",strtoupper("$regione"),$contentpage);

      $contentpage=banners($contentpage,$outfile,$regione);

      $contentpage = banners($contentpage,$outfile);
      $contentpage = unwhite($contentpage);
      $contentpage = ereg_replace("###-GO_SEARCH-###",$settings['link_sx_search'],$contentpage);


        $fh =fopen($outfile,"w");
          $contentpage = ereg_replace("###-ID_STRUTTURA-###",$strucno,$contentpage);
          fputs($fh,$contentpage);
          fclose($fh);

          echo "fatto\n";
          flush();
        }

##############DEBUG #

//if ($lingua != 1){ //echo $outfile; //}

        echo "\n";
        flush();
        
       }

    }

  }


  function buildprovince($domain,$regsnum="99999999"){
    global $settings, $lingua, $langnames;
    $field = array();
    $field2 = array();

// load tabelle valori

   $querya = "select id_location,nome_location from tb_location where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $location[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_location from tb_location where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
        if (strlen($arr[1]) > 0)$location[$arrh] = $arr[1];
      }
   }
   $querya = "select id_carta_credito,nome_carta_credito from tb_carte_credito where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $creditcard[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_carta_credito from tb_carte_credito where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
        $creditcard[$arrh] = $arr[1];
      }
   }
   $querya = "select id_carta_credito,nome_carta_credito from tb_carte_credito where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $creditcard[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_carta_credito from tb_carte_credito where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
        $creditcard[$arrh] = $arr[1];
      }
   }
   $querya = "select id_ristorazione,nome_ristorazione from tb_ristorazione where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $ristorazione[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_ristorazione from tb_ristorazione where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
//        if (strlen($ristorazione[$arrh]) > 0)$ristorazione[$arrh] = $arr[1];
        if (strlen($arr[1]) > 0)$ristorazione[$arrh] = $arr[1];
      }
   }
   $querya = "select id_posteggio,nome_posteggio from tb_posteggi_auto where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $parcheggi[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_posteggio from tb_posteggi_auto where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
        if (strlen($arr[1]) > 0)$parcheggi[$arrh] = $arr[1];
//        if (strlen($parcheggi[$arrh]) > 0)$parcheggi[$arrh] = $arr[1];
      }
   }
   $querya = "select id_servizio,nome_servizio from tb_servizi_strutture where id_lingua=1";
   $resulta = mysql_query($querya);
   while ($arr = mysql_fetch_array($resulta)){
     $arrh = $arr[0];
     $strucservizi[$arrh] = $arr[1];
   }
   if ($lingua != 1){
      $querya = "select rel_id,nome_servizio from tb_servizi_strutture where id_lingua=$lingua";
      $resulta = mysql_query($querya);
      while ($arr = mysql_fetch_array($resulta)){
        $arrh = $arr[0];
        if (strlen($arr[1]) > 0)$strucservizi[$arrh] = $arr[1];
      }
   }

// fine load tabelle valori

    $tipo_query = "select * from tb_tipologie_camping where id_lingua=$lingua;";
    $tipo_result = mysql_query($tipo_query);
    $campingtype = array();
    while ($tipo_arr = mysql_fetch_array($tipo_result)){
      $id = $tipo_arr['rel_id'];
      if ($id == 0)$id = $tipo_arr['id_tipologia'];
      $value = $tipo_arr['nome_tipologia'];
      $campingtype[$id] = $value;
    }

    // restituiamo un elenco di regioni con il quale possiamo lavorare
    // in mancanza del percorso questo viene creato

    $regions_query = "select distinct nome_provincia,id_provincia,id_regione,sigla_provincia from tb_province where id_lingua=$lingua;"; if ($regsnum != 99999999)  
$regions_query = "select distinct nome_provincia,id_provincia,id_regione,sigla_provincia from tb_province where id_province=$regsnum and id_lingua=$lingua;";
    $regions_query = "select distinct nome_provincia,id_provincia,id_regione,sigla_provincia from tb_province where id_lingua=1;"; if ($regsnum != 99999999)  $regions_query 
= "select distinct nome_provincia,id_provincia,id_regione,sigla_provincia from tb_province where id_province=$regsnum and id_lingua=1";
    $regions_result = mysql_query($regions_query);
    if (mysql_num_rows($regions_result) == 0){

      $regions_query = "select distinct nome_provincia,id_provincia,id_regione,sigla_provincia from tb_province where id_lingua=1;";
      $regions_result = mysql_query($regions_query);

    } // while ($regions_arr = mysql_fetch_array($regions_result)){
  

        echo "  controllo esistenza percorso base per elenchi province";
        echo $region . "... ";
        $f