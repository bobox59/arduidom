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

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
include_file('3rdparty', 'jquery.tablesorter/theme.bootstrap', 'css');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.min', 'js');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.widgets.min', 'js');
include_file('core', 'pin', 'config', 'arduidom');
global $ARDUPINMAP_A, $ARDUPINMAP_B, $ARDUPINMAP_C ;
$ArduinoQty = config::byKey('ArduinoQty', 'arduidom', '1');

//if (init('arduid') == '') {
//    throw new Exception('{{L\'id de l\'arduino est requis : }}' . init('op_id'));
//}

//$_AID = init('arduid');
//$daemonRunning = arduidom::ping_arduino($_AID,false);
//if ($daemonRunning != 1) {
//    throw new Exception(__("Action Impossible : Le démon Arduidom " . $_AID . " ne fonctionne pas !", __FILE__));
//}
?>

<ul class="nav nav-pills" id="tab_arid">
    <?php for ($_AID=1; $_AID <= $ArduinoQty; $_AID++) { ?>
    <li<?php $DaemonOK = arduidom::ping_arduino($_AID,false,true); if ($_AID == 1) echo ' class="active"'; ?><?php if ($DaemonOK != 1) echo ' class="disabled"'; ?>><a data-toggle="tab" href="#tab_<?php echo $_AID; ?>">{{Arduino <?php echo '<span class="badge">' . $_AID . '</span>';?> <?php if ($DaemonOK != 1) echo ' (NOK)'; ?>}}</a></li>

    <?php } ?>
</ul>







<div class="tab-content">
    <?php for ($_AID=1; $_AID <= $ArduinoQty; $_AID++) { ?>
        <div class="tab-pane<?php if ($_AID == 1) echo " active" ?>" id="tab_<?php echo $_AID ?>">






            <div id='div_configurePinAlert<?php echo $_AID ?>' style="display: none;"></div>
            Configuration des PINs de l'arduino n°<?php echo $_AID ?>
            <a class="btn btn-success btn-xs pull-right" id="bt_configurePinSave<?php echo $_AID ?>" style="color : white;"><i class="fa fa-check"></i> Sauvegarder</a>
            <br/><br/>
            <table class="table table-bordered table-condensed tablesorter" id="table_configurePin<?php echo $_AID ?>">
                <thead>
                <tr>
                    <th>{{Arduino Pin}}</th>
                    <!--<th>{{Def}}</th>-->
                    <th>{{Logical PIN}}</th>
                    <th>{{Mode}}</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $shieldEthernet = 0;
                if (config::byKey('A' . $_AID . '_port', 'arduidom', 'none') == "Network") {
                    $shieldEthernet = 1;
                }
                $modelPinMap = config::byKey('A' . $_AID . '_model', 'arduidom', 'none');
                $ARDUPINMAP = '';
                if ($modelPinMap == "uno" || $modelPinMap == "duemilanove328" || $modelPinMap == "leo" || $modelPinMap == "nano168" || $modelPinMap == "nano328") $ARDUPINMAP = $ARDUPINMAP_A;
                if ($modelPinMap == "mega1280" || $modelPinMap == "mega2560") $ARDUPINMAP = $ARDUPINMAP_B;
                if ($modelPinMap == "due") $ARDUPINMAP = $ARDUPINMAP_C;

                foreach ($ARDUPINMAP as $logicalId => $pin) {

                    $model = config::byKey('A' . $_AID . '_model', 'arduidom');
                    echo '<tr class="pin" data-logicalId="' . (intval($_AID * 1000) + intval($logicalId)) . '">';
                    echo '<td>' . $pin['arduport'] . '</td>';
                    echo '<td>' . (intval($_AID * 1000) + intval($logicalId)) . '</td>';
                    echo '<td>';



                    // R√©glage des Pins PWM, DIGITAL, ANALOGIQUE et INT (compatible r√©cepteur radio)
                    $pinPWM = 0;
                    $pinINT = 0;
                    $pinANA = 0;
                    $pinDIG = 0;
                    //DUO
                    if ($model == "due") {
                        $pinDIG = 54;
                        $pinANA = 14;
                        if ($logicalId == 2 || $logicalId == 3 || $logicalId == 4 || $logicalId == 5 || $logicalId == 6 || $logicalId == 7 || $logicalId == 8 || $logicalId == 9 || $logicalId == 10 || $logicalId == 11 || $logicalId == 12 || $logicalId == 13) {
                            $pinPWM = 1;
                        } else { $pinPWM = 0; }
                        if ($logicalId > 2 && $logicalId < 66) {
                            $pinINT = 1;
                        } else { $pinINT = 0; }
                    }

                    //MEGA 2560
                    if ($model == "mega1280" || $model == "mega2560") {
                        $pinDIG = 54;
                        $pinANA = 16;
                        if ($logicalId == 2 || $logicalId == 3 || $logicalId == 4 || $logicalId == 5 || $logicalId == 6 || $logicalId == 7 || $logicalId == 8 || $logicalId == 9 || $logicalId == 10 || $logicalId == 11 || $logicalId == 12 || $logicalId == 13 || $logicalId == 46 || $logicalId == 47 || $logicalId == 48) {
                            $pinPWM = 1;
                        } else { $pinPWM = 0; }
                        if ($logicalId == 2 || $logicalId == 3 || $logicalId == 18 || $logicalId == 19 || $logicalId == 20 || $logicalId == 21) {
                            $pinINT = 1;
                        } else { $pinINT = 0; }
                    }

                    //Uno, Nano, Duemi et Leo
                    if ($model == "uno" || $model == "duemilanove168" || $model == "duemilanove328" || $model == "leo" || $model == "nano168" || $model == "nano328") {
                        $pinDIG = 14;
                        $pinANA = 6;
                        if ($logicalId == 3 || $logicalId == 5 || $logicalId == 6 || $logicalId == 9 || $logicalId == 10 || $logicalId == 11) {
                            $pinPWM = 1;
                        } else { $pinPWM = 0; }
                        if ($logicalId == 2 || $logicalId == 3) {
                            $pinINT = 1;
                        } else { $pinINT = 0; }
                    }

                    if ($pin['ethernet'] == 1 && $shieldEthernet == 1) {
                        echo '<select class="form-control input-sm pinAttr" data-l1key="A' . $_AID . '_pin::' . $logicalId . '">';
                        echo '<option value="disable">{{Réservée au Shield Ethernet}}</option>'; // Attention si modifs ici, mettre a jour egalement le AJAX !
                    } else {
                        if ($pin['reserved'] == 0) {
                            echo '<select class="form-control input-sm pinAttr" data-l1key="A' . $_AID . '_pin::' . $logicalId . '">';
                            echo '<option value="disable">{{Désactiver}}</option>'; // Attention si modifs ici, mettre a jour egalement le AJAX !
                            if ($logicalId >= $pinDIG + $pinANA) {
                                echo '<option value="custout">{{Sortie Customisée}}</option>';
                                echo '<option value="custin">{{Entrée Customisée}}</option>';
                            } else {
                                if ($logicalId >= $pinDIG && $logicalId < $pinDIG + $pinANA) {
                                    echo '<option value="ain">{{Entrée analogique}}</option>';
                                    echo '<option value="out">{{Sortie Digitale}}</option>';
                                } else {
                                    echo '<option value="in">{{Entrée Digitale}}</option>';
                                    echo '<option value="inup">{{Entrée Digitale avec Pull-Up}}</option>';
                                    echo '<option value="out">{{Sortie Digitale}}</option>';
                                    if ($pinPWM == 1) { echo '<option value="pout">{{Sortie PWM}}</option>'; }
                                    echo '<option value="dht1">{{Sonde DHT11/DHT22 n°1}}</option>';
                                    echo '<option value="dht2">{{Sonde DHT11/DHT22 n°2}}</option>';
                                    echo '<option value="dht3">{{Sonde DHT11/DHT22 n°3}}</option>';
                                    echo '<option value="dht4">{{Sonde DHT11/DHT22 n°4}}</option>';
                                    echo '<option value="dht5">{{Sonde DHT11/DHT22 n°5}}</option>';
                                    echo '<option value="dht6">{{Sonde DHT11/DHT22 n°6}}</option>';
                                    echo '<option value="dht7">{{Sonde DHT11/DHT22 n°7}}</option>';
                                    echo '<option value="dht8">{{Sonde DHT11/DHT22 n°8}}</option>';
                                    echo '<option value="rout">{{Émetteur Radio 433 MHz}}</option>';
                                    if ($pinINT == 1) { echo '<option value="rin">{{Récepteur Radio 433 MHz}}</option>'; }
                                    echo '</select>';
                                }
                            }
                        } else {
                            if ($logicalId > 500 && $logicalId < 600) {
                                echo '<select class="form-control input-sm pinAttr" data-l1key="A' . $_AID . '_pin::' . $logicalId . '">';
                                echo '<option value="custin">{{Réservée aux sondes DHT}}</option>'; // Attention si modifs ici, mettre a jour egalement le AJAX !
                            } else {
                                echo '<select class="form-control input-sm pinAttr" data-l1key="A' . $_AID . '_pin::' . $logicalId . '">';
                                echo '<option value="disable">{{Réservée a la communication USB}}</option>'; // Attention si modifs ici, mettre a jour egalement le AJAX !
                            }
                        }

                    }
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>


        </div>
    <?php } ?>
</div>


<script>
    <?php for ($_AID=1; $_AID <= $ArduinoQty; $_AID++) { ?>
    $('#bt_configurePinSave<?php echo $_AID ?>').on('click', function() {
        jeedom.config.save({
            configuration: $('#table_configurePin<?php echo $_AID ?>').getValues('.pinAttr')[0],
            plugin: 'arduidom',
            success: function() {
                jeedom.config.load({
                    configuration: $('#table_configurePin<?php echo $_AID ?>').getValues('.pinAttr')[0],
                    plugin: 'arduidom',
                    success: function(data) {
                        $('#table_configurePin<?php echo $_AID ?>').setValues(data, '.pinAttr');
                    }
                });
                modifyWithoutSave = false;
                $('#div_configurePinAlert<?php echo $_AID ?>').showAlert({message: '{{Sauvegarde réussie}}', level: 'success'});
            }
        });

        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/config.ajax.php", // url du fichier php
            data: {
                action: "addKey",
                value: json_encode($('#table_configurePin<?php echo $_AID ?>').getValues('.pinAttr')[0]),
                plugin: 'arduidom'
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('#div_configurePinAlert<?php echo $_AID ?>'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_configurePinAlert<?php echo $_AID ?>').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_configurePinAlert<?php echo $_AID ?>').showAlert({message: '{{Sauvegarde effetuée}}', level: 'success'});
                $.ajax({// fonction permettant de faire de l'ajax
                    type: "POST", // methode de transmission des données au fichier php
                    url: "plugins/arduidom/core/ajax/arduidom.ajax.php", // url du fichier php
                    data: {
                        action: "setPinMapping<?php echo $_AID ?>"
                    },
                    dataType: 'json',
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error, $('#div_configurePinAlert<?php echo $_AID ?>'));
                    },
                    success: function(data) { // si l'appel a bien fonctionné
                        if (data.state != 'ok') {
                            $('#div_configurePinAlert<?php echo $_AID ?>').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                    }
                });
            }
        });
    });

    jeedom.config.load({
        configuration: $('#table_configurePin<?php echo $_AID ?>').getValues('.pinAttr')[0],
        plugin: 'arduidom',
        success: function(data) {

            $('#table_configurePin<?php echo $_AID ?>').setValues(data, '.pinAttr');
        }
    });

    <?php } ?>
</script>


<script>
    initTableSorter();

</script>