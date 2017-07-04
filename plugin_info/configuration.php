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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}

$ArduinoQty = config::byKey('ArduinoQty', 'arduidom', 1);
$Sketch = "Arduidom_unified_v" . (config::byKey("ArduinoRequiredVersion","arduidom",0,true) - 100);
$DaemonReady = arduidom::get_daemon_mode();
?>
<input class="form-control col-md-12" style="background-color: #FFE3AF" value="Cette version de plugin NECESSITE le Sketch <?php echo $Sketch;?> sur vos Arduinos pour pouvoir fonctionner." disabled/>
<br />&nbsp;

<div class="row">
    <div class="form-group">
        <div class="col-lg-3">
            <label class="col-xs-9 control-label-inline">Etat du démon : <?php echo $DaemonReady ?></label>
        </div>
        <div class="col-lg-5">
            <a class="btn btn-danger" id="bt_ResetArduidomDeamonState"><i class="fa fa-arrow-circle-right"></i>{{ Forcer l'etat du démon à KILLED}}</a>&nbsp
        </div>
    </div>

    <hr>

</div>
<div class="row">
    <label class="col-xs-2 control-label">Nombre d'arduino(s) utilisés</label>
    <div class="col-xs-2">
        <select id="Arduinoqty" class="configKey form-control" data-l1key="ArduinoQty">
            <option value="1" id="ArduinoQty">1</option>
            <option value="2" id="ArduinoQty">2</option>
            <option value="3" id="ArduinoQty">3</option>
            <option value="4" id="ArduinoQty">4</option>
            <option value="5" id="ArduinoQty">5</option>
            <option value="6" id="ArduinoQty">6</option>
            <option value="7" id="ArduinoQty">7</option>
            <option value="8" id="ArduinoQty">8</option>
        </select>
    </div>
    Actualiser la page après la Sauvegarde d'un changement.
</div>
<hr>

<ul class="nav nav-pills nav-justified" id="tab_arid">
    <?php for ($i=1; $i <= $ArduinoQty; $i++) {
        if ($i == 1) {
            echo '<li class="active">';
        } else {
            echo '<li>';
        }
        $DaemonReady = arduidom::get_daemon_mode();
        if ($DaemonReady == "OK") {
            $daemonstate = arduidom::ping_arduino($i, false);
        } else {
            $daemonstate = 0;
        }

        echo '<a data-toggle="tab" href="#tab_' . $i . '">{{Arduino ' . $i . ' <span class="label label-' . (($daemonstate == 1) ? 'success' : 'danger') . ' "> PING:' . (($daemonstate == 1) ? 'OK' : 'NOK') . '</span>' . '}}</a></li>';
    } ?>
</ul>

<div class="tab-content" id="arduinotabs">
    <?php for ($i=1; $i <= $ArduinoQty; $i++) { ?>
        <div class="tab-pane<?php if ($i == 1) echo " active" ?>" id="tab_<?php echo $i ?>">
            <hr>
            <form class="form-horizontal">
                <fieldset>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Port de l'Arduino N° <?php echo $i ?></label>
                        <div class="col-lg-9">
                            <select class="configKey form-control" data-l1key="A<?php echo $i ?>_port">
                                <option value="none" id="arduinoportselect">Aucun</option>
                                <?php
                                echo '<optgroup label="Ports USB" />';
                                foreach (arduidom::getUsbArduinos() as $name => $value) {;
                                    echo '<option value="' . $value . '">' . $name . ' (' . $value . ')</option>';
                                };
                                echo '<optgroup label="Réseau" />';
                                echo '<option value="' . "Network" . '">' . "Arduino avec Shield Ethernet / ESP WiFi" . ' (' . "Network" . ')</option>';
                                echo '<optgroup label="Manuel (si non détecté)" />';
                                echo '<option value="' . "/dev/ttyUSB0" . '">' . "Arduino sur USB /dev/ttyUSB0" . ' (' . "/dev/ttyUSB0" . ')</option>';
                                echo '<option value="' . "/dev/ttyUSB1" . '">' . "Arduino sur USB /dev/ttyUSB1" . ' (' . "/dev/ttyUSB1" . ')</option>';
                                echo '<option value="' . "/dev/ttyUSB2" . '">' . "Arduino sur USB /dev/ttyUSB2" . ' (' . "/dev/ttyUSB2" . ')</option>';
                                echo '<option value="' . "/dev/ttyACM0" . '">' . "Arduino sur USB /dev/ttyACM0" . ' (' . "/dev/ttyACM0" . ')</option>';
                                echo '<option value="' . "/dev/ttyACM1" . '">' . "Arduino sur USB /dev/ttyACM1" . ' (' . "/dev/ttyACM1" . ')</option>';
                                echo '<option value="' . "/dev/ttyACM2" . '">' . "Arduino sur USB /dev/ttyACM2" . ' (' . "/dev/ttyACM2" . ')</option>';
                                echo '<option value="' . "/dev/ttyAMA0" . '">' . "Arduino sur port série GPIO Raspberry" . ' (' . "/dev/ttyAMA0" . ')</option>';
                                echo '<option value="' . "/dev/ttyS0" . '">' . "Arduino sur USB /dev/ttyS0" . ' (' . "/dev/ttyS0" . ')</option>';
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Modèle de l'Arduino N° <?php echo $i ?></label>
                        <div class="col-lg-4">
                            <select class="configKey form-control" data-l1key="A<?php echo $i ?>_model">
                                <option value="" id="arduinomodelselect">Aucun - Désactivé</option>
                                <optgroup label="Modèles USB" />
                                <option value="uno" id="arduinomodelselect">Arduino UNO</option>
                                <option value="nano328" id="arduinomodelselect">Arduino NANO (ATMega 328)</option>
                                <option value="mega1280" id="arduinomodelselect">Arduino MEGA 1280</option>
                                <option value="mega2560" id="arduinomodelselect">Arduino MEGA 2560</option>
                                <option value="due" id="arduinomodelselect">Arduino DUE (Non flashable par JeeDom)</option>
                                <option value="leo" id="arduinomodelselect">Arduino LEONARDO (Non flashable par JeeDom)</option>
                                <optgroup label="Modèles WiFi" />
                                <option value="esp201" id="arduinomodelselect">ESP 201 (Wifi - Flash par IDE via USB)</option>
                                <option value="d1mini" id="arduinomodelselect">WeMos D1 Mini (Wifi - Flash par IDE via USB)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Adresse IP</label>
                        <div class="col-lg-2">
                            <input class="configKey form-control" data-l1key="A<?php echo $i ?>_daemonip" />
                        </div>
                    </div>
                    <!--
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Test de liaison</label>
                        <div class="col-lg-4">
                            <a class="btn btn-success" id="bt_CheckArduidomDeamon<?php echo $i ?>"><i class='fa fa-check-square-o'></i>{{ Vérifier la liaison avec le N°<?php echo $i ?>}}</a>&nbsp;
                        </div>
                    </div>
                    -->
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Téléversement</label>
                        <div class="col-lg-4">
                            <a class="btn btn-danger" id="bt_FlashArduino<?php echo $i ?>"><i class="fa fa-arrow-circle-right"></i>{{ Téléverser le Sketch <?php echo $Sketch;?> sur l arduino n°<?php echo $i ?>}}</a>&nbsp;
                        </div>
                    </div>
                    <!-- <a class="btn btn-primary" id="bt_CompileArduino<?php echo $i ?>"><i class="fa fa-check-circle"></i>{{ Compiler le Sketch (en test...)}}</a>&nbsp; -->

                </fieldset>
            </form>
        </div>
    <?php } ?>  <!-- FIN DU For PHP -->
</div>
<hr size="10">
<form class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <label class="col-lg-3 control-label">Test de liaison</label>
            <div class="col-lg-4">
                <a class="btn btn-success" id="bt_CheckArduidomDeamon"><i class='fa fa-check-square-o'></i>{{ Vérifier la liaison avec le(s) Arduino(s)}}</a>&nbsp;
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label">Sketch</label>
            <div class="col-lg-4">
                <a href="plugins/arduidom/ressources/<?php echo $Sketch; ?>.zip" class="btn btn-info" id="bt_Download"><i class='fa fa-download'></i> Télécharger les Sketchs pour USB & Shield Ethernet (<?php echo $Sketch;?>)</a>&nbsp;&nbsp;&nbsp;
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label">Migration</label>
            <div class="col-lg-4">
                <a class="btn btn-danger" id="bt_MigrateArduidom"><i class='fa fa-exclamation-triangle'></i> FORCER la Migration des données</a>
            </div>
        </div>
        <?php if (substr(jeedom::version(),0,1) == 1) {
            echo'<div class="form-group">';
            echo'    <label class="col-lg-3 control-label">Controles (Jeedom < 2.0)</label>';
            echo'    <div class="col-lg-4">';
            echo '<hr><a class="btn btn-danger" id="bt_RestartArduidomDeamon"><i class="fa fa-arrow-circle-right"></i>{{ (Re)Démarrer le démon (bouton temporaire pour retro-compatibilité v2 sur jeedom v1.2)}}</a>&nbsp;';
            echo '<hr><a class="btn btn-warning" id="bt_Prerequis"><i class="fa fa-arrow-circle-right"></i>{{ (Re)installer les dépendances (bouton temporaire pour retro-compatibilité v2 sur jeedom v1.2)}}</a>&nbsp;';
            echo '<hr>Le log du démon python se trouve dans /tmp/arduidom_daemon';
            echo'    </div>';
            echo'</div>';
        } ?>
    </fieldset>
</form>


<script>
    var jsinitok = false;

    $('#bt_ResetArduidomDeamonState').on('click', function () {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/arduidom/core/ajax/arduidom.ajax.php", // url du fichier php
            data: {
                action: "resetDeamon"
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_alert').showAlert({message: 'L état du démon a été correctement changé', level: 'success'});
                $('#ul_plugin .li_plugin[data-plugin_id=arduidom]').click();
            }
        });
        //history.go(0);
    });

    $('#bt_RestartArduidomDeamon').on('click', function () {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/arduidom/core/ajax/arduidom.ajax.php", // url du fichier php
            data: {
                action: "startDaemon"
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_alert').showAlert({message: 'Le démon a été correctement redémarré', level: 'success'});
                $('#ul_plugin .li_plugin[data-plugin_id=arduidom]').click();
            }
        });
        //history.go(0);
    });
    $('#bt_Prerequis').on('click', function () {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/arduidom/core/ajax/arduidom.ajax.php", // url du fichier php
            data: {
                action: "bt_Prerequis"
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_alert').showAlert({message: 'Les dépendances ont été installées', level: 'success'});
            }
        });
        //history.go(0);
    });
    $('#bt_StopArduidomDeamon').on('click', function () {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/arduidom/core/ajax/arduidom.ajax.php", // url du fichier php
            data: {
                action: "stopDaemon"
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_alert').showAlert({message: 'Le démon a été correctement stoppé', level: 'success'});
                $('#ul_plugin .li_plugin[data-plugin_id=arduidom]').click();
            }
        });
        //history.go(0);
    });

    $('#bt_CheckArduidomDeamon').on('click', function () {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/arduidom/core/ajax/arduidom.ajax.php", // url du fichier php
            data: {
                action: "checkDaemon"
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#ul_plugin .li_plugin[data-plugin_id=arduidom]').click();
            }
        });
        //history.go(0);
    });

</script>

<?php for ($i=1; $i <= $ArduinoQty; $i++) { ?>
    <script>

        $('#bt_CheckArduidomDeamon<?php echo $i ?>').on('click', function () {
            $.ajax({// fonction permettant de faire de l'ajax
                type: "POST", // methode de transmission des données au fichier php
                url: "plugins/arduidom/core/ajax/arduidom.ajax.php", // url du fichier php
                data: {
                    action: "checkDaemon<?php echo $i ?>"
                },
                dataType: 'json',
                error: function (request, status, error) {
                    handleAjaxError(request, status, error);
                },
                success: function (data) { // si l'appel a bien fonctionné
                    if (data.state != 'ok') {
                        $('#div_alert').showAlert({message: data.result, level: 'danger'});
                        return;
                    }
                    $('#div_alert').showAlert({message: 'L\'Arduino <?php echo $i ?> fonctionne correctement', level: 'success'});
                    $('#ul_plugin .li_plugin[data-plugin_id=arduidom]').click();
                }
            });
            //history.go(0);
        });

        $('#bt_FlashArduino<?php echo $i ?>').on('click', function () {
            bootbox.confirm('{{<center>ATTENTION</center> !<br><br>Assurez vous d avoir sélectionné le BON MODELE et le bon PORT puis avoir SAUVEGARDER avant de continuer.<br> En cas d erreur, votre arduino peut ne plus fonctionner !<br><br><br>ATTENTION ! Nouvelle procedure depuis Jeedom 2.0 : Le démon va s arreter et couper tous les arduino(s) pendant le flash.<br>Patientez 10 secondes apres la roue crantee pour que le demon se relance de lui meme... <br><br><br>}}', function (result) {
                if (result) {
                    $.ajax({
                        type: 'POST',
                        url: 'plugins/arduidom/core/ajax/arduidom.ajax.php',
                        data: {
                            action: "FlashArduino<?php echo $i ?>"
                        },
                        dataType: 'json',
                        error: function (request, status, error) {
                            handleAjaxError(request, status, error);
                            //handleAjaxError(request, status, error, $('#div_compileDebug<?php echo $i ?>'));
                        },
                        success: function (data) {
                            if (data.state != 'ok') {
                                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                                //$('#div_compileDebug<?php echo $i ?>').showAlert({message: data.result, level: 'danger'});
                                return;
                            }
                            $('#div_alert').showAlert({message: 'Votre arduino <?php echo $i ?> a été programmé', level: 'success'});
                            jeedom.log.autoupdate({
                                log : 'arduidom_log<?php echo $i ?>',
                                display : $('#pre_compilelog<?php echo $i ?>'),
                                search : $('#in_compileLogSearch<?php echo $i ?>'),
                                control : $('#bt_compileLogStopStart<?php echo $i ?>')
                            });
                            $('#ul_plugin .li_plugin[data-plugin_id=arduidom]').click();
                        }
                    });
                }
                else
                {
                    x="You pressed Cancel!";
                }
                //history.go(0);
            });
        });


        $('#bt_CompileArduino<?php echo $i ?>').on('click', function () {
            $.ajax({
                type: 'POST',
                url: 'plugins/arduidom/core/ajax/arduidom.ajax.php',
                data: {
                    action: 'CompileArduino<?php echo $i ?>'
                },
                dataType: 'json',
                error: function (request, status, error) {
                    handleAjaxError(request, status, error, $('#div_compileDebug<?php echo $i ?>'));
                },
                success: function (data) {
                    if (data.state != 'ok') {
                        $('#div_compileDebug<?php echo $i ?>').showAlert({message: data.result, level: 'danger'});
                        //    return;
                    }
                    jeedom.log.autoupdate({
                        log : 'arduidom_log<?php echo $i ?>',
                        display : $('#pre_compilelog<?php echo $i ?>'),
                        search : $('#in_compileLogSearch<?php echo $i ?>'),
                        control : $('#bt_compileLogStopStart<?php echo $i ?>')
                    });
                }
            });
        });

</script>
<?php } ?>  <!-- FIN DU For PHP -->
<script>
    $('#bt_compileLogStopStart').on('click',function(){
        if($(this).attr('data-state') == 1){
            $(this).attr('data-state',0);
            $(this).removeClass('btn-warning').addClass('btn-success');
            $(this).html('<i class="fa fa-play"></i> {{Reprise}}');

        }else{
            $(this).removeClass('btn-success').addClass('btn-warning');
            $(this).html('<i class="fa fa-pause"></i> {{Pause}}');
            $(this).attr('data-state',1);
            jeedom.log.autoupdate({
                log : 'arduidom_log<?php echo $i ?>',
                display : $('#pre_compilelog<?php echo $i ?>'),
                search : $('#in_compileLogSearch<?php echo $i ?>'),
                control : $('#bt_compileLogStopStart<?php echo $i ?>')
            });
        }
    });

    $('#bt_MigrateArduidom').on('click', function () {
        bootbox.confirm('{{<center>Attention !</center><br><br>Etape extremement IMPORTANTE et DELICATE<br> Vous pouvez forcer une migration des données <br>apres avoir mis a jour en 1.xx <br>car il y a eu de tres gros changements depuis !<br> mais NORMALEMENT la migration se fait automatiquement<br> a la mise a jour du plugin.}}', function (result) {
            if (result) {
                $.ajax({// fonction permettant de faire de l'ajax
                    type: "POST", // methode de transmission des données au fichier php
                    url: "plugins/arduidom/core/ajax/arduidom.ajax.php", // url du fichier php
                    data: {
                        action: "MigrateArduidom"
                    },
                    dataType: 'json',
                    error: function (request, status, error) {
                        handleAjaxError(request, status, error);
                    },
                    success: function (data) { // si l'appel a bien fonctionné
                        if (data.state != 'ok') {
                            $('#div_alert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                        $('#div_alert').showAlert({message: 'La Migration a été correctement effectuée.', level: 'success'});
                    }
                });
            }
        });
        //history.go(0);
    });

    $('#Arduinoqty').change(function() {
        if (jsinitok) {
            console.log("Qty Changed ! Saving...");
            document.getElementById("bt_savePluginConfig").click();
            //location.reload();
            $('#ul_plugin .li_plugin[data-plugin_id=arduidom]').click();
        }
    });

    //$(document).ready(function(){
    setTimeout(function() {
        jsinitok = true;
        console.log("initOK");
    }, 3000);
</script>
