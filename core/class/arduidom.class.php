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
include_file('core', 'pin', 'config', 'arduidom');

/* * ***************************Includes********************************* */

class arduidom extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    public static function pull() {
        global $ARDUPINMAP;
        log::add('arduidom', 'debug', 'pull() called');
        foreach ($ARDUPINMAP as $logicalId => $pin) {
            arduidom::getPinValue($logicalId);
        }
    }

    public static function start() {
        self::setPinMapping();
    }

    public static function setPinMapping() {
        global $ARDUPINMAP;
        $CP = "CP";
        foreach ($ARDUPINMAP as $logicalId => $pin) {
            $config = config::byKey('pin::' . $logicalId, 'arduidom');
            log::add('arduidom', 'debug', 'setPinMapping(' . $logicalId . ') ' . $config);
            if ($config == '') { $CP = $CP . "z";}
            if ($config == 'disable') { $CP = $CP . "z";}
            if ($config == 'in') { $CP = $CP . "i";}
            if ($config == 'out') { $CP = $CP . "o";}
            if ($config == 'rin') { $CP = $CP . "r";}
            if ($config == 'rout') { $CP = $CP . "t";}
            if ($config == 'pout') { $CP = $CP . "p";}
            if ($config == 'ain') { $CP = $CP . "a";}
        }
        log::add('arduidom', 'debug', 'setPinMapping to ' . $CP);
        arduidom::sendtoArduino($CP);
    }


    public static function getPinValue($_logicalId) {
        log::add('arduidom', 'debug', 'getPinValue(' . $_logicalId . ') return ');
        $tcpmsg = "GP" . sprintf("%02s", $_logicalId);
        $tcpcheck = arduidom::sendtoArduino($tcpmsg);
        $tcpcheck = str_replace($tcpmsg,'',$tcpcheck);
        $tcpcheck = str_replace("=",'',$tcpcheck);
        $tcpcheck = str_replace("_OK",'',$tcpcheck);
        //throw new Exception(__("Info TCP [" . $tcpcheck . "]", __FILE__));
        return $tcpcheck;
    }

    public static function setPinValue($_logicalId, $_value) {
        log::add('arduidom', 'debug', 'setPinValue(' . $_logicalId . ',' . $_value . ')');
        $tcpmsg = "SP" . sprintf("%02s", $_logicalId) . $_value;
        $tcpcheck = arduidom::sendtoArduino($tcpmsg);
        if ($tcpcheck != $tcpmsg . "_OK") {
            throw new Exception(__("Erreur setPinValue " . $tcpcheck, __FILE__));
        }

    }

    public static function sendtoArduino($_tcpmsg) {
        $fp = fsockopen("127.0.0.1", 58174, $errno, $errstr, 1);
        if (!$fp) {
            if ($errno == 111) {
                throw new Exception(__("Le démon ArduiDom n'est pas lancé, La configuration n'a pas été envoyée a l'arduino", __FILE__));
            } else {
                throw new Exception(__("Erreur de communication avec le démon ArduiDom " . $errstr . $errno, __FILE__));
            }
        } else {
            fwrite($fp, $_tcpmsg);
            while (!feof($fp)) {
                $resp = fgets($fp);
                $_tcpmsg = str_replace('\n', '', $_tcpmsg);
                if (strpos($resp,'_OK') == false) {
                    throw new Exception(__("Erreur: Réponse du démon ArduiDom = " . $resp . " - attendu:" . $_tcpmsg . "_OK", __FILE__));
                    log::add('arduidom', 'debug', "Erreur: Réponse du démon ArduiDom = " . $resp . " - attendu:" . $_tcpmsg . "_OK");
                } else {
                    return $resp;
                }
            }
            fclose($fp);
        }
    }

    public function event() {
        self::pull();
        //echo "OK\n";
    }
    /*     * *********************Methode d'instance************************* */


    /*     * **********************Getteur Setteur*************************** */
}

class arduidomCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */

    public function preSave() {
        $this->setEventOnly(1);
    }

    public function postSave() {
        if ($this->getType() == 'info') {
            $this->event($this->execute());
        }
    }

    public function execute($_options = null) {
        if ($this->getType() == 'action') {
            log::add('arduidom', 'debug', '1cmd(action) called');
            //log::add('arduidom', 'debug', '2cmd(action) return ' . arduidom::setPinValue($this->getLogicalId(), $this->getConfiguration('value')));
            return arduidom::setPinValue($this->getLogicalId(), $this->getConfiguration('value'));
        }
        if ($this->getType() == 'info') {
            log::add('arduidom', 'debug', '1cmd(info) called');
            //log::add('arduidom', 'debug', '2cmd(info) return ' . arduidom::getPinValue($this->getLogicalId()));
            return arduidom::getPinValue($this->getLogicalId());
        }

        log::add('arduidom', 'debug', 'foreach...');
        foreach (eqLogic::byType('arduidom') as $eqLogic){
            log::add('arduidom', 'debug', 'by type arduidom');
            foreach ($eqLogic->getCmd('info') as $cmd) {
                log::add('arduidom', 'debug', 'getCmd info');
                if (array_key_exists($cmd->getConfiguration('value'), $_GET)) {
                    log::add('arduidom', 'debug', 'with value');
                    log::add('arduidom', 'debug', 'Mise à jour de : ' . $cmd->getConfiguration('value') . ':'. $_GET[$cmd->getConfiguration('value')]);
                    $cmd->setValue($_GET[$cmd->getConfiguration('value')]);
                    $cmd->event($_GET[$cmd->getConfiguration('value')]);
                }
            }
            log::add('arduidom', 'event', 'Mise à jout de ' . $eqLogic->getHumanName() . ' terminée');
        }


    }



    /*     * **********************Getteur Setteur*************************** */
}
