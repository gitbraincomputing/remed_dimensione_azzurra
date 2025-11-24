<?php

// file di inclusione
include_once('include/session.inc.php');
//include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/class.User.php');



// nome della tabella
$tablename = "operatori";
$tablename_download = "download_files";
static $errmsg = "";

/**
	SCRIPT BROWSER DETECTION
**/
function browser_detection( $which_test ) 
{
	// initialize variables
	$browser_name = '';
	$browser_number = '';
	// get userAgent string
	$browser_user_agent = strtolower( $_SERVER['HTTP_USER_AGENT'] );
	//pack browser array
	// values [0]= user agent identifier, lowercase, [1] = dom browser, [2] = shorthand for browser,
	$a_browser_types[] = array('opera', true, 'op' );
	$a_browser_types[] = array('msie', true, 'ie' );
	$a_browser_types[] = array('konqueror', true, 'konq' );
	$a_browser_types[] = array('safari', true, 'saf' );
	$a_browser_types[] = array('gecko', true, 'moz' );
	$a_browser_types[] = array('mozilla/4', false, 'ns4' );

	for ($i = 0; $i < count($a_browser_types); $i++)
	{
		$s_browser = $a_browser_types[$i][0];
		$b_dom = $a_browser_types[$i][1];
		$browser_name = $a_browser_types[$i][2];
		// if the string identifier is found in the string
		if (stristr($browser_user_agent, $s_browser)) 
		{
			// we are in this case actually searching for the 'rv' string, not the gecko string
			// this test will fail on Galeon, since it has no rv number. You can change this to 
			// searching for 'gecko' if you want, that will return the release date of the browser
			if ( $browser_name == 'moz' )
			{
				$s_browser = 'rv';
			}
			$browser_number = browser_version( $browser_user_agent, $s_browser );
			break;
		}
	}
	// which variable to return
	if ( $which_test == 'browser' )
	{
		return $browser_name;
	}
	elseif ( $which_test == 'number' )
	{
		return $browser_number;
	}

	/* this returns both values, then you only have to call the function once, and get
	 the information from the variable you have put it into when you called the function */
	elseif ( $which_test == 'full' )
	{
		$a_browser_info = array( $browser_name, $browser_number );
		return $a_browser_info;
	}
}

// function returns browser number or gecko rv number
// this function is called by above function, no need to mess with it unless you want to add more features
function browser_version( $browser_user_agent, $search_string )
{
	$string_length = 8;// this is the maximum  length to search for a version number
	//initialize browser number, will return '' if not found
	$browser_number = '';

	// which parameter is calling it determines what is returned
	$start_pos = strpos( $browser_user_agent, $search_string );
	
	// start the substring slice 1 space after the search string
	$start_pos += strlen( $search_string ) + 1;
	
	// slice out the largest piece that is numeric, going down to zero, if zero, function returns ''.
	for ( $i = $string_length; $i > 0 ; $i-- )
	{
		// is numeric makes sure that the whole substring is a number
		if ( is_numeric( substr( $browser_user_agent, $start_pos, $i ) ) )
		{
			$browser_number = substr( $browser_user_agent, $start_pos, $i );
			break;
		}
	}
	return $browser_number;
}


/****************************
* funzione html_header	    *
*****************************/
function html_header(){

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<? /*if (browser_detection('browser') != 'moz' ){ ?>
<script type="text/javascript">
	window.location = "getfirefox.php";
</script>
<? }*/ ?>

<script>
var home=true;	
	
</script>
<script type="text/javascript" src="script/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="script/ddaccordion.js"></script>
<script type="text/javascript" src="script/jquery.form.js"></script> 	
<script type="text/javascript">
function disabilitaTuttiIForm(){
	$("form").submit(function(){
			$(this).find("input[type=submit]").each(function(){
				//alert("disabilito");
				$(this).attr("disabled","true");
			});
	});
};

function abilitaTuttiIForm(){
	$("form").find("input[type=submit]").each(function(){
		//alert("disabilito");
		$(this).removeAttr("disabled");
	});
};
</script> 
<script src="M_Validation/M_global.js?version=4" type="text/javascript">/**/</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php print VERSIONE; ?></title>

<style>
body{
	margin: 0;
	text-align:left;
	background-color:#FFF;
	font-family:Arial, Sans-Serif;
	font-size:0.75em;
	background: #d6dde5 url(images/sfondo_login.gif) top center no-repeat;	
	}

img, img a{
	border: none;
}

.box-aut{
	margin: 40px auto 0 auto;
	background-color: #FFF;
	width: 600px;
	padding: 30px;
}
.content_all{
	width: 540px;
	background: #FFF;
	float: left;
}
.error{
	float: left;
	padding: 0px 0;
	color: #FF0000;
}
.okmsg{
	float: left;
	padding: 0px 0;
	color: #009900;
}
.form{
	float: left;
	clear: both;
	padding-bottom: 10px;
}

form {
	margin: 0;
	padding: 0;
}

form input.form_element{
	border: 1px solid #CCC;
	background: #CCC;
	width: 250px;
}

form input.button{
	border: 1px solid #990000;
	background: #990000;
	width: 100px;
	padding: 0px 0;
	color: #FFF;
	margin-top: 10px;
}

.credits{
	margin-top: 10px;
	font-size: 9px;
	margin-bottom: 0;
	padding-bottom: 0;
	text-align: right;
	float: left;
	clear: both;
	width: 100%;
}

.error{
	float: left;
	padding-bottom: 10px;
}


div.mandatory {
float: left;
padding-left: 17px;
background: url('M_Validation/images/mandatory.png') center left no-repeat;
}
div.focused {
float: left;
padding-left: 17px;
background: url('M_Validation/images/focused.png') bottom left no-repeat;}
div.correct {
float: left;
padding-left: 17px;
background: url('M_Validation/images/correct.gif') bottom left no-repeat;}
	
div.warning { 
	width: auto;
	float: left;
	padding: 0;
	color: #ff0000;
	clear: both;
}
.b_key u{
	cursor: pointer;
}
</style>
</head>
<body>
<?php
}

/****************************
* funzione html_footer	    *
*****************************/
function html_footer() {

?>
<script language="javascript">
function businessKey(id){	
	var dischi = new Array('D','E','F','G','H','I','L','M','N','O','P','Q','R','S','T','U','V','Z','X','Y','J');
	var lettura="";
	var i = 0;
	
	while(lettura==""&&i<dischi.length){
		lettura = read(dischi[i]+':\\\InfoCamere\\cert.cer');
		i++;
	}
	
	if(lettura==""){
		$(".b_key").html("<strong>businesskey non rilevata</strong> <u>rileva</u>");
		}
	else{ 
		getUser(lettura);
	}
	
	function getUser(cert){		 
		 $.ajax({
		   type: "POST",
		   url: "cert_ajax.php",
		   data: "id="+id+"&certificato="+cert,
		   success: function(msg){
			  if($.trim(msg)!=""){
				 $("#u").val($.trim(msg));
				 $("#k").val("<?echo(CHIAVE);?>");
				 $("#p").focus();
			 }
			 if($.trim(msg)=="")$(".b_key").html("<u>rileva businesskey</u>");
			 else $(".b_key").html("<strong>businesskey rilevata</strong>");
			 //alert( "Data Saved: " + msg );
		   }
		 });
	}
	
	function read(myfile) {
	try
	//{netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");}
	{netscape.security.PrivilegeManager.enablePrivilege("UniversalPreferenceRead");
	netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	}
	catch (e) {/*alert("Permission to read file denied.");*/ return '';}

	var file = Components.classes["@mozilla.org/file/local;1"].createInstance(Components.interfaces.nsILocalFile );
	file.initWithPath(myfile);

	if (!file.exists()) {/*alert("File not found."); */return '';}

	var is =
	Components.classes["@mozilla.org/network/file-input-stream;1"].createInstance(
	Components.interfaces.nsIFileInputStream );
	is.init(file,0x01, 00004, null);
	var sis =
	Components.classes["@mozilla.org/scriptableinputstream;1"].createInstance(
	Components.interfaces.nsIScriptableInputStream );
	sis.init(is);
	var output = sis.read(sis.available());
	return output;
	}
}

</script>

</body>
</html>
<?php
}


/****************************
* funzione login()          *
*****************************/
function login() {

	global $PHP_SELF, $tablename, $errmsg, $uid;

	$user_post=pulisci(trim($_POST['u']));
	$pw_post=pulisci(trim($_POST['p']));
	$key_post=pulisci(trim($_POST['k']));
	$pin_post=pulisci(trim($_POST['pin']));
	
	$conn = db_connect();
	$qy = "SELECT username,usa_firma FROM ".$tablename." WHERE username='".$user_post."'";
	$rs = mssql_query($qy, $conn);
	$row_f=mssql_fetch_assoc($rs);
	$usafirma=$row_f['usa_firma'];
	$_SESSION['businesskey_usafirma']=$usafirma;
	mssql_free_result($rs);
	//$usafirma=1;
	//$errmsg .=$qy;
	if((($usafirma==1)and($_SESSION['businesskey_inserita']=="si")) or($usafirma==0)){
		// verifica che il campo username non sia vuoto
		if(empty($user_post)){
			$errmsg .= "inserire una username";
			
		} else {

			//$conn = db_connect();
			// cripta la pwd con la chiave di default
			$pass_crypt = crypt($pw_post,SALE);
			// esegue la query al db
			$qy = "SELECT uid,gid,username,password,is_root,status,usa_firma,check_pass,check_pass_60 FROM ".$tablename." WHERE cancella='n'";
			$rs = mssql_query($qy, $conn);

			if(!$rs) error_message(mssql_error());

			while( list($userid,$gid,$username,$password,$is_root,$status,$usafirma,$check_pass,$check_pass_60) = mssql_fetch_row($rs) ) {
				// se l'username non combacia, continua			
				if($user_post != $username) continue;
		    	if ( ($user_post == $username) && ($pass_crypt == $password) && ($key_post == CHIAVE) && $status) {
			    	$query_reset="UPDATE ".$tablename." SET tentativi_pass=0 WHERE (uid='$userid')";
					
					// se inserito, memorizzo il pin criptato in un cookie per inviarlo ad ogni richiesta di firma digitale
					if(!empty($pin_post)) 
					{
					/*
						$key_crypt= substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 4); //una stringa random crittograficamente sicura
						$query_crypt = "UPDATE ".$tablename." SET decrypt_key_pin_firma_digitale='$key_crypt' WHERE uid = $userid";
						$rs_crypt = mssql_query($query_crypt, $conn);
						
						$pin_criptato = "";
						
						//XOR pin
						for($i=0; $i<strlen($pin_post); )
						{
							for($j=0; ($j<strlen($key_crypt) && $i<strlen($pin_post)); $j++,$i++)
							{
								$pin_criptato .= $pin_post{$i} ^ $key_crypt{$j};
								//echo 'i=' . $i . ', ' . 'j=' . $j . ', ' . $outText{$i} . '<br />'; // For debugging
							}
						} 
					*/
						
						setcookie('PIN_INFOCERT', $pin_post, time() + (86400 * 30), "/"); // 86400 = 1 day
					}
			    	//echo($query_reset);
			    	//exit();
                    $result = mssql_query($query_reset, $conn);
	                if(!$result)
	                {		
			    	    echo("no");
				        exit();
				        die();
	                }else{
					    if($check_pass=='y'){
						    // avvia reimposta password impostata dall'amministratore
						    $chars="abcdefghijklmnopqrstuvwxyz0123456789";
     	                    $len=36;
	                        $stringa_generata= rand_string($len,$chars);
                    	    $data = date('d/m/Y');
                    	    $ora = date('H:i');
	                        $datadioggi=$data." ".$ora.":00";
	                        $query2="UPDATE ".$tablename." SET recupera_pass='$stringa_generata', data_recupera_pass='$datadioggi' WHERE (uid='$userid')";                          
                            $rs2 = mssql_query($query2,$conn);
	                        html_header();
		                    caricaFormPass($stringa_generata,$errmsg,$okmsg);
		                    html_footer();
						    exit();
						}
						if($check_pass_60=='y'){
						    // controlla se sono passati G_REST_PASS giorni dalla richiesta, se la risposta è positiva al login chiede di reimpostare la password
							$query_pass_60="SELECT * FROM ".$tablename." WHERE (uid='$userid')";
			                $result_pass_60 = mssql_query($query_pass_60, $conn);
							if($row_pass_60 = mssql_fetch_assoc($result_pass_60)){
                                $data_pass_60=formatta_data($row_pass_60['data_pass_60']);
								$data = date('d/m/Y');
                    	        $ora = date('H:i');
	                            $datadioggi=$data." ".$ora.":00";
                                /*echo($datadioggi);
								echo('------');
								echo($data_pass_60);
								*/
								$diff_in_giorni=diff_in_giorni($datadioggi,$data_pass_60);
								/*echo("diff_in_giorni");
								echo($diff_in_giorni);
								exit();*/
								
								if($diff_in_giorni>=G_REST_PASS){
                                    $chars="abcdefghijklmnopqrstuvwxyz0123456789";
     	                            $len=36;
	                                $stringa_generata= rand_string($len,$chars);
   								    $query2="UPDATE ".$tablename." SET recupera_pass='$stringa_generata', data_recupera_pass='$datadioggi' WHERE (uid='$userid')";                          
                                    $rs2 = mssql_query($query2,$conn);
	                                html_header();
		                            caricaFormPass($stringa_generata,$errmsg,$okmsg);
		                            html_footer();
						            exit();	
								}							
							}
						}
                        // crea l'oggetto Utente
		        		$obj_User = new User();
		         		$obj_User->create($userid);
		        		// imposta il cookie
		         		//	setcookie("usrPUBLIACI",$username.$password, 0)
				    	//prelevo i dati che mi servono per creare dinamicamente le icone dei menu
					
			         	$db = odbc_connect(DSN, DB_USER, DB_PASSWORD);
			     		$res  = odbc_exec ($db, "SET TEXTSIZE ".(2 * MAX));
			    		$res = odbc_exec ($db, "SELECT permessi FROM gruppi WHERE gid='".$_SESSION['UTENTE']->get_gid()."'");
			    		odbc_longreadlen ($res, MAX);
			    		$permessi = odbc_result ($res, "permessi");		
		    	    	odbc_close($db);
		        		$stringa_permessi= $permessi;
					
		    	    	/*
			     		$query = "select permessi from gruppi where gid='".$_SESSION['UTENTE']->get_gid()."'";
			   	    	$result = mssql_query($query, $conn);
			     		if(!$result) error_message(mssql_error());
		        		if($row = mssql_fetch_assoc($result)) 
			    		$stringa_permessi = $row['permessi'];
		    	    	mssql_free_result($result);*/	
		        		$array_stringa_permessi = explode(',',$stringa_permessi);
			    		$temp_array = array();
			    		foreach($array_stringa_permessi as $element) {
					        sscanf($element,"%d-%s",$idm,$idp);
				     	    array_push($temp_array, $idm);
				    	}
				    	// inserisce in sessione i permessi
				     	$_SESSION['PERMESSI'] = $temp_array;
			        	$_SESSION['ANNUARIO'] = 0;
		    	    	$_SESSION['SETTORE'] = 0;
			        	$_SESSION['REGIONE'] = 0;
			          	$_SESSION['PROVINCIA'] = 0;
			    		$_SESSION['NXPAGINA'] = 100;

			    	   	//cancella_files();
			    		//scrivi_log("index.php","login");
			     		return true;
                    }
				} else  if ( ($user_post == $username)) {
					$LoginFailed = "yes";
					if($pass_crypt != $password) 
					    if($key_post != CHIAVE) $errmsg .= "chiave errata o mancante<br />";
				} else {
					$LoginFailed = "yes";
					$errmsg .= "username non valida";
				}
			}
			// autenticazione fallita
			$errmsg .= "login fallito!";
			return false;
			// chiude la connsessione al db
			//mssql_close();
		}
	}else{
		$errmsg .= "accesso possibile solo con businesskey inserita";
	}
	
	return false;
}

/****************************
* funzione load($errmsg)    *
*****************************/
function load($errmsg) {
	global $PHP_SELF;
	//open_center_home();
?>
<script language="javascript">
$(document).ready(function(){
	<? if($_REQUEST['do']!='logout'){?>
	businessKey(1);<?}else{?>
	$(".b_key").html("<u>rileva businesskey</u>");
	<?}?>
	$(".b_key").click(function(){		
		$(".b_key").html("rilevazione businesskey in corso...");
		businessKey(1);
	});
});

</script>
<form action="index.php" method="post">
	<input type="hidden" name="action" value="login" />

    <div class="box-aut"><div class="content_all">
        <img src="images/re-med-logo.png" style="padding-bottom: 15px;" />
		<div></div>
        <? if($errmsg){ ?>
        <div class="error"><strong>ATTENZIONE: <?=$errmsg?></strong></div>
        <? } ?>
		<div>
            <div class="form mandatory">
        	    username: <span class="b_key">rilevazione businesskey in corso... </span><br />
                <input type="text" class="form_element" name="u" id="u" />
            </div>
		</div>
		<div>
            <div class="form mandatory">
            	password:<br />
                <input type="password" class="form_element" name="p" id="p"/>
            </div>
		</div>
		<div>
            <div class="form mandatory">
            	chiave:<br />
                <input type="password" class="form_element" name="k" id="k" />
            </div>
		</div>
		<div>
            <div class="form">
            	PIN firma digitale:<br />
                <input style="margin-left: 16px" type="password" class="form_element" name="pin" id="pin" placeholder="se in possesso"/>
            </div>
		</div>
        <div class="form">
        	<input class="button" type="submit" value="accedi" />
        </div>
		</div>
		<div class="form">
		<a  href="index.php?do=reimposta">Password dimenticata?</a>
        </div>
        <div class="credits">
        powered by <a href="http://www.braincomputing.com" target="_blank"><img src="images/logo_brain.gif" title="Brain Computing" /></a>
		
    </div>
    <br style="clear: both;"/></div>
</form>
<?php
	//close_center();
}



/****************************
* funzione stringa random   *
*****************************/

function rand_string($len, $chars)
{
    $string = "";
    for ($i = 0; $i < $len; $i++)
    {
        $pos = rand(0, strlen($chars)-1);
        $string .= $chars{$pos};
    }
    return $string;
}



/****************************
* funzione reimposta password   *
*****************************/

function reimposta_pass() {

error_reporting(E_ALL);
ini_set('display_errors', 1);

	global $PHP_SELF, $tablename, $errmsg, $okmsg;
	
//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;

require './script/phpmailer/PHPMailerAutoload.php';

//Create an instance; passing `true` enables exceptions
	$mail = new PHPMailer;

	try {
		
		$conn = db_connect();
		$username=pulisci(trim($_POST['username']));
		$email=pulisci(trim($_POST['email']));

		$query1 = "SELECT * FROM ".$tablename." WHERE (username='$username') AND (email='$email')";
		//echo($query);
		$rs = mssql_query($query1, $conn);
		$row=mssql_fetch_assoc($rs);
		$username_db=$row['username'];
		$email_db=$row['email'];
		$tentativi_pass=$row['tentativi_pass'];
		//exit();
		if(($username_db=="") || ($email_db=="")){
			$errmsg.="username o e-mail non presenti nel sistema!";
			return false;
		}elseif($tentativi_pass==3){
		   $errmsg.="Reimposta password bloccato per ".$username_db.", contattare l'amministratore del sistema!";
			return false;
		}
		$chars="abcdefghijklmnopqrstuvwxyz0123456789";
		$len=36;
		$stringa_generata= rand_string($len,$chars);
		$tentativi_pass=$tentativi_pass + 1; 
		$data = date('d/m/Y');
		$ora = date('H:i');
		$datadioggi=$data." ".$ora.":00";



		$query2="UPDATE ".$tablename." SET recupera_pass='$stringa_generata', data_recupera_pass='$datadioggi', tentativi_pass=$tentativi_pass WHERE (username='$username_db') AND (email='$email_db')";
		//echo($query2);
		$rs2 = mssql_query($query2,$conn);
		//echo($datadioggi);
		//echo($orarioodierno);
		//exit();

  
		//Server settings
		//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                    // Enable verbose debug output
		$mail->isSMTP();                                            // Send using SMTP
		$mail->Host       = 'mx.braincomputing.com';                // Set the SMTP server to send through
		$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
		$mail->Username   = 'remed@braincomputing.com';             // SMTP username
		$mail->Password   = 'z]0Bo!4u';                             // SMTP password
		$mail->SMTPSecure = 'ssl';                            		// Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;         

		//Recipients
		$mail->setFrom('remed@braincomputing.com', 'reMed');		// Mittente
		$mail->addAddress($email_db);              					// Name is optional
		
	

		//Content
		$mail->isHTML(true);                                  //Set email format to HTML
		$mail->Subject = "ReMed password reset";
		$mail->Body  = "Messaggio inoltrato automaticamente dal sistema software ReMed per reimpostare la password di " . $username_db."<br><br><br>";
		$mail->Body .= "Cliccare sul seguente link per reimpostare la password:\n ".IP_ADDR_REC_PASS."?do=cambio_password&stringa=".$stringa_generata;
		$mail->Body .= "<br>(link valido solo per le postazioni interne alla LAN aziendale)<br><br>";
		$mail->Body .= "Se il link non è selezionabile, copialo e incollalo nella barra degli indirizzi del browser.";
		$mail->Body .= "<br><br><br>ReMed Software";

		$mail->send();
		
		$okmsg.= "Richiesta di aggiornamento password effettuata con successo, controllare la propria e-mail";
		return true;
		
		
		
	} catch (Exception $e) 
	{
		$errmsg .= "Problemi nell'invio della e-mail, contattare l'amministratore!";
	    return false;
	}
	
}   


	
	

/****************************
* funzione carica form per reimposta password load($errmsg)    *
*****************************/
function load_reimposta($errmsg,$okmsg) {
	global $PHP_SELF;
	//open_center_home();
?>

<form action="index.php" method="post">
	<input type="hidden" name="action" value="reimposta_pass" />

    <div class="box-aut"><div class="content_all">
        <img src="images/re-med-logo.png" style="padding-bottom: 15px;" /> 
        <h3>Reimposta password</h3>
        <? if($errmsg){ ?>
        <div class="error"><strong>ATTENZIONE: <?=$errmsg?></strong></div>
        <? } 
		   if(!$okmsg){
		?>
		<div>
			<div class="form mandatory">
				username:<br />
				<input class="form_element" type="text" name="username" id="username" />
			</div>
		</div>
		
		<div>
			<div class="form mandatory email">
				e-mail:<br />
				<input class="form_element" type="text"  name="email" id="email"/>
			</div>
		</div>
        
		<div class="form">
        	<input class="button" type="submit" value="inoltra richiesta" />
        </div>
		 <?}else{?>
		 <div class="okmsg"><strong><?=$okmsg?></strong></div>
		 <?}?>
		</div>
		<div class="form">
		<a  href="index.php">ritorna a login</a>
        </div>
        <div class="credits">
        powered by <a href="http://www.braincomputing.com" target="_blank"><img src="images/logo_brain.gif" title="Brain Computing" /></a>
		
    </div>
    <br style="clear: both;"/></div>
</form>
<?php
	//close_center();
}
    //carica pagina
/****************************
* funzione differenza tra date	    *
*****************************/

     

function caricaFormPass($recupera_pass,$errmsg,$okmsg){
$vediPass="";
//$errmsg="";
//$recupera_pass =($_REQUEST['stringa']);

$conn = db_connect();

$query1 = "SELECT * FROM operatori WHERE (recupera_pass='$recupera_pass')";
//echo($query1);
//exit();
$rs = mssql_query($query1,$conn);

	if($row=mssql_fetch_assoc($rs)){
       
	    $data_recupera_pass=formatta_data_rec_pw($row['data_recupera_pass']);
		
        $data = date('m/d/Y');
	    $ora = date('H:i');
	    $datadioggi=$data." ".$ora.":00";
		
		$data_recupera_pass = strtotime($data_recupera_pass, 0);    
		
        $datadioggi = strtotime($datadioggi, 0);
        
		$difference = $datadioggi - $data_recupera_pass; 
        $datediff = floor($difference/3600); 
		if($datediff>24){
    		if($okmsg==""){
		        $errmsg.="Questa richiesta per una nuova password &egrave scaduta";
			}
		    $vediPass=1;
		}else{
		    $username_db=$row['username'];
            $email_db=$row['email'];
		     //echo();
		}
	}else{	
	    if($okmsg==""){
	        $errmsg.="Questa richiesta per una nuova password &egrave scaduta";
		}
	    $vediPass=1;
	}
	html_header();
?>
<form action="index.php" method="post">
    <input type="hidden" name="recupera_pass" value="<?=$recupera_pass?>" />
	<input type="hidden" name="action" value="carica_pass_POST" />

    <div class="box-aut"><div class="content_all">
        <img src="images/re-med-logo.png" style="padding-bottom: 15px;" />
		<h3>Reimposta password</h3>
    <?  if($errmsg){    ?>
        <div class="error"><strong>ATTENZIONE: <?=$errmsg?></strong></div>
    <?  }if(!$okmsg){   ?>
    <?      if($vediPass!=1){   ?>
		<div>
			<div class="form mandatory">
				Inserire nuova password:<br />
				<input type="password" class="form_element" name="n_password" id="n_password"/>
			</div>
		</div>
		
		<div>
			<div class="form mandatory">
				Confermare password:<br />
				<input type="password" class="form_element" name="c_password" id="c_password"/>
			</div>
		</div>
        
		<div class="form">
        	<input class="button" type="submit" value="salva" />
        </div>
		</div>
	<?    
		    }else{?>  </div>   <?}
	    }else{   ?>
		</div>
		<div class="okmsg"><strong><?=$okmsg?></strong></div>
	<?  }  ?>
		<div class="form">
		<a  href="index.php">ritorna a login</a>
        </div>
        <div class="credits">
        powered by <a href="http://www.braincomputing.com" target="_blank"><img src="images/logo_brain.gif" title="Brain Computing" /></a>
		
    </div>
    <br style="clear: both;"/></div>
</form>

<?
}

function carica_pass_POST(){
	$conn = db_connect();
	$recupera_pass = $_POST['recupera_pass'];
	$n_password = $_POST['n_password'];
	$c_password = $_POST['c_password'];

	if(($n_password=="")||($c_password==""))
		//cattura lato server se non viene inserita la password
		return 1;
	elseif ($n_password!=$c_password)
		//password non coincidono
		return 2;
	elseif(strlen($n_password)<4)
		//verifica se la lunghezza della password e minore di 4
		return 3;
		
	elseif($n_password != pulisci($n_password))
		//verifica se ci sono caratteri speciali 
		return 4;

	else{    
		// cripta la pwd con la chiave corrente
		$tentativi_pass=0;
		$pwd_crypt = crypt($c_password,SALE);
		$query1="SELECT password FROM operatori WHERE (recupera_pass='$recupera_pass')";
		$rs1 = mssql_query($query1,$conn);
		$row_rs1 = mssql_fetch_assoc($rs1);
		$vecchia_password = $row_rs1['password'];

		if($vecchia_password !== $pwd_crypt)
		{
			//die($query1 ."<br>".$vecchia_pssword ." !== " . $pwd_crypt);
			
		  //$query2="UPDATE operatori SET recupera_pass='', data_recupera_pass='', data_pass_60='',   	   password='$pwd_crypt', check_pass='n', check_pass_60='n', tentativi_pass=$tentativi_pass WHERE (recupera_pass='$recupera_pass')";
			$query2="UPDATE operatori SET recupera_pass='', data_recupera_pass=getdate(), data_pass_60=getdate(), password='$pwd_crypt', check_pass='n',					 tentativi_pass=$tentativi_pass WHERE (recupera_pass='$recupera_pass')";
			$rs2 = mssql_query($query2,$conn);		//La password è stata modificata con successo 
			
			return 5;
		} else return 6;
	}
}	

// FUNZIONE FORMATTA DATA UTILIZZATA SOLO PER IL RECUPERO PASSWORD

function formatta_data_rec_pw($data){

if ((strlen($data)==10)or($data==""))
{
    return $data;
    exit();
}

date_default_timezone_set('Europe/Rome');

$datetime = date_create($data);
$data=date_format($datetime, 'm/d/Y'.' H:i:00');	

return $data;

exit();

}

//UTILIZZATA PER IL REIMPOSTA PASSWORD DOPO 60 GIORNI
function diff_in_giorni($first, $second)
{

//isoliamo i valori contenuti nei due array
  $array_f = @explode ("/", $first);
  $array_s = @explode ("/", $second);

  $dd1 = $array_f[0];
  $mm1 = $array_f[1];
  $yyyy1 = $array_f[2];

  $dd2 = $array_s[0];
  $mm2 = $array_s[1];
  $yyyy2 = $array_s[2];

//utilizziamo i valori degli array come termini di confronto 
  $confronto1 = @gregoriantojd($mm1, $dd1, $yyyy1);
  $confronto2 = @gregoriantojd($mm2, $dd2, $yyyy2);
  
//calcoliamo la differenza in giorni 
  return $confronto1 - $confronto2; 
  }
  
//UTILIZZATA PER IL REIMPOSTA PASSWORD DOPO 60 GIORNI
  function formatta_data($data){

if ((strlen($data)==10)or($data==""))
{
return $data;
exit();
}

date_default_timezone_set('Europe/Rome');

$datetime = date_create($data);
if ($datetime!=""){
$data=date_format($datetime, 'd/m/Y');	

return $data;
}
}


/****************************************************************
* funzione logout		   				*
*****************************************************************/
function logout(){

	// elimina tutte le variabili di sessione
	session_destroy(); 
	setcookie('PIN_INFOCERT', '', time() - (86400 * 30), "/");
}


/****************************************************************
* funzione cancella_files		   				*
*****************************************************************/
function cancella_files(){

	global $tablename_download;
	
	$datadioggi = date("d")."/".date("m")."/".date("Y");
	
	$conn = db_connect();

	$query = "SELECT id,nome FROM $tablename_download WHERE (cancella='n') AND (datains <= DATEADD(d,-2, '".$datadioggi."'))";
	//echo $query."<br>";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	//echo "trovati ".mssql_num_rows($result)."<br>";

	// apre la connessione ftp
	//$ip="127.0.0.1";
	$conn_id = ftp_connect(IP_ADDR);	
	


	// login con user name e password
	$login_result = ftp_login($conn_id, FTP_USR, FTP_PWD); 

	// controllo della connessione
	if ((!$conn_id) || (!$login_result)) { 
		echo "Connessione FTP fallita!";
		echo "Tentativo di connessione a $ftp_server per l'utente1 ".IP_ADDR; 
		die; 
	
	} else {

		// passa in passive mode
		ftp_pasv ( $conn_id, true );

		// si posiziona nella directory giusta
		if (!ftp_chdir($conn_id, ESPORTAZIONI_WEB)) {
				error_message("non posso cambiare dir");
				exit;
		} 
		
		// cicla i file da cancellare 
		while($row=mssql_fetch_assoc($result)) {
	
			$id = $row['id'];
			$nomefile = $row['nome'];

			$nuovonomefile = "ccc_".$nomefile;
	
			if(! (ftp_rename($conn_id, $nomefile, $nuovonomefile)) ) {
				error_message("errore rinomina file");
				exit;
			}

			$query_del = "UPDATE $tablename_download SET nome='$nuovonomefile', cancella='y' WHERE id=$id";
			echo $query;
			$result_del = mssql_query($query_del, $conn);
			if(!$result_del) error_message(mssql_error());
			

		}
	
	}
	// chiude la connessione ftp
	ftp_close($conn_id);

}





	$cmd = array();

	
// SE STO EFFETTUANDO IL LOGIN	
if (isset($_POST['action']) && $_POST['action']=='login') {

    if(login()){
	
		echo "<script type=\"text/javascript\">";
		echo "window.location = \"principale.php\";";		
		echo "</script>" ;
		exit();
	} else{
			
		html_header();
		load($errmsg);
		html_footer();
				
	}
} 
// SE HO COMPILATO IL FORM DI RECUPERO PW INSERENDO USER ED EMAIL
elseif (isset($_POST['action']) && $_POST['action']=='reimposta_pass') {

	if(reimposta_pass()){
        //  echo("qui true");
		//exit();
    	html_header();
        load_reimposta('',$okmsg);
	    html_footer();
	    exit();
	
	}else{
		//  echo("qui false");
		//  exit();
		html_header();
        load_reimposta($errmsg,'');
	    html_footer();
	    exit();
	}
		
} 
// SE HO CAMBIATO LA PASSWORD
elseif (isset($_POST['recupera_pass'])) {

	    //html_header();
	    //$check_pws=carica_pass_POST();
	    if (carica_pass_POST()==5){
	        caricaFormPass($_POST['recupera_pass'],'','Password modificata con successo');
	        exit();
	    }elseif(carica_pass_POST()==1){
	        caricaFormPass($_POST['recupera_pass'],'Non &egrave possibile inserire campi vuoti, riprovare','');
	        exit();
        }elseif(carica_pass_POST()==2){
	        caricaFormPass($_POST['recupera_pass'],'Le password inserite non coincidono, riprovare','');
	        exit();
        }elseif(carica_pass_POST()==3){
	        caricaFormPass($_POST['recupera_pass'],'La password inserita deve avere un minimo di 4 caratteri, riprovare','');
	        exit();
        }elseif(carica_pass_POST()==4){
	        caricaFormPass($_POST['recupera_pass'],'Non si possono inserire spazi nella password, riprovare','');
	        exit();
        }elseif(carica_pass_POST()==6){
	        caricaFormPass($_POST['recupera_pass'],'La password inserita &egrave; gi&agrave; stata utilizzata in passato.','');
	        exit();
        }
	 //html_footer();
	 exit();
}

else{
		
	if($_REQUEST['do']=='logout')
		logout();
			
			
	// SE HO CLICCATO SU HO DIMENTICATO LA PASSWORD
	if($_REQUEST['do']=='reimposta')
	{
		// echo("wewewe");
		html_header();
	    load_reimposta($errmsg,$okmsg);
		html_footer();
		exit();
	}
			
	// SE PROVENGO DALLA EMAIL DI RECUPERO PASSWORD
	if($_REQUEST['do']=='cambio_password')
	{
		if (isset($_REQUEST['stringa'])) // SE E' PRESENTE IL CODICE PER IL CONTROLLO
		{
			$stringa=$_REQUEST['stringa'];
			html_header();
			caricaFormPass($stringa,$errmsg,$okmsg);
			html_footer();
			exit();
	    } 
		// BLOCCO L'UTENTE
		else
			exit();
	}
	html_header();
    load('');
	html_footer();
}
function pulisci($testo) {
    $testo = str_replace(" ","",$testo);
	$testo = str_replace("'","''",$testo);
/*	//preg_replace elimina tutti i caratteri speciali ATTENZIONE la functin pulisci($testo) è utilizzata anche per leggere la mail in function reimposta_pass()
	preg_replace("/\W/", "", $testo);  
*/
	$testo=utf8_decode($testo);
	$testo=stripslashes($testo);
	return $testo;

	/*$testo = str_replace("'",'&#39;',$testo);
	$testo = str_replace('"','&quot;',$testo);
	return stripslashes($testo);*/
}
?>


</body>
</html>
