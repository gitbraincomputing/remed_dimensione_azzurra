<?php
// inclusione delle classi
include_once('include/class.User.php');
include_once('include/class.Struttura.php');

// inizializza le variabili di sessione
include_once('include/session.inc.php');

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');

// nome della tabella
$tablename = 'utenti';
$pagetitle = "Gestione pazienti";
$id_menu = $id_permesso = 12;

/****************************************************************
* funzione add() 					           					*
*****************************************************************/
function add() {

	global $PHP_SELF, $pagetitle;

//	echo "permessi ".$_SESSION['UTENTE']->controlla_permessi(5, 1);

	$conn = db_connect();
	/*$query = "SELECT gid,nome FROM gruppi WHERE sid=".$_SESSION['STRUTTURA1']->get_id()." ORDER BY gid";
	$rs = mssql_query($query, $conn);

	if(!mssql_num_rows($rs))
		error_message('Attenzione! In questa struttura non è presente nessun UTENTE. Per creare un UTENTE, è necessario creare prima un gruppo.');
	*/
	// apre il div centrale
	open_center();
?>
	<div id="lista_impegnative">
    <form method="post" name="form0" action="<?php echo $PHP_SELF; ?>" >
	<input type="hidden" name="action" value="create" />
	<input type="hidden" name="op" value="1" />

	<div id="titolo_pag"><h1><small><?php echo $pagetitle ?></small></h1></div>

	<div class="blocco_centralcat">

	<span class="rigo_mask">
		<div class="testo_mask">cognome</div>
		<div class="campo_mask mandatory">
			<input id="c_p" type="text" class="scrittura_campo" name="cognome" SIZE="30" maxlenght="30" />
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask mandatory">
			<input id="n_p" type="text" class="scrittura_campo" name="nome" SIZE="30" maxlenght="30" />
		</div>
	</span>
    
    <span class="rigo_mask">
        <div class="testo_mask">sesso</div>
        <div class="campo_mask mandatory">
            <input id="1s_p" type="radio" name="sesso" value="M" checked="checked"/> M 
            <input id="2s_p" type="radio" name="sesso" value="F" /> F
        </div>
    </span>
    
    <span class="rigo_mask">
		<div class="testo_mask">data di nascita</div>
		<div class="campo_mask mandatory data_passata">
			<input type="text" onclick="displayCalendar(document.getElementById('data_nascita'),'dd/mm/yyyy',this)" class="campo_data scrittura"  name="data_nascita" id="data_nascita" value="" />
			<!--	<input type="button" style=" background-image:url(images/calendar.jpg); background-repeat: no-repeat; border: none; background-color: #abc6f3; width:18px; height:18px; margin: 4px 0 0 5px; float: rigth;" value="" onclick="displayCalendar(document.getElementById('data_nascita'),'dd/mm/yyyy',this)">-->
		</div>
	</span>
    
    
	<span class="rigo_mask">
		<div class="testo_mask">Comune di nascita</div>
		<div class="campo_mask mandatory">
        <select id="comune_nascita" name="comune_nascita" class="scrittura">
        <option value="" selected="selected">Seleziona il comune</option>
			<?php
					$conn = db_connect();
	
					$query = "SELECT id_comune,denominazione FROM comuni ORDER BY denominazione";
					$rs = mssql_query($query, $conn);
					
					while( $row = mssql_fetch_assoc($rs)) {
	
						$cid = $row['id_comune'];
						$nome = $row['denominazione'];
	
						print ('<option value="'.$cid.'">'.$nome.'</option>');
					}						
					?>
         </select>
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">provincia di nascita</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="prov_nascita" SIZE="30" maxlenght="30" readonly/>
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">Codice Fiscale</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura" name="codice_fiscale" SIZE="30" maxlenght="30" />
		</div>
	</span>
    <br /><br />Residenza<br />
     <span class="rigo_mask">
		<div class="testo_mask">indirizzo</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura" name="indirizzo" SIZE="30" maxlenght="30" />
		</div>
	</span>
    
	<span class="rigo_mask">
		<div class="testo_mask">Comune di residenza</div>
		<div class="campo_mask mandatory">
        <select name="comune_residenza" class="scrittura">
        <option value="" selected="selected">Seleziona il comune</option>
			<?php
					$conn = db_connect();
	
					$query = "SELECT id_comune,denominazione FROM comuni ORDER BY denominazione";
					$rs = mssql_query($query, $conn);
					
					while( $row = mssql_fetch_assoc($rs)) {
	
						$cid = $row['id_comune'];
						$nome = $row['denominazione'];
	
						print ('<option value="'.$cid.'">'.$nome.'</option>');
					}						
					?>
         </select>
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">provincia di residenza</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="prov_residenza" SIZE="30" maxlenght="30" readonly/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">CAP</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="cap_residenza" SIZE="30" maxlenght="30" readonly/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">telefono</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura" name="telefono" SIZE="30" maxlenght="30" />
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">fax</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura" name="fax" SIZE="30" maxlenght="30" />
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">cellulare</div>
		<div class="campo_mask mandatory">
			<input id="cell" type="text" class="scrittura" name="cellulare" SIZE="30" maxlenght="30" />
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">e-mail</div>
		<div class="campo_mask mandatory email">
			<input type="text" class="scrittura" name="email" SIZE="30" maxlenght="30" />
		</div>
	</span>
    <br /><br />Tutor<br />
    <span class="rigo_mask">
		<div class="testo_mask">cognome</div>
		<div class="campo_mask">
			<input id="c_t_" type="text" class="scrittura_campo" name="cognome_t" SIZE="30" maxlenght="30" />
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask">
			<input id="n_t_" type="text" class="scrittura_campo" name="nome_t" SIZE="30" maxlenght="30" />
		</div>
	</span>
	
	<span class="rigo_mask">
        <div class="testo_mask">sesso</div>
        <div class="campo_mask">
            <input id="1s_t_" type="radio" name="sesso_t_" value="1" checked="checked"/> M 
            <input id="2s_t_" type="radio" name="sesso_t_" value="2" /> F
        </div>
    </span>
	
	  <span class="rigo_mask">
		<div class="testo_mask">provincia di nascita <a onclick="carica_province(document.getElementById('prov_residenza_tn_').value,4)" class="cambia_valore">cambia provincia</a></div>
		<div class="campo_mask #condition,data_nascita,18,anni_precedenti# province_tut_n">
			<input type="text" class="scrittura readonly" name="prov_residenza_tn_" id="prov_residenza_tn_" SIZE="30" maxlenght="30" value="<?php echo($tut_n_provincia);?>"readonly/>		
        </div>
	</span>    
     <span class="rigo_mask">
		<div class="testo_mask">Comune di nascita <a onclick="carica_comuni(document.getElementById('prov_residenza_tn_').value,4,document.getElementById('comune_residenza_tn_').value)" class="cambia_valore">cambia comune</a></div>
        <div class="campo_mask comuni_tut_n">
		<input type="text" class="scrittura readonly" name="comune_residenza_tn_nome_" id="comune_residenza_tn_nome_" SIZE="30" maxlenght="30" value="<?php echo($tut_n_comune);?>" readonly/>		
		<input type="hidden"  name="comune_residenza_tn_" id="comune_residenza_tn_"  value="<?=$tut_n_comune_id?>"/>                   
		</div>
	</span>
	
    </div>
	<div class="riga"> 
    <span class="rigo_mask">
		<div class="testo_mask">Codice Fiscale <a rel="" class="cambia_valore calcola_cf_t">calcola Codice Fiscale</a></div>
		<div class="campo_mask codice_fiscale">
			<input id="cod_fisc_t_" type="text" class="scrittura" name="codice_fiscale_t_" SIZE="30" maxlength="16" value="<?php echo($tut_codice_fiscale);?>" onKeyUp="this.value=omocodia(this.value)" />
		</div>
	</span>
	    
    
	
    
    <span class="rigo_mask">
		<div class="testo_mask">provincia di residenza</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="prov_residenza_t" SIZE="30" maxlenght="30" readonly/>
		</div>
	</span>
	<span class="rigo_mask">
		<div class="testo_mask">Comune di residenza</div>
		<div class="campo_mask">
        <select name="comune_residenza_t" class="scrittura">
        <option value="" selected="selected">Seleziona il comune</option>
			<?php
					$conn = db_connect();
	
					$query = "SELECT id_comune,denominazione FROM comuni ORDER BY denominazione";
					$rs = mssql_query($query, $conn);
					
					while( $row = mssql_fetch_assoc($rs)) {
	
						$cid = $row['id_comune'];
						$nome = $row['denominazione'];
	
						print ('<option value="'.$cid.'">'.$nome.'</option>');
					}						
					?>
         </select>
		</div>
	</span>
	 <span class="rigo_mask">
		<div class="testo_mask">CAP</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="cap_residenza_t" SIZE="30" maxlenght="30" readonly/>
		</div>
	</span>
	
	<span class="rigo_mask">
		<div class="testo_mask">indirizzo</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="indirizzo_t" SIZE="30" maxlenght="30" />
		</div>
	</span>
   
    <span class="rigo_mask">
		<div class="testo_mask">telefono</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="telefono_t" SIZE="30" maxlenght="30" />
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">fax</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="fax_t" SIZE="30" maxlenght="30" />
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">cellulare</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="cellulare_t" SIZE="30" maxlenght="30" />
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">e-mail</div>
		<div class="campo_mask email">
			<input type="text" class="scrittura" name="email_t" SIZE="30" maxlenght="30" />
		</div>
	</span>
     <span class="rigo_mask">
		<div class="testo_mask">relazione con il paziente</div>
		<div class="campo_mask">
			<input type="text" class="scrittura" name="relazione_t" SIZE="30" maxlenght="30" />
		</div>
	</span>

	
    <span class="rigo_mask">
        <div class="testo_mask">stato</div>
        <div class="campo_mask">
                <select name="stato" class="scrittura">
                <option value="1">attivo</option>
                <option value="0">disattivo</option>
                </select>
        </div>
    </span>
	</div>

	<div class="barra_inf">
		<div class="comandi">
			<input type="image" src="images/salva.gif" title="salva" />
		</div>
	</div>
	</form>
    </div>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
</script>
<?php
	// chiude il div centrale
	close_center();
}


/****************************************************************
* funzione edit()						*
*****************************************************************/
function edit($id){

	global $PHP_SELF, $tablename, $pagetitle;
/*
	$conn = db_connect();

	$query = "SELECT * from $tablename WHERE uid='$id'";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){

		$nome = $row['nome'];
		$descr = $row['descr'];		
		$status = $row['status'];
	}

	// rimuove i caratteri di escape
	$nome = stripslashes($nome);
	$usr = stripslashes($usr);
	$descr = stripslashes($descr);

	mssql_free_result($rs);
*/
	// apre il div centrale
	open_center();
	
	
	?>
	<script type="text/javascript">
            $(function() {

                $('#container-1').tabs();
                $('#container-2').tabs(2);
                $('#container-3').tabs({ fxSlide: true });
                $('#container-4').tabs({ fxFade: true, fxSpeed: 'fast' });
                $('#container-5').tabs({ fxSlide: true, fxFade: true, fxSpeed: 'normal' });
                $('#container-6').tabs({
                    fxFade: true,
                    fxSpeed: 'fast',
                    onClick: function() {
                        alert('onClick');
                    },
                    onHide: function() {
                        alert('onHide');
                    },
                    onShow: function() {
                        alert('onShow');
                    }
                });
                $('#container-7').tabs({ fxAutoHeight: true });
                $('#container-8').tabs({ fxShow: { height: 'show', opacity: 'show' }, fxSpeed: 'normal' });
                $('#container-9').tabs({ remote: true });
                $('#container-10').tabs();
                $('#container-11').tabs({ disabled: [3] });

                $('<p><a href="#">Disable third tab<\/a><\/p>').prependTo('#fragment-28').find('a').click(function() {
                    $(this).parents('div').eq(1).disableTab(3);
                    return false;
                });
                $('<p><a href="#">Activate third tab<\/a><\/p>').prependTo('#fragment-28').find('a').click(function() {
                    $(this).parents('div').eq(1).triggerTab(3);
                    return false;
                });
                $('<p><a href="#">Enable third tab<\/a><\/p>').prependTo('#fragment-28').find('a').click(function() {
                    $(this).parents('div').eq(1).enableTab(3);
                    return false;
                });

            });
        </script>
	
	<div id="titolo_pag"><h1><small><?php echo $pagetitle ?></small></h1></div>

	<h2>Ajax tabs</h2>

    <!--
<div id="container-1">
            <ul>
                <li><a href="re_pazienti.php?do=edit&id=<?=$id?>&tab=1#fragment-1"><span>Anagrafica</span></a></li>
                <li><a href="re_pazienti.php?do=edit&id=<?=$id?>&tab=2#fragment-2"><span>Area Amministrativa</span></a></li>
                <li><a href="re_pazienti.php?do=edit&id=<?=$id?>&tab=3#fragment-3"><span>Area Sanitaria</span></a></li>
            </ul>
            <div id="fragment-1">
			<?
			
			/*if ($_REQUEST['tab']==1)
             include_once('re_pazienti_anagrafica.php');*/
	        
			?>
			</div>
            <div id="fragment-2">
<?/*
			
			if ($_REQUEST['tab']==2)
             include_once('re_pazienti_anagrafica.php');*/
	        
			?>
            </div>
            <div id="fragment-3">
        <?
			/*
			if ($_REQUEST['tab']==3)
             include_once('re_pazienti_anagrafica.php');*/
	        
			?>
		</div>
        </div>
-->
    <div id="container-9">
            <ul>
                <li><a href="re_pazienti_anagrafica.php?id=<?=$id?>"><span>Anagrafica</span></a></li>
                <li><a href="re_pazienti_amministrativa.php"><span>Area Amministrativa</span></a></li>
                <li><a href="re_pazienti_sanitaria.php"><span>Area Sanitaria</span></a></li>
            </ul>
        </div>

<?php
	// chiude il div centrale
	close_center();
}


/****************************************************************
* funzione create()												*
*****************************************************************/
function create() {

	global $tablename;
	
	$isroot = $_SESSION['UTENTE']->is_root();
	$ope = $_SESSION['UTENTE']->get_userid();
	
	$nome = stripslashes($_POST['nome']);
	$cognome = stripslashes($_POST['cognome']);
	$sesso = $_POST['sesso'];
	$data_nascita = $_POST['data_nascita'];
	$comune_nascita = $_POST['comune_nascita'];	
	$codice_fiscale = $_POST['codice_fiscale'];
	$indirizzo = stripslashes($_POST['indirizzo']);
	$comune_residenza  = stripslashes($_POST['comune_residenza']);
	$telefono = $_POST['telefono'];
	$fax = $_POST['fax'];
	$cellulare = $_POST['cellulare'];
	$email = $_POST['email'];
	$cognome_t = stripslashes($_POST['cognome_t']);
	$nome_t = stripslashes($_POST['nome_t']);
	$indirizzo_t = stripslashes($_POST['indirizzo_t']);
	$comune_residenza_t = $_POST['comune_residenza_t'];
	$telefono_t = $_POST['telefono_t'];
	$fax_t = $_POST['fax_t'];
	$cellulare_t = $_POST['cellulare_t'];
	$email_t = $_POST['email_t'];
	$relazione_t = stripslashes($_POST['relazione_t']);	
	$status = $_POST['stato'];
	
	$nome = str_replace("'","''",$nome);
	$cognome = str_replace("'","''",$cognome);
	$indirizzo = str_replace("'","''",$indirizzo);
	$comune_residenza = str_replace("'","''",$comune_residenza);
	$cognome_t = str_replace("'","''",$cognome_t);
	$nome_t = str_replace("'","''",$nome_t);
	$indirizzo_t = str_replace("'","''",$indirizzo_t);
	$relazione_t = str_replace("'","''",$relazione_t);

	$conn = db_connect();

	// cripta la pwd con la chiave corrente
	
	$query = "INSERT INTO $tablename (Cognome,Nome,Sesso,DataNascita,CodiceFiscale,LuogoNascita_id,stato,opeins) VALUES('$cognome','$nome','$sesso','$data_nascita','$codice_fiscale','$comune_nascita','$status','$ope')";
	
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	
	$query = "SELECT MAX(idutente) as id FROM $tablename (opeins=$ope)";
	$rs = mssql_query($query, $conn);	
	$row=mssql_fetch_assoc($rs);
	$id_paz=$row['id'];	
	scrivi_log ($id_paz,'$tablename','ins','idutente');
	
	// recupera l'id del record appena inserito
	$query = "SELECT MAX(idutente) FROM $tablename ";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	if($row = mssql_fetch_row($result)) $idutente=$row[0];
		
	$query = "INSERT INTO utenti_residenze (IdUtente,indirizzo,comune_id,telefono,fax,cellulare,email,opeins) VALUES('$idutente','$indirizzo','$comune_residenza ','$telefono','$fax','$cellulare','$email','$ope')";

	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	$query = "SELECT TOP 1 dbo.utenti_residenze.IdUtente, dbo.utenti_residenze.id as id, dbo.utenti_residenze.opeins FROM dbo.utenti_residenze WHERE (dbo.utenti_residenze.IdUtente = $idutente) and (dbo.utenti_residenze.opeins=$ope) ORDER BY dbo.utenti_residenze.id DESC";
	$rs = mssql_query($query, $conn);	
	$row=mssql_fetch_assoc($rs);
	$id_res=$row['id'];	
	scrivi_log ($id_res,'utenti_residenze','ins','id');
	
	if($cognome_t!=""){
		$query = "INSERT INTO utenti_tutor (IdUtente,nome,cognome,indirizzo,comune_id,telefono,fax,cellulare,email,relazione_paziente,opeins) VALUES('$idutente','$nome_t','$cognome_t ','$indirizzo_t','$comune_residenza_t','$telefono_t','$fax_t','$cellulare_t','$email_t','$relazione_t','$ope')";

	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	$query = "SELECT TOP 1 dbo.utenti_tutor.IdUtente, dbo.utenti_tutor.id as id, dbo.utenti_tutor.opeins FROM dbo.utenti_tutor WHERE (dbo.utenti_tutor.IdUtente = $idutente) and (dbo.utenti_tutor.opeins=$ope) ORDER BY dbo.utenti_tutor.id DESC";
	$rs = mssql_query($query, $conn);	
	$row=mssql_fetch_assoc($rs);
	$id_res=$row['id'];	
	scrivi_log ($id_res,'utenti_tutor','ins','id');	
	
	}
	
	// compila i campi di log
	//scrivi_log ($idutente,$tablename,'ins_op');

	//return $idutente;
	echo("ok;;2;lista_pazienti.php?do=review&id=".$id_paz);	

}


/****************************************************************
* funzione update()						*
*****************************************************************/
function update($id) {

	global $tablename;

	$isroot = $_SESSION['UTENTE']->is_root();

	$nome = stripslashes($_POST['nome']);
	$descr = stripslashes($_POST['descr']);
	$gid = $_POST['gid'];
	$email = $_POST['email'];
	$validazione = $_POST['validazione'];
	$status = $_POST['status'];

	if(empty($nome)) error_message("Inserire il nome e il cognome!");
	if(empty($email)) error_message("Inserire l'email!");

		// aggiunge i caratteri di escape
		$nome = str_replace("'","''",$nome);
		$descr = str_replace("'","''",$descr);

		$conn = db_connect();

		// recupera dal db il vecchio username
		/*$query = "SELECT username FROM $tablename where uid='$id'";
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		if(! $row = mssql_fetch_row($rs)) error_message(mssql_error());
		$old_usr = $row[0];
		mssql_free_result($rs);

		// se si sta tentando di modificare lo username, lo confronta con il vecchio
		if(strcmp($old_usr,$usr) !=0)
			if(!verifica_username($usr)) error_message("Username già presente. Provare con un altro.");

		//if($_SESSION['UTENTE']->is_root())
			$query = "UPDATE $tablename SET nome='$nome',descr='$descr',email='$email',username='$usr',status='$status' WHERE uid='$id'";
		
		//else  {
			//$query = "UPDATE $tablename SET nome='$nome',descr='$descr',username='$usr' WHERE uid='$id'";
		//}*/
		
		// aggiorna il campo username solo se è il proprio
		//if($id==$_SESSION['UTENTE']->get_userid()) $_SESSION['UTENTE']->set_username($usr);

		$query = "UPDATE $tablename SET nome='$nome',gid=$gid, email='$email',descr='$descr',validazione='$validazione' WHERE uid='$id'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());

		// compila i campi di log
		scrivi_log ($id,$tablename,'agg_op');

		// pic
		/*if ((trim($_FILES['pic']['name']) != "") && ($_FILES['pic'] != 'none')) {

			$imgHNDL = fopen($_FILES['pic']['tmp_name'], "rb");
			$imgf = fread($imgHNDL, $_FILES['pic']['size']);
			$eimgf = mssql_escape_string($imgf);

			$query_img ="update $tablename set img='" . $eimgf . "',alt_img='$nome' where uid=$id";
			$result = mssql_query($query_img, $conn);
			if(!$result) error_message(mssql_error());

			fclose($imgHNDL);
		}*/

		// rilascia l'id
		unset($id);

		// scrive il log
        //scrivi_log("users.php","M","users",$id);
		return true;
}


/****************************************************************************************************************
* funzione verifica_username()																					*
*****************************************************************************************************************/
function verifica_username($usr) {

	global $tablename;

	$conn = db_connect();
	$query = "SELECT uid FROM $tablename where username='".addslashes($usr)."'";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if(mssql_num_rows($rs)) return false;
	else return true;
}


/****************************************************************
* funzione confirm_del()					*
*****************************************************************/
function confirm_del($id) {

	global $tablename;
	
	$conn = db_connect();
	$query = "SELECT nome FROM $tablename where uid=$id";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	$row= mssql_fetch_row($rs);

	global $PHP_SELF;
	open_center();
?>
	<form method="post" name="modulo" action="<?php echo $PHP_SELF ?>?id=<?php echo $id ?>">
	<input type="hidden" name="action" value="del" />
	
	<div id="titolo_pag"><h1><small><?php echo $pagetitle ?> - conferma cancellazione</small></h1></div>

	<div class="blocco_centralcat">

		<div id="messaggio_cancella">Cancellare l' utente<br /><strong><?php echo $row[0]; ?></strong> ?</div>
		<div id="tasti_cancella">
			
			<span class="tasto_canc">
			<input class="larghezza_bottone" type="submit" value="si" name="choice" />
			</span>
			<input class="larghezza_bottone" type="button" value="no" onClick="javascript:window.location='<?php echo $PHP_SELF; ?>';" />
		</div>

	</div>
	</form>
<?php
	close_center();
}


/****************************************************************
* funzione del()						*
*****************************************************************/
function del($id) {

	global $tablename;

	$conn = db_connect();

	// verifica se sei root
	//$isroot = $_SESSION['UTENTE']->is_root();
	
	//if($id != $_SESSION['UTENTE']->get_userid()) {

		// cancella l'elemento selezionato
		$query = "UPDATE $tablename SET cancella='y' WHERE uid='$id'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());

		// scrive il log
		//scrivi_log ($id,$tablename,'del');

	//} else {
	
		//error_message("Operazione non permessa!");
	
	//}

  //scrivi_log("users.php","C","users",$id);
}


/****************************************************************
* funzione show_list()						*
*****************************************************************/
function show_list(){

	global $PHP_SELF, $tablename, $pagetitle, $id_permesso;
	
	$myid = $_SESSION['UTENTE']->get_userid();

	// apre il div centrale
	open_center();

	print('<div id="titolo_pag"><h1><small>'.$pagetitle.'</small></h1></div>'."\n");
	print('<div class="blocco_centralcat">'."\n");
?>
	
	

<div id="ricerca"></div>

<table id="flex1" style="display:none"></table>
	
<?
	include("include/lista_pazienti.js");

	print("\n".'</div>'."\n");

	// chiude il div centrale
	close_center();
}


// controllo d'accesso
if( isset($_SESSION['UTENTE'])) {

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		// recupera i dati di sessione dell'UTENTE
		$objUser = $_SESSION['UTENTE'];
	
		$back = "main.php";
		// azzera la variabile do
		if(!isset($do)) $do ='';
	
		//if(authorize_me($objUser->get_userid(), 'none')) {
	
			if (empty($_POST)) $_POST['action'] = "";

			include_once('include/aggiorna.php');
	
			switch($_POST['action']) {
	
				case "create":		
					// verifica i permessi..
					if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
						error_message("Permessi insufficienti per questa operazione!");
						else create();
					break;
	
				case "update":
					// verifica i permessi..
					if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
						error_message("Permessi insufficienti per questa operazione!");
						else update($_POST['id']);
					break;
					//if(!$objUser->is_root()) go_home();
	
				case "del":
					// verifica i permessi..
					if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
						error_message("Permessi insufficienti per questa operazione!");
						else if($_POST['choice']=="si") del($_REQUEST['id']);
					break;
			}
	
			if($do!='logout'){
				html_header();
			}
	
			switch($do) {
	
				case "add":
				add();
				break;
	
				case "edit":
				edit($_REQUEST['id']);
				break;
	
				case "confirm_del":
				confirm_del($_REQUEST['id']);
				break;
	
				case "logout":
				logout();
				break;
	
				default:
				// array comandi disponibili
				show_list();
				break;
			}
				html_footer();
	}
	else { error_message("non hai i permessi per visualizzare questa pagina!"); go_home();}

} else {
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
?>