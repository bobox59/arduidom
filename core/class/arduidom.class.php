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
        self::deamon_stop();

        $model = config::byKey('model', 'arduidom');
        if ($model == "uno" || $model == "duemilanove328" || $model == "leo" || $model == "nano168" || $model == "nano328") {
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
        log::add('arduidom', 'info', "Migration des données pour v1.08 Terminée.");
        log::add('arduidom', 'debug', "startdaemon() [from MigrateDatas]");
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

    public static function dependancy_info()
    {
        $return = array();
        $return['log'] = 'arduidom_update';
        $return['progress_file'] = '/tmp/dependancy_arduidom_in_progress';
        if (file_exists("/usr/bin/arduino") && file_exists("/usr/bin/avrdude")) {
            $return['state'] = 'ok';
        } else {
            $return['state'] = 'nok';
        }
        return $return;
    }

    public static function dependancy_install()
    {
        config::save("ArduinoRequiredVersion","105","arduidom");
        log::remove('arduidom_update');
        $cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install.sh';
        $cmd .= ' >> ' . log::getPathToLog('arduidom_update') . ' 2>&1 &';
        exec($cmd);
    }

    public static function updateArduidom()
    {
        log::remove('arduidom_update');
        $cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install.sh';
        $cmd .= ' >> ' . log::getPathToLog('arduidom_update') . ' 2>&1 &';
        exec($cmd);
    }

    public static function deamon_info() { // Nouvelle methode de gestion des démons
        $General_Debug = file_exists("/tmp/arduidom_debug_mode_on");
        if ($General_Debug) log::add('arduidom', 'debug', "--------------------------------------------------------------------------------------");
        if ($General_Debug) log::add('arduidom', 'debug', "daemon_info() called.");
        $return = array();
        $return['log'] = 'arduidom';
        $return['state'] = 'nok';
        //$daemon_path = realpath(dirname(__FILE__) . '/../../ressources');
        //$pid_file = $daemon_path . "/arduidom" . "1" . ".pid";
        //if (file_exists($pid_file)) {
        //    if (posix_getsid(trim(file_get_contents($pid_file)))) {
        //        $return['state'] = 'ok';
        //    } else {
        //        unlink($pid_file);
        //    }
        //}
        if (self::checkdaemon("", false, true)) $return['state'] = 'ok';

        $return['launchable'] = 'ok';
        $d = 1; // SE BASE UNIQUEMENT SUR LE 1er ARDUINO ! a voir par la suite...
        $model = config::byKey('A' . $d . '_model', 'arduidom', '');
        $port = config::byKey('A' . $d . '_port', 'arduidom', '');
        if ($port != 'none' && $model != 'none' && $port != '' && $model != '') {
            if (file_exists($port) || $port == "Network") {
                if ($port != 'Network') {
                    //$port = jeedom::getUsbMapping($port);
                    if (@!file_exists($port)) {
                        $return['launchable'] = 'nok';
                        $return['launchable_message'] = __('Le port n\'est pas configuré', __FILE__);
                    }
                }
            } else {
                $return['launchable'] = 'nok';
                $return['launchable_message'] = __('Le port n\'est pas configuré', __FILE__);
            }
        } else {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('Le port ou modèle n\'est pas configuré', __FILE__);
        }


        return $return;
    }

    public static function deamon_start($_debug = false) {
        $General_Debug = file_exists("/tmp/arduidom_debug_mode_on");
        if ($General_Debug) log::add('arduidom', 'debug', "--------------------------------------------------------------------------------------");
        if ($General_Debug) log::add('arduidom', 'debug', "daemon_start(debug=$_debug)");
        self::deamon_stop();
        $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != 'ok') {
            throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
        }
        /*
                                                                                                    PARTIE A ETUDIER...
        $replace_config = array(
            '#device#' => $port,
            '#text_mode#' => (config::byKey('text_mode', 'sms') == 1) ? 'yes' : 'no',
            '#socketport#' => config::byKey('socketport', 'sms', 55002),
            '#pin#' => config::byKey('pin', 'sms', 'None'),
            '#smsc#' => config::byKey('smsc', 'sms', 'None'),
            '#log_path#' => log::getPathToLog('sms'),
            '#pid_path#' => '/tmp/sms.pid',
            '#serial_rate#' => config::byKey('serial_rate', 'sms', 115200),
        );
        if (config::byKey('jeeNetwork::mode') == 'slave') {
            $replace_config['#sockethost#'] = network::getNetworkAccess('internal', 'ip', '127.0.0.1');
            $replace_config['#trigger_url#'] = config::byKey('jeeNetwork::master::ip') . '/plugins/sms/core/php/jeeSMS.php';
            $replace_config['#apikey#'] = config::byKey('jeeNetwork::master::apikey');
        } else {
            $replace_config['#sockethost#'] = '127.0.0.1';
            $replace_config['#trigger_url#'] = network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/sms/core/php/jeeSMS.php';
            $replace_config['#apikey#'] = config::byKey('api');
        }
        $config = file_get_contents($sms_path . '/config_tmpl.xml');
        $config = template_replace($replace_config, $config);
        file_put_contents('/tmp/config_sms.xml', $config);
        chmod('/tmp/config_sms.xml', 0777);
        $cmd = '/usr/bin/python ' . $sms_path . '/smscmd.py -l -o /tmp/config_sms.xml';
        if ($_debug) {
            $cmd .= ' -D';
        }
        */
        if ($_debug) {
            file_put_contents("/tmp/arduidom_debug_mode_on","DEBUG");
            for ($d = 1; $d <= 8; $d++) {
                config::save("A" . $d . "_daemonlog", 1, "arduidom");
            }
        } else {
            unlink("/tmp/arduidom_debug_mode_on");
            for ($d = 1; $d <= 8; $d++) {
                config::save("A" . $d . "_daemonlog", 0, "arduidom");
            }
            //log::remove('arduidom'); A REMETTRE APRES TESTS
        }
        log::add('arduidom', 'info', 'Lancement démon arduidom...');
        log::add('arduidom', 'debug', "startdaemon() [from daemon_start()]");
        $daemonOK = self::startdaemon();
        /*
        $result = exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('smscmd') . ' 2>&1 &');
        if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) {
            log::add('smscmd', 'error', $result);
            return false;
        }
        */
        //$i = 0;
        //while ($i < 3) {
        //    $deamon_info = self::deamon_info();
        //    if ($deamon_info['state'] == 'ok') {
        //        break;
        //    }
        //    sleep(10);
        //    $i++;
        //}
        if ($daemonOK == 0) {
            log::add('arduidom', 'error', 'Impossible de lancer le démon arduidom, vérifiez le sketch et le port', 'unableStartDeamon');
            return false;
        }
        message::removeAll('arduidom', 'unableStartDeamon');
        log::add('arduidom', 'info', 'Démon arduidom lancé');
        return true;
    }

    public static function deamon_stop() {
        $General_Debug = file_exists("/tmp/arduidom_debug_mode_on");
        if ($General_Debug) log::add('arduidom', 'debug', "--------------------------------------------------------------------------------------");
        if ($General_Debug) log::add('arduidom', 'debug', "daemon_stop() called.");
        //$deamon_info = self::deamon_info();
        $nbArduinos = intval(config::byKey("ArduinoQty", "arduidom", 1));
        $daemon_path = realpath(dirname(__FILE__) . '/../../ressources');
        for ($d = 1; $d <= $nbArduinos; $d++) {
            config::save('A' . $d . "_daemonenable", 0, 'arduidom');
            touch($daemon_path . "/arduidom" . $d . ".kill");
        }
        sleep(0.5);
        for ($d = 1; $d <= $nbArduinos; $d++) {
            config::save("A" . $d . "_daemonenable", 0, "arduidom");
            log::add('arduidom', 'info', 'Stop Daemon ' . $d);
            $pid_file = $daemon_path . "/arduidom" . $d . ".pid";
            if (file_exists($pid_file)) {
                $pid = intval(trim(file_get_contents($pid_file)));
                $killresult = system::kill($pid);
                log::add('arduidom', 'debug', 'system::kill(' . $pid . ") = " . $killresult);
                log::add('arduidom', 'debug', "removing file " . $daemon_path . "/arduidom" . $d . ".pid");
                unlink($daemon_path . "/arduidom" . $d . ".pid");
            }
            log::add('arduidom', 'debug', "system::fuserk(" . intval(58200 + $d) . ")");
            system::fuserk(intval((58200 + $d)));
        }
        return ("OK");
    }


    public static function stopdaemon($_AID = '')
    {
        $General_Debug = file_exists("/tmp/arduidom_debug_mode_on");
        if ($General_Debug) log::add('arduidom', 'debug', "--------------------------------------------------------------------------------------");
        log::add('arduidom', 'debug', 'stopdaemon(' . $_AID . ") called");
        $nbArduinos = intval(config::byKey("ArduinoQty", "arduidom", 1));
        for ($d = 1; $d <= $nbArduinos; $d++) {
            if ($_AID == '' || $_AID == $d) {
                config::save('A' . $d . "_daemonenable", 0, 'arduidom');
                log::add('arduidom', 'info', 'Stop Daemon ' . $d);
                $daemon_path = realpath(dirname(__FILE__) . '/../../ressources');
                touch($daemon_path . "/arduidom" . $_AID . ".kill");
                sleep(0.5);
                $pid_file = $daemon_path . "/arduidom" . $d . ".pid";
                if (file_exists($pid_file)) {
                    $pid = intval(trim(file_get_contents($pid_file)));
                    $killresult = system::kill($pid);
                    log::add('arduidom', 'debug', 'system::kill(' . $pid . ") = " . $killresult);
                }
                log::add('arduidom', 'debug', "system::fuserk(" . intval(58200 + $d) . ")");
                system::fuserk(intval((58200 + $d)));
                log::add('arduidom', 'debug', "removing file " . $daemon_path . "/arduidom" . $d . ".pid");
                unlink($daemon_path . "/arduidom" . $d . ".pid");
            }

        }
        return ("OK");
    }

    public static function startdaemon($_AID = '')
    {
        $General_Debug = file_exists("/tmp/arduidom_debug_mode_on");
        $daemon_path = realpath(dirname(__FILE__) . '/../../ressources');
        if ($General_Debug) log::add('arduidom', 'debug', "--------------------------------------------------------------------------------------");
        log::add('arduidom', 'debug', 'startdaemon(' . $_AID . ') called');

        $nbArduinos = intval(config::byKey("ArduinoQty", "arduidom", 1));
        for ($d = 1; $d <= $nbArduinos; $d++) {
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
                        $ArduinoRequiredVersion = config::byKey("ArduinoRequiredVersion","arduidom");
                        if (strpos($resp, '_OK_V:' . $ArduinoRequiredVersion) == false) {
                            if (strpos($resp, 'Connection refused') == true) {
                                //event::add('jeedom::alert', array('level' => 'error', 'message' => __("Erreur: Réponse du démon " . $d . " = [" . $resp . "] au lieu de [PING_OK_V:" . $ArduinoRequiredVersion . "] (startdaemon), Redémarrage du démon...", __FILE__),));
                                log::add('arduidom', 'error', 'Erreur: Réponse du démon ' . $d . " = [" . $resp . "] au lieu de [PING_OK_V:" . $ArduinoRequiredVersion . "] (startdaemon)");
                            }
                            if (strpos($resp, '_OK') == true) {
                                event::add('jeedom::alert', array('level' => 'error', 'message' => __("Erreur: Réponse du démon " . $d . " = [" . $resp . "] au lieu de [PING_OK_V:" . $ArduinoRequiredVersion . "] (startdaemon)" . 'Vérifiez votre version du Sketch Arduino !!!', __FILE__),));
                                log::add('arduidom', 'error', "Vérifiez votre version du Sketch Arduino !!!");
                                log::add('arduidom', 'error', 'Erreur: Réponse du démon ' . $d . " = [" . $resp . "] au lieu de [PING_OK_V:" . $ArduinoRequiredVersion . "] (startdaemon)");
                            }
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

    public static function checkdaemon($_AID = '', $AutoSendRF = true, $SimpleCheckOnly = false)
    {
        $randomNb = rand(10,99) . "::";
        $NoDaemonEnabled = 1;
        $General_Debug = file_exists("/tmp/arduidom_debug_mode_on");
        if ($General_Debug) log::add('arduidom', 'debug', "--------------------------------------------------------------------------------------");
        if ($General_Debug) log::add('arduidom', 'debug', $randomNb . "checkdaemon( aid=" . $_AID . ", autosendrf=" . $AutoSendRF . ", simplecheckonly=" . $SimpleCheckOnly . ")");

        $MigrationCheck = config::byKey('db_version', 'arduidom', 0);
        if ($MigrationCheck < 108) {
            if ($General_Debug) log::add('arduidom', 'info', $randomNb . "La version de la base de données Arduidom n'est pas la bonne...");
            log::add('arduidom', 'error', $randomNb . "Une Migration des données Arduidom est necessaire depuis la v1.08 ! vous pouvez l'executer depuis la configuration du Plugin");
        }
        $arduinoQty = config::byKey('ArduinoQty', 'arduidom', 0);
        for ($d = 1; $d <= $arduinoQty; $d++) {
            if ($_AID == '' || $_AID == $d) {
                $DaemonEnabled = config::byKey('A' . $d . "_daemonenable", 'arduidom', 0);
                if ($DaemonEnabled == 0) {
                    if ($General_Debug) log::add('arduidom', 'debug', $randomNb . "L'arduino N°" . $d . ' ne sera pas verifié car il a été stoppé manuellement ou Désactivé');
                    if ($_AID == $d) log::add('arduidom', 'debug', $randomNb . "checkdaemon(" . $_AID . ") returns 0");
                    if ($_AID == $d) return 0;
                } else {
                    $NoDaemonEnabled = 0;
                    $tcpcheck = arduidom::sendtoArduino("PING", $d);
                    $ArduinoRequiredVersion = config::byKey("ArduinoRequiredVersion","arduidom");
                    if ($tcpcheck != "PING_OK_V:" . $ArduinoRequiredVersion) {
                        log::add('arduidom', 'error', $randomNb . "Erreur: Réponse de l'arduino " . $d . " = [" . $tcpcheck . "] au lieu de [PING_OK_V:" . $ArduinoRequiredVersion . "] (checkdaemon)");
                        if (!$SimpleCheckOnly) {
                            return 0;
                            //log::add('arduidom', 'error', "Redémarrage Automatique de l'arduino n°" . $d . " (checkdaemon)"); // JEEDOM GERE LE REDEMARRAGE AUTO DEPUIS V2
                        } else {
                            return 0;
                        }
                    } else {
                        if ($General_Debug) log::add('arduidom', 'debug', $randomNb . "La liaison avec l'arduino n°" . $d . ' fonctionne correctement.');
                        if ($AutoSendRF && !$SimpleCheckOnly) self::sendtoArduino("RF",$d);
                        if ($_AID == $d) log::add('arduidom', 'debug', $randomNb . "checkdaemon(" . $_AID . ") returns 1");
                        if ($_AID == $d) return 1;
                    }
                }
            }
        }
        if ($NoDaemonEnabled == 1) {
            log::add('arduidom', 'debug', $randomNb . "checkdaemon(" . $_AID . ") returns 0 because no daemon enabled");
            return 0;
        } else {
            log::add('arduidom', 'debug', $randomNb . "checkdaemon(" . $_AID . ") returns 1");
            return 1;
        }
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
    if ($modelPinMap == "uno" || $modelPinMap == "duemilanove328" || $modelPinMap == "leo" || $modelPinMap == "nano168" || $modelPinMap == "nano328") $ARDUPINMAP = $ARDUPINMAP_A;
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
    $General_Debug = file_exists("/tmp/arduidom_debug_mode_on");
    if ($General_Debug) log::add('arduidom', 'debug', "--------------------------------------------------------------------------------------");
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
    // if ($tcpcheck != $tcpmsg . "_OK") {
    if ($tcpcheck != "SP_OK") {
        log::add('arduidom','error', "Erreur setPinValue " . $tcpcheck . " (Recu : " . $tcpmsg . ")");
        event::add('jeedom::alert', array('level' => 'error', 'message' => __("Erreur setPinValue " . $tcpcheck . " (Recu : " . $tcpmsg . ")", __FILE__),));
    }
    return $tcpcheck;
}


    public static function sendtoArduino($_tcpmsg, $_AID)
{
    $General_Debug = (file_exists("/tmp/arduidom_debug_mode_on"));
    if ($General_Debug) log::add('arduidom', 'debug', "--------------------------------------------------------------------------------------");
    if ($General_Debug) log::add('arduidom', 'debug', 'sendtoArduino(' . $_tcpmsg . ',' . $_AID . ') called');

    $DaemonEnabled = config::byKey('A' . $_AID . "_daemonenable", 'arduidom', 0);
    if ($DaemonEnabled == 0) {
        log::add('arduidom', 'debug', 'sendtoArduino(' . $_tcpmsg . ',' . $_AID . ') IMPOSSIBLE, Le démon ' . $_AID . ' est stoppé ou Désactivé');
        return "DAEMON_DISABLED";
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

                stream_set_timeout($fp, 1);

                if (!$fp) {
                    log::add('arduidom', 'error', "Le démon " . $_AID . " n'est pas connecté ! (errstr:)" . $errstr);
                    return $errstr;

                } else {

                    if ($General_Debug) log::add('arduidom', 'debug', "Le démon " . $_AID . " est connecté, envoi...");

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
                        if ((time() - $start_time) > 2) { // Time out de 2 secondes pour le check du démon
                            log::add('arduidom', 'error', 'Erreur: TIMEOUT sur Réponse du démon ' . $_AID);
                            return "TIMEOUT";
                        }
                        fclose($fp);

                    } else {

                        while (!feof($fp)) {
                            $resp = fgets($fp);
                            $_tcpmsg = str_replace("\n", '', $_tcpmsg);
                            if ($General_Debug) log::add('arduidom', 'debug', '$_tcpmsg=' . $_tcpmsg);

                            if ((time() - $start_time) > 2) { // Time out de 2 secondes pour le check du démon
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
                return "BAD_CONFIG";
            }
        }
    }
}

    public function CompileArduino($_AID = '') {
        $arduidomRadioCmd = $this->getCmd(null, 'arduidom');
        if (!is_object($arduidomRadioCmd)) {
            $arduidomRadioCmd = new Cmd();
        }
        $arduidomRadioCmd->setName(__('Test', __FILE__));
        $arduidomRadioCmd->setLogicalId('arduidom');
        $arduidomRadioCmd->setEqLogic_id($this->getId());
        $arduidomRadioCmd->setUnite('DATS');
        $arduidomRadioCmd->setType('info');
        $arduidomRadioCmd->setEventOnly(1);
        $arduidomRadioCmd->setSubType('numeric');
        $arduidomRadioCmd->save();
    }


    public static function CompileArduino_OLD_FOR_TEST($_AID = '')
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
    //$port = config::byKey('A' . $_AID . '_port', 'arduidom', 'none');
    switch ($model) {
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

    ?><script>
        $('#md_modal').dialog({title: "{{Flash Arduino}}"});
        $('#md_modal').load('index.php?v=d&plugin=arduidom&modal=show.log').dialog('open');
    </script><?php

    $cmd = 'cd ' . $daemon_path . '/arduidomTest && sudo ino clean && sudo ino build -m ' . $cmd_model . " >> " . $daemon_path . "/../../../log/arduidom_log" . $_AID . " 2>&1 &";
    log::add('arduidom', 'info', 'Compiling arduino ' . $_AID . ': ' . $cmd);
    //while (@ ob_end_flush()); // end all output buffers if any

    popen($cmd, 'r');
    return $result;
}

    public static function FlashArduino($_AID = '')
{
    $result = 0;
    log::add('arduidom', 'info', '#############################flasharduino(' . $_AID . ') called');
    self::deamon_stop();
    //self::stopdaemon($_AID);
    sleep(1);
    if (!file_exists("/usr/bin/avrdude")) {
        throw new Exception(__("le programme avrdude n'est pas installé ! (installer avec apt-get install avrdude)", __FILE__));
    }
    $daemon_path = realpath(dirname(__FILE__) . '/../../ressources');
    $model = config::byKey('A' . $_AID . '_model', 'arduidom', 'none');
    $port = config::byKey('A' . $_AID . '_port', 'arduidom', 'none');
    switch ($model) {
        case "uno":
            $cmd_model = "-patmega328p -carduino";
            $cmd_speed = "115200";
            $cmd_hexfile = "uno";
            break;
        case "duemilanove328":
            $cmd_model = "-patmega328p -carduino";
            $cmd_speed = "57600";
            $cmd_hexfile = "duemilanove328";
            break;
        case "nano328":
            $cmd_model = "-patmega328p -carduino";
            $cmd_speed = "57600";
            $cmd_hexfile = "nano328";
            break;
        case "mega2560":
            $cmd_model = "-patmega2560 -cwiring";
            $cmd_speed = "115200";
            $cmd_hexfile = "mega2560";
            break;
        case "mega1280":
            $cmd_model = "-patmega1280 -cwiring";
            $cmd_speed = "57600";
            $cmd_hexfile = "mega1280";
            break;
        default:
            throw new Exception(__("Modele Arduino " . $model . " n'est pas supporté par la fonction !", __FILE__));
            break;
    }

    $cmd = 'sudo /usr/bin/avrdude -C/etc/avrdude.conf -v -v -v ' . $cmd_model . ' -P' . $port . ' -b' . $cmd_speed . ' -D -Uflash:w:' . $daemon_path . '/' . $cmd_hexfile . '.hex:i';
    $cmd .= ' >> ' . log::getPathToLog("arduidom") . ' 2>&1';
    log::add('arduidom', 'info', 'Flashing arduino ' . $_AID . ': ' . $cmd);

    log::add('arduidom', 'info', '############################# Launching : ' . $cmd);
    exec($cmd);
    sleep(1);
    log::add('arduidom', 'info', '#############################return ' . $result);

    return $result;
}


    public function event()
{
    log::add('arduidom', 'debug', 'arduidom event() called');
}
    /*     * *********************Methode d'instance************************* */


    /*     * **********************Getteur Setteur*************************** */
}

class arduidomCmd extends cmd
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */

    /*

    preInsert ⇒ Méthode appellé avant la création de votre objet

    postInsert ⇒ Méthode appellé après la création de votre objet

    preUpdate ⇒ Méthode appellé avant la mise à jour de votre objet

    postUpdate ⇒ Méthode appellé après la mise à jour de votre objet

    preSave ⇒ Méthode appellé avant la sauvegarde (creation et mise à jour donc) de votre objet

    postSave ⇒ Méthode appellé après la sauvegarde de votre objet

    preRemove ⇒ Méthode appellé avant la supression de votre objet

    postRemove ⇒ Méthode appellé après la supression de votre objet

    */

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
        $results = print_r($_options, true);
        log::add('arduidom', 'debug', 'execute(' . $results . ') called');
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
