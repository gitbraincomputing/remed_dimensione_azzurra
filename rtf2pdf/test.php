<?php
	system('soffice "-env:UserInstallation=file:///C:/web/Republic/rtf2pdf/temp" --headless --convert-to pdf:writer_pdf_Export c:/web/Republic/modelli_word/stampe/tmp_-7723936595836_Cartella_Clinica_(ex26)_-_Anagrafica_-_Anamnestica.rtf --outdir temp_pdf', $esito_convers);
	die("ESITO: " .$esito_convers);
php?>
