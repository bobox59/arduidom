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
if (isset($argv)) {
    foreach ($argv as $arg) {
        $argList = explode('=', $arg);
        if (isset($argList[0]) && isset($argList[1])) {
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
    } catch (Exception $e) {
        echo $e->getMessage();
        log::add('arduidom', 'error', $e->getMessage());
    }
}
$array_recu = "";
foreach ($_GET as $key => $value){
    $array_recu = $array_recu . $key . $value . ' / ';
}

//log::add('arduidom', 'debug', 'Trame recu ' . $key . '=' . $value . '  ******************************');

foreach (eqLogic::byType('arduidom') as $eqLogic){
    foreach ($eqLogic->getCmd('info') as $cmd) {
        if (array_key_exists($cmd->getLogicalId(), $_GET)) {
            //if ($cmd->getLogicalId() == 3) {
            log::add('arduidom','debug', 'Mise à jour de la pin ' . $cmd->getLogicalId() . ' a '. $_GET[$cmd->getLogicalId()]);
            $cmd->setValue($_GET[$cmd->getLogicalId()]);
            $cmd->event($_GET[$cmd->getLogicalId()]);
            log::add('arduidom', 'event', 'Mise à jour de ' . $eqLogic->getHumanName() . ' terminée');
        }
    }
}
