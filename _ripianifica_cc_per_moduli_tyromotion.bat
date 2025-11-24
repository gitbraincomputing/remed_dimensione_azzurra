:: Questo file bat esegue il PHP definito in basso tramite la versione PHP utilizzata da ReMed.
:: Viene richiamato dall'utilita' di pianificazione di Windows ogni notte.
@echo off
"C:\Program Files (x86)\PHP\v5.3\php-cgi.exe" -f "C:\web\republic\_ripianifica_cc_per_moduli_tyromotion.php"