<?php

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$nome_prov=$_REQUEST['nome_prov'];
$id_req=$_REQUEST['id_req'];
if (isset($_REQUEST['id_tutor']))
	$id_tutor=$_REQUEST['id_tutor'];
	else
	$id_tutor="";
if(!(is_numeric($id_tutor))) $id_tutor="";
$conn = db_connect();

	if(($nome_prov=='297') or ($nome_prov==''))
		$query = "SELECT id, nome, sigla FROM dbo.province WHERE sigla<>'(EE)' order by nome";
	else{
		if(is_numeric($nome_prov))
			$query = "SELECT id, nome, sigla FROM dbo.province WHERE id=$nome_prov order by nome";
			else
			$query = "SELECT id, nome, sigla FROM dbo.province WHERE nome='$nome_prov' order by nome";			
		//echo($query);
		$rs = mssql_query($query, $conn);
		$row = mssql_fetch_assoc($rs);
		if($row['sigla']!='(EE)') $query = "SELECT id, nome, sigla FROM dbo.province WHERE sigla<>'(EE)' order by nome";		
		mssql_free_result($rs);
	}
	//echo($query);	
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	switch ($id_req) {
		case 1:
		?>
 <select id="prov_nascita" class="scrittura" name="prov_nascita" onChange="carica_comuni(document.getElementById('prov_nascita').value,1,document.getElementById('comune_nascita').value);" >
	<?php  
		break;
		case 2:
		?>
 <select id="prov_residenza" class="scrittura" name="prov_residenza" onChange="carica_comuni(document.getElementById('prov_residenza').value,2,document.getElementById('comune_residenza').value);" >
	<?php 
		break;
		case 3:
		?>
 <select id="prov_residenza_t_<?=$id_tutor?>" class="scrittura" name="prov_residenza_t_<?=$id_tutor?>" onChange="carica_comuni(document.getElementById('prov_residenza_t_<?=$id_tutor?>').value,3,document.getElementById('comune_residenza_t_<?=$id_tutor?>').value,<?="'".$id_tutor."'"?>);" >
	<?php 
		break;
		case 4:
		?>
 <select id="prov_residenza_tn_<?=$id_tutor?>" class="scrittura" name="prov_residenza_tn_<?=$id_tutor?>" onChange="javascript:carica_comuni(document.getElementById('prov_residenza_tn_<?=$id_tutor?>').value,4,document.getElementById('comune_residenza_tn_<?=$id_tutor?>').value,<?="'".$id_tutor."'"?>);" >
	<?php 
		break;		
		case 5:
		?>
 <select id="med_residenza" class="scrittura" name="med_residenza" onChange="carica_comuni(document.getElementById('med_residenza').value,5,document.getElementById('med_comune_residenza').value);" >
	<?php 
		break;
	} 			    
    print('<option value="">Selezionare una provincia</option>'."\n");
    while ($row = mssql_fetch_assoc($rs)) {
    
        // setta la variabile di sessione
        $idprov = $row['id'];
        $nomeprov = pulisci_lettura($row['nome']);
                            
        print('<option value="'.$idprov.'"');     
        	if(trim($nome_prov)==trim($nomeprov)) echo(" selected=\"selected\"");
        print ('>'.$nomeprov.'</option>'."\n");					
    }
    ?>
</select>





