<?php

// inclusione delle classi
include_once('include/class.User.php');
include_once('include/class.Struttura.php');
include_once("fckeditor/fckeditor.php");

// inizializza le variabili di sessione
include_once('include/session.inc.php');

// file di inclusione
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');

// nome della tabella
$tablename = 'domini';
$id_permesso = $id_menu = 1;
$pagetitle = "gestione domini";

// recupera la struttura corrente
//$SID = $_SESSION['STRUTTURA'];


/****************************
* funzione add()            *
*****************************/
function add() {

	global $PHP_SELF, $tablename, $pagetitle;
	$array_mod=array();
	$admin=$_SESSION['UTENTE']->is_root();
	$giorno_corrente=date('d');
	$mese_corrente=date('m');
	$anno_corrente=date('Y');
	//$sid = $_SESSION['STRUTTURA1']->get_id();

	open_center();
?>
		<form method="post" name="form0" action="<?php echo $PHP_SELF; ?>" enctype="multipart/form-data" onSubmit="return controlla_campi_form('nome','nome','0');">
		<input type="hidden" name="action" value="create" />

		<div id="titolo_pag"><h1><small><?php echo $pagetitle ?> - aggiungi dominio</small></h1></div>

		<div class="blocco_centralcat">
			<div class="titolo_par"><em>I campi in <strong>grassetto</strong> sono obbligatori.</em></div>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">URL senza http://</span></div>
				<div class="campo_mask">
					<input type="textarea" class="scrittura" name="nome" size="50" maxlength="255">
				</div>
			</span>
        
        <span class="rigo_mask">
				<div class="testo_mask">Data Scadenza</div>
				<div class="campo_mask">
					 <select class="scrittura" name="gg_in">
					 <?php 
						for($i=1; $i<=31; $i++) {
						
							print('<option value="'.$i.'"');
							if($i == $giorno_corrente) echo " selected";
							print('>'.$i.'</option>'."\n");
						}
					 ?>
					 </select>
					 <select class="scrittura" name="mm_in">
					 <option value="01" <?php if($mese_corrente=='01') echo "selected"; ?>>gennaio</option>
					 <option value="02" <?php if($mese_corrente=='02') echo "selected"; ?>>febbraio</option>
					 <option value="03" <?php if($mese_corrente=='03') echo "selected"; ?>>marzo</option>
					 <option value="04" <?php if($mese_corrente=='04') echo "selected"; ?>>aprile</option>
					 <option value="05" <?php if($mese_corrente=='05') echo "selected"; ?>>maggio</option>
					 <option value="06" <?php if($mese_corrente=='06') echo "selected"; ?>>giugno</option>
					 <option value="07" <?php if($mese_corrente=='07') echo "selected"; ?>>luglio</option>
					 <option value="08" <?php if($mese_corrente=='08') echo "selected"; ?>>agosto</option>
					 <option value="09" <?php if($mese_corrente=='09') echo "selected"; ?>>settembre</option>
					 <option value="10" <?php if($mese_corrente=='10') echo "selected"; ?>>ottobre</option>
					 <option value="11" <?php if($mese_corrente=='11') echo "selected"; ?>>novembre</option>
					 <option value="12" <?php if($mese_corrente=='12') echo "selected"; ?>>dicembre</option>
					 </select>
					 <select class="scrittura" name="aa_in">
					 <?php 
						for($i=2007; $i<=2010; $i++) {
						
							print('<option value="'.$i.'"');
							if($i == $anno_corrente) echo " selected";
							print('>'.$i.'</option>'."\n");
						}
					 ?>
						 </select>
				</div>
			</span>
        <div class="linea"></div>
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">GoogleKey</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="keygoogle" size="50" maxlength="255">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">GoogleScript</span></div>
				<div class="campo_mask">					
                    <textarea name="script" class="scrittura" rows="5" cols="74"></textarea>
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">file css</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="stile_css" size="50" maxlength="255">
				</div>
			</span>
                      									
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">parola</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="parola" size="50" maxlength="255">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">descrizione</span></div>
				<div class="campo_mask">
                <?	// OGGETTO PER L'EDITOR VISUALE
$oFCKeditor = new FCKeditor('descrizione') ;
$oFCKeditor->BasePath = 'http://netmat.braincomputing.com/netmat/republic/fckeditor/';
$oFCKeditor->Width = 600;
$oFCKeditor->Height= 500;
$oFCKeditor->Value = $descrizione;
$oFCKeditor->Create() ;
?>

				<!-- <textarea name="content" class="scrittura" rows="20" cols="70"><?=$descrizione;?></textarea> -->
                
                					
                    <!--<textarea name="descrizione" class="scrittura" rows="20" cols="74"><?php //print($descrizione); ?></textarea>-->
				<div class="campo_mask">N.B. il titolo all'interno della descrizione deve essere impostato come <strong>H3</strong></div>
                </div>
			</span>
            
            
            <span class="rigo_mask">
				<div class="testo_mask"><span class="need">categorie news</span></div>
				<div class="campo_mask">
			
			
				<SELECT NAME="possible" SIZE="10"  class="scrittura" WIDTH="20" STYLE="width: 250px">
				<?php
				$query = "select id,nome FROM categorie_news WHERE cancella='n' ORDER BY nome ASC";
				$conn = db_connect();
				$result = mssql_query($query, $conn);
				if(!$result) error_message(mssql_error());
				
				while($row = mssql_fetch_assoc($result)) {
			
					$id = $row['id'];
					$nome = $row['nome'];
					
					$query="SELECT dbo.news.id FROM dbo.news INNER JOIN dbo.categorie_news ON dbo.news.categoria = dbo.categorie_news.id GROUP BY dbo.news.id, dbo.categorie_news.id HAVING (dbo.categorie_news.id = $id)";
					$results = mssql_query($query, $conn);
					$num_news=0;
					if (mssql_num_rows($results)>0) $num_news=mssql_num_rows($results);
					
					print('<option value="'.$id.'" ondblclick="copyToList(\'possible\',\'chosen\')">'.$nome.' ('.$num_news.')</option>'."\n");
					
				}
				?>
				</SELECT>
	
				<input type="button" value="&gt; &gt;" onClick="copyToList('possible','chosen')" />
				<input type="button" value="&lt; &lt;" onClick="copyToList('chosen','possible')" />
				<SELECT NAME="chosen" SIZE="10" class="scrittura" WIDTH="20" STYLE="width: 250px;">
				</SELECT>
				<input name="cat_news" type="hidden"  size="20" value="" />
					
				</div>
			</span>
            
            <div class="linea"></div>
            <span class="rigo_mask">
                <div class="testo_mask">meta_Titolo </div>
                <div class="campo_mask">
                <input type="text" class="scrittura_campo" name="meta_titolo" maxlength="255" value="">
                </div>
            </span>
            <span class="rigo_mask">
                <div class="testo_mask">meta_Descrizione </div>
                <div class="campo_mask">
                <textarea class="scrittura" name="meta_descrizione" cols="74" rows="3" ></textarea>
                </div>
            </span>
            <span class="rigo_mask">
                <div class="testo_mask">meta_Keyword </div>
                <div class="campo_mask">
                <input type="text" class="scrittura_campo" name="meta_keyword" maxlength="255" value="">
                </div>
            </span> 		           
            		
			<div class="linea"></div>            
            <span class="rigo_mask">
				<div class="testo_mask"><span class="need">testata</span></div>
				<div class="campo_mask">					
                    <input type="file" class="scrittura" name="img_1_src" size="25"  /><br />
				</div>
			</span>	
			
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">alt testata</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="alt_testata" size="50" maxlength="255">
				</div>
			</span>	            
			<div class="linea"></div>
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">n. strutture web visualizzate</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="top" size="50" maxlength="255" value="0" >
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">stato</span></div>
				<div class="campo_mask">
					<select name="status" class="scrittura">
						<option value="1" selected>online</option>
						<option value="0">offline</option>
					</select>
				</div>
			</span>

	</div>
    
    <div id="titolo_pag"><h1><a id="manager"><small>Gestione Afferenze </small></a></h1></div>	
	<div class="blocco_centralcat">
	<span class="rigo_mask">
        <div class="testo_mask"><span class="need">directory</span></div>
        <div class="campo_mask">    
    
        <SELECT NAME="possible1" SIZE="10"  class="scrittura" WIDTH="20" STYLE="width: 250px">
        <?php
        $query = "SELECT id,nome FROM settori WHERE cancella='n' ORDER BY nome";
                
        $result = mssql_query($query, $conn);
        if(!$result) error_message(mssql_error());
        
        while($row = mssql_fetch_assoc($result)) {    
            $id = $row['id'];
            $nome = $row['nome'];
            print('<option value="'.$id.'">'.$nome.'</option>'."\n");            
        }
        ?>
        </SELECT>

        <input type="button" value="&gt; &gt;" onClick="copyToList1('possible1','chosen1')" />
        <input type="button" value="&lt; &lt;" onClick="copyToList1('chosen1','possible1')" />
        <SELECT NAME="chosen1" SIZE="10" class="scrittura" WIDTH="20" STYLE="width: 250px">
        </SELECT>
        <input name="directory" type="hidden" size="20" value="" />
            
        </div>
    </span>
    <span class="rigo_mask">
        <div class="testo_mask"><span class="need">Province</span></div>
        <div class="campo_mask">    
    <div id="poss2" style="float:left;">
        <SELECT NAME="possible2" SIZE="10" class="scrittura" WIDTH="20" STYLE="width: 250px">
        <?php
        $query = "SELECT province.id, province.nome, province.sigla, regioni.nome AS regione
					FROM  province INNER JOIN
                    regioni ON province.idregione = regioni.id ORDER BY province.nome";
                
        $result = mssql_query($query, $conn);
        if(!$result) error_message(mssql_error());
        
        while ($row = mssql_fetch_assoc($result)) {

			// setta la variabile di sessione
			$idprov = $row['id'];
			$nomeprov = $row['nome'];
			$siglaprov = $row['sigla'];
			$regione = $row['regione'];
			
			print('<option value="'.$idprov.'"');
			if(isset($_SESSION['PROVINCIA']))
				if($_SESSION['PROVINCIA']==$idprov) echo " selected";
			print ('>'.$nomeprov.' '.$siglaprov.'</option>'."\n");
			$stringa_regioni .= $idprov."-".$regione.",";
		}
		?>
			</select>
        </SELECT>
		</div>

        <input type="button" value="&gt; &gt;" onClick="copyToList2('possible2','chosen2')" />
        <input type="button" value="&lt; &lt;" onClick="copyToList2('chosen2','possible2')" />
        <SELECT NAME="chosen2" SIZE="10" class="scrittura" WIDTH="20" STYLE="width: 250px">
        </SELECT>
        <input name="province" type="hidden" size="20" value="" />
            
        </div>
        <div style="padding-left:140px;"> <input name="AllList" type="button" value="inserisci tutti" onclick="copyAllList('possible2','chosen2')" /></div>
    </span>
    
    <span class="rigo_mask">
        <div class="testo_mask">regioni</div>
        <div class="campo_mask" >
            <select name="regione" class="scrittura" onchange="get(this.value);">
            <?php
            $conn = db_connect();
            $query = "SELECT * from regioni WHERE cancella='n' order by nome asc";
            $rs = mssql_query($query, $conn);
            if(!$rs) error_message(mssql_error());
        
            while($row = mssql_fetch_assoc($rs)){
        
                $idreg = $row['id'];
                $nome = $row['nome'];
                ?>
                <option value="<?=$idreg?>"><?=$nome?></option>
                <?
               
            }
            mssql_free_result($rs);
            ?>
            </select>
        
        </div>
	</span>
    
      <span class="rigo_mask">
        <div class="testo_mask">zone</div>
        <div class="campo_mask" >
            <select name="zone" class="scrittura" onchange="get_zone(this.value);">
            <?php
            $conn = db_connect();
            $query = "SELECT zona FROM dbo.regioni GROUP BY zona";
            $rs = mssql_query($query, $conn);
            if(!$rs) error_message(mssql_error());
        
            while($row = mssql_fetch_assoc($rs)){
        
                $zona = $row['zona'];
                ?>
                <option value="<?=$zona?>"><?=$zona?></option>
                <?
               
            }
            mssql_free_result($rs);
            ?>
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
<?php
	close_center();
}


/****************************
* funzione edit($id)        *
*****************************/
function edit($id){

	global $PHP_SELF, $tablename, $pagetitle, $id_permesso;
	$conn = db_connect();
	$query = "SELECT * from $tablename WHERE id='$id'";
	$rs = mssql_query($query, $conn);

	if(!$rs) error_message(mssql_error());

	if($row = mssql_fetch_assoc($rs)){
		
		
		$datascadenza = $row['datascadenza'];			
		$giorno_corrente=(substr($datascadenza,6,2));
		$mese_corrente=(substr($datascadenza,4,2));
		$anno_corrente=(substr($datascadenza,0,4));

		$nome = $row['nome'];
		$status = $row['status'];
		$top = $row['top_strut_web'];
		$status = $row['status'];
		$keygoogle = $row['keygoogle'];
		$script= $row['script'];
		$stile_css= $row['stile_css'];
		$parola= $row['parola'];
		$descrizione= $row['descrizione'];
		$alt_testata= $row['alt_testata'];
		$testata= $row['testata'];
		$meta_titolo=$row['meta_titolo'];
		$meta_descrizione=$row['meta_descrizione'];
		$meta_keyword=$row['meta_keyword'];		

	}

	// rimuove i caratteri di escape
	$nome = stripslashes($nome);
	mssql_free_result($rs);
	
	//categorie news
	$query = "SELECT id_cat_news from domini_cat_news WHERE id_dominio='$id'";	
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	$ARR = array();
	while($row = mssql_fetch_assoc($rs)){
		array_push($ARR, $row['id_cat_news']);
	}	
	$catnewssel = implode(',',$ARR);	
	mssql_free_result($rs);
	
	//directory
	$query = "SELECT id_directory from domini_directory WHERE id_dominio='$id'";	
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	$ARR1 = array();
	while($row = mssql_fetch_assoc($rs)){
		array_push($ARR1, $row['id_directory']);
	}	
	$directorysel = implode(',',$ARR1);	
	mssql_free_result($rs);
	
	//province
	$query = "SELECT id_provincia from domini_province WHERE id_dominio='$id'";	
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	$ARR2 = array();
	while($row = mssql_fetch_assoc($rs)){
		array_push($ARR2, $row['id_provincia']);
	}	
	$provincesel = implode(',',$ARR2);	
	mssql_free_result($rs);

	open_center();
?>
		<form method="post" name="form0" action="<?php echo $PHP_SELF; ?>" enctype="multipart/form-data" onSubmit="return controlla_campi_form('nome','nome','0');">
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />

		<div id="titolo_pag"><h1><small><?php echo $pagetitle ?> - edita dominio</small></h1></div>

		<div class="blocco_centralcat">

	   		<div class="titolo_par"><em>I campi in <strong>grassetto</strong> sono obbligatori.</em></div>

			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">nome dominio</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="nome" size="50" maxlength="50" value="<?php print($nome); ?>">
				</div>
			</span>
      
        <span class="rigo_mask">
				<div class="testo_mask">Data Scadenza</div>
				<div class="campo_mask">
					 <select class="scrittura" name="gg_in">
					 <?php 
						for($i=1; $i<=31; $i++) {
						
							print('<option value="'.$i.'"');
							if($i == $giorno_corrente) echo " selected";
							print('>'.$i.'</option>'."\n");
						}
					 ?>
					 </select>
					 <select class="scrittura" name="mm_in">
					 <option value="01" <?php if($mese_corrente=='01') echo "selected"; ?>>gennaio</option>
					 <option value="02" <?php if($mese_corrente=='02') echo "selected"; ?>>febbraio</option>
					 <option value="03" <?php if($mese_corrente=='03') echo "selected"; ?>>marzo</option>
					 <option value="04" <?php if($mese_corrente=='04') echo "selected"; ?>>aprile</option>
					 <option value="05" <?php if($mese_corrente=='05') echo "selected"; ?>>maggio</option>
					 <option value="06" <?php if($mese_corrente=='06') echo "selected"; ?>>giugno</option>
					 <option value="07" <?php if($mese_corrente=='07') echo "selected"; ?>>luglio</option>
					 <option value="08" <?php if($mese_corrente=='08') echo "selected"; ?>>agosto</option>
					 <option value="09" <?php if($mese_corrente=='09') echo "selected"; ?>>settembre</option>
					 <option value="10" <?php if($mese_corrente=='10') echo "selected"; ?>>ottobre</option>
					 <option value="11" <?php if($mese_corrente=='11') echo "selected"; ?>>novembre</option>
					 <option value="12" <?php if($mese_corrente=='12') echo "selected"; ?>>dicembre</option>
					 </select>
					 <select class="scrittura" name="aa_in">
					 <?php 
						for($i=2007; $i<=2010; $i++) {
						
							print('<option value="'.$i.'"');
							if($i == $anno_corrente) echo " selected";
							print('>'.$i.'</option>'."\n");
						}
					 ?>
						 </select>
				</div>
			</span>	
			
			<div class="linea"></div>
			
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">GoogleKey</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="keygoogle" size="50" maxlength="255" value="<?php print($keygoogle); ?>">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">GoogleScript</span></div>
				<div class="campo_mask">					
                    <textarea name="script" class="scrittura" rows="5" cols="74"><?php print($script); ?></textarea>
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">file css</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="stile_css" size="50" maxlength="255" value="<?php print($stile_css); ?>">
				</div>
			</span>			           
                        
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">parola</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="parola" size="50" maxlength="255" value="<?php print($parola); ?>">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">descrizione</span></div>
				<div class="campo_mask">
                <?	// OGGETTO PER L'EDITOR VISUALE
$oFCKeditor = new FCKeditor('descrizione') ;
$oFCKeditor->BasePath = 'http://netmat.braincomputing.com/netmat/republic/fckeditor/';
$oFCKeditor->Width = 600;
$oFCKeditor->Height= 500;
$oFCKeditor->Value = $descrizione;
$oFCKeditor->Create() ;
?>

				<!-- <textarea name="content" class="scrittura" rows="20" cols="70"><?=$descrizione;?></textarea> -->
                
                					
                    <!--<textarea name="descrizione" class="scrittura" rows="20" cols="74"><?php //print($descrizione); ?></textarea>-->
				<div >        <strong> N.B. il titolo all'interno della descrizione deve essere impostato come H3</strong></div>
                </div>
			</span>	
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">categorie news</span></div>
				<div class="campo_mask">				
				<SELECT MULTIPLE class="scrittura" NAME="possible" SIZE="10"  WIDTH="20" STYLE="width: 250px">
				<?php
				$query = "select id,nome FROM categorie_news WHERE cancella='n' ";				
				$query .= " ORDER BY nome";
				
				$result = mssql_query($query, $conn);
				if(!$result) error_message(mssql_error());
				
				while($row = mssql_fetch_assoc($result)) {
			
					$id = $row['id'];
					$nome = $row['nome'];
					$query="SELECT dbo.news.id FROM dbo.news INNER JOIN dbo.categorie_news ON dbo.news.categoria = dbo.categorie_news.id GROUP BY dbo.news.id, dbo.categorie_news.id HAVING (dbo.categorie_news.id = $id)";
					$results = mssql_query($query, $conn);
					$num_news=0;
					if (mssql_num_rows($results)>0) $num_news=mssql_num_rows($results);
					
					print('<option value="'.$id.'" ondblclick="copyToList(\'possible\',\'chosen\')">'.$nome.' ('.$num_news.')</option>'."\n");
					
				}
				?>
				</SELECT>

				<input type="button" value="&gt; &gt;" onClick="copyToList('possible','chosen')" />
				<input type="button" value="&lt; &lt;" onClick="copyToList('chosen','possible')" />
				<SELECT MULTIPLE class="scrittura" NAME="chosen" SIZE="10" WIDTH="20" STYLE="width: 250px">
				<?php
				foreach ($ARR as $element) {
					$query = "select nome FROM categorie_news WHERE id=$element";
					$result = mssql_query($query, $conn);
					if($r = mssql_fetch_row($result))
						print('<option value="'.$element.'">'.$r[0].'</option>'."\n");
					mssql_free_result($result);
				}
				?>
				</SELECT>
				<input name="cat_news" type="hidden" size="20" value="<?=$catnewssel;?>" />
			
				</div>
			</span>
            <div class="linea"></div>
            <span class="rigo_mask">
                <div class="testo_mask">meta_Titolo </div>
                <div class="campo_mask">
                <input type="text" class="scrittura_campo" name="meta_titolo" maxlength="255" value="<?php print($meta_titolo) ?>">
                </div>
            </span>
            <span class="rigo_mask">
                <div class="testo_mask">meta_Descrizione </div>
                <div class="campo_mask">
                <textarea class="scrittura" name="meta_descrizione" cols="74" rows="3" ><?php print($meta_descrizione) ?></textarea>
                </div>
            </span>
            <span class="rigo_mask">
                <div class="testo_mask">meta_Keyword </div>
                <div class="campo_mask">
                <input type="text" class="scrittura_campo" name="meta_keyword" maxlength="255" value="<?php print($meta_keyword) ?>">
                </div>
            </span> 		
            
            
            <div class="linea"></div> 		
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">testata</span></div>
				<div class="campo_mask">
                	<input type="file"  class="scrittura" name="img_1_src" size="25" /><br />
                    <img src="images/testate/<?php print($testata); ?>" name="img_1_dis" style="border: 1px solid #CCC; margin: 5px 0 5px 0;">
				</div>
			</span>	
			
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">alt testata</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="alt_testata" size="50" maxlength="255" value="<?php print($alt_testata); ?>">
				</div>
			</span>	
			
			<div class="linea"></div> 
			
			
			<span class="rigo_mask">
				<div class="testo_mask"><span class="need">n. strutture web visualizzate</span></div>
				<div class="campo_mask">
					<input type="text" class="scrittura" name="top" size="20" maxlength="255" value="<?php print($top); ?>">
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">stato</div>
				<div class="campo_mask">
					<select name="status" class="scrittura">
					   <?php if ($status) {	?>
						<option value="1" selected>online</option>
						<option value="0">offline</option>
					    <?php } else { ?>
						<option value="1" >online</option>
						<option value="0" selected>offline</option>
					  <?php	} ?>
					</select>
				</div>
			</span>

	</div>
    <div id="titolo_pag"><h1><a id="manager"><small>Gestione Afferenze </small></a></h1></div>	
	<div class="blocco_centralcat">
	<span class="rigo_mask">
        <div class="testo_mask"><span class="need">directory</span></div>
        <div class="campo_mask">    
    
        <SELECT NAME="possible1" SIZE="10"  class="scrittura" WIDTH="20" STYLE="width: 250px">
        <?php
        $query = "SELECT id,nome FROM settori WHERE cancella='n' ORDER BY nome";
                
        $result = mssql_query($query, $conn);
        if(!$result) error_message(mssql_error());
        
        while($row = mssql_fetch_assoc($result)) {    
            $id = $row['id'];
            $nome = $row['nome'];
            print('<option value="'.$id.'" ondblclick="copyToList1(\'possible1\',\'chosen1\')">'.$nome.'</option>'."\n");            
        }
        ?>
        </SELECT>

        <input type="button" value="&gt; &gt;" onClick="copyToList1('possible1','chosen1')"  />
        <input type="button" value="&lt; &lt;" onClick="copyToList1('chosen1','possible1')" />
        <SELECT NAME="chosen1" SIZE="10" class="scrittura" WIDTH="20" STYLE="width: 250px">
        <?php
				foreach ($ARR1 as $element) {			
					$query = "select nome FROM settori WHERE id=$element";
					$result = mssql_query($query, $conn);
					if($r = mssql_fetch_row($result))
						print('<option value="'.$element.'">'.$r[0].'</option>'."\n");
					mssql_free_result($result);
				}
				?>
        </SELECT>
        <input name="directory" type="hidden" size="20" value="<?=$directorysel;?>" />
            
        </div>
    </span>
    <span class="rigo_mask">
        <div class="testo_mask"><span class="need">Province</span></div>
        <div class="campo_mask">    
    <div id="poss2" style="float:left;">
        <SELECT NAME="possible2" SIZE="10" class="scrittura" WIDTH="20" STYLE="width: 250px">
        <?php
        $query = "SELECT province.id, province.nome, province.sigla, regioni.nome AS regione
					FROM  province INNER JOIN
                    regioni ON province.idregione = regioni.id ORDER BY province.nome";
                
        $result = mssql_query($query, $conn);
        if(!$result) error_message(mssql_error());
        
        while ($row = mssql_fetch_assoc($result)) {

			// setta la variabile di sessione
			$idprov = $row['id'];
			$nomeprov = $row['nome'];
			$siglaprov = $row['sigla'];
			$regione = $row['regione'];
			
			print('<option value="'.$idprov.'"');
			if(isset($_SESSION['PROVINCIA']))
				if($_SESSION['PROVINCIA']==$idprov) echo " selected";
			print (' ondblclick="copyToList2(\'possible2\',\'chosen2\')">'.$nomeprov.'</option>'."\n");
			$stringa_regioni .= $idprov."-".$regione.",";
		}
		?>
			</select>
        </SELECT>
		</div>

       
        <input type="button" value="&gt; &gt;" onClick="copyToList2('possible2','chosen2')" />
        <input type="button" value="&lt; &lt;" onClick="copyToList2('chosen2','possible2')" />
        <SELECT NAME="chosen2" SIZE="10" class="scrittura" WIDTH="20" STYLE="width: 250px">
        <?php
				foreach ($ARR2 as $element) {			
					$query = "select nome FROM province WHERE id=$element";
					$result = mssql_query($query, $conn);
					if($r = mssql_fetch_row($result))
						print('<option value="'.$element.'">'.$r[0].'</option>'."\n");
					mssql_free_result($result);
				}
				?>
        </SELECT>
        <input name="province" type="text" size="20" value="<?=$provincesel;?>" />
            
        </div>
     <div style="padding-left:140px;"> <input name="AllList" type="button" value="inserisci tutti" onclick="copyAllList('possible2','chosen2')" /> <input name="LessList" type="button" value="cancella tutti" onclick="LessList('chosen2')" /></div></div>
    
    </span>
    
    
    <span class="rigo_mask">
        <div class="testo_mask">regioni</div>
        <div class="campo_mask" >
            <select name="regione" class="scrittura" onchange="get(this.value);">
            <?php
            $conn = db_connect();
            $query = "SELECT * from regioni WHERE cancella='n' order by nome asc";
            $rs = mssql_query($query, $conn);
            if(!$rs) error_message(mssql_error());
        
            while($row = mssql_fetch_assoc($rs)){
        
                $idreg = $row['id'];
                $nome = $row['nome'];
                ?>
                <option value="<?=$idreg?>"><?=$nome?></option>
                <?
               
            }
            mssql_free_result($rs);
            ?>
            </select>
        
        </div>
	</span>
    
     <span class="rigo_mask">
        <div class="testo_mask">zone</div>
        <div class="campo_mask" >
            <select name="zone" class="scrittura" onchange="get_zone(this.value);">
            <?php
            $conn = db_connect();
            $query = "SELECT zona FROM dbo.regioni GROUP BY zona";
            $rs = mssql_query($query, $conn);
            if(!$rs) error_message(mssql_error());
        
            while($row = mssql_fetch_assoc($rs)){
        
                $zona = $row['zona'];
                ?>
                <option value="<?=$zona?>"><?=$zona?></option>
                <?
               
            }
            mssql_free_result($rs);
            ?>
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
<?php
	close_center();
}


/****************************
* funzione create()         *
*****************************/
function create() {
    
	global $tablename;
	$nome = stripslashes($_POST['nome']);
	$top = trim($_POST['top']);
	$status = $_POST['status'];
	$keygoogle = $_POST['keygoogle'];
	$script= $_POST['script'];
	$stile_css= $_POST['stile_css'];	
	$parola= $_POST['parola'];
	$descrizione= stripslashes($_POST['descrizione']);
	$alt_testata= $_POST['alt_testata'];
	$testata= $_FILES['img_1_src']['name'];	
	$giorno= (int)($_POST['gg_in']);
	$mese= (int)($_POST['mm_in']);
	$anno= (int)($_POST['aa_in']);
	$cat=trim($_POST['cat_news']);
	$dir=trim($_POST['directory']);
	$prov=trim($_POST['province']);
	$meta_titolo=trim($_POST['meta_titolo']);
	$meta_descrizione=trim($_POST['meta_descrizione']);
	$meta_keyword=trim($_POST['meta_keyword']);

	
	if ($giorno<10) 
	$giorno='0'.$giorno;

	if ($mese<10) 
	$mese='0'.$mese;
	
	$datascadenza=$anno.$mese.$giorno;	

if(empty($nome)) error_message('Inserire un nome!');
	
	$nome = str_replace("'","''",$nome);
	$descrizione = str_replace("'","''",$descrizione);

	$conn = db_connect();

	// verifica se la struttura esiste già
	$query = "SELECT id FROM $tablename WHERE nome='".$nome."' and cancella='n'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	if(mssql_num_rows($result)) error_message("Il dominio ".$nome." è già esistente. Provare con un altro nome.");
	mssql_free_result($result);

	$query = "INSERT INTO $tablename (nome,top_strut_web,opeins,status,keygoogle,script,stile_css,parola,descrizione,testata,alt_testata,datascadenza,meta_titolo,meta_descrizione,meta_keyword) VALUES('$nome','$top','".$_SESSION['UTENTE']->get_userid()."','$status','$keygoogle','$script','$stile_css','$parola','$descrizione','$testata','$alt_testata','$datascadenza','$meta_titolo','$meta_descrizione','$meta_keyword')";

	$result = mssql_query($query, $conn);
	if(!$result) error_message("non posso");

	// recupera l'id del record appena inserito
	$query = "SELECT MAX(id) FROM $tablename WHERE opeins='".$_SESSION['UTENTE']->get_userid()."'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	if(!$row = mssql_fetch_row($result))
		die("errore MAX select");
	else $iddom = $row[0];
		
		
	// recupera le cat news selezioneate
		$ARR = array();
		$ARR = explode(',',$cat);		
		foreach($ARR as $element) {			
			if ($element!=""){
				$query = "INSERT INTO domini_cat_news (id_dominio, id_cat_news) VALUES ($iddom,$element)";
				$result = mssql_query($query, $conn);
				if(!$result) error_message("errore nell'insert cat_news");}
		}
	// recupera le directory selezioneate
		$ARR = array();
		$ARR = explode(',',$dir);		
		foreach($ARR as $element) {			
			if ($element!=""){
				$query = "INSERT INTO domini_directory (id_dominio, id_directory) VALUES ($iddom,$element)";
				$result = mssql_query($query, $conn);
				if(!$result) error_message("errore nell'insert directory");}
		}
	// recupera le province selezioneate
		$ARR = array();
		$ARR = explode(',',$prov);		
		foreach($ARR as $element) {			
			if ($element!=""){
				$query = "INSERT INTO domini_province (id_dominio, id_provincia) VALUES ($iddom,$element)";
				$result = mssql_query($query, $conn);
				if(!$result) error_message("errore nell'insert province");}
		}
	
	// immagine	
if ((trim($_FILES['img_1_src']['name']) != "")) {	
	$nomefile = $_FILES['img_1_src']['name'];
	
	$imgHNDL = fopen($_FILES['img_1_src']['tmp_name'], "r");
	$imgf = fread($imgHNDL, $_FILES['img_1_src']['size']);
	//$eimgf = mysql_escape_string($imgf);

	// apre la connessione ftp
	$conn_id = ftp_connect(IP_ADDR);	

	// login con user name e password
	$login_result = ftp_login($conn_id, FTP_USR, FTP_PWD); 

	// controllo della connessione
	if ((!$conn_id) || (!$login_result)) { 
		echo "La connessione FTP è fallita!";
		echo "Tentativo di connessione a $ftp_server per l'utente $ftp_user_name"; 
		die; 	
	} else {
		
		// se il file non esiste, lo crea via ftp
		//if (!file_exists($curr_path.'/'.$filename)) {

			// passa in passive mode			
			ftp_pasv ( $conn_id, true );
			
			$temp = tmpfile();
			fwrite($temp, $imgf);
			fseek($temp, 0);

			// si posiziona nella directory giusta
			if (!ftp_chdir($conn_id, IMAGE_TESTATE_PATH)) {
				 error_message("non posso cambiare dir");
				 exit;
			} 
			
			// upload del file
			if(! ($upload = ftp_fput ($conn_id, $nomefile , $temp ,FTP_BINARY))) {
				 error_message("errore upload del file");
				 exit;
			}
			
			// chiude l'handler del file
			fclose($temp);

	}

	fclose($imgHNDL);

	// chiudere il flusso FTP 
	ftp_quit($conn_id); 
	}
	
		
	// scrive il log
	scrivi_log ($iddom,$tablename,'ins');

}


/****************************
* funzione update($sid)     *
*****************************/
function update($id) {

	global $tablename;
	
	$nome = stripslashes($_POST['nome']);
	$top = trim($_POST['top']);
	$status = $_POST['status'];
	$keygoogle = $_POST['keygoogle'];
	$script= $_POST['script'];	
	$stile_css= $_POST['stile_css'];	
	$parola= $_POST['parola'];
	$descrizione=  stripslashes($_POST['descrizione']);
	$alt_testata= $_POST['alt_testata'];
	$testata= $_FILES['img_1_src']['name'];
	
	$giorno= (int)($_POST['gg_in']);
	$mese= (int)($_POST['mm_in']);
	$anno= (int)($_POST['aa_in']);
	$meta_titolo=$_POST['meta_titolo'];
	$meta_descrizione=$_POST['meta_descrizione'];
	$meta_keyword=$_POST['meta_keyword'];
	
	if ($giorno<10) 
	$giorno='0'.$giorno;

	if ($mese<10) 
	$mese='0'.$mese;
	
	$datascadenza=$anno.$mese.$giorno;
		
		
		$nome = str_replace("'","''",$nome);
		$descrizione = str_replace("'","''",$descrizione);	

	if(empty($nome)) error_message("Inserire il nome!");

	$conn = db_connect();

	// recupera il vecchio nome della struttura
	$query = "SELECT nome from $tablename WHERE id='$id'";
	$rs = mssql_query($query, $conn);
	if(!$rs) error_message(mssql_error());
	if($row = mssql_fetch_assoc($rs))
		$old_dirname = $row['nome'];
	mssql_free_result($rs);

	if($old_dirname!=$nome) {
		// verifica se la struttura non esiste
		$query = "SELECT id FROM $tablename WHERE nome='".$nome."'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());
		if(mssql_num_rows($result)) error_message("Il dominio ".$nome." è già esistente! Provare con una altro nome.");
		mssql_free_result($result);
	}

	// aggiunge i caratteri di escape
	$nome = str_replace("'","''",$nome);
	echo("script".$script);
	//$script = str_replace("'","''",$script);

if ($testata!=""){
	$query = "UPDATE $tablename SET nome='$nome',top_strut_web='$top',opeins='".$_SESSION['UTENTE']->get_userid()."',status='$status', keygoogle='$keygoogle',script='$script',stile_css='$stile_css',parola='$parola',descrizione='$descrizione',testata='$testata',alt_testata='$alt_testata',datascadenza='$datascadenza',meta_titolo='$meta_titolo',meta_descrizione='$meta_descrizione',meta_keyword='$meta_keyword'  where id='$id'";
	}
	else {
	$query = "UPDATE $tablename SET nome='$nome',top_strut_web='$top',opeins='".$_SESSION['UTENTE']->get_userid()."',status='$status', keygoogle='$keygoogle',script='$script',stile_css='$stile_css',parola='$parola',descrizione='$descrizione',alt_testata='$alt_testata',datascadenza='$datascadenza',meta_titolo='$meta_titolo',meta_descrizione='$meta_descrizione',meta_keyword='$meta_keyword' where id='$id'";
	}	
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	
	// recupera le cat merc selezioneate
		$ARR = array();
		$ARR = explode(',',$_POST['cat_news']);
		
		$query = "DELETE FROM domini_cat_news WHERE id_dominio=$id";
		//echo $query;
		$result = mssql_query($query, $conn);
		
		foreach($ARR as $element) {
			if ($element<>"") {
			$query = "INSERT INTO domini_cat_news (id_dominio, id_cat_news) VALUES ($id,$element)";
			//echo $query;
			$result = mssql_query($query, $conn);
			if(!$result) error_message("errore nell'insert");}
		}
	// recupera le directory selezioneate
		$ARR = array();
		$ARR = explode(',',$_POST['directory']);
		
		$query = "DELETE FROM domini_directory WHERE id_dominio=$id";
		//echo $query;
		$result = mssql_query($query, $conn);
		
		foreach($ARR as $element) {
			if ($element<>"") {
			$query = "INSERT INTO domini_directory (id_dominio, id_directory) VALUES ($id,$element)";
			//echo $query;
			$result = mssql_query($query, $conn);
			if(!$result) error_message("errore nell'insert");}
		}
	// recupera le province selezioneate
		$ARR = array();
		$ARR = explode(',',$_POST['province']);
		
		$query = "DELETE FROM domini_province WHERE id_dominio=$id";
		//echo $query;
		$result = mssql_query($query, $conn);
		
		foreach($ARR as $element) {
			if ($element<>"") {
			$query = "INSERT INTO domini_province (id_dominio, id_provincia) VALUES ($id,$element)";
			//echo $query;
			$result = mssql_query($query, $conn);
			if(!$result) error_message("errore nell'insert");}
		}
	
	// immagine	
if ((trim($_FILES['img_1_src']['name']) != "")) {	
	$nomefile = $_FILES['img_1_src']['name'];
	
	$imgHNDL = fopen($_FILES['img_1_src']['tmp_name'], "r");
	$imgf = fread($imgHNDL, $_FILES['img_1_src']['size']);
	//$eimgf = mysql_escape_string($imgf);

	// apre la connessione ftp
	$conn_id = ftp_connect(IP_ADDR);	

	// login con user name e password
	$login_result = ftp_login($conn_id, FTP_USR, FTP_PWD); 

	// controllo della connessione
	if ((!$conn_id) || (!$login_result)) { 
		echo "La connessione FTP è fallita!";
		echo "Tentativo di connessione a $ftp_server per l'utente $ftp_user_name"; 
		die; 	
	} else {
		
		// se il file non esiste, lo crea via ftp
		//if (!file_exists($curr_path.'/'.$filename)) {

			// passa in passive mode			
			ftp_pasv ( $conn_id, true );
			
			$temp = tmpfile();
			fwrite($temp, $imgf);
			fseek($temp, 0);

			// si posiziona nella directory giusta
			if (!ftp_chdir($conn_id, IMAGE_TESTATE_PATH)) {
				 error_message("non posso cambiare dir");
				 exit;
			} 
			
			// upload del file
			if(! ($upload = ftp_fput ($conn_id, $nomefile , $temp ,FTP_BINARY))) {
				 error_message("errore upload del file");
				 exit;
			}
			
			// chiude l'handler del file
			fclose($temp);

	}

	fclose($imgHNDL);

	// chiudere il flusso FTP 
	ftp_quit($conn_id); 
	}
	
	// scrive il log
	scrivi_log ($id,$tablename,'agg');

}



/****************************
* funzione confirm_del($id) *
*****************************/
function confirm_del($id) {

	global $PHP_SELF, $tablename, $pagetitle;

	$conn = db_connect();

	$query = "SELECT nome FROM $tablename where id=$id";
	if(! ($rs = mssql_query($query, $conn))) error_message(mssql_error());
	if(! ($row = mssql_fetch_row($rs))) error_message(mssql_error());
	mssql_free_result($rs);
	open_center();
?>
	<form method="post" name="modulo" action="<?php echo $PHP_SELF ?>?id=<?php echo $id ?>">
	<input type="hidden" name="action" value="del" />
	
	<div id="titolo_pag"><h1><small><?php echo $pagetitle ?> - conferma cancellazione</small></h1></div>

	<div class="blocco_centralcat">

		<div id="messaggio_cancella">Cancellare il dominio<br /><strong><?php echo $row[0]; ?></strong> ?</div>
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

	// cancella l'elemento selezionato
	$query = "UPDATE $tablename SET cancella='y' WHERE id='".$id."'";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	
	$query = "DELETE FROM domini_cat_news WHERE id_dominio=$id";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	
	$query = "DELETE FROM domini_directory WHERE id_dominio=$id";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	
	$query = "DELETE FROM domini_province WHERE id_dominio=$id";
	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	// scrive il log
	scrivi_log ($id,$tablename,'del');

}


/****************************
 funzione show_list()      *
*****************************/
function show_list(){
	
	global $PHP_SELF, $tablename, $pagetitle, $id_menu, $id_permesso;

	if(isset($_REQUEST['page'])) $page = $_REQUEST['page'];
	else $page = 1;
	if(isset($_REQUEST['subpage'])) $subpage = $_REQUEST['subpage'];
	else $subpage = 0;

	// crea la connessione
	$conn = db_connect();

	// apre il div centrale
	open_center();

	print('<div id="titolo_pag"><h1><small>'.$pagetitle.'</small></h1></div>'."\n");
	print('<div class="blocco_centralcat">'."\n");	
		
	//se non è settato il flag, fai una nuova query
	if($_SESSION['RICALCOLA']) {
	
		$_SESSION['TROVATE'] = array();

		// fa la query dei record non cancellati
		if(isset($_REQUEST['letter'])) {
			if($_REQUEST['letter']=='ALL') $query = "SELECT id from $tablename WHERE cancella='n'";
			else $query = "SELECT id FROM $tablename WHERE cancella='n' AND  nome LIKE \"".$_REQUEST['letter']."%\"";
	
		} else {
		
			$query = "SELECT id FROM $tablename WHERE cancella='n'";
		}
		
		
		//if(isset($_SESSION['ANNUARIO']) AND $_SESSION['ANNUARIO']) $query .= " AND annuario=".$_SESSION['ANNUARIO'];
		
		// order by
		$query .=" ORDER BY ";
		if(isset($_SESSION['ORDINAPER'])) {
			
			// criterio 1
			if($_SESSION['ORDINAPER']==1) $query .="$tablename.id";
			else if($_SESSION['ORDINAPER']==2) $query .= "$tablename.nome";
			else if($_SESSION['ORDINAPER']==3) $query .= "$tablename.datascadenza";
			else $query .= "$tablename.nome";
		}
		else $query .= "$tablename.id DESC";		
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
	
		while($row = mssql_fetch_row($rs)){
			// riempie l'array
			array_push($_SESSION['TROVATE'], $row[0]);
		}
		
		$_SESSION['RICALCOLA'] = false;
	}
	
	// recupera il n.	
	$num_rows = sizeof($_SESSION['TROVATE']);	

	if($num_rows > 0) {
	
		print('<div id="pulsantiera">');
		lettere_alfabetiche();
		GestionePagine($num_rows, $page, $tablename);
		$arr1= array('id','alfabetico','scadenza');
		$arr2 = array();
		ordina_per ($arr1, $arr2);
		print('</div>');
	
		print('<p class="intest">Sono stati trovati <strong>'.$num_rows.'</strong> domini');
		if(isset($_REQUEST['letter'])) print(' con la lettera <strong>'.$_REQUEST['letter'].'</strong>');
		print('.</p>'."\n");

		$rpp = $_SESSION['NXPAGINA'];
	
		// calcola la prima e l'ultima pagina
		$prima = ($rpp * ($page-1));
		$ultima = ($rpp * $page);

		print('<ul>'."\n");

		/*print('<li>');
		print('<span class="id_notizia_cat_link">id</span>'."\n");
		print('<span class="id_notizia_cat_link">nome</span>'."\n");
		print('</li>');*/

		$count = 0;
		// parte il ciclo
		for($i=$prima; $i<$ultima; $i++) {
	
			if($i<sizeof($_SESSION['TROVATE'])) {

				$query  ="SELECT nome,status FROM $tablename WHERE id='".$_SESSION['TROVATE'][$i]."'";
				$rs = mssql_query($query, $conn);
				if(!$rs) error_message(mssql_error());
				
				$row = mssql_fetch_assoc($rs);
	
				$id = $_SESSION['TROVATE'][$i];
				$nome = stripslashes($row['nome']);
				$status = $row['status'];
	
				print('<li>');
				print('<span class="id_notizia_cat">'.$id.'</span>'."\n");
				print('<span class="notizia_cat">'."\n");
				print('<a href="principale.php?page='.$PHP_SELF.'&do=edit&id='.$id.'" target="_parent">');
				if(!$status) 
					print ('<span class="offline">'.$nome.' (offline)</span>'."\n");
				else 					
					if (substr($nome,0,3)=="www") {							
							$pR=pageRank($nome);
							//echo($pR);
							if ($pR==""){
								print("<img src=\"images\pr\pr0.gif\" alt=\"page rank 0/10\"> 0/10 - ");}
								else{
								print("<img src=\"images\pr\pr".$pR.".gif\" alt=\"page rank ".$pR."/10\"> ".$pR."/10 - ");							
							}
							}
					print($nome);
							
				print('</a>');
				print('</span>'."\n");
				
				// visualizza le icone dei permessi
				//icone_permessi($id, $id_menu);
	
				print('</li>'."\n");
				
				mssql_free_result($rs);
				$count++;
			}
			
		}
		
		print('</ul>'."\n");
		if($count > 30) {
			print('<div id="pulsantiera">');
			lettere_alfabetiche();
			GestionePagine($num_rows, $page, $tablename);
			ordina_per ($arr1, $arr2);
			print('</div>');
		}
	}

	else print ('<p class="none">Nessun dominio presente.</a>');

	print("\n".'</div>'."\n");

	// chiude il div centrale
	close_center();
}



// controllo d'accesso
$_SESSION['RICALCOLA'] = true;
if(isset($_SESSION['UTENTE'])) {

	// verifica i permessi
	if(in_array($id_permesso, $_SESSION['PERMESSI'])) {


		// recupera i dati di sessione dell'utente
		$objUser = $_SESSION['UTENTE'];

		if(!isset($do)) $do='';

		$back = "main.php";
	
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
					else update($_POST['id']);
			break;
	
			case "del":
				// verifica i permessi..
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 3))
					error_message("Permessi insufficienti per questa operazione!");
					else if($_POST['choice']=="si") del($_REQUEST['id'],'n');
			break;
			
			case "ordina":
				$_SESSION['ORDINAPER'] = $_POST['ordinaper'];
			break;
	
		}
	
		if($do!='logout'){
			html_header();
		}
	
		switch($do) {
	
			case "add":
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					error_message("Permessi insufficienti per questa operazione!");
				else add();
			break;
	
			case "edit":
				if(!$_SESSION['UTENTE']->controlla_permessi($id_permesso, 2))
					error_message("Permessi insufficienti per questa operazione!");
				else edit($_REQUEST['id']);
			break;
	
			case "confirm_del":
				confirm_del($id);
			break;
	
			case "logout":
				logout();
			break;
	
			case "show_list":
				/*if($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					$cmd = array(2);
				menu($cmd, $back);*/
				
				show_list();
			break;

			default:
				/*if($_SESSION['UTENTE']->controlla_permessi($id_permesso, 1))
					$cmd = array(2);
				menu($cmd, $back);*/
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