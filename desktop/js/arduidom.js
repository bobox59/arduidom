
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

$(function() {
    $('#bt_configurePin').on('click', function() {
        $('#md_modal').dialog({title: "{{Configuration des pins}}"});
        $('#md_modal').load('index.php?v=d&plugin=arduidom&modal=configure.pin').dialog('open');
    });

    pin_select = getPinMapping();

    $('#table_cmd').delegate('.cmdAttr[data-l1key=logicalId]', 'change', function() {
        handlePin($(this).closest('tr'));
    });
    $('#table_cmd').delegate('.cmdAttr[data-l1key=configuration][data-l2key=value]', 'change', function() {
        handlePin($(this).closest('tr'));
    });
});

function getPinMapping() {
    var result = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/arduidom/core/ajax/arduidom.ajax.php", // url du fichier php
        data: {
            action: "pinMapping"
        },
        dataType: 'json',
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            for (var i in data.result) {
                if (data.result[i].value != 'disable' && isset(data.result[i].key)) {
                    var name = data.result[i].key.replace("pin::", "");
                    if (isset(data.result[i].name)) {
                        name = data.result[i].name;
                    }
                    result += '<option data-mode="' + data.result[i].value + '" value="' + data.result[i].key.replace("pin::", "") + '">' + name + '</option>';
                }
            }
        }
    });
    return result;
}

function handlePin(tr) {
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'in') {
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        tr.find('.cmdAttr[data-l1key=subType]').value('binary');
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').hide();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'out') {
        tr.find('.cmdAttr[data-l1key=type]').value('action');
        tr.find('.cmdAttr[data-l1key=subType]').value('binary');
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').hide();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').hide();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').show();
    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'rin') {
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        tr.find('.cmdAttr[data-l1key=subType]').value('string');
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').hide();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'rout') {
        tr.find('.cmdAttr[data-l1key=type]').value('action');
        tr.find('.cmdAttr[data-l1key=subType]').value('string');
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').hide();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').hide();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').show();
    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'ain') {
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        tr.find('.cmdAttr[data-l1key=subType]').value('numeric');
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').hide();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'pout') {
        tr.find('.cmdAttr[data-l1key=type]').value('action');
        tr.find('.cmdAttr[data-l1key=subType]').value('numeric');
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').hide();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
    }
}

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name"></td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none;">';
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="logicalId">';
    tr += pin_select;
    tr += '</select><br/>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="type">';
    tr += '<td>';
    //tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="value">';
    //tr += '<option value="0">{{Arret}}</option>';
    //tr += '<option value="1">{{Marche}}</option>';
    //tr += '</select>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="value" disabled>';
    tr += '</td>';
    tr += '</td>';
    tr += '<td>';
    tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" /> {{Historiser}}<br/></span>';
    tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/> {{Afficher}}<br/></span>';
    tr += '<span><input type="checkbox" class="cmdAttr expertModeVisible" data-l1key="display" data-l2key="invertBinary" /> {{Inverser}}<br/></span>';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    handlePin($('#table_cmd tbody tr:last'));
}

/*$('#addStatToTable').on('click', function() {
    var _cmd = {type: 'info'};
    _cmd.configuration = {'type':'stat'};
    addCmdToTable(_cmd);
});
$('#addDataToTable').on('click', function() {
    var _cmd = {type: 'info'};
    _cmd.configuration = {'type':'data'};
    addCmdToTable(_cmd);
});
*/