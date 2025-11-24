<?php

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/session.inc.php');
$nome_prov=$_REQUEST['nome_prov'];
$id_req=$_REQUEST['id_req'];


$conn = db_connect();
	$query = "SELECT id, nome FROM dbo.province WHERE sigla<>'(EE)' order by nome";	
    $rs = mssql_query($query, $conn);
    if(!$rs) error_message(mssql_error()); ?>
	<select id="prov_sede<?=$id_req?>" class="scrittura" name="prov_sede<?=$id_req?>"  onChange="carica_comuni_sede(document.getElementById('prov_sede<?=$id_req?>').value,<?=$id_req?>,document.getElementById('comune_sede<?=$id_req?>').value);" >
	<?php 	
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





