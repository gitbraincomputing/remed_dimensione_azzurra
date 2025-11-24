<?
include_once('include/functions.inc.php');
include_once('include/dbengine.inc.php');
include_once('include/function_page.php');

$ko_confirm = null;

if(isset($_SESSION['UTENTE']))
{

	$idcartella = !empty($_GET['idcartella']) ? $_GET['idcartella'] : $_POST['idcartella'];
	$idpaziente = !empty($_GET['idpaziente']) ? $_GET['idpaziente'] : $_POST['idpaziente'];
	$idmodulo = !empty($_GET['idmodulo']) ? $_GET['idmodulo'] : $_POST['idmodulo'];
	$idregime = !empty($_GET['idregime']) ? $_GET['idregime'] : $_POST['idregime'];
	$cart = !empty($_GET['cart']) ? $_GET['cart'] : $_POST['cart'];
	$imp = !empty($_GET['imp']) ? $_GET['imp'] : $_POST['imp'];

	switch($_POST['do']) {
		case 'match':
			if(!empty($_POST['op_pwd'])) {
				$op_pwd = crypt($_POST['op_pwd'],SALE);		
				
				//$pws_unlock = '4aIClZNGiXT5w'; //'GruppoAries_23'
				//$pws_unlock = '4aGhjqYE7dmv.'; //'Silvana9194'
				$pws_unlock = '4apWu7MccKjuM'; //'@r1&s24'

				if($op_pwd == $pws_unlock) {
					setcookie("unlock_controls", true, (time() + 60 * 10));
?>
<script>
	self.opener.view_istanze_modulo("<?= $idcartella ?>","<?= $idpaziente ?>","<?= $idregime ?>","<?= $idmodulo ?>","<?= $cart ?>","<?= $imp ?>");
	window.close();
</script>
<?
				} else {
					setcookie("unlock_controls", false);
					$ko_confirm = "La password &egrave; errata!";
				}
			}
			break;
		
		case 'lock':
			setcookie("unlock_controls", "", time() - 3600);
?>
<script>
	self.opener.view_istanze_modulo("<?= $idcartella ?>","<?= $idpaziente ?>","<?= $idregime ?>","<?= $idmodulo ?>","<?= $cart ?>","<?= $imp ?>");
	window.close();
</script>
<?
			break;
			
		default:
			break;
	}
} else {
	die("La tua sessione su ReMed &egrave; scaduta.<br>Chiudi questa finestra ed effettua nuovamente il login");
	
}
?>

<html>
	<head>
		<link href="style/flexigrid.css" rel="stylesheet" type="text/css" />
		<link href="style/new_ui.css" rel="stylesheet" type="text/css" />
		<link href="style/new_ui_print.css" rel="stylesheet" type="text/css" media="print" />
		<script type="text/javascript" src="script/jquery-1.2.6.min.js"></script>
	</head>
	<body style="background: #ffffff;">
		<div style="padding: 10px;">
			<div class="titoloalternativo">
				<h1>Sblocca Controlli</h1>
			</div>

			<div class="titolo_pag">
				<div class="comandi" style="float:right;">
					<div class="elimina"><a href="#" onclick="window.close();">chiudi</a></div>
				</div>	
			</div>
			<div id="wrap-content">
				<div class="padding10">   
					<form  method="post" name="form0" action="popup_confirm_password.php" >
						<input type="hidden" name="idcartella" value="<?= $idcartella ?>" >
						<input type="hidden" name="idpaziente" value="<?= $idpaziente ?>" >
						<input type="hidden" name="idmodulo" value="<?= $idmodulo ?>" >
						<input type="hidden" name="idregime" value="<?= $idregime ?>" >
						<input type="hidden" name="cart" value="<?= $cart ?>" >
						<input type="hidden" name="imp" value="<?= $imp ?>" >
						
						<div class="blocco_centralcat">
							<div class="riga">
								<br><br>
							</div>
<?	if(!$_COOKIE['unlock_controls']) { ?>
							<input type="hidden" value="match" name="do"/>
							<div class="riga">
								<div class="rigo_mask">
									<div class="testo_mask">Password: </div>
									<div class="campo_mask mandatory">
										<input type="password" id="op_pwd" name="op_pwd" >
									</div>
								</div>
							</div>
<?		if(!empty($ko_confirm)) { ?>
							<div class="riga">
								<div class="rigo_mask" style="width: 100%; min-height: 20px;">
									<div class="testo_mask"><?=$ko_confirm?></div>
								</div>
							</div>
<?		}
	} else { ?>
							<input type="hidden" value="lock" name="do"/>
							<div class="riga">
								<div class="rigo_mask" style="width: 100%;">
									<div class="testo_mask">Sblocco gi&agrave; attivo, vuoi riattivare i blocchi?</div>
								</div>
							</div>
<?	} ?>
							<div class="riga">
								<br><br>
							</div>
							<div class="titolo_pag">
								<div class="comandi">
									<input type="submit" title="Conferma" value="conferma" class="button_salva"/>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>	
		</div>
	</body>
</html>