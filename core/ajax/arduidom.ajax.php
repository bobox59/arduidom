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



    if (init('action') == 'updateArduidom') {
        arduidom::updateArduidom();
        ajax::success();
    }

    for ($i=1; $i < 9; $i++) {
        if (init('action') == 'restartDaemon' . $i) {
            //arduidom::restoreStates(1);
            arduidom::stopdaemon($i);
            arduidom::startdaemon($i);
            if (arduidom::checkdaemon($i,false,true) == 1) {
                ajax::success();
            } else {
                ajax::error("Le démon " . $i . " n'a pas démarré");
            }
        }

        if (init('action') == 'stopDaemon' . $i) {
            log::add('arduidom', 'info', 'Desactivation du démon ' . $i . '...');
            config::save('A' . $i . "_daemonenable", 0, 'arduidom');
            arduidom::stopdaemon($i);
            if (arduidom::checkdaemon($i,false,true) == 0) {
                ajax::success();
            } else {
                ajax::error("Le démon " . $i . " ne s'est pas arreté");
            }
        }

        if (init('action') == 'checkDaemon' . $i) {
            if (arduidom::checkdaemon($i,false) == 1) {
                ajax::success();
            } else {
                ajax::error("Le démon " . $i . " ne fonctionne pas !");
            }
        }
        if (init('action') == 'FlashArduino' . $i) {
            log::add('arduidom', 'info', 'FlashArduino STEP 1: Exec avrdude and wait finish...');
            $chk = arduidom::FlashArduino($i);
            log::add('arduidom', 'info', 'FlashArduino STEP 2: avrdude finished.' . $chk);
            sleep(1);
            log::add('arduidom', 'info', 'FlashArduino STEP 3: Start  Daemon ' . $i);
            if (config::byKey('A' . $i . "_daemonenable","arduidom",0) == 1) $chk = arduidom::startdaemon($i);
            if ($chk == 1) {
                log::add('arduidom', 'info', 'FlashArduino STEP 4: Daemon ' . $i . ' started' . $chk);
                ajax::success("Le démon " . $i . " a correctement démarré apres le televersement de l'arduino !");
            } else {
                log::add('arduidom', 'info', 'FlashArduino STEP 4: Daemon ' . $i . ' NOT started after flash' . $chk);
                ajax::error("Le démon " . $i . " n'a pas démarré apres le televersement de l'arduino !");
            }
        }

        if (init('action') == 'CompileArduino' . $i) {
            log::add('arduidom', 'info', 'CompileArduino STEP 1: Exec ino build and wait finish...');
            $chk = arduidom::CompileArduino($i);
            log::add('arduidom', 'info', 'CompileArduino STEP 2: ino build finished.' . $chk);
            sleep(1);
            //log::add('arduidom', 'info', 'SETP2: Start  Daemon ' . $i);
            //$chk = arduidom::startdaemon($i);
            //if ($chk == 1) {
            //    log::add('arduidom', 'info', 'STEP3: Daemon ' . $i . ' started' . $chk);
            ajax::success();
            //} else {
            //    log::add('arduidom', 'info', 'STEP3: Daemon ' . $i . ' NOT started after flash' . $chk);
            //    ajax::error("Le démon " . $i . " n'a pas démarré apres le televersement de l'arduino !");
            //}
        }

        if (init('action') == 'setPinMapping' . $i) {
            ajax::success(arduidom::setPinMapping($i));
        }

    } // end of For 1 to 9

    if (init('action') == 'pinMapping' ) {
        global $ARDUPINMAP_A, $ARDUPINMAP_B, $ARDUPINMAP_C ;

        //$result = config::searchKey('pin::', 'arduidom');
        $result = '';

        for ($k=1; $k < 10; $k++) {

            $modelPinMap = config::byKey('A' . $k . '_model', 'arduidom', 'none');
            $ARDUPINMAP = '';
            if ($modelPinMap == "uno" || $modelPinMap == "duemilanove328" || $modelPinMap == "leo" || $modelPinMap == "nano168" || $modelPinMap == "nano328") $ARDUPINMAP = $ARDUPINMAP_A;
            if ($modelPinMap == "mega1280" || $modelPinMap == "mega2560") $ARDUPINMAP = $ARDUPINMAP_B;
            if ($modelPinMap == "due") $ARDUPINMAP = $ARDUPINMAP_C;

            if ($ARDUPINMAP != '') {

                foreach ($ARDUPINMAP as $logicalId => $pin) {
                    if (isset($pin['user_authorized']) && $pin['user_authorized'] == 1) {
                        $conftxt = "";
                        $config = config::byKey('A' . $k . '_pin::' . $logicalId, 'arduidom');
                        //log::add("arduidom", "debug", '###### $i=' . $i . '   $k=' . $k);
                        //log::add("arduidom", "debug", '###### A' . $k . '_pin::' . $logicalId);
                        if ($config == 'in') { $conftxt = " => Entrée digitale";}
                        if ($config == 'out') { $conftxt = " => Sortie digitale";}
                        if ($config == 'rin') { $conftxt = " => Recepteur 433MHz";}
                        if ($config == 'rout') { $conftxt = " => Emetteur 433MHz";}
                        if ($config == 'pout') { $conftxt = " => Sortie PWM";}
                        if ($config == 'ain') { $conftxt = " => Entrée analogique";}
                        if ($config == 'custin') { $conftxt = " => Entrée Customisee";}
                        if ($config == 'custout') { $conftxt = " => Sortie Customisee";}
                        $result[] = array('plugin' => 'arduidom', 'value' => $config, 'key' => 'pin::'.(($k * 1000) + $logicalId), 'name' => $pin['arduport'] . $conftxt);
                    }
                }
            }
        }
        ajax::success($result);
    }


    if (init('action') == 'FullDebugEnable') {
        if (file_put_contents('/tmp/arduidom_debug_mode_on', "debugON") != false) {
            ajax::success();
        } else {
            ajax::error("Une erreur est survenue pendant l'activation du mode Debug Arduidom ! (Problèmes de droits sur /tmp/arduidom_debug_mode_on ?)");;
        };
    }

    if (init('action') == 'FullDebugDisable') {
        if (unlink("/tmp/arduidom_debug_mode_on") == true) {
            ajax::success();
        } else {
            ajax::error("Une erreur est survenue pendant la désactivation du mode Debug Arduidom ! (Problèmes de droits sur /tmp/arduidom_debug_mode_on ?)");
        }
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
            $text = $text . "Copiez ce code dans la valeur : " . $RadioLastCode . " OK \n";
            $text = $text . "Vous pouvez fermer la fenetre. \n";
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

