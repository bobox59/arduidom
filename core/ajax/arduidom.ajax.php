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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');
    include_file('core', 'pin', 'config', 'arduidom');


    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    if (init('action') == 'resetDeamon') {
        if (arduidom::set_daemon_mode("KILLED")) {
            ajax::success();
        } else {
            ajax::error("Le démon n'a pas démarré !");
        }
    }


    if (init('action') == 'startDaemon') {
        if (arduidom::deamon_start(true)) {
            ajax::success();
        } else {
            ajax::error("Le démon n'a pas démarré !");
        }
    }
    if (init('action') == 'bt_Prerequis') {
        arduidom::dependancy_install();
        ajax::success();
    }
    if (init('action') == 'stopDaemon') {
        if (arduidom::deamon_stop()) {
            ajax::success();
        } else {
            ajax::error("Le démon n'a pas été stoppé !");
        }
    }

    if (init('action') == 'checkDaemon') {
        if (arduidom::deamon_info()['state'] == 'ok') {
            ajax::success();
        } else {
            ajax::error("Le démon ne fonctionne pas !");
        }
    }

    for ($i=1; $i < 9; $i++) {
        if (init('action') == 'checkDaemon' . $i) {
            if (arduidom::ping_arduino($i,false) == 1) {
                ajax::success();
            } else {
                ajax::error("L'Arduino " . $i . " ne fonctionne pas !");
            }
        }
        if (init('action') == 'FlashArduino' . $i) {
            log::add('arduidom', 'info', 'FlashArduino STEP 1: Exec avrdude and wait finish...');
            $port = config::byKey('A' . $i . '_port', 'arduidom', 'none');
            if ($port == 'Network') {
                ajax::error("Impossible de téléverser vers un arduino Ethernet !", 1);
            }
            $chk = arduidom::FlashArduino($i);
            log::add('arduidom', 'info', 'FlashArduino STEP 2: avrdude sur arduino n°' . $i . ' = ' . $chk);
            arduidom::set_daemon_mode("KILLED");
            if ($chk == "OK") {
                ajax::success("L'arduino a été programmé !");
            } else {
                ajax::error("Il y a eu des erreurs pendant televersement de l'arduino, voir /tmp/avrdude.log...  " . $chk, 1);
            }
        }

        if (init('action') == 'CompileArduino' . $i) {
            log::add('arduidom', 'info', 'CompileArduino STEP 1: Exec ino build and wait finish...');
            $chk = arduidom::CompileArduino($i);
            log::add('arduidom', 'info', 'CompileArduino STEP 2: ino build finished.' . $chk);
            sleep(1);
            ajax::success();
        }

        if (init('action') == 'setPinMapping' . $i) {
            ajax::success(arduidom::setPinMapping($i));
        }

    } // end of For 1 to 8

    if (init('action') == 'pinMapping' ) {
        global $ARDUPINMAP_A, $ARDUPINMAP_B, $ARDUPINMAP_C, $ARDUPINMAP_D, $ARDUPINMAP_E ;

        //$result = config::searchKey('pin::', 'arduidom');
        $result = '';

        for ($k=1; $k < 10; $k++) {

            $modelPinMap = config::byKey('A' . $k . '_model', 'arduidom', 'none');
            $ARDUPINMAP = '';
            if ($modelPinMap == "uno" || $modelPinMap == "duemilanove328" || $modelPinMap == "leo" || $modelPinMap == "nano168" || $modelPinMap == "nano328") $ARDUPINMAP = $ARDUPINMAP_A;
            if ($modelPinMap == "mega1280" || $modelPinMap == "mega2560") $ARDUPINMAP = $ARDUPINMAP_B;
            if ($modelPinMap == "due") $ARDUPINMAP = $ARDUPINMAP_C;
            if ($modelPinMap == "esp201") $ARDUPINMAP = $ARDUPINMAP_D;
            if ($modelPinMap == "d1mini") $ARDUPINMAP = $ARDUPINMAP_E;

            if ($ARDUPINMAP != '') {

                foreach ($ARDUPINMAP as $logicalId => $pin) {
                    if (isset($pin['user_authorized']) && $pin['user_authorized'] == 1) {
                        $conftxt = "";
                        $config = config::byKey('A' . $k . '_pin::' . $logicalId, 'arduidom');
                        //log::add("arduidom", "debug", '###### $i=' . $i . '   $k=' . $k);
                        //log::add("arduidom", "debug", '###### A' . $k . '_pin::' . $logicalId);
                        if ($config == 'in') { $conftxt = " => Entrée digitale";}
                        if ($config == 'inx') { $conftxt = " => Entrée digitale Inversée";}
                        if ($config == 'inup') { $conftxt = " => Entrée digitale avec Pull-Up";}
                        if ($config == 'out') { $conftxt = " => Sortie digitale";}
                        if ($config == 'outd') { $conftxt = " => Sortie digitale push down";}
                        if ($config == 'rin') { $conftxt = " => Recepteur 433MHz";}
                        if ($config == 'rout') { $conftxt = " => Emetteur 433MHz";}
                        if ($config == 'pout') { $conftxt = " => Sortie PWM";}
                        if ($config == 'dht1') { $conftxt = " HIDE_DHT_1_PIN_" . $logicalId;}
                        if ($config == 'dht2') { $conftxt = " HIDE_DHT_2_PIN_" . $logicalId;}
                        if ($config == 'dht3') { $conftxt = " HIDE_DHT_3_PIN_" . $logicalId;}
                        if ($config == 'dht4') { $conftxt = " HIDE_DHT_4_PIN_" . $logicalId;}
                        if ($config == 'dht5') { $conftxt = " HIDE_DHT_5_PIN_" . $logicalId;}
                        if ($config == 'dht6') { $conftxt = " HIDE_DHT_6_PIN_" . $logicalId;}
                        if ($config == 'dht7') { $conftxt = " HIDE_DHT_7_PIN_" . $logicalId;}
                        if ($config == 'dht8') { $conftxt = " HIDE_DHT_8_PIN_" . $logicalId;}
                        if ($config == 'ain') { $conftxt = " => Entrée analogique";}
                        if ($config == 'custin') { $conftxt = " => Entrée Customisee";}
                        if ($config == 'custout') { $conftxt = " => Sortie Customisee";}
                        if ($config == 'pup') { $conftxt = " => Sortie digitale Pulse UP";}
                        if ($config == 'pdwn') { $conftxt = " => Sortie digitale Pulse DOWN";}
                        if ($config == 'oinv') { $conftxt = " => Sortie digitale à inversion";}
                        if ($config == 'blnk') { $conftxt = " => Sortie digitale à Clignotement";}
                        $result[] = array('plugin' => 'arduidom', 'value' => $config, 'key' => 'pin::'.(($k * 1000) + $logicalId), 'name' => $pin['arduport'] . $conftxt, 'dht' => $dht);
                    }
                }
            }
        }
        ajax::success($result);
    }


    if (init('action') == 'MigrateArduidom') {
        if (arduidom::MigrateDatas() == 1) {
            ajax::success();
        } else {
            ajax::error("Une erreur est survenue pendant la Migration des données Arduidom !");
        }
    }

    if (init('action') == 'LearnRadio') {
        $text = '';
        $etat = '';
        cache::set('arduidom_radio_learn',1);

        $RadioLastCode = cache::byKey('arduidom_radio_lastcode');
        $RadioLastCode = $RadioLastCode->getValue();
        $RadioRepeats = cache::byKey('arduidom_radio_index');
        $RadioRepeats = $RadioRepeats->getValue();
        $RadioLeanMode = cache::byKey('arduidom_radio_learn');
        $RadioLeanMode = $RadioLeanMode->getValue();
        $text = 'UNIX TIME (for debug):' . time() . "\n" . "\n";

        $text = $text . "Mode Apprentissage : " . $RadioLeanMode . " \n";
        if ($RadioRepeats < 3) {
            $text = $text . "Appuyer sur la touche " . (3 - $RadioRepeats) . " fois avec une pause de 3 à 5 secondes entre chaque appui.\n";
            if ($RadioLastCode != '') $text = $text . "Code recu : " . $RadioLastCode . "\n";
        } else {
            $text = $text . "Copiez ce code dans la valeur d'un equipement avec en pin Arduino (Receptions Radios) :  " . $RadioLastCode . " \n";
            $text = $text . "Vous pouvez fermer cette fenetre. \n";
            $etat = "[END SUCCESS]\n";
            cache::set('arduidom_radio_index',0);
            cache::set('arduidom_radio_learn',0);
        }
        if ($etat == '') {
            ajax::success($text);
        } else {
            ajax::success([$etat, $text]);
        }
    }

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}

