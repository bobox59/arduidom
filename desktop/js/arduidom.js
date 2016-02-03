
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


$("#table_cmd").delegate(".bt_LearnCode2", 'click', function () {
    var el = $(this);
    $('#md_modal2').dialog({title: "{{Apprentissage Radio}}"});
    $('#md_modal2').load('index.php?v=d&plugin=arduidom&modal=radiolearn').dialog('open');
    //jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
    //    var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.data('input') + ']');
    //    calcul.atCaret('insert', result.human);
    //});
});

getLearnedCode = function(_options, _callback) { // PAS ENCORE UTILISE NI MODIFIE POUR ADAPTATION !!!
    if (!isset(_options)) {
        _options = {};
    }
    if ($("#mod_insertCmdValue").length == 0) {
        $('body').append('<div id="mod_insertCmdValue" title="{{Sélectionner la commande}}" ></div>');
        $("#mod_insertCmdValue").dialog({
            autoOpen: false,
            modal: true,
            height: 250,
            width: 800
        });
        jQuery.ajaxSetup({
            async: false
        });
        $('#mod_insertCmdValue').load('index.php?v=d&modal=cmd.human.insert');
        jQuery.ajaxSetup({
            async: true
        });
    }
    mod_insertCmd.setOptions(_options);
    $("#mod_insertCmdValue").dialog('option', 'buttons', {
        "Annuler": function() {
            $(this).dialog("close");
        },
        "Valider": function() {
            var retour = {};
            retour.cmd = {};
            retour.human = mod_insertCmd.getValue();
            retour.cmd.id = mod_insertCmd.getCmdId();
            retour.cmd.type = mod_insertCmd.getType();
            retour.cmd.subType = mod_insertCmd.getSubType();
            if ($.trim(retour) != '' && 'function' == typeof(_callback)) {
                _callback(retour);
            }
            $(this).dialog('close');
        }
    });
    $('#mod_insertCmdValue').dialog('open');
};


function getArduidomCodeLearn(_autoUpdate) {
    $.ajax({
        type: 'POST',
        url: 'plugins/arduidom/core/ajax/arduidom.ajax.php',
        data: {
            action: 'LearnRadio'
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
            setTimeout(function () {
                getArduidomCodeLearn(_autoUpdate)
            }, 1000);
        },
        success: function (data) {
            if (data.state != 'ok') {
                setTimeout(function () {
                    getArduidomCodeLearn(_autoUpdate)
                }, 1000);
                return;
            }
            var d = new Date();
            var t= d.getTime();
            //var log = t;
            var log = "";
            //log += '\n';
            log += data.result;
            var regex = /<br\s*[\/]?>/gi;
            if($.isArray(data.result)){
                console.log('IS ARRAY ');
                for (var i in data.result.reverse()) {
                    log += data.result[i][2].replace(regex, "\n");
                    console.log('0:');
                    console.log(data.result[0]);
                    console.log('1:');
                    console.log(data.result[1]);
                    if ($.trim(data.result[1].replace(regex, "\n")) == '[END SUCCESS]') {
                        //printUpdate();
                        $('#div_alert').showAlert({message: '{{L\'opération est réussie}}', level: 'success'});
                        console.log('End Success ');
                        _autoUpdate = 0;
                    }
                }
            }
            $('.progress-bar').css('width', 20+'%').attr('aria-valuenow', 20);
            //$('#pre_ardulog').text(log);
            //console.log(t);
            $('#pre_ardulog').text(log);
            $('#pre_ardulog').parent().scrollTop($('#pre_ardulog').parent().height() + 200000);
            if (init(_autoUpdate, 0) == 1) {
                setTimeout(function () {
                    getArduidomCodeLearn(_autoUpdate)
                }, 500);
            } else {
                $('#bt_Jeedom .fa-refresh').hide();
                $('.bt_Jeedom .fa-refresh').hide();
            }
        }
    });
}


$("#table_cmd").delegate(".listEquipementInfo", 'click', function () { // Rechercher equipement Button
    var el = $(this);
    jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
        var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.data('input') + ']');
        calcul.atCaret('insert', result.human);
    });
});

$(function() {
    $('#bt_LearnCode').on('click', function() {
        $('#md_modal2').dialog({title: "{{Apprentissage Radio}}"});
        $('#md_modal2').load('index.php?v=d&plugin=arduidom&modal=radiolearn').dialog('open');
    });
    $('#bt_configurePin').on('click', function() { // Bouton sur la gauche
        $('#md_modal2').dialog({title: "{{Configuration des pins}}"});
        $('#md_modal2').load('index.php?v=d&plugin=arduidom&modal=configure.pin').dialog('open');
    });
    $('#bt_ArduinologMessage').on('click', function() { // Bouton sur la gauche
        $('#md_modal').dialog({title: "{{Log des messages Arduino}}"});
        $('#md_modal').load('index.php?v=d&plugin=arduidom&modal=show.log.msg').dialog('open');
    });
    $('#bt_ArduinologDaemon').on('click', function() { // Bouton sur la gauche
        $('#md_modal').dialog({title: "{{Log du démon Arduino}}"});
        $('#md_modal').load('index.php?v=d&plugin=arduidom&modal=show.log').dialog('open');
    });
    $('#table_cmd').delegate('.cmdAttr[data-l1key=type]', 'change', function() { // Aux Changement de pin
    //    handlePin($(this).closest('tr'));
    });
    $('#table_cmd').delegate('.cmdAttr[data-l1key=subType]', 'change', function() { // Aux Changement de pin
    //    handlePin($(this).closest('tr'));
    });
    $('#table_cmd').delegate('.cmdAttr[data-l1key=logicalId]', 'change', function() { // Aux Changement de pin
        console.log("change...");
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
            var lastnb = 99;
            var newnb = 0;
            var count = 0;
            var dhtqty = 0;
            var noop = 0;
            var dhts = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
            for (var i in data.result) {
                if (data.result[i].value != 'disable' && isset(data.result[i].key)) {
                    count += 1;
                    //console.log('^ ' + count + ' ----------------------------');
                    var name = "Arduino n°";
                    var ardunb = data.result[i].key.replace("pin::", "");
                    newnb = ardunb.slice(0, -3);
                    name += ardunb.slice(0, -3);
                    name += " Pin ";
                    name += ardunb.slice(1);
                    var actualpin = ardunb.slice(1);
                    //console.log('ardunb=' + ardunb);
                    //console.log('name=' + name);
                    //console.log('pin=' + actualpin);
                    if (newnb != lastnb) {
                        result += '<optgroup label="Arduino n°' + newnb + ' ---------------------------------------------------">';
                        lastnb = newnb
                        noop = 0;
                        dhts = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                    }

                    if (isset(data.result[i].name)) {
                        name += " - ";
                        name += data.result[i].name;
                        //console.log('data.result(i).name=' + data.result[i].name);
                    }
                    if (name.indexOf("HIDE_") > 0) { // Cache si il y a un HIDE_
                        //console.log("DHT Pin Type to HIDE !");
                        var dhtnb = name.substr((name.indexOf("HIDE_DHT_") + 9),1);
                        //console.log("DHT detected number is " + dhtnb);
                        dhtqty += 1;
                        //console.log('name=[' + name + ']');
                        //console.log('index=[' + name.indexOf("HIDE_DHT_") + ']');
                        dhts[dhtnb] = actualpin; //name.substr((name.indexOf("HIDE_DHT_") + 9),1);
                        //console.log('dhts[' + dhtnb + '] = ' + actualpin);
                        //console.log('name=[' + name + ']');
                        //console.log('DHT real pin=[' + name.substr((name.indexOf("PIN_") + 4),9) + ']');
                        //console.log('name=[' + name + ']');
                    } else {
                        noop = 0;
                        var dhtindex=0;
                        while (dhtindex <= 8) {
                            dhtindex++;
                            if (actualpin == ((dhtindex * 2) + 499).toString()) {
                                name = name.replace(((dhtindex * 2) + 499).toString(), dhts[dhtindex]);
                                name = name.replace(" => Entrée Customisee", " => Sonde DHT n°" + (dhtindex));
                                if (dhts[dhtindex] == "") noop = 1;
                            }
                            if (actualpin == ((dhtindex * 2) + 500).toString()) {
                                name = name.replace(((dhtindex * 2) + 500).toString(), dhts[dhtindex]);
                                name = name.replace(" => Entrée Customisee", " => Sonde DHT n°" + (dhtindex));
                                if (dhts[dhtindex] == "") noop = 1;
                            }
                        }
                        if (noop == 0) {
                            result += '<option data-mode="' + data.result[i].value + '" value="' + data.result[i].key.replace("pin::", "") + '">' + name + '</option>';
                        }

                    }
                }
            }
        }
    });
    return result;
}

function handlePin(tr) {
    console.log("-handlepin--");
    //console.log("--1--------------------------------------------------------");
    //console.log(tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected'));
    //console.log("--2--------------------------------------------------------");
    //console.log(tr.find('.cmdAttr[data-l1key=name]').find("Escalier2"));
    //console.log("--3--------------------------------------------------------");

    //D'abord on cache tout et verouille tout
    tr.find('.cmdAttr[data-l1key=type]').prop('disabled', true);
    tr.find('.cmdAttr[data-l1key=subType]').prop('disabled', true);
    tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').hide();
    tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').hide();
    tr.find('.cmdAttr[id="bt_LearnCode2"]').hide();
    tr.find('.cmdAttr[data-l1key=unite]').hide();
    tr.find('.cmdAttr[data-l1key=configuration][data-l2key=minValue]').hide();
    tr.find('.cmdAttr[data-l1key=configuration][data-l2key=maxValue]').hide();
    tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').hide();

    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'radio') {
        console.log("data Mode is : radio");
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        tr.find('.cmdAttr[data-l1key=subType]').value('binary');
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').hide();
        console.log("Value show...");
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').show();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'in') {
        console.log("data Mode is : in");
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        tr.find('.cmdAttr[data-l1key=subType]').value('binary');
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'inup') {
        console.log("data Mode is : inup");
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        tr.find('.cmdAttr[data-l1key=subType]').value('binary');
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'out') {
        console.log("data Mode is : out");
        console.log("type:" + (tr.find('.cmdAttr[data-l1key=type]').value != ''));
        if (tr.find('.cmdAttr[data-l1key=type]').value == '') {
            tr.find('.cmdAttr[data-l1key=type]').value('action');
        }
        if (tr.find('.cmdAttr[data-l1key=type]').value == 'action') {
            tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').show();
        }
        if (tr.find('.cmdAttr[data-l1key=type]').value == 'info') {
            tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').show();
            tr.find('.cmdAttr[data-l1key=unite]').show();
            tr.find('.cmdAttr[data-l1key=configuration][data-l2key=minValue]').show();
            tr.find('.cmdAttr[data-l1key=configuration][data-l2key=maxValue]').show();
            tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
        }
        tr.find('.cmdAttr[data-l1key=type]').prop('disabled', false);



        if (tr.find('.cmdAttr[data-l1key=subType]').value == '') tr.find('.cmdAttr[data-l1key=subType]').value('other');
        tr.find('.cmdAttr[data-l1key=subType]').prop('disabled', false);
        console.log("Value show...");
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').show();
    }

    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'rin') {
        console.log("data Mode is : rin");
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        tr.find('.cmdAttr[data-l1key=subType]').value('string');
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'rout') {
        console.log("data Mode is : rout");
        tr.find('.cmdAttr[data-l1key=type]').value('action');
        tr.find('.cmdAttr[data-l1key=subType]').value('other');
        tr.find('.cmdAttr[data-l1key=subType]').prop('disabled', false);
        console.log("Value show...");
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').show();
    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'ain') {
        console.log("data Mode is : ain");
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        tr.find('.cmdAttr[data-l1key=subType]').value('numeric');
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'custin') {
        console.log("data Mode is : custin");
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        //tr.find('.cmdAttr[data-l1key=subType]').value('numeric');
        tr.find('.cmdAttr[data-l1key=subType]').prop('disabled', false);
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=unite]').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=minValue]').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=maxValue]').show();

    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'dht1') {
        console.log("data Mode is : dht1");
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        //tr.find('.cmdAttr[data-l1key=subType]').value('numeric');
        tr.find('.cmdAttr[data-l1key=subType]').prop('disabled', false);
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=unite]').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=minValue]').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=maxValue]').show();

    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'dht2') {
        console.log("data Mode is : dht2");
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        //tr.find('.cmdAttr[data-l1key=subType]').value('numeric');
        tr.find('.cmdAttr[data-l1key=subType]').prop('disabled', false);
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=unite]').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=minValue]').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=maxValue]').show();

    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'dht3') {
        console.log("data Mode is : dht3");
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        //tr.find('.cmdAttr[data-l1key=subType]').value('numeric');
        tr.find('.cmdAttr[data-l1key=subType]').prop('disabled', false);
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=unite]').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=minValue]').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=maxValue]').show();

    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'dht4') {
        console.log("data Mode is : dht4");
        tr.find('.cmdAttr[data-l1key=type]').value('info');
        //tr.find('.cmdAttr[data-l1key=subType]').value('numeric');
        tr.find('.cmdAttr[data-l1key=subType]').prop('disabled', false);
        tr.find('.cmdAttr[data-l1key=isHistorized]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=display][data-l2key=invertBinary]').closest('span').show();
        tr.find('.cmdAttr[data-l1key=unite]').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=minValue]').show();
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=maxValue]').show();

    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'custout') {
        console.log("data Mode is : custout");
        tr.find('.cmdAttr[data-l1key=type]').value('action');
        tr.find('.cmdAttr[data-l1key=type]').prop('disabled', false);
        //tr.find('.cmdAttr[data-l1key=subType]').value('other');
        tr.find('.cmdAttr[data-l1key=subType]').prop('disabled', false);
        console.log("Value show...");
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').show();
    }
    if (tr.find('.cmdAttr[data-l1key=logicalId]').find('option:selected').attr('data-mode') == 'pout') {
        console.log("data Mode is : pout");
        tr.find('.cmdAttr[data-l1key=type]').value('action');
        tr.find('.cmdAttr[data-l1key=type]').prop('disabled', false);
        //tr.find('.cmdAttr[data-l1key=subType]').value('slider');
        tr.find('.cmdAttr[data-l1key=subType]').prop('disabled', false);
        console.log("Value show...");
        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').show();

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
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="logicalId">';
    tr += '<option value>Aucune</option>';
    tr += '<optgroup label="Reception Radio multi-arduino"></option>';
    tr += '<option data-mode="radio" value="999">Arduino(s) - Receptions Radios</option>';
    tr += getPinMapping();
    tr += '</select><br/>';

    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    /*
    //tr += 'Type<input class="cmdAttr form-control input-sm" data-l1key="type">';
    tr += 'Type<select class="cmdAttr form-control input-sm" data-l1key="type">';
    tr += '<option value="action">{{Action}}</option>';
    tr += '<option value="info">{{Info}}</option>';
    tr += '</select>';

    //tr += 'subType<input class="cmdAttr form-control input-sm" data-l1key="subType">'; //style="display : none;"
    tr += 'subType<select class="cmdAttr form-control input-sm" data-l1key="subType">';
    //                   FOR INFOs...
    tr += '<option value="numeric">{{INFO - Numérique}}</option>';
    tr += '<option value="binary">{{INFO - Binaire}}</option>';
    tr += '<option value="string">{{INFO - Autre}}</option>';
    //                   FOR ACTIONs...
    tr += '<option value="other">{{ACTION - Défaut}}</option>';
    tr += '<option value="slider">{{ACTION - Curseur}}</option>';
    tr += '<option value="message">{{ACTION - Message}}</option>';
    tr += '<option value="color">{{ACTION - Couleur}}</option>';
    tr += '</select>';
*/
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="value">';
    tr += '<a class="btn btn-default cursor listEquipementInfo btn-sm" data-input="value"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>';
    tr += '<a class="btn btn-default tooltips bt_LearnCode2 btn-sm " id="bt_LearnCode2" title="{{Apprentissage Radio}}" style="width : 100%;display: inline-block;"><i class="fa fa-wifi"></i> {{Apprentissage Radio}}</a>';
    tr += '<input style="width : 150px;" class="tooltips cmdAttr form-control expertModeVisible input-sm" data-l1key="cache" data-l2key="lifetime" placeholder="Lifetime cache">';
    tr += '<input class="cmdAttr form-control tooltips input-sm" data-l1key="unite"  style="width : 100px;" placeholder="Unité" title="Unité">';
    tr += '<input class="tooltips cmdAttr form-control input-sm expertModeVisible" data-l1key="configuration" data-l2key="minValue" placeholder="Min" title="Min"> ';
    tr += '<input class="tooltips cmdAttr form-control input-sm expertModeVisible" data-l1key="configuration" data-l2key="maxValue" placeholder="Max" title="Max" style="margin-top : 5px;">';
    tr += '</td>';
    tr += '<td>';
    tr += '<span><input type="checkbox" class="cmdAttr bootstrapSwitch" data-l1key="isVisible" data-size="mini" data-label-text="{{Afficher}}" checked/><br></span> ';
    tr += '<span><input type="checkbox" class="cmdAttr bootstrapSwitch" data-l1key="isHistorized" data-size="mini" data-label-text="{{Historiser}}" /><br></span>';
    tr += '<span><input type="checkbox" class="cmdAttr bootstrapSwitch expertModeVisible" data-l1key="display" data-l2key="invertBinary" data-size="mini" data-label-text="{{Inverser}}" /><br><br></span>';
    tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs">{{Paramètres avancés}}</i></a> ';
    tr += '</td>';
    tr += '<td>';
    if (_cmd.type == 'action') { // is_numeric(_cmd.id)
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.configuration.requestType)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=configuration][data-l2key=requestType]').value(init(_cmd.configuration.requestType));
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=configuration][data-l2key=requestType]').trigger('change');
    }

    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
    initTooltips();
    handlePin($('#table_cmd tbody tr:last'));
}

$('#bt_LearnCode2').on('click', function() {
    $('#md_modal2').dialog({title: "{{Apprentissage Radio}}"});
    $('#md_modal2').load('index.php?v=d&plugin=arduidom&modal=radiolearn').dialog('open');
});
$('#addStatToTable').on('click', function() {
    var _cmd = {type: 'info'};
    _cmd.configuration = {'type':'stat'};
    addCmdToTable(_cmd);
});
$('#addDataToTable').on('click', function() {
    var _cmd = {type: 'info'};
    _cmd.configuration = {'type':'data'};
    addCmdToTable(_cmd);
});
