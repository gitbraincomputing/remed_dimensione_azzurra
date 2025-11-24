<?php

include_once('dbengine.inc.php');
include_once('class.User.php');
include_once('class.Struttura.php');
//include_once('session.inc.php');


/****************************
* funzione is_superadmin()	    *
*****************************/
function is_superadmin(){

	if ( ($_SESSION['UTENTE']->get_type()== 0)) return true;
	else return false;

}

function is_admin(){

	if ( ($_SESSION['UTENTE']->get_type()== 1)) return true;
	else return false;

}


/****************************
* funzione controlla_permessi()*
*****************************/
function controlla_permessi2($curr_struct) {  

	$start_structure = $_SESSION['UTENTE']->get_start_struct();
	$flag = false;
	//$padre = get_padre($curr_struct);
	
	// se sei il super-admin setta il flag a true
	if($_SESSION['UTENTE']->is_root()) $flag = true;
	
	while($padre!=0)
	{   
	    if ($start_structure==$curr_struct)
		{
			$flag=true;
			break;
		}
		$curr_struct = $padre;
		//$padre = get_padre($padre);
    }
	
	return $flag;

}


/****************************
* funzione scrivi_log()		*
*****************************/
function scrivi_log($id, $tablename, $operazione) {

	$data = date('d/m/Y');
	$ora = date('H:i');
	$ip = getenv('REMOTE_ADDR');
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$conn = db_connect();
		
	switch($operazione) {

		case 'ins_op': 
			$query = "UPDATE $tablename SET datains='$data',orains='$ora',opeins='$ope',ipins='$ip' WHERE uid='$id'";
			break;

		case 'agg_op': 
			$select = "SELECT updcount FROM $tablename WHERE uid='$id'";
			$rs = mssql_query($select, $conn);
			if(!$rs) error_message(mssql_error());
			if(!$row=mssql_fetch_row($rs)) error_message(mssql_error());
			
			$query = "UPDATE $tablename SET updcount = ".($row[0]+1).",dataagg='$data',oraagg='$ora',opeagg='$ope',ipagg='$ip' WHERE uid='$id'";
			break;

		case 'ins': 
			$query = "UPDATE $tablename SET datains='$data',orains='$ora',opeins='$ope',ipins='$ip' WHERE id='$id'";
			break;

		case 'agg': 
			$select = "SELECT updcount FROM $tablename WHERE id='$id'";
			$rs = mssql_query($select, $conn);
			if(!$rs) error_message(mssql_error());
			if(!$row=mssql_fetch_row($rs)) error_message(mssql_error());
			
			$query = "UPDATE $tablename SET updcount = ".($row[0]+1).",dataagg='$data',oraagg='$ora',opeagg='$ope',ipagg='$ip' WHERE id='$id'";
			break;

	}	
//	echo $query;
	
	$rt = mssql_query($query, $conn);
	if(!$rt) error_message(mssql_error());
		
}


/****************************
* funzione briciole di pane	    *
*****************************/

function briciole_pane()
{
	global $PHP_SELF;

	$is_link=true;
	// crea l'oggetto
	/*$objStruttura = new Struttura();*/
	$objStruttura = $_SESSION['STRUTTURA1'];
	//echo "struttura =".$_SESSION['STRUTTURA'];
	$curr_struct = $_SESSION['STRUTTURA1']->get_id();
	$start_struct= $_SESSION['UTENTE']->get_start_struct();
	
	// recupera il padre
	//$padre = get_padre($curr_struct);

	$famiglia = array();

	//array_push($famiglia, "<a href=\"".$PHP_SELF."?do=goto&amp;id=".$curr_struct."\" title=\"vai a ".$row[0]."\">".$objStruttura->get_label()."</a> / ");
	array_push($famiglia, "<a title=\"sei in ".$objStruttura->get_label()."\">".$objStruttura->get_label()."</a>"); 
 	
	$conn = db_connect();
	
	while($padre!=0)
	{

		if($curr_struct==$start_struct) $is_link = false;
		
		// recupera il nome della curr_struct
		$query = "SELECT nome FROM strutture WHERE id='".$padre."'";
		$rt = mssql_query($query, $conn);
		if(!$rt) error_message(mssql_error());
		if(!$row = mssql_fetch_row($rt)) error_message(mssql_error());

		if($padre!=$start_struct)
		{
				$curr_struct = $padre;
				if($is_link) array_push($famiglia, "<a href=\"".$PHP_SELF."?do=goto&amp;id=".$curr_struct."\" title=\"vai a ".$row[0]."\">".$row[0]."</a> / ");
				else array_push($famiglia, "<span class=\"offline\"><a title=\"non hai accesso a questa struttura\">".$row[0]."</a></span> / ");
		}	
		else
		{
				$is_link=false;
				$curr_struct = $padre;
				array_push($famiglia, "<a href=\"".$PHP_SELF."?do=goto&amp;id=".$curr_struct."\" title=\"vai a ".$row[0]."\">".$row[0]."</a> / ");
		} 
		mssql_free_result($rt);
		
		//$padre=get_padre($curr_struct);	

	}//END WHILE

	//asort($famiglia);
	//reset($famiglia);
	// inverte l'array
	$briciole = array_reverse($famiglia);
	
	return $briciole;
	//foreach($briciole as $elem) print($elem);
	
}//END FUNCTION BRICIOLE DI PANE



/****************************
* funzione authorize_me	    *
*****************************/
function authorize_me($uid, $rule){

	$tablename = "users";


	// se hai settato il cookie..
	if(isset($_SESSION['UTENTE'])) {

		// se non sei l'amministratore..
		if(! ($_SESSION['UTENTE']->is_root())){

			if($rule=='none') return true;
			else{

				$conn = db_connect();

				$query = "SELECT cando".$rule." FROM $tablename where uid='$uid'";
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());

				if(! ($row=mssql_fetch_row($rs))) error_message(mssql_error());

				if($row[0] == 1) return true;	// accesso confermato
				else return false;		// accesso fallito

			}
		}

		// sei l'amministratore
		else return true;

	} else {
		ob_start();
		Header("Location: index.php");
		ob_end_flush();
	}
	return false;
}

/****************************
* funzione go_home()	    *
*****************************/
function go_home(){
	ob_start();
	Header("Location: main.php");
	ob_end_flush();
}

/****************************
* funzione get_padre()	    *
*****************************/
function get_padre($SID){

	$conn = db_connect();
	$query = "SELECT padre FROM strutture WHERE id='$SID'";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	if(!$row=mssql_fetch_row($rs)) error_message(mssql_error());
	return $row[0];
}



/*********************************
* funzione analizza funzioni *
**********************************/
function analizza_funzioni ($lista_funzioni){

	foreach($lista_funzioni as $funzione) {
	
		switch($funzione){
		
			case 'foto': 				foto();
										break;

			case 'vuoto_1': 			scegli_modulo(1);
										break;

			case 'vuoto_2': 			//scegli_modulo(2);
										break;

			case 'blocchi_dinamici': 	//blocchi_dinamici();
										break;
		
		// fine switch
		}
	
	// fine foreach
	}

}


/*********************************
* funzione scegli_modulo *
**********************************/
/*function scegli_modulo($n) {

	global $PHP_SELF;
	
	$conn = db_connect();
	$sid = $_SESSION['STRUTTURA1']->get_id();

	//if($n==1) {
	
		$query = "SELECT moduli.id, moduli.nome, moduli_disponibili.sid FROM moduli RIGHT JOIN moduli_disponibili ON moduli.id = moduli_disponibili.moduli_id WHERE (((moduli_disponibili.sid)='$sid'))";
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());

// 		print('<form method="post" action="moduli.php" enctype="multipart/form-data">'."\n");
// 		print('<input type="hidden" name="indice_modulo" value="'.$n.'" />'."\n");
		//print('<input type="hidden" name="valore_modulo" value="'.$n.'" />'."\n");
		print('blocco '.$n."<br>");
		print('<select name="modulo_'.$n.'">');
		print('<option value="-1">scegliere un modulo</option>'."\n");
		//print('<option value="0">spazio libero</option>'."\n");
		
		while($row=mssql_fetch_row($rs)) {
		
			print('<option value="'.$row[0].'">'.$row[1].'</option>'."\n");
		
		}
		print('</select>');

		print('<br /><br /><input type="submit" name="action" value="edita blocco '.$n.'" />');
// 		print('</form>');

	//}

	//else if($n==2) {
		
	//}
	
}*/

/****************************
* funzione recupera_firma() *
*****************************/
/*function recupera_firma($objNews) {

	$isroot= $_SESSION['UTENTE']->is_root();
	$mysign = $_SESSION['UTENTE']->get_signature();

	// scelta della firma
	if($isroot){

		// recupera la firma della news
		if(!empty($objNews))
			$autore = $objNews->get_signature();
		else $autore = $_SESSION['UTENTE']->get_userid();

		print('<select name="firma">'."\n");
		$conn = db_connect();
		$query = "SELECT uid,signature FROM users ORDER BY signature";
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());

		while($row = mssql_fetch_assoc($rs)){

			$uid = $row['uid'];
			$firma = stripslashes($row['signature']);

			print('<option value="'.$uid.'"');
			if($uid==$autore) print(' selected');
			print('>'.$firma.'</option>'."\n");
		}
		print('</select>'."\n");

		mssql_free_result($rs);
	}
	// firma del redattore
	else print ('<input type="hidden" name="firma" value="'.$_SESSION['UTENTE']->get_userid().'" />'.$_SESSION['UTENTE']->get_signature());
}*/


/****************************
* funzione wordlimit *
*****************************/
function wordlimit($string, $length = 20, $ellipsis = "..."){

   $paragraph = explode(" ", $string);

   if($length < count($paragraph))
   {
       for($i = 0; $i < $length; $i++)
       {
           if($i < $length - 1)
               $output .= $paragraph[$i] . " ";
           else
               $output .= $paragraph[$i] . $ellipsis;
       }

       return $output;
}

   return $string;
}

/****************************
* funzione html_header	    *
*****************************/
function html_header(){

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="it">
<head>
<title>Editoriale Publiaci - pannello di amministrazione</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<meta name="description" content="Pannello di amministrazione di Editoriale Publiaci" />
<meta name="author" content="Brain Computing LTD" />
<meta name="copyright" content="Copyright 2006 Brain Computing LTD" />
<meta name="language" content="italian" />
<?php
// recupera lo useragent
/*$browse = getenv("http_user_agent");
$str_da_cercare = "PPC" ;
if(!(eregi($str_da_cercare , $browse) )) {*/
?>
<link rel="stylesheet" media="screen" href="css/schermo.css" type="text/css" />
<link rel="stylesheet" type="text/css" media="print" href="css/print.css" />
<style type="text/css">

a#ant1 { background-image: url('images/homepage.gif'); }
a#ant2 { background-image: url('images/categorie.gif'); }
a#ant3 { background-image: url('images/strutture.gif'); }
a#ant4 { background-image: url('images/categorie.gif'); }
a#ant5 { background-image: url('images/utenti.gif'); }
a#ant6 { background-image: url('images/download.gif'); }
a#ant7 { background-image: url('images/news.gif'); }
a#ant8 { background-image: url('images/dossier.gif'); }
a#ant9 { background-image: url('images/pagine.gif'); }
a#ant10 { background-image: url('images/links.gif'); }
</style>
<?php include_once('code.js'); ?>
</head>
<body id="sfondo_grigio">

<table id="all" summary="tabella per l'impaginazione" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
<?php
}

/****************************
* funzione testata	    *
*****************************/
function testata($n) {
?>
	<!-- inizio testata -->
	<div id="testata2">

		<div class="left">
			<div id="logo">
				<a href="main.php" title="torna alla home" accesskey="h"><img src="images/logo.gif" alt="logo publiaci" /></a>
			</div>
		</div>

		<div id="testata_right">
		<?php
		if($n) {
			$conn = db_connect();
			
			$query = "select id,nome from menu where id='".$n."'";
			$result = mssql_query($query, $conn);
			if(!$result) error_message(mssql_error());
			$row = mssql_fetch_assoc($result);
			$id = $row['id'];
			$nome_menu = $row['nome'];
			mssql_free_result($result);
	
			print ('<div class="anteprima_gestione">'."\n");
			print ('<div class="logo_gestione">'."\n");
			//print ('<a id="ant'.$id.'" class="img_gestione" title="'.$nome_menu.'"></a>'."\n");
			print ('<a id="ant2" class="img_gestione" title="'.$nome_menu.'"></a>'."\n");
			print ('</div>'."\n");
			print ('<div class="testo_gestione">');
			print ('<small>'.$nome_menu.'</small>'."\n");
			print ('</div>'."\n");
			print ('</div>'."\n");
		}
	?>
		</div>

		<div class="center">
			<div id="titolo_pannello">Editoriale Publiaci<br />pannello di amministrazione</div>
		</div>

	</div>
	<!-- fine testata -->
<?php
}

/****************************
* funzione testata_login	    *
*****************************/
function testata_login() {
?>
	<!-- inizio testata -->
	<div id="testata">

		<div class="left"></div>

		<div class="right"></div>

		<div class="center">
		</div>

	</div>
	<!-- fine testata -->
<?php
}

/****************************
* funzione navbar()	    *
*****************************/
function navbar($objUser) {

	global $PHP_SELF;
?>
	<!-- inizio navbar -->
	<div id="navbar">

		<div class="left">
			<?php if($PHP_SELF!="/admin/main.php") { ?>
			<div class="loghetto">
				<div class="loghetto_img">
					<a href="main.php" title="torna alla home page">
					<img src="images/home_back.gif" alt="torna alla home page" />
					</a>
				</div>
				<div class="loghetto_scritta">
					<a href="main.php" title="torna alla home page" accesskey="h">
					[h] menu principale
					</a>
				</div>
			</div>
			<?php } ?>
		</div>

		<div class="right">
			<div class="loghetto">
				<div class="loghetto_img">
					<a href="<?php echo $PHP_SELF ?>?do=logout" title="disconnetti">
					<img src="images/disconnetti.gif" alt="disconnetti" />
					</a>
				</div>
				<div class="loghetto_scritta">
					<a href="<?php echo $PHP_SELF ?>?do=logout" title="disconnetti" accesskey="d">
					[d] disconnetti
					</a>
				</div>
			</div>
		</div>

		<div class="center">
			Benvenuto <strong><?php echo $_SESSION['UTENTE']->get_username(); ?></strong>.
			Il tuo gruppo di appartenenza &egrave; <strong>
			<?php
				// recupera il nome del gruppo di appartenenza
				$conn = db_connect();
				$query = "SELECT nome FROM gruppi WHERE gid='".$_SESSION['UTENTE']->get_gid()."'";
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
			
				if($row = mssql_fetch_assoc($rs)){
			
					print('<strong>'.$row['nome'].'</strong>');
				}
			?>
			</strong>.
		</div>

	</div>
	<!-- fine navbar -->

<?php
}

/****************************
* funzione menu($cmd)	    *
*****************************/
function menu($cmd, $back) {

	global $PHP_SELF, $id;

	/**********************************
		1	indietro
		2	aggiungi
		3	cancella
		4	cerca
		5	copia pagina
		6	visualizza ultime 20
		7	visualizza tutte
		8	visualizza le mie
		9	cambia nome
		10  sposta pagina
	***********************************/
?>
	<?php
	if($back) {
	?>
	<!-- inizio menu di destra -->
	<div class="right">
	
		<div id="titolo_menusin"><h1><small>operazioni</small></h1></div>
		<div id="menu_sin">
			<ul>
<?php

$i=0;

if(sizeof($cmd)!=0){


	foreach($cmd as $voce){

		$i++;
		switch($voce){

			case 2:
				print ('<li><a accesskey="'.$i.'" href="'.$PHP_SELF.'?do=add" title="aggiungi"><img src="images/aggiungi.gif"> aggiungi</a></li>'."\n");
				break;

			case 3:
				print('<li><a accesskey="'.$i.'" href="'.$PHP_SELF.'?do=confirm_del&id='.$id.'"><img src="images/cancella.gif"> cancella</a></li>'."\n");
				break;

			case 4:
				print ('<li><a accesskey="'.$i.'" href="ricerca.php" title="cerca">ricerca</a></li>'."\n");
				break;

			case 5:
				print ('<li><a accesskey="'.$i.'" href="'.$PHP_SELF.'?do=copia_pagina" title="copia una pagina"><img src="images/copia.gif"> copia pagina</a></li>'."\n");
				break;
			case 6:
				print ('<li><a accesskey="'.$i.'" href="'.$PHP_SELF.'?do=view_last" title="vedi ultime">['.$i.'] vedi ultime</a></li>'."\n");
				break;
			case 7:
				print ('<li><a accesskey="'.$i.'" href="'.$PHP_SELF.'" title="visualizza tutte">['.$i.'] vedi tutte</a></li>'."\n");
				break;
			case 8:
				print ('<li><a accesskey="'.$i.'" href="'.$PHP_SELF.'?do=view_my" title="vedi personali">['.$i.'] vedi personali</a></li>'."\n");
				break;
			case 9:
				print ('<li><a accesskey="'.$i.'" href="'.$PHP_SELF.'?do=cambia" title="cambia il nome">['.$i.'] cambia nome</a></li>'."\n");
				break;
			case 10:
				print ('<li><a accesskey="'.$i.'" href="'.$PHP_SELF.'?do=sposta_pagina" title="sposta una pagina"><img src="images/copia.gif"> sposta pagina</a></li>'."\n");
				break;
		

		}
	}

}
	if($back) print ('<li><a accesskey="'.($i+1).'" href="'.$back.'" title="indietro"><img src="images/indietro.gif"> indietro</a></li>'."\n");

?>
			</ul>
		</div>

		<div id="titolo_menusin"><h1><small>ricerca</small></h1></div>
		<div id="menu_sin">
		<form name="ricerca" method="post" action="ricerca.php">
			<ul>
				<li>
				struttura<br />
				<input type="text" name="q_strutture" class="campo_ricerca" />
				</li>

				<li>
				manager<br />
				<input type="text" name="q_managers" class="campo_ricerca" />
				</li>

				<li>
				prodotto<br />
				<input type="text" name="q_prodotti" class="campo_ricerca" />
				</li>

				<li>
				altro dato<br />
				<input type="text" name="q_altridati" class="campo_ricerca" />
				</li>

				<li>
				cerca <input type="image" src="images/cerca.gif" name="action" value="cerca" />
				</li>
			</ul>
		</form>
		</div>

	<?php
	}
	?>

	</div>
	<!-- fine menu di destra -->

<?php
}


/****************************
* funzione naviga()	    *
*****************************/
function naviga() {

	global $PHP_SELF;
	$objStruttura = $_SESSION['STRUTTURA1'];
?>
	<!-- inizio colonna sinistra -->
	<div class="left">

		<!-- inizio menu di sinistra -->
		<div id="titolo_menusin"><h1><small>navigazione</small></h1></div>
		<div id="naviga">
		<ul>
			<li>
			annuario selezionato<br />
			<?php
			if($_SESSION['ANNUARIO']) 
				print('<span class="id_notizia_cat2">'.$_SESSION['ANNUARIO'].'</span><strong>'.$_SESSION['NOME_ANNUARIO'].'</strong>'."\n");
			else print('<strong>TUTTI</strong>');
			?>
			</li>

			<li>
			settore selezionato<br />
			<?php
			if($_SESSION['SETTORE']) 
				print('<span class="id_notizia_cat2">'.$_SESSION['SETTORE'].'</span><strong>'.$_SESSION['NOME_SETTORE'].'</strong>'."\n");
			else print('<strong>TUTTI</strong>');
			?>
			</li>

			<li>
			regione selezionata<br />
			<?php
			if($_SESSION['REGIONE']) 
				print('<span class="id_notizia_cat2">'.$_SESSION['REGIONE'].'</span><strong>'.$_SESSION['NOME_REGIONE'].'</strong>'."\n");
			else print('<strong>TUTTE</strong>');
			?>
			</li>

			<li>
			provincia selezionata<br />
			<?php
			if($_SESSION['PROVINCIA']) 
				print('<span class="id_notizia_cat2">'.$_SESSION['PROVINCIA'].'</span><strong>'.$_SESSION['NOME_PROVINCIA'].'</strong>'."\n");
			else print('<strong>TUTTE</strong>');
			?>
			</li>

			<li class="indietro">
			<?php 
			$uri_arr = array(); 
			$uri_arr = explode('/',$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
			$uri = $uri_arr[(sizeof($uri_arr)-1)];
			?>
			<a href="cambia.php?page=<?php echo $uri; ?>"><img src="images/modifica.gif" /> mofifica</a>
			</li>
		</ul>
	</div>

	</div>
	<!-- fine menu di sinistra -->

<?php
}


/****************************
* funzione lettere_alfabetiche	    *
*****************************/
function lettere_alfabetiche() {
	
global $PHP_SELF;

print('<div id="lettere_alfabetiche">');

print('<div id="lettere">');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=a"><img src="images/letter_a.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=b"><img src="images/letter_b.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=c"><img src="images/letter_c.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=d"><img src="images/letter_d.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=e"><img src="images/letter_e.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=f"><img src="images/letter_f.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=g"><img src="images/letter_g.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=h"><img src="images/letter_h.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=i"><img src="images/letter_i.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=j"><img src="images/letter_j.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=k"><img src="images/letter_k.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=l"><img src="images/letter_l.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=m"><img src="images/letter_m.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=n"><img src="images/letter_n.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=o"><img src="images/letter_o.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=p"><img src="images/letter_p.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=q"><img src="images/letter_q.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=r"><img src="images/letter_r.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=s"><img src="images/letter_s.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=t"><img src="images/letter_t.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=u"><img src="images/letter_u.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=v"><img src="images/letter_v.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=w"><img src="images/letter_w.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=x"><img src="images/letter_x.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=y"><img src="images/letter_y.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list&letter=z"><img src="images/letter_z.gif"></a></div>');
print('<div class="lettera"><a href="'.$PHP_SELF.'?do=show_list"><img src="images/all.gif"></a></div>');
print('</div>'."\n");
	
}

/****************************
* funzione GestionePagine   *
*****************************/
function GestionePagine($num_rows, $page, $tablename) {

	global $PHP_SELF; 
	$maxpages = MAXPAGES;

	$conn = db_connect();

	// risultati per pagina
	$rpp = $_SESSION['NXPAGINA'];

	if ($page == 0) $page = 1;

	// recupera le pagine totali
	$pages = ceil($num_rows / $rpp);
	$subpages = ceil($pages / $maxpages);
	$subpage = (ceil($page / $maxpages)-1);
	// calcola la prima e l'ultima pagina da visualizzare
	$prima = (($subpage *$maxpages)+1);
	$ultima = ($prima+$maxpages-1);

	/*echo "pagina corrente ".$page."<br>";
	echo "subpagina corrente ".$subpage."<br>";
	echo "pagine totali ".$pages."<br>";
	echo "subpagine totali ".$subpages."<br>";
	echo "prima ".$prima."<br>";
	echo "ultima ".$ultima."<br>";*/

	print('<div class="numerazione">'."\n");

	if($subpage)
		print('<a href="'.$PHP_SELF.'?do=show_list&page='.($prima-1).'&tablename='.$tablename.'"><img src="images/indietro_indietro.gif" /></a> '."\n");

	if ($page > 1)
		print('<a href="'.$PHP_SELF.'?do=show_list&page='.($page-1).'&tablename='.$tablename.'" title="pagina precedente"><img src="images/indietro.gif" /></a> '."\n");

	for($i=$prima; $i<=$ultima; $i++){

		if($i <= $pages) {
			print('<span class="numero">');
			if($i==$page) print('<strong>'.$i.'</strong>'."\n");
			else print('<a href="'.$PHP_SELF.'?do=show_list&page='.$i.'&tablename='.$tablename.'" title="pagina successiva">'.$i.'</a>'."\n");
			print('</span>');
		}
	}

	if ($page < $pages)
		print(' <a href="'.$PHP_SELF.'?do=show_list&page='.($page+1).'&tablename='.$tablename.'" title="pagina successiva"><img src="images/avanti.gif" /></a>'."\n");

	if($ultima < $pages)
		print('<a href="'.$PHP_SELF.'?do=show_list&page='.($ultima+1).'&tablename='.$tablename.'"><img src="images/avanti_avanti.gif" /></a> '."\n");

	print('</div>'."\n");

	print('<div class="cambia_nxpagina">');
	print('<form method="post" action="'.$PHP_SELF.'">'."\n");
	print('<input type="text" name="nxpagina" size="4" maxlength="4" value="'.$_SESSION['NXPAGINA'].'" />');
	print('<input type="submit" name="action" value="aggiorna" />');
	print('</form>'."\n");
	print('</div>'."\n");

	print('</div>'."\n");
	?>
	<hr class="hide" />
<?php
}


function cambia_nxpagina($value) {

	$_SESSION['NXPAGINA'] = $value;
}

/****************************
* funzione pulisci	    *
*****************************/
function pulisci($testo) {

	$munnezz = array("", "", "", "&", "", "");
	$pulito = array("'", "\"", "\"", "&amp;", "&lt;", "&gt;");
	return str_replace($munnezz, $pulito, $testo);
}


/****************************
* funzione spalla	    *
*****************************/
function spalla() {

	global $PHP_SELF;
?>
		<!-- inizio colonna destra -->
		<div class="right">
		</div>
		<!-- fine colonna destra -->
<?php
}

/**************************
* funzione open_center	    *
*****************************/
function open_center() {

	global $PHP_SELF;
?>

		<!-- inizio contenuto centrale -->
		<div class="center">
<?php
}

/****************************
* funzione close_center	    *
*****************************/
function close_center() {

	global $PHP_SELF;
?>
		<div id="credits" align="center">Editoriale Publiaci - copyrights 2006<br /><a href="http://www.braincomputing.com"><img src="images/brain.gif" title="vai al sito di Brain Computing"></a></div>
		</div>
		<!-- fine contenuto centrale -->
<?php
}

/**************************
* funzione open_center_home*
*****************************/
function open_center_home() {

	global $PHP_SELF;
?>
		<!-- fine colonna sinistra -->
		</div>

		<!-- inizio contenuto centrale -->
		<div class="center_home">
<?php
}


/********************************
* funzione icone_permessi	*
*********************************/
function icone_permessi($id, $id_menu) {

	global $PHP_SELF;

	$gid = $_SESSION['UTENTE']->get_gid();
	$conn = db_connect();
	$query = "select permessi from gruppi where gid='".$gid."'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	$row = mssql_fetch_assoc($result);
	$stringa_permessi = $row['permessi'];
	mssql_free_result($result);
	$array_stringa_permessi = explode(',',$stringa_permessi);

	foreach($array_stringa_permessi as $element) {
		
		sscanf($element,"%d-%s",$idm,$idp);
			
		if ($idm==$id_menu) {
			$a=$idp{0}; 
			$e=$idp{1}; 
			$c=$idp{2}; 
			break;
		}
    }

	print('<span class="icone">'."\n");
	//print('<a href="'.$PHP_SELF.'?do=down&id='.$id.'"><img class="icona_singola" src="images/giu.gif" alt="sposta gi&ugrave;" /></a>'."\n");
	//print('<a href="'.$PHP_SELF.'?do=up&id='.$id.'"><img class="icona_singola" src="images/su.gif" alt="sposta su" /></a>'."\n");
	if ($c=='y') 
		print('<a href="'.$PHP_SELF.'?do=confirm_del&id='.$id.'"><img class="icona_singola" src="images/cancella.gif" alt="cancella" /></a>'."\n");
	else
		print('<img class="icona_singola" src="images/cancella_no.gif" alt="cancella" />'."\n");
	
	if ($e=='y')
		print('<a href="'.$PHP_SELF.'?do=edit&id='.$id.'"><img class="icona_singola" src="images/edita.gif" alt="edita" /></a>'."\n");
	else
		print('<img class="icona_singola" src="images/edita_no.gif" alt="edita" />'."\n");
	
	print('</span>'."\n");

}

/********************************
* funzione semaforo		*
*********************************/
function semaforo($corretto) {

print('<span class="semaforo">');
if($corretto=='y') print('<img src="images/sem_verde.gif" alt="corretto" />');
else print('<img src="images/sem_rosso.gif" alt="non corretto" />');
print('</span>');
}

/****************************
* funzione html_footer	    *
*****************************/
function html_footer() {

	global $PHP_SELF;

	/*echo "annuario ".$_SESSION['ANNUARIO']."<br>";
	echo "settore ".$_SESSION['SETTORE']."<br>";
	echo "regione ".$_SESSION['REGIONE']."<br>";*/

	/*if(isset($_SESSION['NEWS'])) echo "news:".$_SESSION['NEWS']->get_id();
	if(isset($_SESSION['ARCHIVIO']))echo "<br>archivio:".$_SESSION['ARCHIVIO']->get_id();
	if(isset($_SESSION['DOSSIER'])) echo "<br>dossier:".$_SESSION['DOSSIER']->get_id();
	if($_SESSION['HOME']) echo "home =".$_SESSION['HOME'];
	if(isset($_SESSION['TABLE_HOME'])) echo "tabella home =".$_SESSION['TABLE_HOME'];
	if(isset($_SESSION['HOME_FLASH'])) echo "notizia flash:".$_SESSION['HOME_FLASH'];
	if(isset($_SESSION['HOME_PPIANO'])) echo "primo piano:".$_SESSION['HOME_PPIANO'];*/
	//echo "<br>struttura = ".$_SESSION['STRUTTURA1']->get_label();
	//print_r($_SESSION['PERMESSI']);
?>
<img src="images/spacer.gif" alt="fine pagina" class="minwidth" />
</td>
</tr>
</table>

</body>
</html>
<?php
}

/****************************
* funzione error_message    *
*****************************/
function error_message($msg) {

echo "<SCRIPT language=\"JavaScript\">alert(\"$msg\");history.go(-1)</SCRIPT>";
exit;
}

/****************************
* funzione confirm   *
*****************************/
function confirm($msg, $url) {
echo "<SCRIPT language=\"JavaScript\">alert(\"$msg\");window.location='".$url."';</SCRIPT>";
exit;
}


/****************************
* funzione logout	    *
*****************************/
function logout(){

	setcookie("usrPUBLIACI","", 0);
	// rimuove tutte le variabili di sessione dell'utente
	unset($_SESSION['UTENTE']);
	//unset($_SESSION['STRUTTURA1']);
	unset($_SESSION['PERMESSI']);
	
	if (isset($_COOKIE['PIN_INFOCERT'])) {
		unset($_COOKIE['PIN_INFOCERT']);
		setcookie('PIN_INFOCERT', '', time() - 3600, '/'); 
	}
	
	//unset($_SESSION['PAGINA']);
	//unset($_SESSION['AZIONE']);
	//unset($_SESSION['IMG']);
	//unset($_SESSION['ALT_IMG']);
	//unset($_SESSION['MODULO']);
	//unset($_SESSION['MODULO_ID']);
	//unset($_SESSION['MODULI_ATTIVI']);
	//unset($_SESSION['I']);
	
	ob_start();
	$link = "index.php";
	Header("Location: ". $link);
	ob_end_flush();
}
?>