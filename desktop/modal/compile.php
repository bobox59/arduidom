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
//sendVarToJs('debugMode_slaveId', init('slave_id'));
echo '<div class="alert alert-warning">{{En attente...}}</div>';
?>
<div id='div_compileDebug' style="display: none;"></div>
<a class="btn btn-warning pull-right" data-state="0" id="bt_compileStart"><i class="fa fa-play"></i> {{Compiler...}}</a>
<a class="btn btn-warning pull-right" data-state="1" id="bt_compileLogStopStart"><i class="fa fa-pause"></i> {{Pause}}</a>
<input class="form-control pull-right" id="in_compileLogSearch" style="width : 300px;" placeholder="{{Rechercher}}" />
<br/><br/><br/>
<pre id='pre_compilelog' style='overflow: auto; height: 80%;with:90%;'></pre>


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
                log : 'arduidom_log',
                slaveId : debugMode_slaveId,
                display : $('#pre_compilelog'),
                search : $('#in_compileLogSearch'),
                control : $('#bt_compileLogStopStart'),
            });
        }
    });

    $('#bt_compileStart').on('click',function(){
        $.ajax({
            type: 'POST',
            url: 'plugins/arduidom/core/ajax/arduidom.ajax.php',
            data: {
                action: 'CompileArduino3',
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error, $('#div_compileDebug'));
            },
            success: function (data) {
                if (data.state != 'ok') {
                    $('#div_compileDebug').showAlert({message: data.result, level: 'danger'});
                    //    return;
                }
                jeedom.log.autoupdate({
                    log : 'arduidom_log',
                    display : $('#pre_compilelog'),
                    search : $('#in_compileLogSearch'),
                    control : $('#bt_compileLogStopStart'),
                });
            }
        });
    });

</script>