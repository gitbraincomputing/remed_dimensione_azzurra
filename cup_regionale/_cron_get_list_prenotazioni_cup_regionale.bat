:: Questo file bat esegue il PHP definito in basso.
:: Viene richiamato dall'utilita' di pianificazione di Windows ogni minuto.
@echo off
"C:\Program Files (x86)\PHP\v7.2\php-cgi.exe" -f C:\web\republic\cup_regionale\cron_get_list_prenotazioni_cup_regionale.php usephp=7 >> C:\web\republic\cup_regionale\output.log 2>&1
pause