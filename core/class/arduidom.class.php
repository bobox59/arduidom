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
        self::deamon_start();
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
        $return['state'] = 'ok';

        $ressource_path = realpath(dirname(__FILE__) . '/../../ressources');

        if (config::byKey("ArduinoRequiredVersion","arduidom","",true) != 108) $return['state'] = 'nok' ;

        // FICHIERS NECESSAIRES
        if (!file_exists("/usr/bin/arduino")) $return['state'] = 'nok';
        if (!file_exists("/usr/bin/avrdude")) $return['state'] = 'nok';

        // FICHIERS INUTILES
        if (file_exists($ressource_path . "/arduidom.py")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom1.py")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom2.py")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom3.py")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom4.py")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom5.py")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom6.py")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom7.py")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom8.py")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom1.pid")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom2.pid")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom3.pid")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom4.pid")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom5.pid")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom6.pid")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom7.pid")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom8.pid")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom.kill")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom1.kill")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom2.kill")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom3.kill")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom4.kill")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom5.kill")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom6.kill")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom7.kill")) $return['state'] = 'nok';
        if (file_exists($ressource_path . "/arduidom8.kill")) $return['state'] = 'nok';

        return $return;
    }

    public static function dependancy_install()
    {
        log::add('arduidom','debug','Installation des dependances....');
        config::save("ArduinoRequiredVersion","108","arduidom");
        log::remove('arduidom_update');
        chmod(dirname(__FILE__) . '/../../ressources/install.sh',0775);
        $cmd = 'sudo ' . dirname(__FILE__) . '/../../ressources/install.sh';
        if (substr(jeedom::version(),0,1) == 2) {
            $cmd .= ' >> ' . log::getPathToLog('arduidom_update') . ' 2>&1 &'; // & final retiré pour jeedom v1
        } else {
            $cmd .= ' >> ' . log::getPathToLog('arduidom_update') . ' 2>&1'; // & final retiré pour jeedom v1
        }
        log::add('arduidom','debug','Installation des dependances : ' . $cmd);
        exec($cmd);
        $ressource_path = realpath(dirname(__FILE__) . '/../../ressources');
        if (file_exists($ressource_path . "/arduidom.py")) unlink($ressource_path . "/arduidom.py");
        if (file_exists($ressource_path . "/arduidom1.py")) unlink($ressource_path . "/arduidom1.py");
        if (file_exists($ressource_path . "/arduidom2.py")) unlink($ressource_path . "/arduidom2.py");
        if (file_exists($ressource_path . "/arduidom3.py")) unlink($ressource_path . "/arduidom3.py");
        if (file_exists($ressource_path . "/arduidom4.py")) unlink($ressource_path . "/arduidom4.py");
        if (file_exists($ressource_path . "/arduidom5.py")) unlink($ressource_path . "/arduidom5.py");
        if (file_exists($ressource_path . "/arduidom6.py")) unlink($ressource_path . "/arduidom6.py");
        if (file_exists($ressource_path . "/arduidom7.py")) unlink($ressource_path . "/arduidom7.py");
        if (file_exists($ressource_path . "/arduidom8.py")) unlink($ressource_path . "/arduidom8.py");
        if (file_exists($ressource_path . "/arduidom1.pid")) unlink($ressource_path . "/arduidom1.pid");
        if (file_exists($ressource_path . "/arduidom2.pid")) unlink($ressource_path . "/arduidom2.pid");
        if (file_exists($ressource_path . "/arduidom3.pid")) unlink($ressource_path . "/arduidom3.pid");
        if (file_exists($ressource_path . "/arduidom4.pid")) unlink($ressource_path . "/arduidom4.pid");
        if (file_exists($ressource_path . "/arduidom5.pid")) unlink($ressource_path . "/arduidom5.pid");
        if (file_exists($ressource_path . "/arduidom6.pid")) unlink($ressource_path . "/arduidom6.pid");
        if (file_exists($ressource_path . "/arduidom7.pid")) unlink($ressource_path . "/arduidom7.pid");
        if (file_exists($ressource_path . "/arduidom8.pid")) unlink($ressource_path . "/arduidom8.pid");
        if (file_exists($ressource_path . "/arduidom.kill")) unlink($ressource_path . "/arduidom.kill");
        if (file_exists($ressource_path . "/arduidom1.kill")) unlink($ressource_path . "/arduidom1.kill");
        if (file_exists($ressource_path . "/arduidom2.kill")) unlink($ressource_path . "/arduidom2.kill");
        if (file_exists($ressource_path . "/arduidom3.kill")) unlink($ressource_path . "/arduidom3.kill");
        if (file_exists($ressource_path . "/arduidom4.kill")) unlink($ressource_path . "/arduidom4.kill");
        if (file_exists($ressource_path . "/arduidom5.kill")) unlink($ressource_path . "/arduidom5.kill");
        if (file_exists($ressource_path . "/arduidom6.kill")) unlink($ressource_path . "/arduidom6.kill");
        if (file_exists($ressource_path . "/arduidom7.kill")) unlink($ressource_path . "/arduidom7.kill");
        if (file_exists($ressource_path . "/arduidom8.kill")) unlink($ressource_path . "/arduidom8.kill");
    }

    public static function set_daemon_mode($mode = "") { // CREE CAR TROP DE SOUCIS AVEC LE CACHE DE JEEDOM (pendant Beta 2.0)
        $General_Debug = config::byKey('generalDebug','arduidom',0, true);
        // * MODE via CONFIG JEEDOM */
        config::save("daemonmode",$mode,"arduidom");
        // * MODE via CACHE */
        //cache::set('arduidom_daemon_mode',$mode);
        if ($General_Debug) log::add('arduidom','DEBUG', "Set daemon mode to " . $mode); // . " => " . $test );
    }

    public static function get_daemon_mode() {
        $General_Debug = config::byKey('generalDebug','arduidom',0, true);
        // * MODE via CONFIG JEEDOM */
        $buffer = config::byKey('daemonmode','arduidom','KILLED', 1);
        // * MODE via CACHE */
        //$buffer = cache::byKey('arduidom_daemon_mode');
        //log::add('arduidom','DEBUG', "Get daemon mode : " . $buffer );
        return $buffer;
    }

    public static function deamon_info() { // Nouvelle methode de gestion des démons
        $return = array();
        $return['log'] = 'arduidom';
        $return['state'] = 'nok';
        $return['state_message'] = 'nok';
        $return['launchable'] = 'ok';

        $General_Debug = config::byKey('generalDebug','arduidom',0, true);
        $daemonmode = self::get_daemon_mode();
        if ($daemonmode == "FLASHING") {
            $return['state'] = 'ok';
            return $return;
        }

        $pid_file = realpath(dirname(__FILE__) . '/../../ressources/arduidomx.pid');
        if (file_exists($pid_file)) {
            if (posix_getsid(trim(file_get_contents($pid_file)))) {
                $return['state'] = 'ok';
            } else {
                unlink($pid_file);
            }
        }

        /* TRY OTHER METHOD
        //if ($daemonmode != "STARTING" && $daemonmode != "ERROR" && $daemonmode != "KILLING") { // ne check pas les arduino pendant un démarrage
        if ($daemonmode == "OK") { // ne ping les arduino que si OK
            //// Verifie si le python est necessaire... (inutile si aucun arduino USB)
            $usb_arduinos = 0;
            //if ($_debug) log::add('arduidom','DEBUG','strpos1:' . strpos(config::byKey('A1_port', 'arduidom', '', 1), "/dev"));
            //if ($_debug) log::add('arduidom','DEBUG','strpos2:' . strpos(config::byKey('A2_port', 'arduidom', '', 1), "/dev"));
            if (strpos(config::byKey('A1_port', 'arduidom', '', true), "dev/") != false) $usb_arduinos += 1;
            if (strpos(config::byKey('A2_port', 'arduidom', '', true), "dev/") != false) $usb_arduinos += 1;
            if (strpos(config::byKey('A3_port', 'arduidom', '', true), "dev/") != false) $usb_arduinos += 1;
            if (strpos(config::byKey('A4_port', 'arduidom', '', true), "dev/") != false) $usb_arduinos += 1;
            if (strpos(config::byKey('A5_port', 'arduidom', '', true), "dev/") != false) $usb_arduinos += 1;
            if (strpos(config::byKey('A6_port', 'arduidom', '', true), "dev/") != false) $usb_arduinos += 1;
            if (strpos(config::byKey('A7_port', 'arduidom', '', true), "dev/") != false) $usb_arduinos += 1;
            if (strpos(config::byKey('A8_port', 'arduidom', '', true), "dev/") != false) $usb_arduinos += 1;

            if ($usb_arduinos > 0) {
                $tcpcheck = arduidom::sendtoArduino("PING", 0);
            if ($tcpcheck == "PING_OK") {
                $return['state'] = 'ok';
            } else {
                log::add('arduidom', 'error', "Erreur: Réponse du démon = [" . $tcpcheck . "] au lieu de [PING_OK] (in daemon_info())");
            }
            } else { // aucun arduino USB
                $tcpcheck = arduidom::sendtoArduino("PING", 1);
                if ($tcpcheck == "PING_OK") {
                    $return['state'] = 'ok';
                } else {
                    log::add('arduidom', 'error', "Erreur: Réponse du démon = [" . $tcpcheck . "] au lieu de [PING_OK] (in daemon_info())");
                }

            }
        } else {
            if ($daemonmode == "STARTING") $return['state'] = 'ok';
            if ($daemonmode == "STARTED") $return['state'] = 'ok';
            //if ($General_Debug) log::add('arduidom', 'error', "DaemonMode is not OK (in daemon_info())");

        }
    */
        $d = 1; // LAUNCHABLE SE BASE UNIQUEMENT SUR LE 1er ARDUINO ! a voir par la suite...
        $model = config::byKey('A' . $d . '_model', 'arduidom', '');
        $port = config::byKey('A' . $d . '_port', 'arduidom', '');
        if ($port != 'none' && $model != 'none' && $port != '' && $model != '') {
            if (file_exists($port) || $port == "Network") {
                if ($port != 'Network') {
                    //$port = jeedom::getUsbMapping($port);
                    if (@!file_exists($port)) {
                        $return['launchable'] = 'nok';
                        $return['launchable_message'] = __('Le port n\'est pas configuré ou n\'existe pas', __FILE__);
                    }
                }
            } else {
                $return['launchable'] = 'nok';
                $return['launchable_message'] = __('Le port n\'est pas configuré ou n\'existe pas', __FILE__);
            }
        } else {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('Le port ou modèle n\'est pas configuré', __FILE__);
        }

        $MigrationCheck = config::byKey('db_version', 'arduidom', 0);
        if ($MigrationCheck < 108) {
            log::add('arduidom', 'error', "Une Migration des données Arduidom est necessaire depuis la v1.08 ! vous pouvez l'executer depuis la configuration du Plugin");
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __("Une Migration des données Arduidom est necessaire depuis la v1.08 ! vous pouvez l'executer depuis la configuration du Plugin", __FILE__);
        }

        return $return;
    }


    public static function deamon_start($_debug = false) {
        //// Démarrage du démon
        $daemonmode = self::get_daemon_mode();
        if ($daemonmode == "STARTING" || $daemonmode == "KILLING" || $daemonmode == "FLASHING") {
            //log::add('arduidom', 'debug', "Another session of starting daemon in progress... wait 1 minute before retry...");
            if ($_debug == false) return false;
        }
        self::set_daemon_mode("STARTING");
        sleep(1); // Delai de sécurité anti-collisions du start
        if ($_debug == false) {
            config::save('generalDebug', 0, 'arduidom');
        } else {
            config::save('generalDebug', 1, 'arduidom');
        }
        $ressource_path = realpath(dirname(__FILE__) . '/../../ressources');
        if ($_debug) log::add('arduidom', 'debug', "************************** --------------------------------------------------------------------------------------");
        if ($_debug) log::add('arduidom', 'debug', "* daemon_start(debug=$_debug) *");
        if ($_debug) log::add('arduidom', 'debug', "**************************");
        $nbArduinos = intval(config::byKey("ArduinoQty", "arduidom", 1, true));
        $daemon_path = realpath(dirname(__FILE__) . '/../../ressources');

        //// Termine les démons restants
        touch($daemon_path . "/arduidomx.kill");
        sleep(1.5);
        if ($_debug) log::add('arduidom', 'info', 'kill daemon(s)...');
        $pid_file = $daemon_path . "/arduidomx.pid";
        if (file_exists($pid_file)) {
            $pid = intval(trim(file_get_contents($pid_file)));
            if (substr(jeedom::version(),0,1) == 2) $killresult = system::kill($pid);
            if (substr(jeedom::version(),0,1) == 2) if ($_debug) log::add('arduidom', 'debug', 'system::kill(' . $pid . ") = " . $killresult);
            if ($_debug) log::add('arduidom', 'debug', "removing file " . $daemon_path . "/arduidomx.pid");
            unlink($daemon_path . "/arduidomx.pid");
        }
        if ($_debug) log::add('arduidom', 'debug', "system::fuserk(" . intval(58201) . ")");
        if (substr(jeedom::version(),0,1) == 2) system::fuserk(intval((58200)));
        if (substr(jeedom::version(),0,1) == 2) system::fuserk(intval((58201)));

        if (file_exists(log::getPathToLog('arduidom_daemon'))) unlink(log::getPathToLog('arduidom_daemon'));
        touch(log::getPathToLog('arduidom_daemon'));
        chown(log::getPathToLog('arduidom_daemon'),"www-data");
        chmod(log::getPathToLog('arduidom_daemon'), 0777);

        //// Recreation du XML de config
        if (file_exists($ressource_path . '/config_arduidom.xml')) unlink($ressource_path . '/config_arduidom.xml');
        $replace_config = array(
            '#ArduinoVersion#' => config::byKey("ArduinoRequiredVersion", "arduidom", ""),
            '#ArduinoQty#' => config::byKey("arduinoqty","arduidom",0),
            '#A1_serial_port#' => config::byKey('A1_port', 'arduidom', ''),
            '#A2_serial_port#' => config::byKey('A2_port', 'arduidom', ''),
            '#A3_serial_port#' => config::byKey('A3_port', 'arduidom', ''),
            '#A4_serial_port#' => config::byKey('A4_port', 'arduidom', ''),
            '#A5_serial_port#' => config::byKey('A5_port', 'arduidom', ''),
            '#A6_serial_port#' => config::byKey('A6_port', 'arduidom', ''),
            '#A7_serial_port#' => config::byKey('A7_port', 'arduidom', ''),
            '#A8_serial_port#' => config::byKey('A8_port', 'arduidom', ''),
            '#log_path#' => log::getPathToLog('arduidom'),
            '#pid_path#' => $ressource_path . '/arduidomx.pid',
        );
        if (config::byKey('jeeNetwork::mode') == 'slave') {
            $replace_config['#sockethost#'] = network::getNetworkAccess('internal', 'ip', '127.0.0.1');
            $replace_config['#socketport#'] = 58300;
            $replace_config['#trigger_url#'] = config::byKey('jeeNetwork::master::ip') . '/plugins/arduidom/core/php/jeeArduidom.php';
            $replace_config['#apikey#'] = config::byKey('jeeNetwork::master::apikey');
        } else {
            $replace_config['#sockethost#'] = '0.0.0.0'; //'127.0.0.1';
            $replace_config['#socketport#'] = 58200;
            $replace_config['#trigger_url#'] = network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/arduidom/core/php/jeeArduidom.php';
            $replace_config['#apikey#'] = config::byKey('api');
        }
        $config = file_get_contents($ressource_path . '/config_template.xml');
        $config = template_replace($replace_config, $config);
        file_put_contents($ressource_path . '/config_arduidom.xml', $config);
        chmod($ressource_path . '/config_arduidom.xml', 0777);

        //// Verifie si le python est necessaire... (inutile si aucun arduino USB)
        $usb_arduinos = 0;
        //if ($_debug) log::add('arduidom','DEBUG','strpos1:' . strpos(config::byKey('A1_port', 'arduidom', '', 1), "/dev"));
        //if ($_debug) log::add('arduidom','DEBUG','strpos2:' . strpos(config::byKey('A2_port', 'arduidom', '', 1), "/dev"));
        for ($a = 1; $a <= 8; $a++) {
            if (strpos(config::byKey('A' . $a . '_port', 'arduidom', '', true), "dev/") != false) $usb_arduinos += 1;
        }
        if ($usb_arduinos > 0) {
            $cmd = 'nohup nice -n 19 /usr/bin/python ' . $ressource_path . '/arduidomx.py';
            if ($_debug) $cmd .= ' -lDEBUG';
            unlink($ressource_path . '/arduidomx.kill');
            $cmd .= ' >> ' . log::getPathToLog('arduidom_cmd') . ' 2>&1 &';
            config::save('daemonstarted', 0, 'arduidom');
            log::add('arduidom', 'info', 'Lancement démon : ' . $cmd);
            //// Execution du Python...
            $result = exec($cmd);
            if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) {
                log::add('arduidom', 'error', $result);
                self::set_daemon_mode("ERROR");
                return false;
            }

            log::add('arduidom', 'info', 'Attente du démarrage complet du démon... (30 secondes max.)');
            $timeout = time();
            while (config::byKey('daemonstarted', 'arduidom', 0, true) == 0) {
                sleep(1);
                if ((time() - $timeout) >= 30) {
                    self::set_daemon_mode("ERROR"); // daemonstarted = Etat du démon : 0=non demarré - 1=démon OK - 2=erreur version
                    if (config::byKey('daemonstarted', 'arduidom', 0, true) == 0) {
                        log::add('arduidom', 'error', ' Dépassement du délai de démarrage du démon...');
                        if (substr(jeedom::version(),0,1) == 2) event::add('jeedom::alert', array('level' => 'error', 'message' => __("Dépassement du délai de démarrage du démon...", __FILE__),));
                    }
                    return false;
                }
            }
            if (config::byKey('daemonstarted', 'arduidom', 0, true) == 2) {
                self::set_daemon_mode("ERROR"); // daemonstarted = Etat du démon : 0=non demarré - 1=démon OK - 2=erreur version
                log::add('arduidom', 'error', " Un ou plusieurs Arduino n'ont pas la version du sketch requise !...");
                if (substr(jeedom::version(),0,1) == 2) event::add('jeedom::alert', array('level' => 'error', 'message' => __("Un ou plusieurs Arduino n'ont pas la version du sketch requise !", __FILE__),));
                return false;
            }
        } else {
            if ($_debug) log::add('arduidom', 'info', 'Skipping python program, no USB arduino configured.');
        }

        //// Verifications, SetPin, RestoreStates
        $errorCounter = 0;
        $nbArduinos = intval(config::byKey("ArduinoQty", "arduidom", 1));
        self::set_daemon_mode("STARTED"); // necessaire pour le restorestates
        for ($d = 1; $d <= $nbArduinos; $d++) {
            $result = self::setPinMapping($d);
            if ($result != "OK") $errorCounter += 1;
            log::add('arduidom', 'debug', 'arduino ' . $d . ' : Pin Mapping = ' . $result);
            $result = self::restoreStates($d);
            if ($result != "OK") $errorCounter += 1;
            log::add('arduidom', 'debug', 'arduino ' . $d . ' : Restore State = ' . $result);
        }
        if ($errorCounter > 0) {
            self::set_daemon_mode("ERROR");
            log::add('arduidom', 'error', $errorCounter . ' Erreur(s) pendant le lancement du démon...');
            return false;
        }
        self::set_daemon_mode("OK");
        message::removeAll('arduidom', 'unableStartDeamon');
        log::add('arduidom', 'info', 'Démon arduidom lancé sans erreur.');
        return true;
    }

    public static function deamon_stop() {
        $General_Debug = config::byKey('generalDebug','arduidom',0, true);
        $daemonmode = self::get_daemon_mode();
        if ($daemonmode == "KILLING" || $daemonmode == "STARTING") {
            if ($General_Debug) log::add('arduidom', 'debug', "Another session of stopping daemon in progress... wait 1 minute before retry...");
            return false;
        }
        if ($daemonmode != "FLASHING") self::set_daemon_mode("KILLING");
        if ($General_Debug) log::add('arduidom', 'debug', "************************* --------------------------------------------------------------------------------------");
        if ($General_Debug) log::add('arduidom', 'debug', "* daemon_stop() called. *");
        if ($General_Debug) log::add('arduidom', 'debug', "*************************");
        log::add('arduidom', 'info', 'Arret du démon...');
        $daemon_path = realpath(dirname(__FILE__) . '/../../ressources');
        touch($daemon_path . "/arduidomx.kill");
        sleep(1.5);
        $pid_file = $daemon_path . "/arduidomx.pid";
        if (file_exists($pid_file)) {
            $pid = intval(trim(file_get_contents($pid_file)));
            if (substr(jeedom::version(),0,1) == 2) {
                $killresult = system::kill($pid);
                if ($General_Debug) log::add('arduidom', 'debug', 'system::kill(' . $pid . ") = " . $killresult);
            }
            if ($General_Debug) log::add('arduidom', 'debug', "removing file " . $daemon_path . "/arduidomx.pid");
            unlink($daemon_path . "/arduidomx.pid");
        }
        if ($General_Debug) log::add('arduidom', 'debug', "system::fuserk(" . intval(58201) . ")");
        if (substr(jeedom::version(),0,1) == 2) system::fuserk(intval((58201)));
        if ($daemonmode != "FLASHING") self::set_daemon_mode("KILLED");
        return ("OK");
    }


    public static function ping_arduino($_AID = '', $AutoSendRF = true)
    {
        $General_Debug = config::byKey('generalDebug','arduidom',0, true);
        $daemonmode = self::get_daemon_mode();
        if ($daemonmode == "STARTING") { // ne check pas les arduino pendant un démarrage
            if ($General_Debug) log::add("arduidom", "debug", "Daemon is in STARTING mode, skipping.");
            return 0;
        }
        if ($daemonmode == "FLASHING") { // ne check pas les arduino pendant un démarrage
            if ($General_Debug) log::add("arduidom", "debug", "Daemon is in FLASHING mode, skipping.");
            return 0;
        }
        $arduinoQty = config::byKey('ArduinoQty', 'arduidom', 0);
        if ($_AID != '') {
            $tcpcheck = arduidom::sendtoArduino("PING", $_AID);
            $ArduinoRequiredVersion = "PING_OK_V:" . config::byKey("ArduinoRequiredVersion","arduidom");
            if ($_AID == 0) $ArduinoRequiredVersion = "PING_OK";

            if ($tcpcheck != $ArduinoRequiredVersion) {
                log::add('arduidom', 'error', "Erreur: Réponse de l'arduino " . $_AID . " = [" . $tcpcheck . "] au lieu de [" . $ArduinoRequiredVersion . "] (checkdaemon)");
                    return 0;
            } else {
                //if ($General_Debug) log::add('arduidom', 'debug', $randomNb . "La liaison avec l'arduino n°" . $d . ' fonctionne correctement.');
                if ($AutoSendRF) self::sendtoArduino("RF",$_AID);
                if ($General_Debug) log::add('arduidom', 'debug', "ping_arduino(" . $_AID . ") returns 1");
                return 1;
            }
        }
        return 0;
    }

    public static function setPinMapping($_AID)
{
    $General_Debug = config::byKey('generalDebug','arduidom',0, true);
    global $ARDUPINMAP_A, $ARDUPINMAP_B, $ARDUPINMAP_C;
    log::add('arduidom', 'debug', 'setPinMapping(' . $_AID . ') ...');
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
            // Si Modifs, penser a les mettres aussi dans ajax !
            if ($config == '') $CP = $CP . "z";
            if ($config == 'disable') $CP = $CP . "z";
            if ($config == 'in') $CP = $CP . "i";
            if ($config == 'inup') $CP = $CP . "y";
            if ($config == 'out') $CP = $CP . "o";
            if ($config == 'rin') $CP = $CP . "r";
            if ($config == 'rout') $CP = $CP . "t";
            if ($config == 'pout') $CP = $CP . "p";
            if ($config == 'ain') $CP = $CP . "a";
            if ($config == 'custin') $CP = $CP . "c";
            if ($config == 'custout') $CP = $CP . "d";
            if ($config == 'dht1') $CP = $CP . "1";
            if ($config == 'dht2') $CP = $CP . "2";
            if ($config == 'dht3') $CP = $CP . "3";
            if ($config == 'dht4') $CP = $CP . "4";
            if ($config == 'dht5') $CP = $CP . "5";
            if ($config == 'dht6') $CP = $CP . "6";
            if ($config == 'dht7') $CP = $CP . "7";
            if ($config == 'dht8') $CP = $CP . "8";
            if ($config == 'pup') $CP = $CP . "u";
            if ($config == 'pdwn') $CP = $CP . "v";
            if ($config == 'oinv') $CP = $CP . "x";
            if ($config == 'blnk') $CP = $CP . "b";
        }
    }
    if ($General_Debug) log::add('arduidom', 'debug', 'send the setPinMapping to ' . $CP);
    $tcp_check = self::sendtoArduino($CP, $_AID);
    if ($General_Debug) log::add('arduidom', 'debug', 'setPinMapping returns ' . $tcp_check);
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
    $General_Debug = config::byKey('generalDebug','arduidom',0, true);
    if ($General_Debug) log::add('arduidom', 'debug', "^--------------------------------------------------------------------------------------");
    if ($General_Debug) log::add('arduidom', 'debug', 'restoreStates(' . $_AID . ') called');
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
    if ($General_Debug) log::add('arduidom', 'debug', "v--------------------------------------------------------------------------------------");
    return ("OK");
}


    public static function setPinValue($_logicalId, $_value)
{
    $General_Debug = config::byKey('generalDebug','arduidom',0, true);
    $DaemonReady = self::get_daemon_mode();
    if ($General_Debug) log::add('arduidom', 'debug', 'setPinValue(' . $_logicalId . ',' . $_value . ') called');
    $func_start_time = getmicrotime(true);
    if ($DaemonReady != "OK" && $DaemonReady != "STARTED") {
        if ($General_Debug) log::add("arduidom","debug","le démon n'est pas pret");
        return "DAEMON_NOT_OK";
    }

    $arduid = 0;
    if ($_logicalId > 999) {
        $arduid = $_logicalId[0];
        $_logicalId = intval(substr($_logicalId, 1));
    }
    $cachekey = "arduidom::lastSetPin" . (intval($arduid) * 1000 + intval($_logicalId));
    cache::set($cachekey, $_value, 0);
    if ($General_Debug) log::add('arduidom','debug', '   cache::set(' . $cachekey . ',' . $_value . ')');
    $tcpmsg = "";
    log::add('arduidom', 'debug', 'setPinValue(' . $_logicalId . ',' . $_value . ') for arduino ' . $arduid);
    $config = config::byKey('A' . $arduid . '_pin::' . $_logicalId, 'arduidom');
    if ($General_Debug) log::add('arduidom','debug', '   $config=' . $config);
    if ($config == 'disable') {
        $tcpmsg = "SP" . sprintf("%02s", $_logicalId) . $_value;
    }
    if ($config == 'out') {
        $tcpmsg = "SP" . sprintf("%02s", $_logicalId) . $_value;
    }
    if ($config == 'pup' || $config == 'pdwn' || $config == 'blnk') {
        $tcpmsg = "SP" . sprintf("%02s", $_logicalId) . sprintf("%04s", $_value);
    }
    if ($config == 'oinv') {
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
    if ($tcpcheck != $tcpmsg . "_OK") {
    //if ($tcpcheck != "SP_OK") {
        log::add('arduidom','error', "Erreur sur setPinValue(" . $arduid . ',' . $tcpmsg . ") - (Recu : " . $tcpcheck . ")");
        if (substr(jeedom::version(),0,1) == 2) event::add('jeedom::alert', array('level' => 'error', 'message' => __("Erreur sur setPinValue(" . $arduid . ',' . $tcpmsg . ") - (Recu : " . $tcpcheck . ")", __FILE__),));
    }
    $elapsed_time = getmicrotime(true) - $func_start_time;
    $elapsed_time = $elapsed_time * 1000;
    $elapsed_time = number_format($elapsed_time, 1, '.', '') . " ms";
    if ($General_Debug) log::add('arduidom', 'debug', 'setPinValue(' . $_logicalId . ',' . $_value . ') takes ' . $elapsed_time);
    return $tcpcheck;
}


    public static function sendtoArduino($_tcpmsg, $_AID)
{
    $General_Debug = config::byKey('generalDebug','arduidom',0, true);
    if ($_AID == 0) $General_Debug = 0; // Pas de log sur démon direct, trop bavard...
    if ($General_Debug) log::add('arduidom', 'debug', "^--------------------------------------------------------------------------------------");
    if ($General_Debug) log::add('arduidom', 'debug', 'sendtoArduino(' . $_tcpmsg . ',' . $_AID . ') called');
    $func_start_time = getmicrotime(true);
    $daemonmode = self::get_daemon_mode();
    if ($daemonmode == "FLASHING") {
        log::add("arduidom","debug","sendtoArduino impossible, arduino en cours de flashage...");
        return "DAEMON_NOT_OK";
    }
        $port = config::byKey('A' . $_AID . '_port', 'arduidom', 'none', true);
        $ip = config::byKey('A' . $_AID . '_daemonip', 'arduidom', '127.0.0.1', true);
        if (($port != 'none' && $port != "") || $_AID == 0) {
            if (file_exists($port) || $port == 'Network' || $_AID == 0) {
                if ($port != 'Network') {
                    //if ($General_Debug) log::add('arduidom', 'debug', 'Le démon ' . $_AID . ' est un démon python');
                    if ($General_Debug) log::add('arduidom','debug','IP: ' . $ip . ":" . (58200 + intval($_AID)));
                    $fp = fsockopen($ip, (58200 + intval($_AID)), $errno, $errstr, 10);
                } else {
                    //if ($General_Debug) log::add('arduidom', 'debug', 'Le démon ' . $_AID . ' est un arduino Réseau (IP:' . $ip . ")");
                    if ($General_Debug) log::add('arduidom','debug','IP: ' . $ip . ":58174");
                    $fp = fsockopen($ip, (58174), $errno, $errstr, 1);
                }


                if (!$fp) {
                    log::add('arduidom', 'error', "Le démon " . $_AID . " n'est pas connecté ! (errstr:)" . $errstr);
                    if ($General_Debug) log::add('arduidom', 'debug', "v--------------------------------------------------------------------------------------");
                    return $errstr;

                } else {

                    stream_set_timeout($fp, 10);
                    if ($General_Debug) log::add('arduidom', 'debug', "Le démon " . $_AID . " est connecté, envoi...");

                    if ($port == "Network") $_tcpmsg = $_tcpmsg . "\n"; // ajout d'une fin de ligne pour shield ethernet
                    fwrite($fp, $_tcpmsg);
                    $start_time = time();
                    $resp = "";

                    if ($port == "Network") {
                        if ($General_Debug) log::add('arduidom', 'debug', "Attente de la réponse arduino " . $_AID . " ...");
                        while (!feof($fp)) {
                            $resp = $resp . fgets($fp);
                            if ((time() - $start_time) > 10) { // Time out de 10 secondes pour le check du démon
                                log::add('arduidom', 'error', 'Erreur: TIMEOUT sur attente de réponse arduino ' . $_AID);
                                if (substr(jeedom::version(),0,1) == 2) event::add('jeedom::alert', array('level' => 'error', 'message' => __('Erreur: TIMEOUT sur attente de réponse arduino ' . $_AID, __FILE__),));
                                if ($General_Debug) log::add('arduidom', 'debug', "v--------------------------------------------------------------------------------------");
                                return "TIMEOUT";
                            }
                        }
                        $resp = str_replace("\r", '', $resp);
                        $resp = str_replace("\n", '', $resp);
                        if ($General_Debug) log::add('arduidom', 'debug', 'Réponse TCP recue = ' . $resp);
                        if ((time() - $start_time) > 10) { // Time out de 10 secondes pour le check du démon
                            log::add('arduidom', 'error', 'Erreur: TIMEOUT sur Réponse du démon ' . $_AID);
                            if (substr(jeedom::version(),0,1) == 2) event::add('jeedom::alert', array('level' => 'error', 'message' => __('Erreur: TIMEOUT sur Réponse du démon ' . $_AID, __FILE__),));
                            if ($General_Debug) log::add('arduidom', 'debug', "v--------------------------------------------------------------------------------------");
                            return "TIMEOUT";
                        }
                        fclose($fp);
                    } else {

                        while (!feof($fp)) {
                            $resp = fgets($fp);
                            $_tcpmsg = str_replace("\n", '', $_tcpmsg);
                            if ($General_Debug) log::add('arduidom', 'debug', '$_tcpmsg=' . $_tcpmsg);

                            if ((time() - $start_time) > 10) { // Time out de 10 secondes pour le check du démon
                                log::add('arduidom', 'error', 'Erreur: TIMEOUT sur Réponse du démon ' . $_AID);
                                if ($General_Debug) log::add('arduidom', 'debug', "v--------------------------------------------------------------------------------------");
                                return "TIMEOUT";
                            }
                        }
                        fclose($fp);
                    }
                    $elapsed_time = getmicrotime(true) - $func_start_time;
                    $elapsed_time = $elapsed_time * 1000;
                    $elapsed_time = number_format($elapsed_time, 1, '.', '') . " ms";
                    if ($General_Debug) log::add('arduidom', 'debug', 'sendtoArduino(' . $_tcpmsg . ',' . $_AID . ') reply [' . $resp . '] takes ' . $elapsed_time);
                    if ($General_Debug) log::add('arduidom', 'debug', "v--------------------------------------------------------------------------------------");
                    return $resp;
                }
            } else {
                log::add('arduidom', 'error', __('Le port Arduino (' . $port . ')est vide ou n\'existe pas', __FILE__), 'noArduinoComPort');
                if ($General_Debug) log::add('arduidom', 'debug', "v--------------------------------------------------------------------------------------");
                return "BAD_CONFIG";
            }
        }

    if ($General_Debug) log::add('arduidom', 'debug', "sendtoarduino return EMPTY");
    if ($General_Debug) log::add('arduidom', 'debug', "v--------------------------------------------------------------------------------------");
    return("EMPTY");
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
    log::add('arduidom', 'info', "Téléversement de l'arduino " . $_AID . '...');

    self::set_daemon_mode("FLASHING");
    self::deamon_stop();
    sleep(1);
    if (!file_exists("/usr/bin/avrdude")) {
        self::set_daemon_mode("KILLED");
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
            self::set_daemon_mode("KILLED");
            throw new Exception(__("Modele Arduino " . $model . " n'est pas supporté par la fonction !", __FILE__));
            break;
    }
    $logfile = "/tmp/avrdude.log";
    if (file_exists($logfile)) unlink($logfile);
    $cmd = 'sudo /usr/bin/avrdude -C/etc/avrdude.conf -v -v -v -l ' . $logfile . ' ' . $cmd_model . ' -P' . $port . ' -b' . $cmd_speed . ' -D -Uflash:w:' . $daemon_path . '/' . $cmd_hexfile . '.hex:i';
    $cmd .= ' >> ' . log::getPathToLog("arduidom") . ' 2>&1';
    log::add('arduidom', 'info', 'Execution du téléversement: ' . $cmd);
    $result = exec($cmd);
    sleep(2);
    log::add('arduidom', 'info', '# Fin du téléversement (' . $result . ')');
    self::set_daemon_mode("KILLED");
    if( strpos(file_get_contents($logfile),"bytes of flash written") !== false) {
        if( strpos(file_get_contents($logfile),"bytes of flash verified") !== false) {
            if( strpos(file_get_contents($logfile),"avrdude done.") !== false) {
            return "OK";// do stuff
            }
        }
    }
    return $result;
}


    public function event()
{
    //log::add('arduidom', 'debug', 'arduidom event() called');
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
        //$results = print_r($_options, true);
        //log::add('arduidom', 'debug', 'execute(' . $results . ') called');
        //exec('echo "execute 2" >> /tmp/arduidom');
        if ($this->getType() == 'action') {
            try {
                //exec('echo "execute 3" >> /tmp/arduidom');
                //log::add('arduidom', 'debug', 'execute() called on type action');
                //exec('echo "execute 4" >> /tmp/arduidom');
                //log::add('arduidom','info','subTYPE=' . $this->getSubType());
                $subType = $this->getSubType();
                if ($subType == 'slider') {
                    $value = str_replace('#slider#', $_options['slider'], $this->getConfiguration('value'));
                    return arduidom::setPinValue($this->getLogicalId(), $value);
                }

                if ($subType == 'color') {
                    $value = str_replace('#color#', $_options['color'], $this->getConfiguration('value'));
                    return arduidom::setPinValue($this->getLogicalId(), $value);
                }
                if ($subType == 'other') {
                    //GROS LAG RETIRE ICI - VERIFIER QUE CA N'A PAS D'INCIDENCE SUR LES FORMULES EN VALEURS//
                    $value = $this->getConfiguration('value');
                    if ($value != 0 || $value != 1 || $value != "0" || $value != "1") {
                        $value = jeedom::evaluateExpression($value);
                    }
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
