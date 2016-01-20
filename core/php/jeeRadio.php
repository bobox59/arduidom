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
$time_reset = 0;
if (isset($argv)) {
    foreach ($argv as $arg) {
        $argList = explode('=', $arg);
        if (isset($argList[0]) && isset($argList[1])) {
            if ($argList[0] == 'arduid') {
                $arduid = $argList[1];
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
        $code_radio = $_GET["code"];
        $time_reset = $_GET["time"];
    } catch (Exception $e) {
        echo $e->getMessage();
        log::add('arduidom', 'error', $e->getMessage());
    }
}

log::add('arduidom','info','jeeRadio Called with params:' . $code_radio . ',' . $time_reset);

    foreach (eqLogic::byType('arduidom') as $eqLogic){
        foreach ($eqLogic->getCmd('info') as $cmd) {
            $pin_nb = $cmd->getLogicalId();
            if ($pin_nb == 999 && $code_radio != '') { // Cherche les cmd Arduidom avec la pin 999 (RADIO VIRTUEL)
                $valueToCheck = $cmd->getConfiguration('value');
                log::add('arduidom','debug', '===============TEST============== compare ' . $valueToCheck . ' & ' . $code_radio);
                if ($valueToCheck == $code_radio) {
                    if (is_object($cmd)) {
                        log::add('arduidom','debug', 'Action (Reception Radio) sur ' . $cmd->getHumanName());
                        $cmd->setCollectDate('');
                        $cmd->event(1);
                        if ($time_reset != 0 && $time_reset < 99) {
                            sleep($time_reset);
                            $cmd->setCollectDate('');
                            $cmd->event(0);
                        }
                    }
                }
            }
        }
    }
