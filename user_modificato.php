<?php

$do=$_REQUEST['doing'];
if ($do=="1") $err=("Inserire l'email!");
if ($do=="2") $err=("La password di conferma non coincide con la password inserita!");
if ($do=="3") $err="Modifiche avvenute con successo!";
?>
<div id="wrap-content"><div class="padding10">
	<div class="logo"><img src="images/re-med-logo.png" /></div>

				<div id="briciola" style="margin-bottom:20px;">
				<div class="elem0"><a href="#">home</a></div>
				<div class="elem_pari"><a href="#">benvenuto in Re.Med.</a></div>
			  
			</div>

	<div class="titoloalternativo">
			<h1><?=$err?></h1>
		</div>
	</div>
	</div>