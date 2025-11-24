<script>
$(document).ready(function(){
	
	
	$('#container-2').tabs({ remote: true });
	
     
});

</script>					
					
					<div id="container-2">
                        <ul>
                            <li><a href="re_pazienti_anagrafica.php?do=review&id=<?=$id?>"><span>Review</span></a></li>
                            <li><a href="#fragment-5"><span>Two</span></a></li>
                            <li><a href="#fragment-6"><span>Tabs are flexible again</span></a></li>
                        </ul>
                        <div id="fragment-4">
                            <table id="flex1" style="display:none"></table>
                        </div>
                        <div id="fragment-5">
                            Lorem ipsum 
                        </div>
                        <div id="fragment-6">
                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                        </div>
                    </div>  
<?php
// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
$id_permesso = $id_menu = 11;
include_once('include/function_page.php');

//$idutente=$id;


function edit($id){

$idutente=$id;
$conn = db_connect();

	$query = "SELECT * from re_utenti_anagrafica where idutente=$idutente";	
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){
	
		$codiceutente = $row['CodiceUtente'];
		$cognome =$row['Cognome'];
		$nome =$row['Nome'];
		$data_nascita = formatta_data($row['DataNascita']);
		$sesso=$row['Sesso'];
		$codice_fiscale = $row['CodiceFiscale'];	
		$stato=$row['stato'];
		$comune_nascita = $row['luogonascita'];
		$comune_nascita_id = $row['luogonascita_id'];
		$cap_nascita_id = $row['cap'];
		$prov_nascita=$row['prov'];	
		$reg_nascita=$row['reg'];	
	
	}
	
	$query = "SELECT TOP 1 dbo.utenti_residenze.id, dbo.utenti_residenze.indirizzo, dbo.utenti_residenze.telefono, dbo.utenti_residenze.fax,dbo.utenti_residenze.cellulare, dbo.utenti_residenze.email, dbo.utenti_residenze.stato, dbo.utenti_residenze.cancella, dbo.utenti_residenze.IdUtente, dbo.comuni.denominazione, dbo.comuni.cap, dbo.comuni.provincia, dbo.comuni.sigla, dbo.utenti_residenze.comune_id, dbo.comuni.id_comune, dbo.province.nome AS nome_prov FROM dbo.utenti_residenze INNER JOIN dbo.comuni ON dbo.utenti_residenze.comune_id = dbo.comuni.id_comune INNER JOIN dbo.province ON dbo.comuni.provincia = dbo.province.id WHERE (dbo.utenti_residenze.stato = 1) AND (dbo.utenti_residenze.cancella = 'n') AND (dbo.utenti_residenze.IdUtente = $idutente) ORDER BY dbo.utenti_residenze.id DESC";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){
	
		$res_indirizzo=trim($row['indirizzo']);
		$res_telefono=trim($row['telefono']);
		$res_fax=trim($row['fax']);
		$res_cellulare=trim($row['cellulare']);
		$res_email=trim($row['email']);
		$res_comune=trim($row['denominazione']);
		$res_cap=trim($row['cap']);
		$res_provincia=trim($row['nome_prov']);
		$res_sigla=trim($row['sigla']);		
	}
	
	$query = "SELECT TOP 1 dbo.comuni.denominazione, dbo.comuni.cap, dbo.comuni.provincia, dbo.comuni.sigla, dbo.comuni.id_comune, dbo.utenti_tutor.cancella, dbo.utenti_tutor.stato, dbo.utenti_tutor.relazione_paziente, dbo.utenti_tutor.email, dbo.utenti_tutor.cellulare, dbo.utenti_tutor.fax, dbo.utenti_tutor.telefono, dbo.utenti_tutor.indirizzo, dbo.utenti_tutor.cognome, dbo.utenti_tutor.nome, dbo.utenti_tutor.id, dbo.utenti_tutor.IdUtente, dbo.province.nome AS nome_prov
FROM dbo.comuni INNER JOIN dbo.utenti_tutor ON dbo.comuni.id_comune = dbo.utenti_tutor.comune_id INNER JOIN dbo.province ON dbo.comuni.provincia = dbo.province.id
WHERE (dbo.utenti_tutor.stato = 1) AND (dbo.utenti_tutor.cancella = 'n') AND (dbo.utenti_tutor.IdUtente = $idutente) ORDER BY dbo.utenti_tutor.id DESC";	
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){
	
		$tut_nome=trim($row['nome']);
		$tut_cognome=trim($row['cognome']);
		$tut_indirizzo=trim($row['indirizzo']);
		$tut_tel=trim($row['telefono']);
		$tut_fax=trim($row['fax']);
		$tut_cell=trim($row['cellulare']);
		$tut_mail=trim($row['email']);
		$tut_relazione=trim($row['relazione_paziente']);
		$tut_comune=trim($row['denominazione']);
		$tut_cap=trim($row['cap']);
		$tut_provincia=trim($row['nome_prov']);
		$tut_sigla=trim($row['sigla']);	
	}
	
?>





<form id="myForm" method="post" name="form0" action="re_pazienti_anagrafica_POST.php">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="op" value="1" />
	<input type="hidden" name="id" value="<?php echo($id);?>" />
    
 

	<div class="titolo_pag"><h1>Dati del Paziente</h1></div>
	
	<div class="blocco_centralcat">

	<span class="rigo_mask">
		<div class="testo_mask">cognome</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="cognome" SIZE="30" maxlenght="30" value="<?php echo($cognome);?>"/>
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura_campo" name="nome" SIZE="30" maxlenght="30" value="<?php echo($nome);?>"/>
		</div>
	</span>
    
    <span class="rigo_mask">
        <div class="testo_mask">sesso</div>
        <div class="campo_mask mandatory">
		
		
		
            <input type="radio" name="sesso" value="M" <?php if($sesso=="M") echo("checked");?>/> M 
            <input type="radio" name="sesso" value="F" <?php if($sesso=="F") echo("checked");?>/> F
        </div>
    </span>
    
    <span class="rigo_mask">
		<div class="testo_mask">data di nascita</div>
		<div class="campo_mask mandatory data_passata">
			<input type="text" class="campo_data"  name="data_nascita" id="data_nascita" value="<?php echo($data_nascita);?>" />
			<!--	<input type="button" style=" background-image:url(images/calendar.jpg); background-repeat: no-repeat; border: none; background-color: #abc6f3; width:18px; height:18px; margin: 4px 0 0 5px; float: rigth;" value="" onclick="displayCalendar(document.getElementById('data_nascita'),'dd/mm/yyyy',this)">-->
		</div>
	</span>
    
        
    <span class="rigo_mask">
		<div class="testo_mask">provincia di nascita <a onclick="carica_province(form0.prov_nascita.value,1)" class="cambia_valore">cambia provincia</a></div>
		<div class="campo_mask mandatory" id="province">
			<input type="text" class="scrittura readonly" name="prov_nascita" id="prov_nascita" SIZE="30" maxlenght="30" value="<?php echo($prov_nascita);?>" readonly/>
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">Comune di nascita <a onclick="carica_comuni(form0.prov_nascita.value,1,form0.comune_nascita.value)" class="cambia_valore">cambia comune</a></div>
		<div class="campo_mask mandatory" id="comuni">
        <input type="text" class="scrittura readonly" name="comune_nascita" id="comune_nascita" SIZE="30" maxlenght="30" value="<?php echo($comune_nascita);?>" readonly/>
         
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">Codice Fiscale</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura" name="codice_fiscale" SIZE="30" maxlenght="30" value="<?php echo($codice_fiscale);?>"/>
		</div>
	</span>
	
	</div>
	<div class="titolo_pag"><h1>Residenza</h1></div>
	<div class="blocco_centralcat">
     <span class="rigo_mask">
		<div class="testo_mask">indirizzo</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura" name="indirizzo" SIZE="30" maxlenght="30" value="<?php echo($res_indirizzo);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">provincia di residenza <a onclick="carica_province(form0.prov_residenza.value,2)" class="cambia_valore">cambia provincia</a></div>
		<div class="campo_mask mandatory" id="province_res">
			<input type="text" class="scrittura readonly" name="prov_residenza" id="prov_residenza" SIZE="30" maxlenght="30" value="<?php echo($res_provincia);?>" readonly/>
            
		</div>
	</span>
	<span class="rigo_mask">
		<div class="testo_mask">Comune di residenza <a onclick="carica_comuni(form0.prov_residenza.value,2,form0.comune_residenza.value)" class="cambia_valore">cambia comune</a></div>
		<div class="campo_mask mandatory" id="comuni_res">
         <input type="text" class="scrittura readonly" name="comune_residenza" id="comune_residenza" SIZE="30" maxlenght="30" value="<?php echo($res_comune);?>" readonly/>
              
		</div>
	</span>       
    <span class="rigo_mask">
		<div class="testo_mask">CAP</div>
		<div class="campo_mask">
			<input type="text" class="scrittura readonly" name="cap_residenza" SIZE="30" maxlenght="30" value="<?php echo($res_cap);?>"readonly/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">telefono</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura" name="telefono" SIZE="30" maxlenght="30" value="<?php echo($res_telefono);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">fax</div>
		<div class="campo_mask mandatory">
			<input type="text" class="scrittura" name="fax" SIZE="30" maxlenght="30" value="<?php echo($res_fax);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">cellulare</div>
		<div class="campo_mask mandatory">
			<input id="cell" type="text" class="scrittura" name="cellulare" SIZE="30" maxlenght="30" value="<?php echo($res_cellulare);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">e-mail</div>
		<div class="campo_mask mandatory email">
			<input type="text" class="scrittura" name="email" SIZE="30" maxlenght="30" value="<?php echo($res_email);?>"/>
		</div>
	</span>
    </div>
    <div class="titolo_pag"><h1>Tutor</h1></div>
    <div class="blocco_centralcat">
    <span class="rigo_mask">
		<div class="testo_mask">cognome</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura_campo" name="cognome_t" SIZE="30" maxlenght="30" value="<?php echo($tut_cognome);?>"/>
		</div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">nome</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura_campo" name="nome_t" SIZE="30" maxlenght="30" value="<?php echo($tut_nome);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">indirizzo</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura" name="indirizzo_t" SIZE="30" maxlenght="30" value="<?php echo($tut_indirizzo);?>"/>
		</div>
	</span>
    
	<span class="rigo_mask">
		<div class="testo_mask">provincia di residenza <a onclick="carica_province(form0.prov_residenza_t.value,3)" class="cambia_valore">cambia provincia</a></div>
		<div class="campo_mask nomandatory" id="province_tut">
			<input type="text" class="scrittura readonly" name="prov_residenza_t" id="prov_residenza_t" SIZE="30" maxlenght="30" value="<?php echo($tut_provincia);?>"readonly/>
		
        </div>
	</span>
    
    <span class="rigo_mask">
		<div class="testo_mask">Comune di residenza <a onclick="carica_comuni(form0.prov_residenza_t.value,3,form0.comune_residenza_t.value)" class="cambia_valore">cambia comune</a></div>
		<div class="campo_mask nomandatory" id="comuni_tut">
        <input type="text" class="scrittura readonly" name="comune_residenza_t" id="comune_residenza_t" SIZE="30" maxlenght="30" value="<?php echo($tut_comune);?>" readonly/>
                   
		</div>
	</span>
        
    <span class="rigo_mask">
		<div class="testo_mask">CAP</div>
		<div class="campo_mask">
			<input type="text" class="scrittura readonly" name="cap_residenza_t" SIZE="30" maxlenght="30" value="<?php echo($tut_cap);?>"readonly/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">telefono</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura" name="telefono_t" SIZE="30" maxlenght="30" value="<?php echo($tut_tel);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">fax</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura" name="fax_t" SIZE="30" maxlenght="30" value="<?php echo($tut_fax);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">cellulare</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura" name="cellulare_t" SIZE="30" maxlenght="30" value="<?php echo($tut_cell);?>"/>
		</div>
	</span>
    <span class="rigo_mask">
		<div class="testo_mask">e-mail</div>
		<div class="campo_mask email">
			<input type="text" class="scrittura" name="email_t" SIZE="30" maxlenght="30" value="<?php echo($tut_mail);?>"/>
		</div>
	</span>
     <span class="rigo_mask">
		<div class="testo_mask">relazione con il paziente</div>
		<div class="campo_mask nomandatory">
			<input type="text" class="scrittura" name="relazione_t" SIZE="30" maxlenght="30" value="<?php echo($tut_relazione);?>"/>
		</div>
	</span>

	
    <span class="rigo_mask">
        <div class="testo_mask">stato</div>
        <div class="campo_mask">
                <select name="stato" class="scrittura">
                <option value="1" <?php if($stato=="1") echo("selected");?>>attivo</option>
                <option value="0" <?php if($stato=="0") echo("selected");?>>disattivo</option>
                </select>
        </div>
    </span>
	</div>
    
    <div class="titolo_pag">
		<div class="comandi">
			<input type="submit" title="salva" value="salva" class="button_salva"/>
		</div>
	</div>
	</form>
	
	
	<script type="text/javascript" language="javascript">
	$(document).ready(function() {			
				$.mask.addPlaceholder('~',"[+-]");	
				$(".campo_data").mask("99/99/9999");				
		
	});	
</script>
	
	<script type="text/javascript"> 
       // prepare the form when the DOM is ready 
$(document).ready(function() { 

x=inizializza();
    var options = { 
     //   target:        '#layer_nero',   // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse  // post-submit callback 
 
        // other available options: 
        //url:       url         // override for form's 'action' attribute 
        //type:      type        // 'get' or 'post', override for form's 'method' attribute 
        //dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
        //clearForm: true        // clear all form fields after successful submit 
        //resetForm: true        // reset the form after successful submit 
 
        // $.ajax options can be used here too, for example: 
        //timeout:   3000 
    }; 
 
    // bind to the form's submit event 
    $('#myForm').submit(function() { 
        // inside event callbacks 'this' is the DOM element so we first 
        // wrap it in a jQuery object and then invoke ajaxSubmit 
        $(this).ajaxSubmit(options); 
 
        // !!! Important !!! 
        // always return false to prevent standard browser submit and page navigation 
        return false; 
    }); 
}); 
 
// pre-submit callback 
function showRequest(formData, jqForm, options) { 
var x;
x=false;



if (validateform==true)
{
$('#layer_nero').toggle();
return true;
}
else
return false;

 
} 
 
// post-submit callback 
function showResponse(responseText, statusText)  { 

   
   valore=responseText;
   //valore='ok'; 
if (valore=="")
	valore='no';
   avvisi_loading(valore);
  

} 
			
			/*$('#myForm').ajaxForm(
			function() {
					
                alert("I dati sono stati aggiornati!"); 
            }
			
			); */
        //}); 
    </script> 	
	
	
	
	
<?php
 }
 

if(isset($_SESSION['UTENTE'])) {
	
	if(!isset($do)) $do='';
	$back = "lista_pazienti.php";	
	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {

		if (empty($_POST)) $_POST['action'] = "";

		
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
					else update($_REQUEST['id']);
			break;

			case "del":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
					else if($_POST['choice']=="si") del($_REQUEST['id']);
			break;
		}

		
		switch($do) {

			case "add":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
					else add();
			break;

			case "edit":
				// verifica i permessi..				
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else edit($_REQUEST['id']);
			break;
			
			case "review":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
					else review($_REQUEST['id']);
			break;

			case "confirm_del":
				confirm_del($_REQUEST['id']);
			break;

			case "logout":
			logout();
			break;

			default:
				show_list();
			break;
		}
			html_footer();
	}
	else { error_message("non hai i permessi per visualizzare questa pagina!"); go_home();}

} else {
	print("ciao");
	exit();
	ob_start();
	Header("Location: index.php");
	ob_end_flush();
}
?>