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
<? if (browser_detection('browser') != 'moz' ){ ?>
<script type="text/javascript">
	window.location = "getfirefox.php";
</script>
<? } ?>

<script>
var home=true;	
	
</script>
<script type="text/javascript" src="script/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="script/ddaccordion.js"></script>
<script type="text/javascript" src="script/jquery.form.js"></script> 	
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
background: url('../M_Validation/images/mandatory.png') center left no-repeat;
}
div.focused {
float: left;
padding-left: 17px;
background: url('../M_Validation/images/focused.png') bottom left no-repeat;}
div.correct {
float: left;
padding-left: 17px;
background: url('../M_Validation/images/correct.gif') bottom left no-repeat;}
	
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

	
	$conn = db_connect();
	$qy = "SELECT username,usa_firma FROM ".$tablename." WHERE username='".$_POST['u']."'";
	$rs = mssql_query($qy, $conn);
	$row_f=mssql_fetch_assoc($rs);
	$usafirma=$row_f['usa_firma'];
	$_SESSION['businesskey_usafirma']=$usafirma;
	mssql_free_result($rs);
	//$usafirma=1;
	//$errmsg .=$qy;
	if((($usafirma==1)and($_SESSION['businesskey_inserita']=="si")) or($usafirma==0)){
		// verifica che il campo username non sia vuoto
		if(empty($_POST['u'])){
			$errmsg .= "inserire una username";
			
		} else {

			//$conn = db_connect();
			// cripta la pwd con la chiave di default
			$pass_crypt = crypt($_POST['p'],SALE);
			// esegue la query al db
			$qy = "SELECT uid,gid,username,password,is_root,status,usa_firma FROM ".$tablename." WHERE cancella='n'";
			$rs = mssql_query($qy, $conn);

			if(!$rs) error_message(mssql_error());

			while( list($userid,$gid,$username,$password,$is_root,$status,$usafirma) = mssql_fetch_row($rs) ) {
				// se l'username non combacia, continua			
				if($_POST['u'] != $username) continue;
				 
				
			if ( ($_POST['u'] == $username) && ($pass_crypt == $password) && ($_POST['k'] == CHIAVE) && $status) {

				$LoginFailed = "";
					// crea l'oggetto Utente
					$obj_User = new User();
					$obj_User->create($userid);

					// imposta il cookie
				//	setcookie("usrPUBLIACI",$username.$password, 0);
					
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

				} else  if ( ($_POST['u'] == $username)) {
					$LoginFailed = "yes";
					if($pass_crypt != $password) 
					if($_POST['k'] != CHIAVE) $errmsg .= "chiave errata o mancante<br />";
				} else  {
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
	document.form1.u.focus();
});

</script>
<form action="<?php echo $PHP_SELF; ?>" method="post" name="form1">
	<input type="hidden" name="action" value="login" />

    <div class="box-aut"><div class="content_all">
        <img src="images/re-med-logo.png" style="padding-bottom: 15px;" />
        <? if($errmsg){ ?>
        <div class="error"><strong>ATTENZIONE: <?=$errmsg?></strong></div>
        <? } ?>
        <div class="form mandatory">
        	username: <span class="b_key">rilevazione businesskey in corso... </span><br />
            <input type="text" class="form_element" name="u" id="u" />
        </div>
        <div class="form mandatory">
        	password:<br />
            <input type="password" class="form_element" name="p" id="p"/>
        </div>
        <div class="form mandatory">
        	chiave:<br />
            <input type="password" class="form_element" name="k" id="k" />
        </div>
        <div class="form">
        	<input class="button" type="submit" value="accedi" />
        </div>
        <div class="credits">
        powered by <a href="http://www.braincomputing.com" target="_blank"><img src="images/logo_brain.gif" title="Brain Computing" /></a>
        </div>
    
    </div>
    <br style="clear: both;"/></div>
</form>
<?php
	//close_center();
}


/****************************************************************
* funzione logout		   				*
*****************************************************************/
function logout(){

	// elimina tutte le variabili di sessione
	session_destroy(); 
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

	if (isset($_POST['action']) && $_POST['action']=='login') {

		if(login()) {
	
			echo "<script type=\"text/javascript\">";
			echo "window.location = \"principale.php\";";		
			echo "</script>";

			exit();
			

		} else {
	
				html_header();
				load($errmsg);
				html_footer();
		}

	} else {

		//if($_REQUEST['do']=='logout')logout();

			html_header();
			load('');
			html_footer();
	}
?>

</body>
</html>
