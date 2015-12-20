<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */
require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";
$ardulogfile = dirname(__FILE__) . "/../../../../log/arduidom.message";
$arduid = 0;
$time_before = microtime(true) ;
//$bench_id = 0;
//$bench_id++; $elapsed_time = microtime(true) - $time_before; log::add('arduidom','debug', '                                                                                   benchmark b(' . $bench_id . '): ' . ($elapsed_time * 1000) . " ms ");
if (isset($argv)) {
    foreach ($argv as $arg) {
        $argList = explode('=', $arg);
        if (isset($argList[0]) && isset($argList[1])) {
            if ($argList[0] == 'arduid') {
                $arduid = $argList[1];
            }
            if (is_numeric($argList[0])) {
                $valuetoconvert = $argList[0];
                $argList[0] = (1000 * intval($arduid)) + $valuetoconvert;
            }
            $_GET[$argList[0]] = $argList[1];
        }
    }
}
if (!isset($argv)) {
    $idArray = explode('&',$_SERVER["QUERY_STRING"]);
    foreach ($idArray as $arg) {
        $argList = explode('=', $arg);
        if (isset($argList[0]) && isset($argList[1])) {
            if ($argList[0] == 'arduid') {
                $arduid = $argList[1];
            }
            if (is_numeric($argList[0])) {
                $valuetoconvert = $argList[0];
                $argList[0] = (1000 * intval($arduid)) + $valuetoconvert;

            }
            $_GET[$argList[0]] = $argList[1];
        }
    }
}

if (config::byKey('api') != '') {
    try {
        if($_GET["api"] != config::byKey('api')){
            if (php_sapi_name() != 'cli' || isset($_SERVER['REQUEST_METHOD']) || !isset($_SERVER['argc'])) {
                if (config::byKey('api') != init('apikey')) {
                    connection::failed();
                    echo 'Clef API non valide, vous n\'etes pas autorisé à effectuer cette action (jeeApi)';
                    log::add('arduidom', 'error', 'Problème avec la clé API, modifiez la puis redémarrez le plugin');
                    die();
                }
            }
        }
        if($_GET["arduid"] < 1){
            echo 'ID Arduidom Non fourni, vous devez mettre a jour votre version d Arduidom';
            log::add('arduidom', 'error', 'ID Arduidom Non fourni, vous devez mettre a jour votre version d Arduidom');
            die();
        } else {
            $id = $_GET["arduid"];
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        log::add('arduidom', 'error', $e->getMessage());
    }
}

if (file_exists($ardulogfile) == false) {
    log::add('arduidom', 'info', "Creation de arduidom.message");
    file_put_contents($ardulogfile, date("Y-m-d H:i:s") . "",FILE_APPEND);
    if (file_exists($ardulogfile) == false) {
        log::add('arduidom', 'error', "Impossible de creer le fichier log arduidom.message (Probleme de droits ?)");
    }
}
if (file_exists($ardulogfile) == true && filesize($ardulogfile) > 50000) { // Limit log size
    copy($ardulogfile, $ardulogfile . ".1");
    file_put_contents($ardulogfile, date("Y-m-d H:i:s") . " => File rotate, old file to arduidom.message.1\r\n");
}

//log::add('arduidom','debug','=============DECODAGE RADIO DE LA TABLE $_GET => AFFICHAGE DANS Log Messages Arduidom===================3');
$array_recu = "";
$jeedebugkeys = "";
$code_radio = '';
foreach ($_GET as $key => $value){ // DECODAGE RADIO DE LA TABLE $_GET => AFFICHAGE DANS Log Messages Arduidom
    $array_recu = $array_recu . $key . $value . ' / ';
    $jeedebugkeys = $jeedebugkeys . $key . "=" . $value . " & ";

    if (strpos($value,'RFD:') !== false) { // DECODAGE D'UN CODE RADIO RECU
        $code_radio = $value;
        log::add('arduidom','debug', 'Code radio recu :: ' . $code_radio);
        $valueList = explode(':', $value);
        if ($valueList[5] == "1") { // P:1 => TRISTATE
            $decodedvalue = intval ($valueList[1]);
            $decodedtris = str_pad(decbin($decodedvalue), 24, "0", STR_PAD_LEFT);
            $chunk = chunk_split($decodedtris, 2, "/");
            $splitedtris = explode("/",$chunk);
            $decodedtris = "";
            foreach ($splitedtris as $splitsvalue) {
                if ($splitsvalue == "00") { $decodedtris .= "0"; };
                if ($splitsvalue == "11") { $decodedtris .= "1"; };
                if ($splitsvalue == "01") { $decodedtris .= "F"; };
            }
            $decodedtris = str_pad($decodedtris, 12, '0', STR_PAD_LEFT);;
            file_put_contents($ardulogfile, date("Y-m-d H:i:s") . " => " . $key . " => " . $value . " (TRISTATE : T" . $decodedtris . ")\r\n",FILE_APPEND);
        }

        if ($valueList[5] == "4") { // P:4 => HOMEEASY  H:11111111GVDD = 11111111(id) G(group) V(value) DD(code device)
            $decodedsender = intval ($valueList[1]);
            $decodedvalue = 0;
            $values = intval ($valueList[3]);
            if ($values > 999) {
                $decodedvalue += 1000;
                $values = $values - 1000;
            }
            if ($values > 99) {
                $decodedvalue += 100;
                $values = $values - 100;
            }
            $decodedvalue += $values;

            $decodedhe = str_pad($decodedsender, 8, "0", STR_PAD_LEFT) . str_pad($decodedvalue, 4, "0", STR_PAD_LEFT);
            file_put_contents($ardulogfile, date("Y-m-d H:i:s") . " => " . $key . " => " . $value . " (HomeEasy : H" . $decodedhe . ")\r\n",FILE_APPEND);
        }

        if ($valueList[5] == "9") { // P:9 => BOBOX DIY RADIO)
            file_put_contents($ardulogfile, date("Y-m-d H:i:s") . " => " . $key . " => " . $value . " (Bobox : B" . $valueList[3] . ")\r\n",FILE_APPEND);
        }
    }


}
//log::add('arduidom','debug','========jee========ID:' . $id . "  " . $jeedebugkeys);


$ApprentissageRadio = cache::byKey('arduidom_radio_learn');
$ApprentissageRadio = $ApprentissageRadio->getValue();
if ($ApprentissageRadio == "1" && $code_radio != '') {
    log::add('arduidom','info', 'Radio Code = ' . $code_radio);
    log::add('arduidom','info', 'Learning Mode = ' . $ApprentissageRadio);
    $RadioCacheId = cache::byKey('arduidom_radio_index');
    $RadioCacheId = $RadioCacheId->getValue();
    $RadioCacheLastCode = cache::byKey('arduidom_radio_lastcode');
    $RadioCacheLastCode = $RadioCacheLastCode->getValue();
    log::add('arduidom','info',"READ CACHE arduidom_radio_lastcode=" . $RadioCacheLastCode);
    if ($RadioCacheId == '' || ($RadioCacheLastCode != $code_radio)) $RadioCacheId = 0;
    log::add('arduidom','debug', '===============CACHE============== index:' . $RadioCacheId);
    $RadioCacheId++;
    cache::set('arduidom_radio_index',$RadioCacheId);
    log::add('arduidom','info',"Write CACHE arduidom_radio_lastcode=" . $code_radio);
    cache::set('arduidom_radio_lastcode', $code_radio);
    $RadioCacheLastCode = cache::byKey('arduidom_radio_lastcode');
    $RadioCacheLastCode = $RadioCacheLastCode->getValue();
    log::add('arduidom','info',"READ CACHE arduidom_radio_lastcode=" . $RadioCacheLastCode);
}

// **** ACTIONS SUR LES DONNEES **** //

foreach (eqLogic::byType('arduidom') as $eqLogic){
    foreach ($eqLogic->getCmd('info') as $cmd) {
        $pin_nb = $cmd->getLogicalId();
        if ($pin_nb == 999 && $code_radio != '' && $ApprentissageRadio != "1") { // Cherche les cmd Arduidom avec la pin 999 (RADIO VIRTUEL)
            $valueToCheck = $cmd->getConfiguration('value');
            //log::add('arduidom','debug', '===============TEST============== compare ' . $valueToCheck . ' & ' . $code_radio);
            if ($valueToCheck == $code_radio) {
                if (is_object($cmd)) {
                    log::add('arduidom','debug', 'Arduino n°' . $id . ' Action (Reception Radio) sur ' . $cmd->getHumanName());
                    $daemon_path = realpath(dirname(__FILE__) . '/../../core/php');
                    $cmd = 'nice -n 19 php ' . $daemon_path . '/jeeRadio.php api=' . config::byKey('api') . " code=" . $code_radio . " time=" . "5" ;
                    //log::add('arduidom', 'info', 'Lancement radio : ' . $cmd);
                    exec($cmd . ' > /dev/null&');
                    //log::add('arduidom', 'info', 'Fin du lancement radio : ' . $cmd);
                }
            }

        } else {

            if (array_key_exists($pin_nb, $_GET)) {
                if (is_object($cmd)) {
                    $cmd->event($_GET[$pin_nb]);
                }
                log::add('arduidom', 'event', 'Arduino n°' . $id . ' Mise à jour de ' . $eqLogic->getHumanName() . ' terminée (pin' . $pin_nb . ' = '. $_GET[$pin_nb] . ')');
            }
        }
    }
}
//$bench_id++; $elapsed_time = microtime(true) - $time_before; log::add('arduidom','debug', '                                                    ------------------        LAST benchmark(' . $bench_id . '): ' . ($elapsed_time * 1000) . " ms ");
