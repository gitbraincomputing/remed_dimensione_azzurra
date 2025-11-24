<?php

final class Cda
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param int $instanceId
     * @return array
     */
    public function getModuleData(int $instanceId): array
    {

        // ESEGUO LA QUERY PER ESTRARMI I DATI
        $query = "SELECT * FROM re_estrai_dati_cda_xml_fse WHERE id_istanza_testata = {$instanceId}";
        $result = $this->conn->query($query)->fetchAll();

        $query_valori_modulo = "SELECT c.etichetta, id.valore FROM istanze_dettaglio AS id
							JOIN campi As c ON c.idcampo = id.idcampo
							WHERE id.id_istanza_testata = {$instanceId}";
        $result_valori_modulo = $this->conn->query($query_valori_modulo)->fetchAll();


        ##########################
        ### RACCOLTA VARIABILI ###
        ##########################

        // INFO MODULO
        $nome_modulo = trim($result[0]['nome_modulo']);

        // elimino le parte non necessarie dal nome del modulo
        if ((strpos($nome_modulo, "Cartella Clinica - ")) !== false) $nome_modulo = substr($nome_modulo, strlen('Cartella Clinica - '));
        elseif ((strpos($nome_modulo, "Cart. Clinica ex26 - Sezione ")) !== false) $nome_modulo = substr($nome_modulo, strlen('Cart. Clinica ex26 - Sezione ') + 4);
        elseif ((strpos($nome_modulo, "Cartella Clinica (ex26) - ")) !== false) $nome_modulo = substr($nome_modulo, strlen('Cartella Clinica (ex26) - '));
        elseif ((strpos($nome_modulo, "Cartella Clinica (ex26 e Legge8) - ")) !== false) $nome_modulo = substr($nome_modulo, strlen('Cartella Clinica (ex26 e Legge8) - '));
        elseif ((strpos($nome_modulo, "Cartella Clinica (Legge8) - ")) !== false) $nome_modulo = substr($nome_modulo, strlen('Cartella Clinica (Legge8) - '));
        elseif ((strpos($nome_modulo, "Cart. Clinica legge8 - ")) !== false) $nome_modulo = substr($nome_modulo, strlen('Cart. Clinica legge8 - '));
        elseif ((strpos($nome_modulo, "Cartella Infermieristica - ")) !== false) $nome_modulo = substr($nome_modulo, strlen('Cartella Infermieristica - '));

        $progressivo = $result[0]['progressivo'];
        $data_creazione = date('Ymd', strtotime($result[0]['datains'])) . date('Hi', strtotime($result[0]['orains'])) . "00+0100";

        $flag_offuscamento = strtoupper($result[0]['offuscamento_fse']);
        if ($flag_offuscamento == 'N') $display_name_offuscamento = "Normal";
        elseif ($flag_offuscamento == 'V') $display_name_offuscamento = "Very Restricted";

        $valori_compilati_modulo = "";
        foreach ($result_valori_modulo as $etichetta_valori) {
            $valori_compilati_modulo .= $etichetta_valori['etichetta'] . "\n" . $etichetta_valori['valore'] . "\n\n";
        }

        // INFO PAZIENTE
        $cognome_paziente = trim($result[0]['cognome_paziente']);
        $nome_paziente = trim($result[0]['nome_paziente']);

        if (trim($result[0]['sesso_paziente']) == "1") {
            $sesso_sigla_paziente = "M";
            $sesso_descrizione_paziente = "Maschio";
        } else {
            $sesso_sigla_paziente = "F";
            $sesso_descrizione_paziente = "Femmina";
        }

        $data_nascita_paziente = date('Ymd', strtotime($result[0]['data_nascita_paziente']));
        $codice_fiscale_paziente = trim($result[0]['codice_fiscale_paziente']);

        if (!empty(trim($result[0]['tel_paziente'])))
            $tel_paziente = '<telecom use="HP" value="tel:' . trim($result[0]['tel_paziente']) . '"></telecom>';
        else $tel_paziente = "";

        if (!empty(trim($result[0]['cell_paziente'])))
            $cell_paziente = '<telecom use="HP" value="tel:' . trim($result[0]['cell_paziente']) . '"></telecom>';
        else $cell_paziente = "";

        if (!empty(trim($result[0]['email_paziente'])))
            $email_paziente = '<telecom use="HP" value="mailto://' . trim($result[0]['email_paziente']) . '"></telecom>';
        else $email_paziente = "";

        $indirizzo_res_paziente = trim($result[0]['indirizzo_res_paziente']);
        $comune_res_paziente = trim($result[0]['comune_res_paziente']);
        $prov_res_paziente = trim($result[0]['prov_res_paziente']);
        $cap_res_paziente = trim($result[0]['cap_res_paziente']);


        // INFO DIRETTORE SANITARIO
        // il campo completo del direttore sanitario è valorizzato con "De Sena Pasqua", 
        // per cui divido la stringa per l'ultimo spazio e mi prendo i valori
        $cognome_ds = substr($result[0]['nominativo_ds'], 0, strrpos($result[0]['nominativo_ds'], ' '));            // "De Sena"
        $nome_ds = substr($result[0]['nominativo_ds'], strrpos($result[0]['nominativo_ds'], ' ') + 1);            // "Pasqua"

        if (!empty(trim($result[0]['email_ds'])))
            $email_ds = '<telecom use="HP" value="mailto://' . trim($result[0]['email_ds']) . '"></telecom>';
        else $email_ds = "";

        $codice_fiscale_ds = trim($result[0]['codice_fiscale_ds']);


        // INFO STRUTTURA
        $nome_struttura = trim($result[0]['nome_struttura']);
        $codice_struttura = trim($result[0]['codice_struttura']);
        $email_struttura = trim($result[0]['email_struttura']);
        $cod_asl_rif = trim($result[0]['cod_asl_rif']);
        $regione_struttura = trim($result[0]['regione_struttura']);

        switch ($regione_struttura) {
            case 10:
                $nome_regione = "Regione Piemonte";
                break;
            case 20:
                $nome_regione = "Regione Autonoma Val d'Aosta";
                break;
            case 30:
                $nome_regione = "Regione Lombardia";
                break;
            case 41:
                $nome_regione = "Provincia autonoma di Bolzano";
                break;
            case 42:
                $nome_regione = "Provincia autonoma di Trento";
                break;
            case 50:
                $nome_regione = "Regione Veneto";
                break;
            case 60:
                $nome_regione = "Regione Friuli Venezia Giulia";
                break;
            case 70:
                $nome_regione = "Regione Liguria";
                break;
            case 80:
                $nome_regione = "Regione Emilia Romagna";
                break;
            case 90:
                $nome_regione = "Regione Toscana";
                break;
            case 100:
                $nome_regione = "Regione Umbria";
                break;
            case 110:
                $nome_regione = "Regione Marche";
                break;
            case 120:
                $nome_regione = "Regione Lazio";
                break;
            case 130:
                $nome_regione = "Regione Abruzzo";
                break;
            case 140:
                $nome_regione = "Regione Molise";
                break;
            case 150:
                $nome_regione = "Regione Campania";
                break;
            case 160:
                $nome_regione = "Regione Puglia";
                break;
            case 170:
                $nome_regione = "Regione Basilicata";
                break;
            case 180:
                $nome_regione = "Regione Calabria";
                break;
            case 190:
                $nome_regione = "Regione Sicilia";
                break;
            case 200:
                $nome_regione = "Regione Sardegna";
                break;
            default:
                $nome_regione = "";
                break;
        }

        return compact(
            'nome_modulo',
            'progressivo',
            'data_creazione',
            'flag_offuscamento',
            'display_name_offuscamento',
            'cognome_paziente',
            'nome_paziente',
            'sesso_sigla_paziente',
            'sesso_descrizione_paziente',
            'data_nascita_paziente',
            'codice_fiscale_paziente',
            'tel_paziente',
            'cell_paziente',
            'email_paziente',
            'indirizzo_res_paziente',
            'comune_res_paziente',
            'prov_res_paziente',
            'cap_res_paziente',
            'cognome_ds',
            'nome_ds',
            'email_ds',
            'codice_fiscale_ds',
            'nome_struttura',
            'codice_struttura',
            'regione_struttura',
            'nome_regione',
            'cod_asl_rif',
            'email_struttura'
        );
    }
}
