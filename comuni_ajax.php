<?php

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_prov=$_REQUEST['id_prov'];
$nome_com=$_REQUEST['nome_com'];
$id_req=$_REQUEST['id_req'];
if (isset($_REQUEST['id_tutor']))
	$id_tutor=$_REQUEST['id_tutor'];
	else
	$id_tutor="";
if(!(is_numeric($id_tutor))) $id_tutor="";
$conn = db_connect();

//$query = "SELECT id, nome FROM dbo.province WHERE (id=".$id_prov.")";
//$rs = mssql_query($query, $conn);
//$row=mssql_fetch_assoc($rs);
//$prov_nome=$row['nome'];
//mssql_free_result($rs);
//$prov_nome=str_replace("'","''",$prov_nome);
//echo("id_p ".$id_prov." nome_com ".$id_prov);
	
	if($nome_com=="")
		$query = "SELECT id_comune, denominazione, provincia FROM dbo.comuni WHERE (provincia='".$id_prov."') ORDER BY denominazione ASC";	
		elseif (is_numeric($id_prov))
		$query = "SELECT dbo.comuni.id_comune, dbo.comuni.denominazione, dbo.comuni.provincia, dbo.province.nome, dbo.province.id FROM dbo.comuni INNER JOIN dbo.province ON dbo.comuni.provincia = dbo.province.id WHERE (dbo.province.id ='".$id_prov."') ORDER BY dbo.comuni.denominazione ASC";
			else
			$query = "SELECT dbo.comuni.id_comune, dbo.comuni.denominazione, dbo.comuni.provincia, dbo.province.nome, dbo.province.id FROM dbo.comuni INNER JOIN dbo.province ON dbo.comuni.provincia = dbo.province.id WHERE (dbo.province.nome ='".$id_prov."') ORDER BY dbo.comuni.denominazione ASC";
    $rs = mssql_query($query, $conn);
	
	if (mssql_num_rows($rs)==0){
		if (is_numeric($id_prov))
		$query = "SELECT dbo.comuni.id_comune, dbo.comuni.denominazione, dbo.comuni.provincia, dbo.province.nome, dbo.province.id FROM dbo.comuni INNER JOIN dbo.province ON dbo.comuni.provincia = dbo.province.id WHERE (dbo.province.id ='".$id_prov."') ORDER BY dbo.comuni.denominazione ASC";
		else
		$query = "SELECT dbo.comuni.id_comune, dbo.comuni.denominazione, dbo.comuni.provincia, dbo.province.nome, dbo.province.id FROM dbo.comuni INNER JOIN dbo.province ON dbo.comuni.provincia = dbo.province.id WHERE (dbo.province.nome ='".$id_prov."') ORDER BY dbo.comuni.denominazione ASC";
		
		 $rs = mssql_query($query, $conn);
	}

    if(!$rs) error_message(mssql_error());
	if(mssql_num_rows($rs)==0){
		mssql_free_result($rs);
		if (is_numeric($id_prov)) 
			$cond="id=$id_prov";
			else
			$cond="nome='$id_prov'";
		$query = "SELECT dbo.province.id, dbo.comuni.id_comune, dbo.comuni.denominazione FROM dbo.province INNER JOIN
                      dbo.comuni ON dbo.province.nome = dbo.comuni.denominazione WHERE $cond order by nome";
		$rs = mssql_query($query, $conn);
	}
	//echo($query);	
	switch ($id_req) {
		case 1:
		?>
 		<select id="comune_nascita" class="scrittura" name="comune_nascita">
	<?php  
		break;
		case 2:
		?>
 		<select id="comune_residenza" class="scrittura" name="comune_residenza" onChange="carica_cap(1,document.getElementById('comune_residenza').value);">
	<?php 
		break;
		case 3:
		?>
 		<select id="comune_residenza_t_<?=$id_tutor?>" class="scrittura" name="comune_residenza_t_<?=$id_tutor?>" onChange="carica_cap(2,getElementById('comune_residenza_t_<?=$id_tutor?>').value);">
	<?php 
		break;
		case 4:
		?>
 		<select id="comune_residenza_tn_<?=$id_tutor?>" class="scrittura" name="comune_residenza_tn_<?=$id_tutor?>" >
	<?php 
		break;
		case 5:
		?>
 		<select id="med_comune_residenza" class="scrittura" name="med_comune_residenza" onChange="carica_cap(3,document.getElementById('med_comune_residenza').value);">
	<?php 
		break;
	}
	if (mssql_num_rows($rs)==0) 			    
  		print('<option value="">Scegliere prima una provincia</option>'."\n");
		else
		print('<option value="">Selezionare un comune</option>'."\n");
   
    while ($row = mssql_fetch_assoc($rs)) {
    
        // setta la variabile di sessione
        $idcomune = $row['id_comune'];
        $nomecom = pulisci_lettura($row['denominazione']);
                            
        print('<option value="'.$idcomune.'"');     
        	if($nomecom==$idcomune) echo(" selected=\"selected\"");
        print ('>'.$nomecom.'</option>'."\n");					
    }
    ?>
</select>





