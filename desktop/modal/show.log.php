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
?>



<ul class="nav nav-pills" id="tab_arid">
    <li class="active"><a data-toggle="tab" href="#tab_1">{{Démon <span class="badge">1</span> <?php if (arduidom::ping_arduino(1,false,true) != 1) echo ' (NOK)' ?>}}</a></li>
    <li<?php $DaemonOK = arduidom::ping_arduino(2,false,true); if ($DaemonOK != 1) echo ' class="disabled"' ?> ><a data-toggle="tab" href="#tab_2">{{Démon <span class="badge">2</span><?php if ($DaemonOK != 1) echo ' (Offline)' ?>}}</a></li>
    <li<?php $DaemonOK = arduidom::ping_arduino(3,false,true); if ($DaemonOK != 1) echo ' class="disabled"' ?> ><a data-toggle="tab" href="#tab_3">{{Démon <span class="badge">3</span><?php if ($DaemonOK != 1) echo ' (Offline)' ?>}}</a></li>
    <li<?php $DaemonOK = arduidom::ping_arduino(4,false,true); if ($DaemonOK != 1) echo ' class="disabled"' ?> ><a data-toggle="tab" href="#tab_4">{{Démon <span class="badge">4</span><?php if ($DaemonOK != 1) echo ' (Offline)' ?>}}</a></li>
    <li<?php $DaemonOK = arduidom::ping_arduino(5,false,true); if ($DaemonOK != 1) echo ' class="disabled"' ?> ><a data-toggle="tab" href="#tab_5">{{Démon <span class="badge">5</span><?php if ($DaemonOK != 1) echo ' (Offline)' ?>}}</a></li>
    <li<?php $DaemonOK = arduidom::ping_arduino(6,false,true); if ($DaemonOK != 1) echo ' class="disabled"' ?> ><a data-toggle="tab" href="#tab_6">{{Démon <span class="badge">6</span><?php if ($DaemonOK != 1) echo ' (Offline)' ?>}}</a></li>
    <li<?php $DaemonOK = arduidom::ping_arduino(7,false,true); if ($DaemonOK != 1) echo ' class="disabled"' ?> ><a data-toggle="tab" href="#tab_7">{{Démon <span class="badge">7</span><?php if ($DaemonOK != 1) echo ' (Offline)' ?>}}</a></li>
    <li<?php $DaemonOK = arduidom::ping_arduino(8,false,true); if ($DaemonOK != 1) echo ' class="disabled"' ?> ><a data-toggle="tab" href="#tab_8">{{Démon <span class="badge">8</span><?php if ($DaemonOK != 1) echo ' (Offline)' ?>}}</a></li>
</ul>

<div class="tab-content">
    <?php for ($i=1; $i < 9; $i++) { ?>
        <div class="tab-pane<?php if ($i == 1) echo " active" ?>" id="tab_<?php echo $i ?>">
            <hr size="14" color="blue">ARDUINO N° <?php echo $i ?>
            <pre id='pre_ardulog<?php echo $i ?>' style='overflow: auto; height: 95%;width:90%;'></pre>
        </div>
    <?php } ?>
</div>


<script>
<?php for ($i=1; $i < 9; $i++) { ?>
        function getArduLog_<?php echo $i ?>(_autoUpdate) {
            $.ajax({
                type: 'POST',
                url: 'core/ajax/log.ajax.php',
                data: {
                    action: 'get',
                    logfile: 'arduidom_daemon'
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
                    $('#pre_ardulog<?php echo $i ?>').text(log);
                    $('#pre_ardulog<?php echo $i ?>').scrollTop($('#pre_ardulog<?php echo $i ?>').height() + 200000);
                    if (!$('#pre_ardulog<?php echo $i ?>').is(':visible')) {
                        _autoUpdate = 0;
                    }

                    if (init(_autoUpdate, 0) == 1) {
                        setTimeout(function() {
                            getArduLog(_autoUpdate)
                        }, 3000);
                    }
                }
            });
        }
<?php } ?>
    </script>

<?php for ($i=1; $i < 9; $i++) { ?>
<div class="tab-pane<?php if ($i == 1) echo " active" ?>" id="tab_<?php echo $i ?>">
    <script>
        getArduLog_<?php echo $i ?>(1);
    </script>
</div>
<?php } ?>
