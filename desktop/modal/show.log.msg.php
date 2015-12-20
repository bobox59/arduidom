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
    throw new Exception('401 Unauthorized');
}
//if (config::byKey('enableLogging', 'arduidom', 0) == 0) {
//    echo '<div class="alert alert-danger">{{Vous n\'avez pas activé l\'enregistrement de tous les messages : allez dans Générale -> Plugin puis rfxcom et coché la case correspondante}}</div>';
//}
$_AID = init('arduid');
$daemonRunning = arduidom::checkdaemon($_AID);
if ($daemonRunning != 1) {
    throw new Exception(__("Action Impossible : Le démon Arduidom " . $_AID . " ne fonctionne pas !", __FILE__));
}
?>


<pre id='pre_ardulog' style='overflow: auto; height: 95%;with:90%;'></pre>


<script>
    getArduLog(1);

    function getArduLog(_autoUpdate) {
        $.ajax({
            type: 'POST',
            url: 'core/ajax/log.ajax.php',
            data: {
                action: 'get',
                logfile: 'arduidom.message'
            },
            dataType: 'json',
            global: false,
            error: function(request, status, error) {
                setTimeout(function() {
                    getJeedomLog(_autoUpdate, _log)
                }, 3000);
            },
            success: function(data) {
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                var log = '';
                var regex = /<br\s*[\/]?>/gi;
                for (var i in data.result.reverse()) {
                    log += data.result[i][2].replace(regex, "\n");
                }
                $('#pre_ardulog').text(log);
                $('#pre_ardulog').scrollTop($('#pre_ardulog').height() + 200000);
                if (!$('#pre_ardulog').is(':visible')) {
                    _autoUpdate = 0;
                }

                if (init(_autoUpdate, 0) == 1) {
                    setTimeout(function() {
                        getArduLog(_autoUpdate)
                    }, 1000);
                }
            }
        });
    }

</script>