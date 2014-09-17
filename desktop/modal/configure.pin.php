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
global $ARDUPINMAP;
?>
<div id='div_configurePinAlert' style="display: none;"></div>
<a class="btn btn-success btn-xs pull-right" id="bt_configurePinSave" style="color : white;"><i class="fa fa-check"></i> Sauvegarder</a>
<br/><br/>
<table class="table table-bordered table-condensed tablesorter" id="table_configurePin">
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
        foreach ($ARDUPINMAP as $logicalId => $pin) {
            echo '<tr class="pin" data-logicalId="' . $logicalId . '">';
            echo '<td>' . $pin['arduport'] . '</td>';
            echo '<td>' . $logicalId . '</td>';
            echo '<td>';
            if ($pin['reserved'] == 0) {
                echo '<select class="form-control input-sm pinAttr" data-l1key="pin::' . $logicalId . '">';
                echo '<option value="disable">{{Désactiver}}</option>';
                if ($logicalId > 13+1) {
                    echo '<option value="ain">{{Entrée analogique}}</option>';
                } else {
                    echo '<option value="in">{{Entrée Digitale}}</option>';
                    echo '<option value="out">{{Sortie Digitale}}</option>';
                    if ($logicalId == 3+1 || $logicalId == 5+1 || $logicalId == 6+1 || $logicalId == 9+1 || $logicalId == 10+1 || $logicalId == 11+1) {
                        echo '<option value="pout">{{Sortie PWM}}</option>';
                    }
                    echo '<option value="rout">{{Émetteur Radio 315/433 MHz}}</option>';
                    if ($logicalId == 2+1 || $logicalId == 3+1) {
                        echo '<option value="rin">{{Récepteur Radio 315/433 MHz}}</option>';
                    }
                    echo '</select>';
                }
            } else {
                echo 'Reservée pour la communication USB avec le démon ArduiDom';
            }
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

<script>
    initTableSorter();
    $('#bt_configurePinSave').on('click', function() {
        jeedom.config.save({
            configuration: $('#table_configurePin').getValues('.pinAttr')[0],
            plugin: 'arduidom',
            success: function() {
                jeedom.config.load({
                    configuration: $('#table_configurePin').getValues('.pinAttr')[0],
                    plugin: 'arduidom',
                    success: function(data) {
                        $('#table_configurePin').setValues(data, '.pinAttr');
                    }
                });
                modifyWithoutSave = false;
                $('#div_configurePinAlert').showAlert({message: '{{Sauvegarde réussie}}', level: 'success'});
            }
        });

        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/config.ajax.php", // url du fichier php
            data: {
                action: "addKey",
                value: json_encode($('#table_configurePin').getValues('.pinAttr')[0]),
                plugin: 'arduidom'
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('#div_configurePinAlert'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_configurePinAlert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_configurePinAlert').showAlert({message: '{{Sauvegarde effetuée}}', level: 'success'});
                $.ajax({// fonction permettant de faire de l'ajax
                    type: "POST", // methode de transmission des données au fichier php
                    url: "plugins/arduidom/core/ajax/arduidom.ajax.php", // url du fichier php
                    data: {
                        action: "setPinMapping",
                    },
                    dataType: 'json',
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error, $('#div_configurePinAlert'));
                    },
                    success: function(data) { // si l'appel a bien fonctionné
                        if (data.state != 'ok') {
                            $('#div_configurePinAlert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                    }
                });
            }
        });
    });

    jeedom.config.load({
        configuration: $('#table_configurePin').getValues('.pinAttr')[0],
        plugin: 'arduidom',
        success: function(data) {
            $('#table_configurePin').setValues(data, '.pinAttr');
        }
    });
</script>