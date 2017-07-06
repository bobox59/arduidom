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
if ($_SERVER['REMOTE_ADDR'] != '') {
    $ip = $_SERVER['REMOTE_ADDR'];
}

$DebugTimetoDo = true;
$starttime = microtime(true);
$rid = rand(10,99) . " ^ ";
$steps = 0;
if ($DebugTimetoDo) log::add('arduidom', 'debug', $rid . "$$$$$$$$$$$$$ jeeArduidom Start " . $rid . " @ " . $starttime);
if ($DebugTimetoDo) log::add('arduidom', 'debug', "IP: " . $ip . " - " . $rid . " @ " . $starttime);
//echo( $rid . "$$$$$$$$$$$$$ jeeArduidom Start " . $rid . " @ " . $starttime . "\n\r");
//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");

$ardulogfile = dirname(__FILE__) . "/../../../../log/arduidom.message";
$arduid = 0;
$daemonreadyfound = 0;
$time_before = microtime(true) ;
$bench_id = 0;
$bench_id++; $elapsed_time = microtime(true) - $time_before;
if ($DebugTimetoDo) log::add('arduidom','debug', $rid . '                                                                                   benchmark b(' . $bench_id . '): ' . ($elapsed_time * 1000) . " ms ");
//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");
if (isset($argv)) {
    foreach ($argv as $arg) {
        $argList = explode('=', $arg);
        if (isset($argList[0]) && isset($argList[1])) {
            if ($argList[0] == 'arduid') {
                if ($argList[1] == "net") { // Detection automatique de l'arduino ID pour les shield ethernet
                    if ($DebugTimetoDo) log::add('arduidom', 'debug', "Recherche de l'ID par l'IP..." . $d);
                    $nbArduinos = intval(config::byKey("ArduinoQty", "arduidom", 1));
                    for ($d = 1; $d <= $nbArduinos; $d++) {
                        if (config::byKey('A' . $d . '_port', 'arduidom', '', true) == "Network") {
                            if (config::byKey('A' . $d . '_daemonip', 'arduidom', '', true) != "127.0.0.1") {
                                if (config::byKey('A' . $d . '_daemonip', 'arduidom', '', true) == $ip) {
                                    if ($DebugTimetoDo) log::add('arduidom', 'debug', "Shield Ethernet detecté en ID:" . $d);
                                        $argList[1] = $d;
                                        $arduid = $d;
                                }
                            }
                        }
                    }
                } else {
                    $arduid = $argList[1];
                }
            }
            if ($argList[0] == 'daemonready') {
                $arduid = $argList[1];
                $daemonreadyfound = 1;
            }
            if (is_numeric($argList[0])) {
                $valuetoconvert = $argList[0];
                $argList[0] = (1000 * intval($arduid)) + $valuetoconvert;
            }
            $_GET[$argList[0]] = $argList[1];
        }
    }
}
//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");
if (!isset($argv)) {
    $idArray = explode('&',$_SERVER["QUERY_STRING"]);
    foreach ($idArray as $arg) {
        $argList = explode('=', $arg);
        if (isset($argList[0]) && isset($argList[1])) {
            if ($argList[0] == 'arduid') {
                if ($argList[1] == "net") { // Detection automatique de l'arduino ID pour les shield ethernet
                    if ($DebugTimetoDo) log::add('arduidom', 'debug', "Recherche de l'ID par l'IP..." . $d);
                    $nbArduinos = intval(config::byKey("ArduinoQty", "arduidom", 1));
                    for ($d = 1; $d <= $nbArduinos; $d++) {
                        if (config::byKey('A' . $d . '_port', 'arduidom', '', true) == "Network") {
                            if (config::byKey('A' . $d . '_daemonip', 'arduidom', '', true) != "127.0.0.1") {
                                log::add('arduidom', 'debug', "Compare " . config::byKey('A' . $d . '_daemonip', 'arduidom', '', true) . " ?= " . $ip);
                                if (config::byKey('A' . $d . '_daemonip', 'arduidom', '', true) == $ip) {
                                    if ($DebugTimetoDo) log::add('arduidom', 'debug', "Shield Ethernet detecté en ID:" . $d);
                                    $argList[1] = $d;
                                    $arduid = $d;

                                }

                            }
                        }
                    }
                } else {
                    $arduid = $argList[1];
                }
            }
            if ($argList[0] == 'daemonready') {
                $arduid = $argList[1];
                $daemonreadyfound = 1;
            }
            if (is_numeric($argList[0])) {
                $valuetoconvert = $argList[0];
                $argList[0] = (1000 * intval($arduid)) + $valuetoconvert;

            }
            $_GET[$argList[0]] = $argList[1];
        }
    }
}

//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");

//log::add('arduidom', 'debug', "$$$ jeeArduidom Check API");

if (!jeedom::apiAccess(init('api'))) {
    connection::failed();
    echo 'Clef API (' . $_GET['api'] . ') non valide, vous n\'etes pas autorisé à effectuer cette action (arduidom)';
    log::add('arduidom', 'error', "Clef API (" . $_GET['api'] . ") non valide, vous n\'etes pas autorisé à effectuer cette action");
    die();
}
//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");
//log::add('arduidom', 'debug', "$$$ jeeArduidom API OK, ID=" . $arduid);
//log::add('arduidom', 'debug', "$$$ jeeArduidom API OK, GET=" . $_GET['daemonready']);

if ($daemonreadyfound == 1) { // informe start_daemon() que python est pret.
    if ($_GET['daemonready'] == 1) { // informe start_daemon() que python est pret.
        log::add('arduidom', 'info', "Le démon python est pret.");
        config::save('daemonstarted', 1, 'arduidom');
        die();
    }
    if ($_GET['daemonready'] == 2) {
        log::add('arduidom', 'error', "Un ou plusieurs Arduino n'ont pas la version du sketch requise !");
        config::save('daemonstarted', 2, 'arduidom');
        die();
    }
}
//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");

if (file_exists($ardulogfile) == false) {
    log::add('arduidom', 'info', $rid . "Creation de arduidom.message");
    file_put_contents($ardulogfile, date("Y-m-d H:i:s") . "",FILE_APPEND);
    if (file_exists($ardulogfile) == false) {
        log::add('arduidom', 'error', $rid . "Impossible de creer le fichier log arduidom.message (Probleme de droits ?)");
    }
}
//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");
if (file_exists($ardulogfile) == true && filesize($ardulogfile) > 50000) { // Limit log size
    copy($ardulogfile, $ardulogfile . ".1");
    file_put_contents($ardulogfile, date("Y-m-d H:i:s") . " => File rotate, old file to arduidom.message.1\r\n");
}
//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");

//log::add('arduidom','debug','=============DECODAGE RADIO DE LA TABLE $_GET => AFFICHAGE DANS Log Messages Arduidom===================3');
$array_recu = "";
$jeedebugkeys = "";
$code_radio = '';
//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");
foreach ($_GET as $key => $value){ // DECODAGE RADIO DE LA TABLE $_GET => AFFICHAGE DANS Log Messages Arduidom
    $array_recu = $array_recu . $key . $value . ' / ';
    $jeedebugkeys = $jeedebugkeys . $key . "=" . $value . " & ";

    if (strpos($value,'RFD:') !== false) { // DECODAGE D'UN CODE RADIO RECU
        $code_radio = $value;
        log::add('arduidom','debug', $rid . 'Code radio recu :: ' . $code_radio . " @ " . (microtime(true) - $starttime));
        $valueList = explode(':', $value);
        switch($valueList[5]) { // PROTOCOLE
        case "1":  // P:1 => TRISTATE
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
            file_put_contents($ardulogfile, date("Y-m-d H:i:s") . " " . $key . " - Valeurs: pour eq en INFO: [ " . $value . " ] - pour eq en ACTION: [ T" . $decodedtris . " ] (protocol:TRISTATE)\r\n",FILE_APPEND);
            break;

        case "4":  // P:4 => HOMEEASY  H:11111111GVDD = 11111111(id) G(group) V(value) DD(code device)
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
            file_put_contents($ardulogfile, date("Y-m-d H:i:s") . " " . $key . " - Valeurs: pour eq en INFO: [ " . $value . " ] - pour eq en ACTION: [ H" . $decodedhe . " ] (protocol:CHACON/HOMEEASY)\r\n",FILE_APPEND);
            //file_put_contents($ardulogfile, date("Y-m-d H:i:s") . " => " . $key . " => " . $value . " (HomeEasy : H" . $decodedhe . ")\r\n",FILE_APPEND);
            break;

        case "9": // P:9 => BOBOX DIY RADIO)
            file_put_contents($ardulogfile, date("Y-m-d H:i:s") . " " . $key . " - Valeurs: pour eq en INFO: [ " . $value . " ] (protocol:BOBOX)\r\n",FILE_APPEND);
            break;

        default:
            file_put_contents($ardulogfile, date("Y-m-d H:i:s") . " " . $key . " - Valeurs: pour eq en INFO: [ " . $value . " ] (protocol:INCONNU)\r\n",FILE_APPEND);
            break;
        }// end of switch
    }


}
//log::add('arduidom','debug','========jee========ID:' . $arduid . "  " . $jeedebugkeys);

//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");

$ApprentissageRadio = cache::byKey('arduidom_radio_learn');
$ApprentissageRadio = $ApprentissageRadio->getValue();
if ($ApprentissageRadio == "1" && $code_radio != '') {
    log::add('arduidom','info', $rid . 'Radio Code = ' . $code_radio);
    log::add('arduidom','info', $rid . 'Learning Mode = ' . $ApprentissageRadio);
    $RadioCacheId = cache::byKey('arduidom_radio_index');
    $RadioCacheId = $RadioCacheId->getValue();
    $RadioCacheLastCode = cache::byKey('arduidom_radio_lastcode');
    $RadioCacheLastCode = $RadioCacheLastCode->getValue();
    if ($DebugTimetoDo) log::add('arduidom','info',$rid . "READ CACHE arduidom_radio_lastcode=" . $RadioCacheLastCode);
    if ($RadioCacheId == '' || ($RadioCacheLastCode != $code_radio)) $RadioCacheId = 0;
    if ($DebugTimetoDo) log::add('arduidom','debug', $rid . '===============CACHE============== index:' . $RadioCacheId);
    $RadioCacheId++;
    cache::set('arduidom_radio_index',$RadioCacheId);
    if ($DebugTimetoDo) log::add('arduidom','info',$rid . "Write CACHE arduidom_radio_lastcode=" . $code_radio);
    cache::set('arduidom_radio_lastcode', $code_radio);
    $RadioCacheLastCode = cache::byKey('arduidom_radio_lastcode');
    $RadioCacheLastCode = $RadioCacheLastCode->getValue();
    if ($DebugTimetoDo) log::add('arduidom','info',$rid . "READ CACHE arduidom_radio_lastcode=" . $RadioCacheLastCode);
}
//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");

// **** ACTIONS SUR LES DONNEES **** //
if ($DebugTimetoDo) log::add('arduidom', 'debug', $rid . "$$$ jeeArduidom actions sur les données");

foreach (eqLogic::byType('arduidom') as $eqLogic){
    foreach ($eqLogic->getCmd('info') as $cmd) {
        $pin_nb = $cmd->getLogicalId();
        if ($pin_nb == 999 && $code_radio != '' && $ApprentissageRadio != "1") { // Cherche les cmd Arduidom avec la pin 999 (RADIO VIRTUEL)
            $valueToCheck = $cmd->getConfiguration('value');
            if ($DebugTimetoDo) log::add('arduidom','debug', $rid . '===============TEST============== compare ' . $valueToCheck . ' & ' . $code_radio);
            if ($valueToCheck == $code_radio) {
                if (is_object($cmd)) {
                    log::add('arduidom','debug', $rid . 'Arduino n°' . $arduid . ' Action (Reception Radio) sur ' . $cmd->getHumanName() . " @ " . (microtime(true) - $starttime));
                    $daemon_path = realpath(dirname(__FILE__) . '/../../core/php');
                    $command = 'nice -n 19 php ' . $daemon_path . '/jeeRadio.php api=' . config::byKey('api') . " code=" . $code_radio . " time=" . "5" ;
                    log::add('arduidom', 'info', $rid . 'Lancement radio : ' . $command . " @ " . (microtime(true) - $starttime));
                    exec($command . ' > /dev/null&');
                    log::add('arduidom', 'info', $rid . 'Fin du lancement radio : ' . $command . " @ " . (microtime(true) - $starttime));
                }
            }

        } else {

            //log::add('arduidom', 'debug', "$$$ jeeArduidom Compare [" . $pin_nb . "]");
            if (array_key_exists($pin_nb, $_GET) && $_GET[$pin_nb] != "na") {
                //log::add('arduidom', 'debug', "$$$ jeeArduidom Compare [" . $pin_nb . "] : It Exists 1 :)");
                if (is_object($cmd)) {
                    //log::add('arduidom', 'debug', "$$$ jeeArduidom Compare [" . $pin_nb . "] : Is an object :)");
                    //log::add('arduidom', 'debug', '$$$ jeeArduidom before $cmd->event(' . $_GET[$pin_nb] . ")");
                    $cmd->setCollectDate('');
                    $cmd->event($_GET[$pin_nb]);
                    //log::add('arduidom', 'debug', '$$$ jeeArduidom after $cmd->event(' . $_GET[$pin_nb] . ") on pin " . $pin_nb);
                    if ($DebugTimetoDo) log::add('arduidom', 'debug', $rid . 'Arduino n°' . $arduid . ' Mise à jour de ' . $eqLogic->getHumanName() . ' terminée (pin' . $pin_nb . ' = ' . $_GET[$pin_nb] . ')' . " @ " . (microtime(true) - $starttime));
                    log::add('arduidom', 'event', $rid . 'Arduino n°' . $arduid . ' Mise à jour de ' . $eqLogic->getHumanName() . ' terminée (pin' . $pin_nb . ' = ' . $_GET[$pin_nb] . ')' . " @ " . (microtime(true) - $starttime));
                } else {
                    //log::add('arduidom', 'debug', "$$$ jeeArduidom Compare [" . $cmd . "] :: Is NOT an object :(");
                }
            } else {
                //log::add('arduidom', 'debug', "$$$ jeeArduidom Compare [" . $pin_nb . "] : Not in Array :(");
            }
        }
    }
}
//log::add('arduidom', 'debug', "$$$ jeeArduidom END !!!!!!!!!!!!!!");
//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");

$bench_id++; $elapsed_time = microtime(true) - $time_before; if ($DebugTimetoDo) log::add('arduidom','debug', $rid . '                                                    ------------------        LAST benchmark(' . $bench_id . '): ' . ($elapsed_time * 1000) . " ms ");
if ($DebugTimetoDo) log::add('arduidom', 'debug', $rid . "$$$$$$$$$$$$$ jeeArduidom END " . $rid . " @ " . (microtime(true) - $starttime));
//echo($rid . "$$$$$$$$$$$$$ jeeArduidom END " . $rid . " @ " . (microtime(true) - $starttime) . "\n\r");
//echo( $rid . "$$$" . $steps++ . " @ " . (microtime(true) - $starttime) . "\n\r");
