<?php
	//system('soffice "-env:UserInstallation=file:///C:/web/Republic/firmadigitale/temp" --headless --convert-to pdf:writer_pdf_Export --accept=socket,host=0,port=8100;urp; c:\web\Republic\modelli_word\stampe\tmp_-6773449145836_Cartella_Clinica_(ex26)_-_Anagrafica_-_Anamnestica.rtf --outdir pdf', $esito_convers);
	system('"C:\Program Files\LibreOffice\program\soffice" "-env:UserInstallation=file:///C:/web/Republic/firmadigitale/temp" --headless --convert-to "pdf:writer_pdf_Export" c:\web\Republic\modelli_word\stampe\tmp_-6773449145836_Cartella_Clinica_(ex26)_-_Anagrafica_-_Anamnestica.rtf --outdir pdf', $esito_convers);
	die("ESITO: " .$esito_convers);

	//exec('start /B c:\web\Republic\firmadigitale\convert.bat');
php?>
