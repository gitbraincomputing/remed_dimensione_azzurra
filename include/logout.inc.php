<?php
/************************************************************************
* funzione logout	    												*
*************************************************************************/
function logout(){

	setcookie("usrPUBLIACI","", 0);
	// rimuove tutte le variabili di sessione dell'utente
	unset($_SESSION['UTENTE']);
	unset($_SESSION['PERMESSI']);
	//unset($_SESSION['PAGINA']);
	//unset($_SESSION['AZIONE']);
	//unset($_SESSION['IMG']);
	//unset($_SESSION['ALT_IMG']);
	//unset($_SESSION['MODULO']);
	//unset($_SESSION['MODULO_ID']);
	//unset($_SESSION['MODULI_ATTIVI']);
	//unset($_SESSION['I']);
	
	echo "<script tpye=\"text/javascript\">";
	echo "parent.location = \"index.php\";";
	echo "</script>";
	exit();
}
?>