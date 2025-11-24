<?php

// definizione della classe
class Ricerca{

	/************************************************************************************************
	* funzione form_cerca_struttura																	 *
	*************************************************************************************************/
	function form_cerca_struttura(){

	$pagetitle = "ricerca struttura";
	$url = 'ricerca_struct.php';

	// apre il div centrale
	open_center();

	print('<form method="post" name="form0" action="principale.php" target="_parent">'."\n");
	//print('<div id="titolo_pag"><h1><small>'.$pagetitle.'</small></h1></div>');
	//print('<div class="comandi"><input type="submit" name="action" value="cerca struttura" /></div>'."\n");
	//print('</div>'."\n");
	?>
		<div class="barra_inf">
			<div class="comandi">
				<!-- <input type="submit" name="action" value="cerca struttura" /> -->
				<input type="image" src="images/search.gif" title="cerca struttura" />
				<input type="hidden" name="action" value="cerca struttura" />
			</div>
		</div>
	<?php
	print('<div class="blocco_centralcat">'."\n");
	print('<div class="titolo_par">Compilare i campi con i criteri di ricerca desiderati. Inserire il carattere speciale <strong>"#"</strong> (cancelletto) per effettuare una ricerca dei records con campi nulli.</div>'."\n");
	print('<input type="hidden" name="n_campi" value="9" />'."\n");
	print('<input type="hidden" name="url_ricerca" value="risultati_strutture.php" />'."\n");
	?>
	<span class="rigo_mask">
		<div class="testo_mask">annuario</div>
		<div class="campo_mask">
		<?php
		$conn = db_connect();
	
		// annuario
		$query = "select id,nome FROM annuario WHERE cancella='n' ORDER BY id";
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());
		print('<select class="scrittura" name="annuario" onChange="cambia_finestra(\''.$url.'\');">');
		
		print('<option value="0"');
		if(!$_SESSION['ANNUARIO'] OR $_REQUEST['annuario']==0) echo " selected";
		print('>TUTTI</option>');
	
		while($row = mssql_fetch_assoc($result)) {
	
			$id = $row['id'];
			$nome = $row['nome'];
			print('<option value="'.$id.'"');
			if(isset($_SESSION['ANNUARIO']))
				if($_SESSION['ANNUARIO']==$id AND !isset($_REQUEST['annuario'])) echo " selected";
			if(isset($_REQUEST['annuario']))
				if($_REQUEST['annuario']==$id) echo " selected";
			print('>'.$nome.'</option>'."\n");
	
		}
		print('</select>');
		?>
		</div>
	</span>

	<span class="rigo_mask">
		<div class="testo_mask">settore</div>
		<div class="campo_mask">
	<?php
	// settore
	if(isset($_REQUEST['annuario']))
			$query = "select id,nome FROM settori WHERE annuario='".$_REQUEST['annuario']."' AND cancella='n' ORDER BY nome";
	else $query = "select id,nome FROM settori WHERE annuario='".$_SESSION['ANNUARIO']."' AND cancella='n' ORDER BY nome";

	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	print('<select class="scrittura" name="settore">');
	print('<option value="0"');
	if(!$_SESSION['SETTORE'] OR $_REQUEST['settore']==0) echo " selected";
	print('>TUTTI</option>');

	
	while($row = mssql_fetch_assoc($result)) {

		$id = $row['id'];
		$nome = $row['nome'];
		print('<option value="'.$id.'"');
		if(isset($_SESSION['SETTORE']))
				if($_SESSION['SETTORE']==$id AND !isset($_REQUEST['settore'])) echo " selected";
		if(isset($_REQUEST['settore']))
			if($_REQUEST['settore']==$id) echo " selected";
		print('>'.$nome.'</option>'."\n");
	}
	print('</select>');
	?>
		</div>
	</span>

	<span class="rigo_mask">
		<div class="testo_mask">regione</div>
		<div class="campo_mask">
	<?php

	// regioni
	$query = "select id,nome FROM regioni WHERE cancella='n' ORDER BY nome";

	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());
	print('<select class="scrittura" name="regione" onChange="cambia_finestra(\''.$url.'\');">');
	
	print('<option value="0"');
	if(!$_SESSION['REGIONE'] OR $_REQUEST['regione']==0) echo " selected";
	print('>TUTTE</option>');

	while($row = mssql_fetch_assoc($result)) {

		$id = $row['id'];
		$nome = $row['nome'];
		print('<option value="'.$id.'"');
		if(isset($_SESSION['REGIONE']))
				if($_SESSION['REGIONE']==$id AND !isset($_REQUEST['regione'])) echo " selected";
		if(isset($_REQUEST['regione']))
			if($_REQUEST['regione']==$id) echo " selected";
		print('>'.$nome.'</option>'."\n");
	}
	print('</select>');
	?>
		</div>
	</span>

	<span class="rigo_mask">
		<div class="testo_mask">provincia</div>
		<div class="campo_mask">
	<?php

	// province
	if(isset($_REQUEST['regione']))
		$query = "select id,nome FROM province WHERE idregione='".$_REQUEST['regione']."' AND cancella='n' ORDER BY nome";
	else $query = "select id,nome FROM province WHERE cancella='n' ORDER BY nome";

	$result = mssql_query($query, $conn);
	if(!$result) error_message(mssql_error());

	print('<select class="scrittura" name="provincia">');
	
	print('<option value="0"');
	if(!$_SESSION['PROVINCIA'] OR $_REQUEST['provincia']==0) echo " selected";
	print('>TUTTE</option>');

	while($row = mssql_fetch_assoc($result)) {

		$id = $row['id'];
		$nome = $row['nome'];
		print('<option value="'.$id.'"');
		if(isset($_SESSION['PROVINCIA']))
			if($_SESSION['PROVINCIA']==$id) echo " selected";
		print('>'.$nome.'</option>'."\n");
	}
	print('</select>');
	?>
			</div>
		</span>

		<span class="rigo_mask">
			<div class="testo_mask">id struttura</div>
			<div class="campo_mask">
				<input type="text" class="lettura_piccolo" name="q_id" size="50" maxlength="255" />&nbsp;
				<input type="image" src="images/search_pic.gif" title="cerca struttura per id" /><br />
				<em>(separare gli id con la virgola per effettuare una ricerca multipla)</em>
			</div>
		</span>

		<span class="rigo_mask">
			<div class="testo_mask">nome struttura</div>
			<div class="campo_mask">
				<input type="text" class="scrittura" name="q_0" size="50" maxlength="255" />
				 <input type="radio" name="c_0" value="3" checked /> contiene
				 <input type="radio" name="c_0" value="1" /> uguale a
				 <input type="radio" name="c_0" value="2" /> comincia per
			</div>
		</span>

		<span class="rigo_mask">
			<div class="testo_mask"><strong>agg. multiplo</strong></div>
			<div class="campo_mask">
				<input type="checkbox" name="agg_mult" /> <em>(selezionare se si intende effettuare un aggiornamento multiplo)</em>
			</div>
		</span>

		<span class="rigo_mask">
			<div class="testo_mask">parentela</div>
			<div class="campo_mask">
				 <input type="radio" name="grado" value="3" /> bisnonno
				 <input type="radio" name="grado" value="2" /> nonno
				 <input type="radio" name="grado" value="1" /> padre
				 <input type="radio" name="grado" value="0" checked /> figlio
			</div>
		</span>

		<div class="linea"></div>
		
		<span class="rigo_mask">
			<div class="testo_mask">citt&agrave;</div>
			<div class="campo_mask">
				<input type="text" class="scrittura" name="q_1" size="50" maxlength="255" />
				 <input type="radio" name="c_1" value="3" checked /> contiene
				 <input type="radio" name="c_1" value="1" /> uguale a
				 <input type="radio" name="c_1" value="2" /> comincia per
			</div>
		</span>

		<span class="rigo_mask">
			<div class="testo_mask">indirizzo</div>
			<div class="campo_mask">
				<input type="text" class="scrittura" name="q_2" size="50" maxlength="255" />
				 <input type="radio" name="c_2" value="3" checked /> contiene
				 <input type="radio" name="c_2" value="1" /> uguale a
				 <input type="radio" name="c_2" value="2" /> comincia per
			</div>
		</span>
		
		<span class="rigo_mask">
			<div class="testo_mask">c.a.p.</div>
			<div class="campo_mask">
				<input type="text" class="scrittura" name="q_3" size="50" maxlength="5" />
				 <input type="radio" name="c_3" value="3" checked /> contiene
				 <input type="radio" name="c_3" value="1" /> uguale a
				 <input type="radio" name="c_3" value="2" /> comincia per
			</div>
		</span>
		
		<span class="rigo_mask">
			<div class="testo_mask">telefono</div>
			<div class="campo_mask">
				<input type="text" class="scrittura" name="q_4" size="50" maxlength="20" />
				 <input type="radio" name="c_4" value="3" checked /> contiene
				 <input type="radio" name="c_4" value="1" /> uguale a
				 <input type="radio" name="c_4" value="2" /> comincia per
			</div>
		</span>
		
		<span class="rigo_mask">
			<div class="testo_mask">fax</div>
			<div class="campo_mask">
				<input type="text" class="scrittura" name="q_5" size="50" maxlength="20" />
				 <input type="radio" name="c_5" value="3" checked /> contiene
				 <input type="radio" name="c_5" value="1" /> uguale a
				 <input type="radio" name="c_5" value="2" /> comincia per
			</div>
		</span>
		
		<span class="rigo_mask">
			<div class="testo_mask">centralino</div>
			<div class="campo_mask">
				<input type="text" class="scrittura" name="q_6" size="50" maxlength="20" />
				 <input type="radio" name="c_6" value="3" checked /> contiene
				 <input type="radio" name="c_6" value="1" /> uguale a
				 <input type="radio" name="c_6" value="2" /> comincia per
			</div>
		</span>
		
		<span class="rigo_mask">
			<div class="testo_mask">email</div>
			<div class="campo_mask">
				<input type="text" class="scrittura" name="q_7" size="50" maxlength="20" />
				 <input type="radio" name="c_7" value="3" checked /> contiene
				 <input type="radio" name="c_7" value="1" /> uguale a
				 <input type="radio" name="c_7" value="2" /> comincia per
			</div>
		</span>
		
		<span class="rigo_mask">
			<div class="testo_mask">http</div>
			<div class="campo_mask">
				<input type="text" class="scrittura" name="q_8" size="50" maxlength="20" />
				 <input type="radio" name="c_8" value="3" checked /> contiene
				 <input type="radio" name="c_8" value="1" /> uguale a
				 <input type="radio" name="c_8" value="2" /> comincia per
			</div>
		</span>
		
		</div>
		
		<div class="barra_inf">
			<div class="comandi">
				<input type="image" src="images/search.gif" title="cerca struttura" />
			</div>
		</div>
	</form>
	<?php
	close_center();
	}


	/************************************************************************************************
	* funzione form_cerca_manager																	 *
	*************************************************************************************************/
	function form_cerca_manager(){

	$pagetitle = "ricerca manager";
	$url = 'ricerca_manager.php';

	// apre il div centrale
	open_center();

	print('<form method="post" name="form0" action="principale.php" target="_parent">'."\n");
	//print('<div id="titolo_pag"><h1><small>'.$pagetitle.'</small></h1></div>');
	//print('<div class="comandi"><input type="submit" name="action" value="cerca struttura" /></div>'."\n");
	//print('</div>'."\n");
	?>
		<div class="barra_inf">
			<div class="comandi">
				<!-- <input type="submit" name="action" value="cerca manager" /> -->
				<input type="image" src="images/search.gif" title="cerca manager" />
				<input type="hidden" name="action" value="cerca manager" />
			</div>
		</div>
	<?php
	print('<div class="blocco_centralcat">'."\n");
	print('<div class="titolo_par">Compilare i campi con i criteri di ricerca desiderati. Inserire il carattere speciale <strong>"#"</strong> (cancelletto) per effettuare una ricerca dei records con campi nulli.</div>'."\n");
	print('<input type="hidden" name="n_campi" value="6" />'."\n");
	print('<input type="hidden" name="url_ricerca" value="managers.php?do=show_list&flag=1" />'."\n");
	?>
			<span class="rigo_mask">
				<div class="testo_mask">funzione</div>
				<div class="campo_mask">
				<select name="funzione" class="scrittura">
				<option value="0">QUALSIASI</option>
				<option value="-1">ALMENO UNA FUNZIONE</option>
				<?php 
				$conn = db_connect();
				$query = "SELECT id,nome FROM funzioni_manager WHERE cancella='n' ORDER BY nome";
				$rr = mssql_query($query, $conn);
				while($row = mssql_fetch_assoc($rr)){
			
					$idfunzione = $row['id'];
					$nomefunzione = $row['nome'];
									
					print ('<option value="'.$idfunzione.'"');
					print('>'.$nomefunzione.'</option>'."\n");
				}
				mssql_free_result($rr);
				?>
				</select>
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask">nome e cognome</div>
				<div class="campo_mask">
				<input type="text" class="scrittura" name="q_0" size="50" maxlength="255" />
				 <input type="radio" name="c_0" value="3" checked /> contiene
				 <input type="radio" name="c_0" value="1" /> uguale a
				 <input type="radio" name="c_0" value="2" /> comincia per
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">telefono</div>
				<div class="campo_mask">
				<input type="text" class="scrittura" name="q_1" size="50" maxlength="255" />
				 <input type="radio" name="c_1" value="3" checked /> contiene
				 <input type="radio" name="c_1" value="1" /> uguale a
				 <input type="radio" name="c_1" value="2" /> comincia per
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">fax</div>
				<div class="campo_mask">
				<input type="text" class="scrittura" name="q_2" size="50" maxlength="255" />
				 <input type="radio" name="c_2" value="3" checked /> contiene
				 <input type="radio" name="c_2" value="1" /> uguale a
				 <input type="radio" name="c_2" value="2" /> comincia per
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">email</div>
				<div class="campo_mask">
				<input type="text" class="scrittura" name="q_3" size="50" maxlength="255" />
				 <input type="radio" name="c_3" value="3" checked /> contiene
				 <input type="radio" name="c_3" value="1" /> uguale a
				 <input type="radio" name="c_3" value="2" /> comincia per
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">http</div>
				<div class="campo_mask">
				<input type="text" class="scrittura" name="q_4" size="50" maxlength="255" />
				 <input type="radio" name="c_4" value="3" checked /> contiene
				 <input type="radio" name="c_4" value="1" /> uguale a
				 <input type="radio" name="c_4" value="2" /> comincia per
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask">altri rif.</div>
				<div class="campo_mask">
				<textarea class="scrittura" name="q_5" rows="3" cols="38"></textarea>
				 <input type="radio" name="c_5" value="3" checked /> contiene
				 <input type="radio" name="c_5" value="1" /> uguale a
				 <input type="radio" name="c_5" value="2" /> comincia per
				</div>
			</span>
		</div>
		
		<div class="barra_inf">
			<div class="comandi">
				<!-- <input type="submit" name="action" value="cerca manager" /> -->
				<input type="image" src="images/search.gif" title="cerca manager" />
			</div>
		</div>
	</form>
	<?php
	close_center();
	}

	/************************************************************************************************
	* funzione form_cerca_prodotto																	 *
	*************************************************************************************************/
	function form_cerca_prodotto(){

	$pagetitle = "ricerca categoria merceologica";
	$url = 'ricerca_prodotto.php';

	// apre il div centrale
	open_center();

	print('<form method="post" name="form0" action="principale.php" target="_parent">'."\n");
	//print('<div id="titolo_pag"><h1><small>'.$pagetitle.'</small></h1></div>');
	//print('<div class="comandi"><input type="submit" name="action" value="cerca struttura" /></div>'."\n");
	//print('</div>'."\n");
	?>
		<div class="barra_inf">
			<div class="comandi">
				<!-- <input type="submit" name="action" value="cerca prodotto" /> -->
				<input type="image" src="images/search.gif" title="cerca categoria merceologica" />
				<input type="hidden" name="action" value="cerca categoria merceologica" />
			</div>
		</div>
	<?php
	print('<div class="blocco_centralcat">'."\n");
	print('<div class="titolo_par">Compilare i campi con i criteri di ricerca desiderati. Inserire il carattere speciale <strong>"#"</strong> (cancelletto) per effettuare una ricerca dei records con campi nulli.</div>'."\n");
	print('<div class="titolo_par"><em>NOTA: la ricerca verr  effettuata tenendo conto del <strong>primo filtro</strong> di sessione presenti nella barra in alto.</em></div>');
	print('<input type="hidden" name="n_campi" value="1" />'."\n");
	print('<input type="hidden" name="url_ricerca" value="prodotti.php?do=show_list&flag=1" />'."\n");
	?>
			<span class="rigo_mask">
				<div class="testo_mask">categoria merceologica</div>
				<div class="campo_mask">
				<input type="text" class="scrittura" name="q_0" size="50" maxlength="255" />
				 <input type="radio" name="c_0" value="3" checked /> contiene
				 <input type="radio" name="c_0" value="1" /> uguale a
				 <input type="radio" name="c_0" value="2" /> comincia per
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">&nbsp;</div>
				<div class="campo_mask">
				<input type="checkbox" name="ric_strut_prod" /> ricerca la categoria merceologica nelle strutture
				</div>
			</span>
			
		</div>
		
		<div class="barra_inf">
			<div class="comandi">
				<!-- <input type="submit" name="action" value="cerca prodotto" /> -->
				<input type="image" src="images/search.gif" title="cerca categoria merceologica" />
			</div>
		</div>
	</form>
	<?php
	close_center();
	}


	/************************************************************************************************
	* funzione form_cerca_altrodato																	 *
	*************************************************************************************************/
	function form_cerca_altrodato(){

	$pagetitle = "ricerca altro dato";
	$url = 'ricerca_altrodato.php';

	// apre il div centrale
	open_center();

	print('<form method="post" name="form0" action="strutture_altridati.php?do=show_list&flag=1" target="_self">'."\n");
	//print('<div id="titolo_pag"><h1><small>'.$pagetitle.'</small></h1></div>');
	//print('<div class="comandi"><input type="submit" name="action" value="cerca struttura" /></div>'."\n");
	//print('</div>'."\n");
	?>
		<div class="barra_inf">
			<div class="comandi">
				<!-- <input type="submit" name="action" value="cerca altro dato" /> -->
				<input type="image" src="images/search.gif" title="cerca altro dato" />
				<input type="hidden" name="action" value="cerca altro dato" />
			</div>
		</div>
	<?php
	print('<div class="blocco_centralcat">'."\n");
	print('<div class="titolo_par">Compilare i campi con i criteri di ricerca desiderati.</div>'."\n");
	print('<div class="titolo_par"><em>NOTA: la ricerca verr effettuata tenendo conto dei <strong>primi 2 filtri</strong> di sessione presenti nella barra in alto.</em></div>');
	print('<input type="hidden" name="n_campi" value="2" />'."\n");
	print('<input type="hidden" name="url_ricerca" value="strutture_altridati.php?do=show_list&flag=1" />'."\n");
	?>
			<span class="rigo_mask">
				<div class="testo_mask">tipo dato</div>
				<div class="campo_mask">
				<select name="tipodato" class="scrittura">
				<option value="0">QUALSIASI</option>
				<?php 
				$conn = db_connect();
				$query = "SELECT id,nome FROM tipidati WHERE cancella='n' ORDER BY nome";
				$rr = mssql_query($query, $conn);
				while($row = mssql_fetch_assoc($rr)){
			
					$idtipodato = $row['id'];
					$nometipodato = $row['nome'];
									
					print ('<option value="'.$idtipodato.'"');
					print('>'.$nometipodato.'</option>'."\n");
				}
				mssql_free_result($rr);
				?>
				</select>
				</div>
			</span>

			<span class="rigo_mask">
				<div class="testo_mask">dato</div>
				<div class="campo_mask">
				<input type="text" class="scrittura" name="q_0" size="50" maxlength="255" />
				 <input type="radio" name="c_0" value="3" checked /> contiene
				 <input type="radio" name="c_0" value="1" /> uguale a
				 <input type="radio" name="c_0" value="2" /> comincia per
				</div>
			</span>
			
			<span class="rigo_mask">
				<div class="testo_mask">cat. merc.</div>
				<div class="campo_mask">
				<select class="scrittura" name="catmerc" disabled />
				<option value="0">QUALSIASI</option>
				<?php

// se ho l'annuario
if(isset($_SESSION['ANNUARIO']) && $_SESSION['ANNUARIO']) {

	// recuper il catmerc del settore
	if(isset($_SESSION['SETTORE']) && $_SESSION['SETTORE']) {
	
		$query = "SELECT catmerc FROM settori WHERE id=".$_SESSION['SETTORE'];
		$rs = mssql_query($query, $conn);
		if($row = mssql_fetch_assoc($rs))
			if($row['catmerc']=='s') {
	
				$query_cat = "SELECT id,nome FROM prodotti WHERE cancella='n'";
				if(isset($_SESSION['ANNUARIO']) && $_SESSION['ANNUARIO'])
					$query_cat .= " AND annuario=".$_SESSION['ANNUARIO'];
				$query_cat .= " ORDER BY nome";
				echo $query_cat;
				$rc = mssql_query($query_cat, $conn);
				while($row_c = mssql_fetch_assoc($rc)) {
	
					print('<option value="'.$row_c['id'].'">'.$row_c['nome'].'</option>'."\n");
	
				}
				mssql_free_result($rc);
	
			}
		mssql_free_result($rs);
	
	} else {

		$query_cat = "SELECT id,nome FROM prodotti WHERE cancella='n'";
		$query_cat .= " AND annuario=".$_SESSION['ANNUARIO'];
		$query_cat .= " ORDER BY nome";
		echo $query_cat;
		$rc = mssql_query($query_cat, $conn);
		while($row_c = mssql_fetch_assoc($rc)) {

			print('<option value="'.$row_c['id'].'">'.$row_c['nome'].'</option>'."\n");

		}
		mssql_free_result($rc);
	
	}

} else {


	// recuper il catmerc del settore
	if(isset($_SESSION['SETTORE']) && $_SESSION['SETTORE']) {
	
		$query = "SELECT catmerc FROM settori WHERE id=".$_SESSION['SETTORE'];
		$rs = mssql_query($query, $conn);
		if($row = mssql_fetch_assoc($rs))
			if($row['catmerc']=='s') {
	
				$query_cat = "SELECT id,nome FROM prodotti WHERE cancella='n'";
				$query_cat .= " ORDER BY nome";
				echo $query_cat;
				$rc = mssql_query($query_cat, $conn);
				while($row_c = mssql_fetch_assoc($rc)) {
	
					print('<option value="'.$row_c['id'].'">'.$row_c['nome'].'</option>'."\n");
	
				}
				mssql_free_result($rc);
	
			}
		mssql_free_result($rs);
	
	} else {

		$query_cat = "SELECT id,nome FROM prodotti WHERE cancella='n'";
		$query_cat .= " ORDER BY nome";
		echo $query_cat;
		$rc = mssql_query($query_cat, $conn);
		while($row_c = mssql_fetch_assoc($rc)) {

			print('<option value="'.$row_c['id'].'">'.$row_c['nome'].'</option>'."\n");

		}
		mssql_free_result($rc);
	
	}

}

				?>
				</select>
				<br /><em>(in costruzione)</em>
				</div>
			</span>
			
		</div>
		
		<div class="barra_inf">
			<div class="comandi">
				<!-- <input type="submit" name="action" value="cerca altro dato" /> -->
				<input type="image" src="images/search.gif" title="cerca altro dato" />
			</div>
		</div>
	</form>
	<?php
	close_center();
	}

	/************************************************************************************************
	* funzione set_ricerca										*
	*************************************************************************************************/
	function set_ricerca($value){

		// setta la varibile di ricerca al valore settato
		//$_SESSION['RICERCA'] = $value;

		// azzera le trovate e le keywords
		unset($_SESSION['TROVATE']);
		//unset($_SESSION['KEYWORDS']);
	}




	/************************************************************************************************
	* funzione cerca_struttura																		*
	*************************************************************************************************/
	function cerca_struttura() {
	
	
		global $PHP_SELF;
		
		$tablename = 'strutture';
		
		// recupera il n. dei campi postati
		$n_campi = 9;
		$CAMPI = array ('nome','citta','indirizzo','cap','tel','fax','centralino','email','http');
		
		$conn = db_connect();
		
		// se si sta cercando per id...
		if(isset($_POST['q_id']) && (trim($_POST['q_id'])!='')) {
		
			$id_strut = $_POST['q_id'];

			$query = "	SELECT  id
						FROM $tablename 
						WHERE (id IN (".$id_strut.") ) AND (cancella='n')";
	
		} else {

			// recupera il grado di parentela
			$grado = $_POST['grado'];
			
			if($grado) {
			
				$query = "	SELECT  $tablename.id
							FROM $tablename LEFT OUTER JOIN
							dbo.province ON $tablename.idprovincia = dbo.province.id LEFT OUTER JOIN
							dbo.settori ON $tablename.idsettore = dbo.settori.id LEFT OUTER JOIN
							dbo.gerarchia ON $tablename.id = dbo.gerarchia.idparente FULL OUTER JOIN
							dbo.regioni ON dbo.province.idregione = dbo.regioni.id
							WHERE ($tablename.cancella='n') AND (dbo.gerarchia.livello = ".$grado.")";
	
			} else {
			
				// prepara la query
				$query = "SELECT $tablename.id
							FROM $tablename
							INNER JOIN	settori ON $tablename.idsettore = settori.id
							INNER JOIN	province ON $tablename.idprovincia = province.id 
							INNER JOIN	regioni ON province.idregione = regioni.id ";
				$query .= " WHERE ($tablename.cancella='n')";
			}
		
			if($_POST['annuario']) {
				$_SESSION['ANNUARIO'] = $_POST['annuario'];
				$query .= " AND ($tablename.idannuario='".$_POST['annuario']."')";
			}
			if($_POST['settore'])  {
				$_SESSION['SETTORE'] = $_POST['settore'];
				$query .= " AND (settori.id='".$_POST['settore']."')";
			}
			if($_POST['regione'])  {
				$_SESSION['REGIONE'] = $_POST['regione'];
				$query .= " AND (province.idregione='".$_POST['regione']."')";
			}
			if($_POST['provincia']) {
				$_SESSION['PROVINCIA'] = $_POST['provincia'];
				$query .= " AND (province.id='".$_POST['provincia']."')";
			}
			
			
			
	
			for($i=0; $i<$n_campi; $i++) {
	
				if($_POST['q_'.$i]=='#') {
						$query .= " AND ($tablename.".$CAMPI[$i]." = NULL)";
				}
			
				else if($_POST['q_'.$i]!='') {
						$var=str_replace("'","''",$_POST['q_'.$i]);
						$var=str_replace('"','chr(34)',$var);
	
						if($_POST['c_'.$i]==1) $query .= " AND ($tablename.".$CAMPI[$i]."='".$_POST['q_'.$i]."')";
						else if($_POST['c_'.$i]==2) $query .= " AND ($tablename.".$CAMPI[$i]." LIKE 4\"".$_POST['q_'.$i]."%\")";
						else if($_POST['c_'.$i]==3) $query .= " AND ($tablename.".$CAMPI[$i]." LIKE \"%".$_POST['q_'.$i]."%\")";
						else $query .= " AND ($tablename.".$CAMPI[$i]."='".$_POST['q_'.$i]."')";
				}
			}


		


			// setta la variabile di sessione
			$_SESSION['QUERY'] = $query;
	
			$query .= " ORDER BY $tablename.id";
	
			//echo $query;
			
			//echo "trovate ".mssql_num_rows($result);


		// fine else
		}

		$result = mssql_query($query, $conn);
		//if(!$result) error_message(mssql_error());

		// azzera la variabile di sessione
		$_SESSION['TROVATE'] = array();
		$_SESSION['RICALCOLA'] = false;

		// inserisce gli id nell'array di sessione
		while( ($row = mssql_fetch_row($result))) {
		
			$id_strut = $row[0];
			array_push($_SESSION['TROVATE'], $id_strut);

		} // fine while

		// prepara la risposta
		if(sizeof($_SESSION['TROVATE'])) {
			$risposta = "Sono state trovate <strong>".sizeof($_SESSION['TROVATE'])."</strong>";
			$_SESSION['CONTENENTI'] = " contenenti la parola <strong>".$_POST['q_0']."</strong> ";
		}
		else $risposta = '<em>La ricerca non ha prodotto alcun risultato.</em>';
	
		return $risposta;

	}
	/************************************************************************************************
	* end funzione cerca										*
	*************************************************************************************************/



	/************************************************************************************************
	* funzione cerca_manager																		*
	*************************************************************************************************/
	function cerca_manager() {

		global $PHP_SELF;
		
		$tablename = 'strutture_managers';
		
		// recupera il n. dei campi postati
		$n_campi = 6;
		$CAMPI = array ('nome','tel','fax','email','http','altririf');
		
		// prepara la query
		$query = "SELECT $tablename.id
					FROM $tablename";
		$query .= " WHERE ($tablename.cancella='n' ";

		for($i=0; $i<$n_campi; $i++) {
		
			if($_POST['q_'.$i]=='#') {
					$query .= " AND $tablename.".$CAMPI[$i]." = NULL ";
			}
		
			else if($_POST['q_'.$i]!='') {

					if($_POST['funzione']) $query .= " AND $tablename.idfunzione='".$_POST['funzione']."' ";
					if($_POST['c_'.$i]==1) $query .= " AND $tablename.".$CAMPI[$i]."='".$_POST['q_'.$i]."' ";
					else if($_POST['c_'.$i]==2) $query .= "AND $tablename.".$CAMPI[$i]." LIKE \"".$_POST['q_'.$i]."%\" ";
					else if($_POST['c_'.$i]==3) $query .= "AND $tablename.".$CAMPI[$i]." LIKE \"%".$_POST['q_'.$i]."%\" ";
					else $query .= " AND $tablename.".$CAMPI[$i]."='".$_POST['q_'.$i]."' ";
			}
		}
		
		$query .= ") ORDER BY $tablename.id";

		// azzera la variabile di sessione
		$_SESSION['TROVATE'] = array();
		$_SESSION['RICALCOLA'] = false;
	
		$conn = db_connect();
	
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());

		//echo "trovate ".mssql_num_rows($result);

		while( ($row = mssql_fetch_row($result))) {

			// lo inserisce nell'array
			array_push($_SESSION['TROVATE'], $row[0]);

		} // fine while

		// prepara la risposta
		if(sizeof($_SESSION['TROVATE'])) {
			$risposta = "Managers trovati: <strong>".sizeof($_SESSION['TROVATE'])."</strong>.";
		}
		else $risposta = '<em>La ricerca non ha prodotto alcun risultato.</em>';

		return $risposta;

	}
	/************************************************************************************************
	* end funzione cerca										*
	*************************************************************************************************/


	/************************************************************************************************
	* funzione cerca_prodotto																		*
	*************************************************************************************************/
	function cerca_prodotto() {

		global $PHP_SELF;
		
		$tablename = 'prodotti';

		// recupera il n. dei campi postati
		$n_campi = 1;
		$CAMPI = array ('nome');
		
		// prepara la query
		$query = "SELECT $tablename.id
					FROM $tablename";
		$query .= " WHERE ($tablename.cancella='n' ";

		for($i=0; $i<$n_campi; $i++) {
		
			if($_POST['q_'.$i]=='#') {
					$query .= " AND $tablename.".$CAMPI[$i]." = NULL";
			}
		
			else if($_POST['q_'.$i]!='') {

					if($_POST['c_'.$i]==1) $query .= " AND $tablename.".$CAMPI[$i]."='".$_POST['q_'.$i]."' ";
					else if($_POST['c_'.$i]==2) $query .= "AND $tablename.".$CAMPI[$i]." LIKE \"".$_POST['q_'.$i]."%\" ";
					else if($_POST['c_'.$i]==3) $query .= "AND $tablename.".$CAMPI[$i]." LIKE \"%".$_POST['q_'.$i]."%\" ";
					else $query .= " AND $tablename.".$CAMPI[$i]."='".$_POST['q_'.$i]."' ";
			}
		}
		
		$query .= ") ORDER BY $tablename.id";

		//echo $query;
		// azzera la variabile di sessione
		$_SESSION['TROVATE'] = array();
		$_SESSION['RICALCOLA'] = false;
	
		$conn = db_connect();
	
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());

		//echo "trovate ".mssql_num_rows($result);
		//echo $query;

		while( ($row = mssql_fetch_row($result))) {

			// lo inserisce nell'array
			array_push($_SESSION['TROVATE'], $row[0]);

		} // fine while

		// prepara la risposta
		if(sizeof($_SESSION['TROVATE'])) {
			$risposta = "Prodotti trovati: <strong>".sizeof($_SESSION['TROVATE'])."</strong>.";
		}
		else $risposta = '<em>La ricerca non ha prodotto alcun risultato.</em>';

		return $risposta;

	}
	/************************************************************************************************
	* end funzione cerca										*
	*************************************************************************************************/


	/************************************************************************************************
	* funzione cerca_altrodato																	*
	*************************************************************************************************/
	function cerca_altrodato() {

		global $PHP_SELF;
		
		$tablename = 'strutture_altridati';
		
		// recupera il n. dei campi postati
		$n_campi = 2;
		$CAMPI = array ('dato');
		
		// prepara la query
		$query = "SELECT $tablename.id
					FROM $tablename";
		$query .= " WHERE ($tablename.cancella='n' ";

		for($i=0; $i<$n_campi; $i++) {
		
			if($_POST['q_'.$i]=='#') {
					$query .= " AND $tablename.".$CAMPI[$i]." = NULL";
			}
		
			else if($_POST['q_'.$i]!='') {

					if($_POST['tipodato']) $query .= " AND $tablename.idtipodato='".$_POST['tipodato']."' ";
					if($_POST['c_'.$i]==1) $query .= " AND $tablename.".$CAMPI[$i]." LIKE '".$_POST['q_'.$i]."' ";
					else if($_POST['c_'.$i]==2) $query .= "AND $tablename.".$CAMPI[$i]." LIKE \"".$_POST['q_'.$i]."%\" ";
					else if($_POST['c_'.$i]==3) $query .= "AND $tablename.".$CAMPI[$i]." LIKE \"%".$_POST['q_'.$i]."%\" ";
					else $query .= " AND $tablename.".$CAMPI[$i]."='".$_POST['q_'.$i]."' ";
			}
		}
		
		$query .= ") ORDER BY $tablename.id";

		echo $query;
		$_SESSION['QUERY2'] =$query;

		// azzera la variabile di sessione
		$_SESSION['TROVATE'] = array();
		$_SESSION['RICALCOLA'] = false;
	
		$conn = db_connect();
	
		$result = mssql_query($query, $conn);
		if(!$result) error_message(mssql_error());

		while( ($row = mssql_fetch_row($result))) {

			// lo inserisce nell'array
			array_push($_SESSION['TROVATE'], $row[0]);

		} // fine while

		// prepara la risposta
		if(sizeof($_SESSION['TROVATE'])) {
			$risposta = "Altri dati trovati: <strong>".sizeof($_SESSION['TROVATE'])."</strong>.";
		}
		else $risposta = '<em>La ricerca non ha prodotto alcun risultato.</em>';

		return $risposta;

	}
	/************************************************************************************************
	* end funzione cerca										*
	*************************************************************************************************/
}




/****************************************************************
* funzione q_struttura()									*
*****************************************************************/
function q_struttura(){

	// effettua la ricerca
	$objRicerca = new Ricerca();
	$risposta = $objRicerca->cerca_struttura();
	
}

/****************************************************************
* funzione q_manager()									*
*****************************************************************/
function q_manager(){

	// effettua la ricerca
	$objRicerca = new Ricerca();
	$risposta = $objRicerca->cerca_manager();
	
}

/****************************************************************
* funzione q_prodotto()									*
*****************************************************************/
function q_prodotto(){

	// effettua la ricerca
	$objRicerca = new Ricerca();
	$risposta = $objRicerca->cerca_prodotto();
	
}

/****************************************************************
* funzione q_altrodato()									*
*****************************************************************/
function q_altrodato(){

	// effettua la ricerca
	$objRicerca = new Ricerca();
	$risposta = $objRicerca->cerca_altrodato();
	
}


?>
