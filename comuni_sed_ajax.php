<?php

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$id_prov=$_REQUEST['id_prov'];
$nome_com=$_REQUEST['nome_com'];
$id_req=$_REQUEST['id_req'];

$conn = db_connect();

	if($nome_com=="")
		$query = "SELECT id_comune, denominazione, provincia FROM dbo.comuni WHERE (provincia='".$id_prov."') ORDER BY denominazione ASC";	
		else
		$query = "SELECT dbo.comuni.id_comune, dbo.comuni.denominazione, dbo.comuni.provincia, dbo.province.nome, dbo.province.id FROM dbo.comuni INNER JOIN dbo.province ON dbo.comuni.provincia = dbo.province.id WHERE (dbo.province.nome ='".$id_prov."') ORDER BY dbo.comuni.denominazione ASC";					
    $rs = mssql_query($query, $conn);
	if (mssql_num_rows($rs)==0){
		$query = "SELECT dbo.comuni.id_comune, dbo.comuni.denominazione, dbo.comuni.provincia, dbo.province.nome, dbo.province.id FROM dbo.comuni INNER JOIN dbo.province ON dbo.comuni.provincia = dbo.province.id WHERE (dbo.province.id ='".$id_prov."') ORDER BY dbo.comuni.denominazione ASC";	
		 $rs = mssql_query($query, $conn);
	}
    if(!$rs) error_message(mssql_error());
		?>
 		<select id="comune_sede<?=$id_req?>" class="scrittura" name="comune_sede<?=$id_req?>" onChange="carica_cap_sede(<?=$id_req?>,document.getElementById('comune_sede<?=$id_req?>').value);" >		
	<?php		
	if (mssql_num_rows($rs)==0) 			    
  		print('<option value="">Scegliere prima una provincia</option>'."\n");
		else
		print('<option value="">Selezionare un comune</option>'."\n");
   
    while ($row = mssql_fetch_assoc($rs)) {
    
        // setta la variabile di sessione
        $idcomune = $row['id_comune'];
        $nomecom = pulisci_lettura($row['denominazione']);
                            
        print('<option value="'.$nomecom.'"');     
        	if($nomecom==$idcomune) echo(" selected=\"selected\"");
        print ('>'.$nomecom.'</option>'."\n");					
    }
    ?>
</select>





