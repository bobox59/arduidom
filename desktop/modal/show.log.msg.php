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

if (arduidom::get_daemon_mode() != "OK") {
    if (substr(jeedom::version(),0,1) == 2) event::add('jeedom::alert', array('level' => 'error', 'message' => __("Action Impossible : Le démon ne fonctionne pas !", __FILE__)));
}
?>


<pre id='pre_ardulog' style='overflow: auto; height: 95%;width:90%;'></pre>


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
                console.log("ERR");
                setTimeout(function() {
                    getJeedomLog(_autoUpdate, _log)
                }, 3000);
            },
            success: function(data) {
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                //console.log("data.result1:\n");
                //console.log(data.result[1]);
                var log = '';
                var regex = /<br\s*[\/]?>/gi;
                for (var i in data.result.reverse()) {
                    log += data.result[i];
                    log += "\n";
                }
                //console.log("log:\n");
                //console.log(log);
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