<?php



function formatta_data_user($data){
	
	
date_default_timezone_set('Europe/Rome');

$datetime = date_create($data);

$data=date_format($datetime, 'd/m/Y');	

return $data;
exit();
}

// definizione della classe User
class User{

	function create($userID){

		// include il file di sessione
		include_once('session.inc.php');
		include_once('dbengine.inc.php');
		//include_once('errors.inc.php');

		$tablename = 'operatori';

		// inizializzazione delle propriet�
		$this->_properties = array();
		$this->_properties['uid'] = $userID;
		$this->_properties['gid'] = null;
		$this->_properties['username'] = null;
 		$this->_properties['permessi'] = null;
		$this->_properties['validazione'] = null;
		$this->_properties['errmsg'] = null;
		$this->_properties['status'] = null;
		$this->_properties['tipoaccesso'] = null;
		$this->_properties['is_ds'] = null;
		$this->_properties['is_dt'] = null;
		$this->_properties['is_gc'] = null;
		$this->_properties['nome'] = null;
		$this->_properties['nome_gruppo'] = null;
		$this->_properties['privacy_cartella'] = null;

		// connessione al db
		if(!($conn = db_connect()))
			error_message("errore nella connessione al db");

		$sql = "SELECT * FROM $tablename WHERE uid='".$userID."'";

		if(!($rs = mssql_query($sql,$conn)))
			error_message("errore nella select");

		if(!($row = mssql_fetch_assoc($rs)))
			error_message(mssql_error());

		// aggiorna le propriet
		$this->_properties['username'] = $row['username'];
		$this->_properties['gid'] = $row['gid'];
		$this->_properties['is_root'] = $row['is_root'];
		$this->_properties['validazione'] = $row['validazione'];
		$this->_properties['status'] = $row['status'];
		$this->_properties['is_ds'] = $row['dir_sanitario'];
		$this->_properties['is_dt'] = $row['dir_tecnico'];
		$this->_properties['is_gc'] = $row['gestore_cartella'];
		$this->_properties['privacy_cartella'] = $row['privacy_cartella'];
		$this->_properties['nome'] = stripslashes($row['nome']);
		
		// rilascia la connessione al db
		mssql_free_result($rs);
		
		if ($this->_properties['is_ds']=='y')
		{
		
			$query = "select CONVERT(CHAR(10), data_inizio, 103) as data_inizio,CONVERT(CHAR(10), data_fine, 103)  as data_fine from operatori_direttore where tipo=1 and uid=$userID";
			$result = mssql_query($query, $conn);
			if(!$result) error_message("non riesco a recuperare i permessi");
			$row = mssql_fetch_assoc($result);
			$this->_properties['data_inizio_ds'] = $row['data_inizio'];
			$this->_properties['data_fine_ds'] = $row['data_fine'];
			
			mssql_free_result($result);
		}
		
		if ($this->_properties['is_dt']=='y')
		{
		
			$query = "select data_inizio,data_fine from operatori_direttore where tipo=2 and uid=$userID";
			$result = mssql_query($query, $conn);
			if(!$result) error_message("non riesco a recuperare i permessi");
			$row = mssql_fetch_assoc($result);
			$this->_properties['data_inizio_dt'] = formatta_data_user($row['data_inizio']);
			$this->_properties['data_fine_dt'] = formatta_data_user($row['data_fine']);
			
			mssql_free_result($result);
		}
		

		// recupera la stringa dei permessi
		$query = "select tipo,nome from gruppi where gid='".$this->_properties['gid']."'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message("non riesco a recuperare i permessi");
		$row = mssql_fetch_assoc($result);		
		$this->_properties['tipoaccesso']=$row['tipo'];
		$this->_properties['nome_gruppo']=$row['nome'];
		
		mssql_free_result($result);
		
		$db = odbc_connect(DSN, DB_USER, DB_PASSWORD);
		$res  = odbc_exec ($db, "SET TEXTSIZE ".(2 * MAX));
		$res = odbc_exec ($db, "SELECT permessi FROM gruppi WHERE gid='".$this->_properties['gid']."'");
		odbc_longreadlen ($res, MAX);
		$permessi = odbc_result ($res, "permessi");		
		odbc_close($db);
		$this->_properties['permessi'] = $permessi;
		
		//$array_stringa_permessi = explode(',',$stringa_permessi);

		// associa l'oggetto corrente alla variabile di sessione
		$_SESSION['UTENTE'] = $this;
	}

	// funzioni dell'utente
	function get_userid(){

		return $this->_properties['uid'];
	}
	
	function get_cognome_nome(){

		return $this->_properties['nome'];
	}
	
	function get_nome_gruppo(){

		return $this->_properties['nome_gruppo'];
	}
	
	function is_ds(){
		if ($this->_properties['is_ds']=='y')
		return 1;
		else	
		return 0;
	}
	
	function get_data_inizio_ds(){
		return $this->_properties['data_inizio_ds'];
	}
	
	function get_data_fine_ds(){
		return $this->_properties['data_fine_ds'];
	}
	
	function get_data_inizio_dt(){
		return $this->_properties['data_inizio_dt'];
	}
	
	function get_data_fine_dt(){
		return $this->_properties['data_fine_dt'];
	}
	
	
	
	function is_dt(){
		if ($this->_properties['is_dt']=='y')
		return 1;
		else	
		return 0;
	}
	
	function is_gc(){
		if ($this->_properties['is_gc']=='y')
		return 1;
		else	
		return 0;
	}
	
	function get_username(){

		return $this->_properties['username'];
	}
	function get_gid(){

		return $this->_properties['gid'];
	}
	function get_validazione(){

		return $this->_properties['validazione'];
	}

	function set_username($username){

		$this->_properties['username'] = $username;
	}
	function is_root(){

		if($this->_properties['is_root']) return 1;
		else return 0;
	}
	function get_permessi(){

		return $this->_properties['permessi'];
	}
	
	function get_tipoaccesso(){

		return $this->_properties['tipoaccesso'];
	}
	
	function get_status(){

		return $this->_properties['status'];
	}
	
	function get_privacy_cartella(){

		return $this->_properties['privacy_cartella'];
	}

	/****************************************************************************************************************
	* funzione controlla_permessi()																					*
	****** AGGIUNTA DA RAFFAELE *****
	* funzione che serve a verificare effettivamente se che l'utente abbia i permessi per poter eseguire l'operazione
	******************************************************************************************************************/
	function controlla_permessi($id_permesso, $operazione) {
	
	
		$flag = false;
		
		$array_stringa_permessi = explode(',',$this->_properties['permessi']);
		//print_r($array_stringa_permessi);
		
		$conn = db_connect();

		foreach($array_stringa_permessi as $element) {

			sscanf($element,"%d-%s",$idm,$idp);
			if($idm==$id_permesso) {
			
				if($operazione==1 && $idp{0}=='y'){ $flag = true; $ok="ok1"; }
				else if($operazione==2 && $idp{1}=='y') { $flag = true; $ok="ok2"; }
				else if($operazione==3 && $idp{2}=='y') {$flag = true; $ok="ok3"; }
				break;
			}
			//if (strlen(strrchr($idp,'y'))>0)
		}
		//echo ($flag);
		
		return $ok;

	}

}
?>