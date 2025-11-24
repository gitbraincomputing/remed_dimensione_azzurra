<?PHP

	session_start();
	$_SESSION['pagina_precedente'] = $_POST['pagina_precedente'];
	$_SESSION['pagina_chiamata'] = $_POST['pagina_chiamata'];
	echo $_SESSION['pagina_precedente'];

?>