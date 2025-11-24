<?php
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
		$this->_properties['gid_tipo'] = null;
		$this->_properties['username'] = null;
 		$this->_properties['permessi'] = null;
		$this->_properties['validazione'] = null;
		$this->_properties['errmsg'] = null;
		$this->_properties['status'] = null;

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
		
		// rilascia la connessione al db
		mssql_free_result($rs);

		// recupera la stringa dei permessi
		/*$query = "select permessi from gruppi where gid='".$this->_properties['gid']."'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message("non riesco a recuperare i permessi");
		$row = mssql_fetch_assoc($result);
		$this->_properties['permessi'] = $row['permessi'];
		mssql_free_result($result);*/
		
		
		$db = odbc_connect(DSN, DB_USER, DB_PASSWORD);
		$res  = odbc_exec ($db, "SET TEXTSIZE ".(2 * MAX));
		$res = odbc_exec ($db, "SELECT permessi FROM gruppi WHERE gid='".$this->_properties['gid']."'");
		odbc_longreadlen ($res, MAX);
		$permessi = odbc_result ($res, "permessi");		
		odbc_close($db);
		$this->_properties['permessi'] = $permessi;
		
		//$array_stringa_permessi = explode(',',$stringa_permessi);

		$query = "select tipo from gruppi where gid='".$this->_properties['gid']."'";
		$result = mssql_query($query, $conn);
		if(!$result) error_message("non riesco a recuperare i permessi");
		$row = mssql_fetch_assoc($result);
		$this->_properties['gid_tipo'] = $row['tipo'];
		// associa l'oggetto corrente alla variabile di sessione
		$_SESSION['UTENTE'] = $this;
	}

	// funzioni dell'utente
	function get_userid(){

		return $this->_properties['uid'];
	}
	function get_username(){

		return $this->_properties['username'];
	}
	function get_gid(){

		return $this->_properties['gid'];
	}
	function get_gid_tipo(){

		return $this->_properties['gid_tipo'];
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
	function get_status(){

		return $this->_properties['status'];
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
		return $ok;

	}

}
?>