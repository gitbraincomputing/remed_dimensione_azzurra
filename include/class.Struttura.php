<?php
/****************************************************************/
/* Universit�degli Studi di Salerno							*/
/* http://www.unisa.it											*/
/****************************************************************/

// definizione della classe
class Struttura{

	function get($id){

		// include il file di sessione
		include_once('session.inc.php');
		include_once('dbengine.inc.php');
		//include_once('errors.inc.php');

		$tablename = "strutture";

		// inizializzazione delle proprietà
		$this->_properties = array();
		$this->_properties['id'] = $id;
		$this->_properties['bisnonno'] = null;
		$this->_properties['nonno'] = null;
		$this->_properties['padre'] = null;
		$this->_properties['nome_bisnonno'] = null;
		$this->_properties['nome_nonno'] = null;
		$this->_properties['nome_padre'] = null;
		$this->_properties['bisnonno_old'] = null;
		$this->_properties['nonno_old'] = null;
		$this->_properties['padre_old'] = null;
		$this->_properties['nome'] = null;
		$this->_properties['idannuario'] = null;
		$this->_properties['idsettore'] = null;
		$this->_properties['citta'] = null;
		$this->_properties['idprovincia'] = null;
		$this->_properties['sede'] = null;
		$this->_properties['indirizzo'] = null;
		$this->_properties['cap'] = null;
		$this->_properties['tel'] = null;
		$this->_properties['fax'] = null;
		$this->_properties['centralino'] = null;
		$this->_properties['email'] = null;
		$this->_properties['http'] = null;
		$this->_properties['agg'] = null;
		$this->_properties['note'] = null;
		$this->_properties['cliente'] = null;
		$this->_properties['tipo_intervento'] = null;
		$this->_properties['evidenziato'] = null;
		$this->_properties['notecliente'] = null;
		$this->_properties['corretto'] = null;
		$this->_properties['pw'] = null;
		$this->_properties['status'] = null;
		$this->_properties['partita_iva'] = null;
		
		$this->_properties['descrizione'] = null;
		$this->_properties['meta_titolo'] = null;
		$this->_properties['meta_descrizione'] = null;
		$this->_properties['meta_keyword'] = null;
		$this->_properties['latitudine'] = null;
		$this->_properties['longitudine'] = null;
		
		$this->_properties['fat_ragsoc'] = null;
		$this->_properties['fat_indirizzo'] = null;
		$this->_properties['fat_cap'] = null;
		$this->_properties['fat_citta'] = null;
		$this->_properties['fat_provincia'] = null;		
		

		// connessione al db
		if(!($conn = db_connect()))
			error_message("errore nella connessione al db");

		$query = "SELECT * FROM $tablename WHERE id='$id'";

		if(!($rs = mssql_query($query,$conn)))
			error_message(mssql_error());

		if($row = mssql_fetch_assoc($rs)){

			// aggiorna le proprietà
			$this->_properties['nome'] = stripslashes($row['nome']);
			
			$this->_properties['idannuario'] = $row['idannuario'];
			$this->_properties['idsettore'] = $row['idsettore'];
			$this->_properties['citta'] = stripslashes($row['citta']);
			$this->_properties['idprovincia'] = $row['idprovincia'];
			$this->_properties['sede'] = $row['sede'];
			$this->_properties['indirizzo'] = stripslashes($row['indirizzo']);
			$this->_properties['cap'] = $row['cap'];
			$this->_properties['tel'] = $row['tel'];
			$this->_properties['fax'] = $row['fax'];
			$this->_properties['centralino'] = $row['centralino'];
			$this->_properties['email'] = stripslashes($row['email']);
			$this->_properties['http'] = stripslashes($row['http']);
			$this->_properties['agg'] = $row['agg'];
			$this->_properties['note'] = stripslashes($row['note']);
			$this->_properties['cliente'] = $row['cliente'];
			$this->_properties['tipo_intervento'] = $row['tipo_intervento'];
			$this->_properties['evidenziato'] = $row['evidenziato'];
			$this->_properties['notecliente'] = stripslashes($row['notecliente']);
			$this->_properties['corretto'] = stripslashes($row['corretto']);
			$this->_properties['pw'] = $row['pw'];
			$this->_properties['status'] = $row['status'];
			$this->_properties['partita_iva'] = $row['partita_iva'];
			$this->_properties['descrizione'] = $row['descrizione'];
			
			
			$this->_properties['meta_titolo'] = stripslashes($row['meta_titolo']);
			$this->_properties['meta_descrizione'] = stripslashes($row['meta_descrizione']);
			$this->_properties['meta_keyword'] = $row['meta_keyword'];
			$this->_properties['latitudine'] = $row['latitudine'];
			$this->_properties['longitudine'] = $row['longitudine'];
			
			$this->_properties['fat_ragsoc'] = stripslashes($row['fat_ragsoc']);
			$this->_properties['fat_indirizzo'] = stripslashes($row['fat_indirizzo']);
			$this->_properties['fat_cap'] = $row['fat_cap'];
			$this->_properties['fat_citta'] = $row['fat_citta'];
			$this->_properties['fat_provincia'] = $row['fat_provincia'];

		}
		
		// recupera il bisnonno
		$query = "SELECT strutture.nome AS nomestruttura, gerarchia.id, gerarchia.idparente AS idbisnonno
				FROM strutture INNER JOIN gerarchia 
				ON strutture.id = gerarchia.idparente
				WHERE (idstruttura='$id' AND livello='3')";
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		if($row = mssql_fetch_assoc($rs)) {
			$this->_properties['bisnonno'] = $row['idbisnonno'];
			$this->_properties['nome_bisnonno'] = stripslashes($row['nomestruttura']);
		}
		
		// recupera il nonno
		$query = "SELECT strutture.nome AS nomestruttura, gerarchia.id, gerarchia.idparente AS idnonno
				FROM strutture INNER JOIN gerarchia 
				ON strutture.id = gerarchia.idparente
				WHERE (idstruttura='$id' AND livello='2')";
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		if($row = mssql_fetch_assoc($rs)) {
			$this->_properties['nonno'] = $row['idnonno'];
			$this->_properties['nome_nonno'] = stripslashes($row['nomestruttura']);
		}
	
		// recupera il padre
		$query = "SELECT strutture.nome AS nomestruttura, gerarchia.id, gerarchia.idparente AS idpadre
				FROM strutture INNER JOIN gerarchia 
				ON strutture.id = gerarchia.idparente
				WHERE (idstruttura='$id' AND livello='1')";
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		if($row = mssql_fetch_assoc($rs)) {
			$this->_properties['padre'] = $row['idpadre'];
			$this->_properties['nome_padre'] = stripslashes($row['nomestruttura']);
		}
		
		// associa l'oggetto corrente alla variabile di sessione
		$_SESSION['STRUTTURA'] = $this;

		// rilascia la connessione al db
		mssql_free_result($rs);
	}


	// funzioni di get
	function get_id(){

		return $this->_properties['id'];
	}
	function get_bisnonno(){

		return $this->_properties['bisnonno'];
	}
	function get_nonno(){

		return $this->_properties['nonno'];
	}
	function get_padre(){

		return $this->_properties['padre'];
	}
	function get_nome_bisnonno(){

		return $this->_properties['nome_bisnonno'];
	}
	function get_nome_nonno(){

		return $this->_properties['nome_nonno'];
	}
	function get_nome_padre(){

		return $this->_properties['nome_padre'];
	}
	function get_bisnonno_old(){

		return $this->_properties['bisnonno_old'];
	}
	function get_nonno_old(){

		return $this->_properties['nonno_old'];
	}
	function get_padre_old(){

		return $this->_properties['padre_old'];
	}
	function get_nome(){

		return $this->_properties['nome'];
	}
	function get_idannuario(){

		return $this->_properties['idannuario'];
	}
	function get_idsettore(){

		return $this->_properties['idsettore'];
	}
	function get_citta(){

		return $this->_properties['citta'];
	}
	function get_idprovincia(){

		return $this->_properties['idprovincia'];
	}
	
	function get_idregione(){
	
		$conn = db_connect();
		
		$query = "SELECT idregione FROM province WHERE id='".$this->_properties['idprovincia']."'";
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		
		if($row = mssql_fetch_assoc($rs)) {
			
				return $row['idregione'];
			}

		
	}
	
	function get_provincia(){
		$conn = db_connect();
		
		$query = "SELECT sigla FROM province WHERE id='".$this->_properties['idprovincia']."'";
		$rs = mssql_query($query, $conn);
		if(!$rs) error_message(mssql_error());
		if($row = mssql_fetch_assoc($rs)) {
			return $row['sigla'];
		}
	}
	function get_sede(){

		return $this->_properties['sede'];
	}
	function get_indirizzo(){

		return $this->_properties['indirizzo'];
	}
	function get_cap(){

		return $this->_properties['cap'];
	}
	function get_tel(){

		return $this->_properties['tel'];
	}
	function get_fax(){

		return $this->_properties['fax'];
	}
	function get_centralino(){

		return $this->_properties['centralino'];
	}
	function get_email(){

		return $this->_properties['email'];
	}
	function get_http(){

		return $this->_properties['http'];
	}
	function get_agg(){

		return $this->_properties['agg'];
	}
	function get_note(){

		return $this->_properties['note'];
	}
	function get_cliente(){

		return $this->_properties['cliente'];
	}
	function get_tipo_intervento(){

		return $this->_properties['tipo_intervento'];
	}
	function get_evidenziato(){

		return $this->_properties['evidenziato'];
	}
	function get_note_cliente(){

		return $this->_properties['notecliente'];
	}
	function get_corretto(){

		return $this->_properties['corretto'];
	}
	function get_pw(){

		return $this->_properties['pw'];
	}
	function get_status(){

		return $this->_properties['status'];
	}
	function get_partita_iva(){

		return $this->_properties['partita_iva'];
	}
	
	function get_meta_titolo(){

		return $this->_properties['meta_titolo'];
	}
	function get_descrizione(){

		return $this->_properties['descrizione'];
	}
	
	function get_meta_descrizione(){

		return $this->_properties['meta_descrizione'];
	}
	function get_meta_keyword(){

		return $this->_properties['meta_keyword'];
	}
	function get_latitudine(){

		return $this->_properties['latitudine'];
	}
	function get_longitudine(){

		return $this->_properties['longitudine'];
	}
	
	function get_fat_ragsoc(){

		return $this->_properties['fat_ragsoc'];
	}
	function get_fat_indirizzo(){

		return $this->_properties['fat_indirizzo'];
	}
	function get_fat_cap(){

		return $this->_properties['fat_cap'];
	}
	function get_fat_citta(){

		return $this->_properties['fat_citta'];
	}
	function get_fat_provincia(){

		return $this->_properties['fat_provincia'];
	}


	// funzioni di set
	/*function set_id($value){

		if($this->_properties['id'] = $value)
			return true;
		else return false;
	}*/
	function set_bisnonno($value,$nome){

		$this->_properties['bisnonno'] = $value;
		$this->_properties['nome_bisnonno'] = $nome;
		// recupera il nome del bisnonno
		/*$conn = db_connect();
		$query = "SELECT nome FROM $tablename WHERE id='".$value."'";
		if(!($rs = mssql_query($query,$conn)))
			error_message(mssql_error());
		if($row = mssql_fetch_row($rs))
			$this->_properties['nome_bisnonno'] = $row[0];
		*/
		return true;
	}
	function set_nonno($value,$nome){

		$this->_properties['nonno'] = $value;
		$this->_properties['nome_nonno'] = $nome;
		// recupera il nome del nonno
		/*$conn = db_connect();
		$query = "SELECT nome FROM $tablename WHERE id='".$value."'";
		if(!($rs = mssql_query($query,$conn)))
			error_message(mssql_error());
		if($row = mssql_fetch_row($rs))
			$this->_properties['nome_nonno'] = $row[0];
		*/
		return true;
	}
	function set_padre($value,$nome){

		$this->_properties['padre'] = $value;
		$this->_properties['nome_padre'] = $nome;
		// recupera il nome del padre
		/*$conn = db_connect();
		$query = "SELECT nome FROM $tablename WHERE id='".$value."'";
		if(!($rs = mssql_query($query,$conn)))
			error_message(mssql_error());
		if($row = mssql_fetch_row($rs))
			$this->_properties['nome_padre'] = $row[0];
		*/
		return true;
	}
	function set_bisnonno_old($value){

		$this->_properties['bisnonno_old'] = $value;
		return true;
	}
	function set_nonno_old($value){

		$this->_properties['nonno_old'] = $value;
		return true;
	}
	function set_padre_old($value){

		$this->_properties['padre_old'] = $value;
		return true;
	}

	function set_nome($value){

		if($this->_properties['nome'] = $value)
			return true;
		else return false;
	}
	function set_idannuario($value){

		if($this->_properties['idannuario'] = $value)
			return true;
		else return false;
	}
	function set_idsettore($value){

		if($this->_properties['idsettore'] = $value)
			return true;
		else return false;
	}
	function set_citta($value){

		if($this->_properties['citta'] = $value)
			return true;
		else return false;
	}
	function set_idprovincia($value){

		if($this->_properties['idprovincia'] = $value)
			return true;
		else return false;
	}
	function set_sede($value){

		if($this->_properties['sede'] = $value)
			return true;
		else return false;
	}
	function set_indirizzo($value){

		if($this->_properties['indirizzo'] = $value)
			return true;
		else return false;
	}
	function set_cap($value){

		if($this->_properties['cap'] = $value)
			return true;
		else return false;
	}
	function set_tel($value){

		if($this->_properties['tel'] = $value)
			return true;
		else return false;
	}
	function set_fax($value){

		if($this->_properties['fax'] = $value)
			return true;
		else return false;
	}
	function set_centralino($value){

		if($this->_properties['centralino'] = $value)
			return true;
		else return false;
	}
	function set_email($value){

		if($this->_properties['email'] = $value)
			return true;
		else return false;
	}
	function set_http($value){

		if($this->_properties['http'] = $value)
			return true;
		else return false;
	}
	function set_agg($value){

		if($this->_properties['agg'] = $value)
			return true;
		else return false;
	}
	function set_note($value){

		if($this->_properties['note'] = $value)
			return true;
		else return false;
	}
	function set_cliente($value){

		if($this->_properties['cliente'] = $value)
			return true;
		else return false;
	}
	function set_tipo_intervento($value){

		if($this->_properties['tipo_intervento'] = $value)
			return true;
		else return false;
	}
	function set_evidenziato($value){

		if($this->_properties['evidenziato'] = $value)
			return true;
		else return false;
	}
	function set_note_cliente($value){

		if($this->_properties['notecliente'] = $value)
			return true;
		else return false;
	}
	function set_corretto($value){

		if($this->_properties['corretto'] = $value)
			return true;
		else return false;
	}
	function set_pw($value){

		if($this->_properties['pw'] = $value)
			return true;
		else return false;
	}
	function set_status($value){

		if($this->_properties['status'] = $value)
			return true;
		else return false;
	}
	function set_partita_iva($value){

		if($this->_properties['partita_iva'] = $value)
			return true;
		else return false;
	}
	
	function set_meta_titolo($value){

		if($this->_properties['meta_titolo'] = $value)
			return true;
		else return false;
	}
	function set_descrizione($value){

		if($this->_properties['descrizione'] = $value)
			return true;
		else return false;
	}
	
	function set_meta_descrizione($value){

		if($this->_properties['meta_descrizione'] = $value)
			return true;
		else return false;
	}
	function set_meta_keyword($value){

		if($this->_properties['meta_keyword'] = $value)
			return true;
		else return false;
	}
	function set_latitudine($value){

		if($this->_properties['latitudine'] = $value)
			return true;
		else return false;
	}
	function set_longitudine($value){

		if($this->_properties['longitudine'] = $value)
			return true;
		else return false;
	}
	
	function set_fat_ragsoc($value){

		if($this->_properties['fat_ragsoc'] = $value)
			return true;
		else return false;
	}
	function set_fat_indirizzo($value){

		if($this->_properties['fat_indirizzo'] = $value)
			return true;
		else return false;
	}
	function set_fat_cap($value){

		if($this->_properties['fat_cap'] = $value)
			return true;
		else return false;
	}
	function set_fat_citta($value){

		if($this->_properties['fat_citta'] = $value)
			return true;
		else return false;
	}
	function set_fat_provincia($value){

		if($this->_properties['fat_provincia'] = $value)
			return true;
		else return false;
	}


}

?>
