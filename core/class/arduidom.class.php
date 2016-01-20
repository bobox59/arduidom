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

class arduidom extends eqLogic
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    public static function MigrateDatas()
    {
        //log::add('arduidom', 'debug', 'MigrateDatas() called'
        log::add('arduidom', 'info', "*************************************************************************");
        log::add('arduidom', 'info', "* Début de la migration des données arduidom pour la mise a jour > 1.07 *");
        log::add('arduidom', 'info', "*************************************************************************");
        self::stopdaemon();

        $model = config::byKey('model', 'arduidom');
        if ($model == "bobox59" || $model == "uno" || $model == "duemilanove328" || $model == "leo" || $model == "nano168" || $model == "nano328") {
            for ($i = 0; $i < 100; $i++) { // Migration des configs de pins DIGI et ANA
                $tomigrate = 'pin::' . $i;
                $DataToMigrate = config::byKey($tomigrate, 'arduidom', '');
                if ($DataToMigrate != '') {
                    log::add('arduidom', 'info', "Migration de " . $tomigrate . " vers A1_" . $tomigrate . "...");
                    config::save('A1_' . $tomigrate, $DataToMigrate, 'arduidom');
                    log::add('arduidom', 'info', "Suppression de " . $tomigrate . "...");
                    config::remove($tomigrate, 'arduidom');
                }
            }
            for ($i = 101; $i < 130; $i++) { // Migration des configs de pins DHT
                $tomigrate = 'pin::' . $i;
                $DataToMigrate = config::byKey($tomigrate, 'arduidom', '');
                if ($DataToMigrate != '') {
                    log::add('arduidom', 'info', "Migration de " . $tomigrate . " vers A1_" . 'pin::' . ($i + 400) . "...");
                    config::save('A1_' . 'pin::' . ($i + 400), $DataToMigrate, 'arduidom');
                    log::add('arduidom', 'info', "Suppression de " . $tomigrate . "...");
                    config::remove($tomigrate, 'arduidom');
                }
            }
        }
        if ($model == "mega1280" || $model == "mega2560") {
        }
        if ($model == "due") {
        }
        $tomigrate = "port"; // Migration de la config du Port du 1er Arduino
        $DataToMigrate = config::byKey($tomigrate, 'arduidom', '');
        if ($DataToMigrate != '') {
            log::add('arduidom', 'info', "Migration de " . $tomigrate . " vers A1_" . $tomigrate . "...");
            $DataToMigrate = config::byKey($tomigrate, 'arduidom', 'none');
            config::save('A1_' . $tomigrate, $DataToMigrate, 'arduidom');
            config::save('A2_' . $tomigrate, "none", 'arduidom');
            config::save('A3_' . $tomigrate, "none", 'arduidom');
            config::save('A4_' . $tomigrate, "none", 'arduidom');
            config::save('A5_' . $tomigrate, "none", 'arduidom');
            config::save('A6_' . $tomigrate, "none", 'arduidom');
            config::save('A7_' . $tomigrate, "none", 'arduidom');
            config::save('A8_' . $tomigrate, "none", 'arduidom');
            log::add('arduidom', 'info', "Suppression de " . $tomigrate . "...");
            config::remove($tomigrate, 'arduidom');
        }

        $tomigrate = "daemonip"; // Migration de la config du Daemon du 1er Arduino
        $DataToMigrate = config::byKey($tomigrate, 'arduidom', '');
        if ($DataToMigrate != '') {
            //log::add('arduidom', 'info', "Migration de " . $tomigrate . " vers A1_" . $tomigrate . "...");
            //$DataToMigrate = config::byKey($tomigrate, 'arduidom', 'none');
            log::add('arduidom', 'info', "Suppression de " . $tomigrate . "...");
            config::remove($tomigrate, 'arduidom');
        }
        config::save('A1_' . $tomigrate, "127.0.0.1", 'arduidom');
        config::save('A2_' . $tomigrate, "127.0.0.1", 'arduidom');
        config::save('A3_' . $tomigrate, "127.0.0.1", 'arduidom');
        config::save('A4_' . $tomigrate, "127.0.0.1", 'arduidom');
        config::save('A5_' . $tomigrate, "127.0.0.1", 'arduidom');
        config::save('A6_' . $tomigrate, "127.0.0.1", 'arduidom');
        config::save('A7_' . $tomigrate, "127.0.0.1", 'arduidom');
        config::save('A8_' . $tomigrate, "127.0.0.1", 'arduidom');

        $tomigrate = "daemonenable"; // Migration de la config du StopDaemon du 1er Arduino
        config::save('A1_' . $tomigrate, 0, 'arduidom');
        config::save('A2_' . $tomigrate, 0, 'arduidom');
        config::save('A3_' . $tomigrate, 0, 'arduidom');
        config::save('A4_' . $tomigrate, 0, 'arduidom');
        config::save('A5_' . $tomigrate, 0, 'arduidom');
        config::save('A6_' . $tomigrate, 0, 'arduidom');
        config::save('A7_' . $tomigrate, 0, 'arduidom');
        config::save('A8_' . $tomigrate, 0, 'arduidom');

        $tomigrate = "model"; // Migration de la config du Modele du 1er Arduino
        $DataToMigrate = config::byKey($tomigrate, 'arduidom', '');
        if ($DataToMigrate != '') {
            log::add('arduidom', 'info', "Migration de " . $tomigrate . " vers A1_" . $tomigrate . "...");
            $DataToMigrate = config::byKey($tomigrate, 'arduidom', 'none');
            config::save('A1_' . $tomigrate, $DataToMigrate, 'arduidom');
            config::save('A2_' . $tomigrate, "", 'arduidom');
            config::save('A3_' . $tomigrate, "", 'arduidom');
            config::save('A4_' . $tomigrate, "", 'arduidom');
            config::save('A5_' . $tomigrate, "", 'arduidom');
            config::save('A6_' . $tomigrate, "", 'arduidom');
            config::save('A7_' . $tomigrate, "", 'arduidom');
            config::save('A8_' . $tomigrate, "", 'arduidom');
            log::add('arduidom', 'info', "Suppression de " . $tomigrate . "...");
            config::remove($tomigrate, 'arduidom');
        }


        foreach (eqLogic::byType('arduidom') as $eqLogic) { // Migration des LogicalPin du 1er Arduino
            foreach ($eqLogic->getCmd() as $cmd) {
                if ($cmd->getLogicalId() < 101) {
                    $newvalue = ($cmd->getLogicalId() + 1000);
                    log::add('arduidom', 'info', '$$MIGRATION$$ ' . $cmd->getLogicalId() . ' => ' . $newvalue . ' pour l equipement ' . $cmd->getHumanName());
                    $cmd->setLogicalId($newvalue);
                    $cmd->save();
                }
                if ($cmd->getLogicalId() > 100 && $cmd->getLogicalId() < 1000) {
                    $newvalue = ($cmd->getLogicalId() + 1400);
                    log::add('arduidom', 'info', '$$MIGRATION$$ ' . $cmd->getLogicalId() . ' => ' . $newvalue . ' pour l equipement ' . $cmd->getHumanName());
                    $cmd->setLogicalId($newvalue);
                    $cmd->save();
                }
            }
        }

        $oldlogfile = realpath(dirname(__FILE__)) . '/../../../../log/arduidom_daemon';
        log::add('arduidom', 'info', "Delete old log file " . $oldlogfile . " => " . unlink($oldlogfile));
        config::save('db_version', 108, 'arduidom'); // Inscrit la version de migration dans la config
        log::add('arduidom', 'error', "Migration des données pour v1.08 Terminée.");
        self::startdaemon();
        return 1;
    }


    public static function getUsbArduinos($_name = '')
    {
        $cache = cache::byKey('arduidom::usbMapping');
        if (!is_json($cache->getValue()) || $_name == '') {
            $usbMapping = array();
            foreach (ls('/dev/serial/by-path/', '*') as $usb) {
                $vendor = '';
                $model = '';
                $serialnb = '';
                foreach (explode("\n", shell_exec('udevadm info --name=/dev/serial/by-path/' . $usb . ' --query=all')) as $line) {
                    if (strpos($line, 'E: ID_MODEL_FROM_DATABASE=') !== false) {
                        $model = trim(str_replace(array('E: ID_MODEL_FROM_DATABASE=', '"'), '', $line));
                    }
                    if (strpos($line, 'E: ID_VENDOR_FROM_DATABASE=') !== false) {
                        $vendor = trim(str_replace(array('E: ID_VENDOR_FROM_DATABASE=', '"'), '', $line));
                    }
                    if (strpos($line, 'E: ID_SERIAL_SHORT=') !== false) {
                        $serialnb = trim(str_replace(array('E: ID_SERIAL_SHORT=', '"'), '', $line));
                    }
                }
                if ($vendor == '' && $model == '') {
                    $usbMapping['/dev/serial/by-path/' . $usb] = '/dev/serial/by-path/' . $usb;
                } else {
                    $name = trim($vendor . ' - ' . $model . ' (S/N=' . $serialnb . ')');
                    $number = 2;
                    while (isset($usbMapping[$name])) {
                        $name = trim($vendor . ' - ' . $model . ' (S/N=' . $serialnb . ')' . ' ' . $number);
                        $number++;
                    }
                    $usbMapping[$name] = '/dev/serial/by-path/' . $usb;
                }
            }
            cache::set('arduidom::usbMapping', json_encode($usbMapping), 0);
        } else {
            $usbMapping = json_decode($cache->getValue(), true);
        }
        if ($_name != '') {
            if (isset($usbMapping[$_name])) {
                return $usbMapping[$_name];
            }

            $usbMapping = array();
            foreach (ls('/dev/serial/by-path/', '*') as $usb) {
                $vendor = '';
                $model = '';
                $serialnb = '';
                foreach (explode("\n", shell_exec('udevadm info --name=/dev/serial/by-path/' . $usb . ' --query=all')) as $line) {
                    if (strpos($line, 'E: ID_MODEL_FROM_DATABASE=') !== false) {
                        $model = trim(str_replace(array('E: ID_MODEL_FROM_DATABASE=', '"'), '', $line));
                    }
                    if (strpos($line, 'E: ID_VENDOR_FROM_DATABASE=') !== false) {
                        $vendor = trim(str_replace(array('E: ID_VENDOR_FROM_DATABASE=', '"'), '', $line));
                    }
                    if (strpos($line, 'E: ID_SERIAL_SHORT=') !== false) {
                        $serialnb = trim(str_replace(array('E: ID_SERIAL_SHORT=', '"'), '', $line));
                    }
                }
                if ($vendor == '' && $model == '') {
                    $usbMapping['/dev/serial/by-path/' . $usb] = '/dev/serial/by-path/' . $usb;
                } else {
                    $name = trim($vendor . ' - ' . $model . ' (S/N=' . $serialnb . ')');
                    $number = 2;
                    while (isset($usbMapping[$name])) {
                        $name = trim($vendor . ' - ' . $model . ' (S/N=' . $serialnb . ')' . ' ' . $number);
                        $number++;
                    }
                    $usbMapping[$name] = '/dev/serial/by-path/' . $usb;
                }
            }
            cache::set('arduidom::usbMapping', json_encode($usbMapping), 0);
            if (isset($usbMapping[$_name])) {
                return $usbMapping[$_name];
            }
            if (file_exists($_name)) {
                return $_name;
            }
            return '';
        }
        return $usbMapping;
    }

    public static function dependancy_info() {
        $return = array();
        $return['log'] = 'arduidom_update';
        $return['progress_file'] = '/tmp/dependancy_arduidom_in_progress';
        if (file_exists("/usr/bin/arduino") && file_exists("/usr/local/bin/ino")) {
            $return['state'] = 'ok';
        } else {
            $return['state'] = 'nok';
        }
        return $return;
    }

    public static function dependancy_install() {
        log::remove('arduidom_update');
        $cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install.sh';
        $cmd .= ' >> ' . log::getPathToLog('arduidom_update') . ' 2>&1 &';
        exec($cmd);
    }

    public static function updateArduidom() {
        log::remove('arduidom_update');
        $cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install.sh';
        $cmd .= ' >> ' . log::getPathToLog('arduidom_update') . ' 2>&1 &';
        exec($cmd);
    }


    public static function start()
    {
        log::add('arduidom', 'debug', 'start() called');
        $port = config::byKey('A1_port', 'arduidom', 'none');
        if ($port != 'none') {
            self::startdaemon();
        }
    }


    public static function restartdaemon($_AID = '')
    {
        self::stopdaemon($_AID);
        $retour = self::startdaemon($_AID);
        for ($d = 1; $d < 9; $d++) {
            if ($_AID == '' || $_AID == $d) {
                self::restoreStates($d);
            }
        }
        return $retour;
    }


    public static function stopdaemon($_AID = '')
    {
        $General_Debug = (file_exists("/tmp/arduidom_debug_mode_on"));
        for ($d = 1; $d < 9; $d++) {
            if ($_AID == '' || $_AID == $d) {
                log::add('arduidom', 'info', 'Stop Daemon ' . $d);
                $daemon_path = realpath(dirname(__FILE__) . '/../../ressources');

                $pid_file = $daemon_path . "/arduidom" . $d . ".pid";
                if (file_exists($pid_file)) {
                    $pid = intval(trim(file_get_contents($pid_file)));
                    system::kill($pid);
                }
                system::fuserk(config::byKey('socketport', 'sms', intval((58200 + $d))));
                unlink($daemon_path . "/arduidom" . $d . ".pid");
            }

            // Ancienne methode a supprimer, conservée quelques temps pour compatibilité avec version avant 30 déc 2015
            $result = exec("ps aux | grep arduidom" . $d . ".py | grep -v grep | awk '{print $2}'");
            if ($result != "") log::add('arduidom', 'debug', 'PIDs encore démarrés : ' . $result);

            if ($General_Debug) log::add('arduidom', 'debug', 'Desactivation du démon ' . $d . '...');
            config::save('A' . $d . "_daemonenable", 0, 'arduidom');

        }
    return ("OK");
}

public static function startdaemon($_AID = '')
{
    $General_Debug = file_exists("/tmp/arduidom_debug_mode_on");
    $daemon_path = realpath(dirname(__FILE__) . '/../../ressources');

    log::add('arduidom', 'debug', 'startdaemon(' . $_AID . ').');
    for ($d = 1; $d < 9; $d++) {
        if ($_AID == '' || $_AID == $d) {
            $model = config::byKey('A' . $d . '_model', 'arduidom', '');
            $port = config::byKey('A' . $d . '_port', 'arduidom', '');
            if ($port != 'none' && $model != 'none' && $port != '' && $model != '') {
                config::save('A' . $d . "_daemonenable", 1, 'arduidom');
                if (file_exists($port) || $port == "Network") {

                    if ($General_Debug) log::add('arduidom', 'debug', 'Tentative de démarrage du Démon arduidom ' . $d . '...');

                    if ($port != 'Network') {
                        unlink($daemon_path . '/arduidom' . $d . '.kill');
                        $cmd = 'nice -n 19 /usr/bin/python ' . $daemon_path . '/arduidomx.py' . " -p " . (58200 + $d) . " -d " . $port . " -l " . config::byKey('A' . $d . '_daemonlog', 'arduidom') . " -i " . $d . " -a " . config::byKey('api') . " -e " . config::byKey('A' . $d . '_daemonip', 'arduidom', "127.0.0.1");
                        log::add('arduidom', 'info', 'Lancement démon ' . $d . ' : ' . $cmd);
                        $result = exec($cmd . ' >> ' . log::getPathToLog('arduidom') . ' 2>&1 &');
                        log::add('arduidom', 'info', 'Lancement démon ' . $d . ' : result : ' . $result);
                        if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) {
                            //log::add('arduidom', 'error', $result);
                            if ($_AID == $d) return 0;
                        }
                        sleep(4);
                    }

                    $resp = self::sendtoArduino("PING", $d);
                    if (strpos($resp, '_OK') == false) {
                        log::add('arduidom', 'error', 'Erreur: Réponse du démon ' . $d . " = [" . $resp . "] au lieu de [PING_OK] (startdaemon)");
                        if ($_AID == $d) return 0;
                    } else {
                        log::add('arduidom', 'debug', 'Le Démon arduidom ' . $d . ' a bien démarré.');
                        self::setPinMapping($d);
                        //self::restoreStates($d);
                        if ($_AID == $d) return 1;
                    }
                } else {
                    log::add('arduidom', 'error', __("Le port ou modèle de l'Arduino " . $d . " n'est pas configuré", __FILE__), 'noArduinoComPort');
                    if ($_AID == $d) return 0;
                }
                message::removeAll('arduidom', 'noArduinoComPort');
            } else {
                log::add('arduidom', 'error', __("Verifier que l'arduino " . $d . " est bien branché et reconnu", __FILE__), 'noArduinoComPort');
                if ($_AID == $d) return 0;
            }
        }
    }
    return 1;
}


    public static function checkdaemon($_AID)
{
    $General_Debug = file_exists("/tmp/arduidom_debug_mode_on");
    if ($General_Debug) log::add('arduidom', 'debug', "checkdaemon(" . $_AID . ")");

    $MigrationCheck = config::byKey('db_version', 'arduidom', 0);
    if ($MigrationCheck < 108) {
        if ($General_Debug) log::add('arduidom', 'info', "La version de la base de données Arduidom n'est pas la bonne...");
        log::add('arduidom', 'error', "Une Migration des données Arduidom est necessaire depuis la v1.08 ! vous pouvez l'executer depuis la configuration du Plugin");
    }

    $arduinoQty = config::byKey('ArduinoQty', 'arduidom', 0);
    for ($d = 1; $d <= $arduinoQty; $d++) {
        if ($_AID == '' || $_AID == $d) {
            $DaemonEnabled = config::byKey('A' . $d . "_daemonenable", 'arduidom', 0);
            if ($DaemonEnabled == 0) {
                if ($General_Debug) log::add('arduidom', 'debug', 'Le démon ' . $d . ' ne sera pas verifié car il a été stoppé manuellement ou Désactivé');
                if ($_AID == $d) return 0;
            } else {
                $tcpcheck = arduidom::sendtoArduino("PING", $d);
                if ($tcpcheck != "PING_OK") {
                    log::add('arduidom', 'error', "Erreur: Réponse du démon " . $d . " = [" . $tcpcheck . "] au lieu de [PING_OK] (checkdaemon)");
                    log::add('arduidom', 'error', "Redémarrage Automatique du démon " . $d . " (checkdaemon)");
                    arduidom::stopdaemon($d);
                    sleep(1);
                    if ($_AID == $d) {
                        return arduidom::startdaemon($d);
                    } else {
                        arduidom::startdaemon($d);
                    }
                } else {
                    if ($General_Debug) log::add('arduidom', 'debug', 'Le démon ' . $d . ' fonctionne correctement.');
                    self::sendtoArduino("RF",$d);
                    if ($_AID == $d) return 1;
                }
            }
        }
    }
    return 1;
}


    public static function setPinMapping($_AID)
{
    $General_Debug = (file_exists("/tmp/arduidom_debug_mode_on"));
    global $ARDUPINMAP_A, $ARDUPINMAP_B, $ARDUPINMAP_C;
    log::add('arduidom', 'debug', 'setPinMapping(' . $_AID . ') called');
    //sleep(2);
    $CP = "CPzz";
    $modelPinMap = config::byKey('A' . $_AID . '_model', 'arduidom', 'none');
    $ARDUPINMAP = '';
    if ($modelPinMap == "bobox59" || $modelPinMap == "uno" || $modelPinMap == "duemilanove328" || $modelPinMap == "leo" || $modelPinMap == "nano168" || $modelPinMap == "nano328") $ARDUPINMAP = $ARDUPINMAP_A;
    if ($modelPinMap == "mega1280" || $modelPinMap == "mega2560") $ARDUPINMAP = $ARDUPINMAP_B;
    if ($modelPinMap == "due") $ARDUPINMAP = $ARDUPINMAP_C;
    foreach ($ARDUPINMAP as $logicalId => $pin) {
        if ($logicalId > 1) {
            $config = config::byKey('A' . $_AID . '_pin::' . $logicalId, 'arduidom');
            if ($General_Debug) log::add('arduidom', 'debug', 'setPinMapping(' . $logicalId . ') ' . $config);
            if ($config == '') {
                $CP = $CP . "z";
            } // Si Modifs, penser a les mettres aussi dans ajax !
            if ($config == 'disable') {
                $CP = $CP . "z";
            }
            if ($config == 'in') {
                $CP = $CP . "i";
            }
            if ($config == 'out') {
                $CP = $CP . "o";
            }
            if ($config == 'rin') {
                $CP = $CP . "r";
            }
            if ($config == 'rout') {
                $CP = $CP . "t";
            }
            if ($config == 'pout') {
                $CP = $CP . "p";
            }
            if ($config == 'ain') {
                $CP = $CP . "a";
            }
            if ($config == 'custin') {
                $CP = $CP . "c";
            }
            if ($config == 'custout') {
                $CP = $CP . "d";
            }
        }
    }
    if ($General_Debug) log::add('arduidom', 'debug', 'setPinMapping to ' . $CP);
    $tcp_check = self::sendtoArduino($CP, $_AID);
    if (config::byKey('A' . $_AID . '_port', 'arduidom', 'none') == "Network") { // Envoi de la clé API aux arduino ethernet
        if ($tcp_check != "CP_OK") {
            log::add('arduidom', 'error', "Erreur lors de l'envoi du CP a l'arduino [" . $tcp_check . "] != [CP_OK]");
            return ("BAD");
        }
        sleep(2);
        $tcp_check = self::sendtoArduino("AP" . config::byKey('api'), $_AID);
        if ($tcp_check != "AP" . config::byKey('api') . "_OK") {
            log::add('arduidom', 'error', "Erreur lors de l'envoi de l'api a l'arduino ethernet (" . $tcp_check . ")");
            return ("BAD");
        }
    } else {
        //if ($tcp_check != $CP . "_OK") {
        if ($tcp_check != "CP_OK") {
            log::add('arduidom', 'error', "Erreur lors de l'envoi du CP a l'arduino [" . $tcp_check . "] != [CP_OK]");
            return ("BAD");
        }
    }
    return ("OK");
}


    public static function restoreStates($_AID)
{
    log::add('arduidom', 'debug', 'restoreStates(' . $_AID . ') called');
    $array[] = "";
    foreach (eqLogic::byType('arduidom') as $eqLogic) {
        foreach ($eqLogic->getCmd('action') as $cmd) {
            $pin_nb = $cmd->getLogicalId();
            $ArduinoID = intval(substr($pin_nb, 0, 1));
            if (!in_array($pin_nb, $array) && $_AID == $ArduinoID){
                array_push($array, $pin_nb);
                $pin_nb_reduce = intval(substr($pin_nb, 1));
                //for ($pin_nb_reduce = $pin_nb; $pin_nb_reduce > 1000; $pin_nb_reduce = $pin_nb_reduce - 1000);
                $pinmode = config::byKey('A' . $_AID . '_pin::' . $pin_nb_reduce, 'arduidom');
                if ($pinmode == "out" || $pinmode == "pout" || $pinmode == "custout") {
                    $cachekey = 'arduidom::lastSetPin' . $pin_nb;
                    $cache1 = cache::byKey($cachekey, "");
                    $pin_last_value = $cache1->getValue();
                    if (($pin_last_value != 0 && $pin_last_value != "0")) {
                        self::setPinValue($pin_nb, $pin_last_value);
                    }
                }
            }
        }
    }
    return ("OK");
}


    public static function setPinValue($_logicalId, $_value)
{
    $arduid = 0;
    if ($_logicalId > 999) {
        $arduid = $_logicalId[0];
        $_logicalId = intval(substr($_logicalId, 1));
    }
    $cachekey = "arduidom::lastSetPin" . (intval($arduid) * 1000 + intval($_logicalId));
    cache::set($cachekey, $_value, 0);
    log::add('arduidom','debug', '   cache::set(' . $cachekey . ',' . $_value . ')');
    $tcpmsg = "";
    log::add('arduidom', 'debug', 'setPinValue(' . $_logicalId . ',' . $_value . ') for arduino ' . $arduid);
    $config = config::byKey('A' . $arduid . '_pin::' . $_logicalId, 'arduidom');
    log::add('arduidom','debug', '   $config=' . $config);
    if ($config == 'disable') {
        $tcpmsg = "SP" . sprintf("%02s", $_logicalId) . $_value;
    }
    if ($config == 'out') {
        $tcpmsg = "SP" . sprintf("%02s", $_logicalId) . $_value;
    }
    if ($config == 'rout') {
        $tcpmsg = "SP" . sprintf("%02s", $_logicalId) . $_value;
    }
    if ($config == 'pout') {
        $tcpmsg = "SP" . sprintf("%02s", $_logicalId) . sprintf("%03s", $_value);
    }
    if ($config == 'custout') {
        $tcpmsg = "SP" . sprintf("%02s", $_logicalId) . sprintf("%010s", $_value);
    }
    $tcpcheck = arduidom::sendtoArduino($tcpmsg, $arduid);
    if ($tcpcheck != "SP_OK") {
        // if ($tcpcheck != $tcpmsg . "_OK") {
        log::add('arduidom','error', "Erreur setPinValue " . $tcpcheck . " (Recu : " . $tcpmsg . ")");
        throw new Exception(__("Erreur setPinValue " . $tcpcheck . " (Recu : " . $tcpmsg . ")", __FILE__));
    }
    return $tcpcheck;
}


    public static function sendtoArduino($_tcpmsg, $_AID)
{
    $General_Debug = (file_exists("/tmp/arduidom_debug_mode_on"));
    if ($General_Debug) log::add('arduidom', 'debug', 'sendtoArduino(' . $_tcpmsg . ',' . $_AID . ') called');

    $DaemonEnabled = config::byKey('A' . $_AID . "_daemonenable", 'arduidom', 0);
    if ($DaemonEnabled == 0) {
        log::add('arduidom', 'debug', 'sendtoArduino(' . $_tcpmsg . ',' . $_AID . ') IMPOSSIBLE, Le démon ' . $_AID . ' est stoppé ou Désactivé');
    } else {


        $port = config::byKey('A' . $_AID . '_port', 'arduidom', 'none');
        $ip = config::byKey('A' . $_AID . '_daemonip', 'arduidom', '127.0.0.1');
        if ($port != 'none' && $port != "") {
            if (file_exists($port) || $port == 'Network') {
                if ($port != 'Network') {
                    if ($General_Debug) log::add('arduidom', 'debug', 'Le démon ' . $_AID . ' est un démon python');
                    $fp = fsockopen($ip, (58200 + intval($_AID)), $errno, $errstr, 1);
                } else {
                    if ($General_Debug) log::add('arduidom', 'debug', 'Le démon ' . $_AID . ' est un arduino Réseau (IP:' . $ip . ")");
                    $fp = fsockopen($ip, (58174), $errno, $errstr, 1);
                }

                stream_set_timeout($fp, 7);

                if (!$fp) {
                    //if ($General_Debug) log::add('arduidom', 'error', "Le démon ArduiDom " . $_AID . " n'est pas connecté ! (connection impossible)");
                    //if ($General_Debug) log::add('arduidom', 'error', "Le démon ArduiDom " . $_AID . " n'est pas connecté ! (errno:)" . $errno);
                    log::add('arduidom', 'error', "Le démon ArduiDom " . $_AID . " n'est pas connecté ! (errstr:)" . $errstr);
                    return $errstr;

                } else {

                    if ($General_Debug) log::add('arduidom', 'debug', "Le démon ArduiDom " . $_AID . " est connecté, envoi...");

                    if ($port == "Network") $_tcpmsg = $_tcpmsg . "\n"; // ajout d'une fin de ligne pour shield ethernet
                    fwrite($fp, $_tcpmsg);
                    $start_time = time();
                    $resp = "";

                    if ($port == "Network") {
                        if ($General_Debug) log::add('arduidom', 'debug', "Attente de la réponse du démon ArduiDom " . $_AID . " ...");
                        while (!feof($fp)) {
                            $resp = $resp . fgets($fp);
                        }
                        $resp = str_replace("\r", '', $resp);
                        $resp = str_replace("\n", '', $resp);
                        if ($General_Debug) log::add('arduidom', 'debug', 'Réponse TCP recue  $_tcpmsg=' . $resp);
                        if ((time() - $start_time) > 7) { // Time out de 7 secondes pour le check du démon
                            log::add('arduidom', 'error', 'Erreur: TIMEOUT sur Réponse du démon ' . $_AID);
                            return "TIMEOUT";
                        }
                        fclose($fp);

                    } else {

                        while (!feof($fp)) {
                            $resp = fgets($fp);
                            $_tcpmsg = str_replace("\n", '', $_tcpmsg);
                            if ($General_Debug) log::add('arduidom', 'debug', 'Debug: $_tcpmsg=' . $_tcpmsg);

                            if ((time() - $start_time) > 7) { // Time out de 7 secondes pour le check du démon
                                log::add('arduidom', 'error', 'Erreur: TIMEOUT sur Réponse du démon ' . $_AID);
                                return "TIMEOUT";
                            }
                        }
                        fclose($fp);
                    }
                    return $resp;
                }


            } else {
                log::add('arduidom', 'error', __('Le port Arduino (' . $port . ')est vide ou n\'existe pas', __FILE__), 'noArduinoComPort');
            }
        }
    }
}


    public static function CompileArduino($_AID = '')
{
    $result = 0;
    log::add('arduidom', 'info', 'compilearduino(' . $_AID . ') called');
    if (!file_exists("/usr/local/bin/ino")) {
        throw new Exception(__("le programme ino n'est pas installé ! (installer avec 'sudo easy_install ino')", __FILE__));
    }
    if (!file_exists("/usr/share/arduino")) {
        throw new Exception(__("le programme arduino n'est pas installé ! (installer avec 'sudo apt-get install arduino')", __FILE__));
    }
    $daemon_path = realpath(dirname(__FILE__) . '/../../ressources');
    $model = config::byKey('A' . $_AID . '_model', 'arduidom', 'none');
    $port = config::byKey('A' . $_AID . '_port', 'arduidom', 'none');
    switch ($model) {
        case "bobox59":
            $cmd_model = "uno";
            break;
        case "uno":
            $cmd_model = "uno";
            break;
        case "duemilanove328":
            $cmd_model = "atmega328";
            break;
        case "nano328":
            $cmd_model = "nano328";
            break;
        case "mega2560":
            $cmd_model = "mega2560";
            break;
        case "mega1280":
            $cmd_model = "mega";
            break;
        default:
            throw new Exception(__("Modele Arduino " . $model . " n'est pas supporté par la fonction !", __FILE__));
            break;
    }
    exec("sudo echo '' > " . $daemon_path . "/../../../log/arduidom_log" . $_AID); // Clear log file
    $cmd = 'cd ' . $daemon_path . '/arduidomTest && sudo ino clean && sudo ino build -m ' . $cmd_model . " >> " . $daemon_path . "/../../../log/arduidom_log" . $_AID . " 2>&1 &";
    log::add('arduidom', 'info', 'Compiling arduino ' . $_AID . ': ' . $cmd);
    //while (@ ob_end_flush()); // end all output buffers if any

    popen($cmd, 'r');
    /*

    echo '<pre>';
    while (!feof($proc))
    {
        echo fread($proc, 4096);
        @ flush();
    }
    echo '</pre>';

    */
    //$result = passthru($cmd); // . ' >> ' . log::getPathToLog('arduidom')

    return $result;
}

    public static function FlashArduino($_AID = '')
{
    $result = 0;
    log::add('arduidom', 'info', '#############################flasharduino(' . $_AID . ') called');
    self::stopdaemon($_AID);
    if (!file_exists("/usr/bin/avrdude")) {
        throw new Exception(__("le programme avrdude n'est pas installé ! (installer avec apt-get install avrdude)", __FILE__));
    }
    $daemon_path = realpath(dirname(__FILE__) . '/../../ressources');
    $model = config::byKey('A' . $_AID . '_model', 'arduidom', 'none');
    $port = config::byKey('A' . $_AID . '_port', 'arduidom', 'none');
    switch ($model) {
        case "bobox59":
            $cmd_model = "atmega328p";
            $cmd_speed = "115200";
            $cmd_hexfile = "bobox59";
            break;
        case "uno":
            $cmd_model = "atmega328p";
            $cmd_speed = "115200";
            $cmd_hexfile = "uno";
            break;
        case "duemilanove328":
            $cmd_model = "atmega328p";
            $cmd_speed = "57600";
            $cmd_hexfile = "duemilanove328";
            break;
        case "nano328":
            $cmd_model = "atmega328p";
            $cmd_speed = "57600";
            $cmd_hexfile = "nano328";
            break;
        case "mega2560":
            $cmd_model = "atmega2560";
            $cmd_speed = "115200";
            $cmd_hexfile = "mega2560";
            break;
        case "mega1280":
            $cmd_model = "atmega1280";
            $cmd_speed = "57600";
            $cmd_hexfile = "mega1280";
            break;
        default:
            throw new Exception(__("Modele Arduino " . $model . " n'est pas supporté par la fonction !", __FILE__));
            break;
    }
    $cmd = 'avrdude -C/etc/avrdude.conf -v -v -v -p' . $cmd_model . ' -carduino -P' . $port . ' -b' . $cmd_speed . ' -D -Uflash:w:' . $daemon_path . '/' . $cmd_hexfile . '.hex:i' . " >> " . $daemon_path . "/../../../log/arduidom_log" . $_AID;
    log::add('arduidom', 'info', 'Flashing arduino ' . $_AID . ': ' . $cmd);

    //$result = exec("sudo " . $cmd . " 2>&1");
    //@exec("sudo /path/to/osascript myscript.scpt ");
    log::add('arduidom', 'info', '############################# Launch popen...');
    popen($cmd, 'r');
    //log::add('arduidom', 'info', '############################# startdaemon(' . $_AID . ')');
    //self::startdaemon($_AID);
    log::add('arduidom', 'info', '#############################return ' . $result);

    return $result;
}


    public function event()
{
    log::add('arduidom', 'debug', 'arduidom event() called');
    //self::pull();
}
    /*     * *********************Methode d'instance************************* */


    /*     * **********************Getteur Setteur*************************** */
}

class arduidomCmd extends cmd
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */

    public function preSave()
    {
        $this->setEventOnly(1);
    }

    public function postSave()
    {
        if ($this->getType() == 'info') {
            $this->event($this->execute());
        }
    }

    public function execute($_options = null)
    {
        //file_put_contents("/tmp/arduidom2", time() . " :  arduidom_execute" . "\n", FILE_APPEND);
        //log::add('arduidom', 'debug', 'execute(' . $_options['slider'] . ') called');
        //exec('echo "execute 2" >> /tmp/arduidom');
        if ($this->getType() == 'action') {
            try {
                //exec('echo "execute 3" >> /tmp/arduidom');
                //log::add('arduidom', 'debug', 'execute() called on type action');
                //exec('echo "execute 4" >> /tmp/arduidom');


                if ($this->getSubType() == 'slider') {
                    $value = str_replace('#slider#', $_options['slider'], $this->getConfiguration('value'));
                    return arduidom::setPinValue($this->getLogicalId(), $value);
                }

                if ($this->getSubType() == 'color') {
                    $value = str_replace('#color#', $_options['color'], $this->getConfiguration('value'));
                    return arduidom::setPinValue($this->getLogicalId(), $value);
                }
                if ($this->getSubType() == 'other') {
                    //GROS LAG RETIRE ICI - VERIFIER QUE CA N'A PAS D'INCIDENCE SUR LES FORMULES EN VALEURS//

                    $value = jeedom::evaluateExpression($this->getConfiguration('value'));
                    return arduidom::setPinValue($this->getLogicalId(), $value);
                }
            } catch (Exception $e) {
                //exec('echo "execute 5" >> /tmp/arduidom');
                //log::add('arduidom', 'debug', 'execute() ' . $e);
                return "bad";
            }
        }
        /*if ($this->getType() == 'info') {
            try{
                //exec('echo "execute 6" >> /tmp/arduidom');
                //log::add('arduidom', 'debug', 'execute() called on type info');
                return arduidom::getPinValue($this->getLogicalId());
            } catch (Exception $e) {
                //exec('echo "execute 7" >> /tmp/arduidom');
                //log::add('arduidom', 'debug', 'execute() ' . $e);
                return "bad";
            }
        }*/
        /*

        throw new Exception(__("Erreur: XXXXXXXXXXXXXXXXX", __FILE__));
        log::add('arduidom', 'debug', 'foreach.....................');
        foreach (eqLogic::byType('arduidom') as $eqLogic){
            log::add('arduidom', 'debug', 'by type arduidom');
            foreach ($eqLogic->getCmd('info') as $cmd) {
                log::add('arduidom', 'debug', 'getCmd info');
                if (array_key_exists($cmd->getConfiguration('value'), $_GET)) {
                    log::add('arduidom', 'debug', 'with value');
                    log::add('arduidom', 'debug', 'Mise à jour de : ' . $cmd->getConfiguration('value') . ':'. $_GET[$cmd->getConfiguration('value')]);
                    $cmd->setValue($_GET[$cmd->getConfiguration('value')]);
                    $cmd->event($_GET[$cmd->getConfiguration('value')]);
                    $cmd->save();
                }
            }
            log::add('arduidom', 'event', 'Mise à jour de ' . $eqLogic->getHumanName() . ' terminée');
        }
        */

        return "BAD";
    }


    /*     * **********************Getteur Setteur*************************** */
}
