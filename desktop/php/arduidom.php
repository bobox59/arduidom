<?php
if (!isConnect('admin')) {
    throw new Exception('Error 401 Unauthorized');
}
sendVarToJS('eqType', 'arduidom');
$eqLogics = eqLogic::byType('arduidom')
?>

<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <center>
                    <a class="btn btn-default btn-sm tooltips" id="bt_configurePin" title="{{Configurer les pins}}" style="width : 100%;"><i class="fa fa-cogs"></i> {{Configurer les pins}}</a>
                    <a class="btn btn-default btn-sm tooltips expertModeVisible" id="bt_ArduinologMessage" title="{{Log des messages Arduino}}" style="width : 100%;display: inline-block;"><i class="fa fa-file-o"></i> {{Log des messages RADIO}}</a>
                    <a class="btn btn-default btn-sm tooltips" id="bt_LearnCode" title="{{Apprentissage Radio}}" style="width : 100%;display: inline-block;"><i class="fa fa-wifi"></i> {{Apprentissage Radio}}</a>
                </center>
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                foreach ($eqLogics as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend>{{Mes équipements Arduidom}}</legend>
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
                <center>
                <i class="fa fa-plus-circle" style="font-size : 7em;color:#16979B;"></i>
                </center>
                <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#16979B"><center>Ajouter</center></span>
            </div>
            <?php
            foreach ($eqLogics as $eqLogic) {
                $eq_enabled = ($eqLogic->getIsEnable() != 1) ? 'opacity:0.3;' : '';
                echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $eq_enabled . '" >';
                echo "<center>";
                echo '<img class="lazy" src="plugins/arduidom/doc/images/logos/' . $eqLogic->getConfiguration("logo", "_autre.png") . '" height="105"  />';
                echo "</center>";
                echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                echo '</div>';
            }
            ?>
        </div>

    </div>

    <div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <form class="form-horizontal">
            <fieldset>
                <legend>
                    <i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}
                    <i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i>
                    <a class="btn btn-xs btn-default pull-right eqLogicAction" data-action="copy"><i class="fa fa-files-o"></i> {{Dupliquer}}</a>
                </legend>
                <div class="form-group">
                    <label class="col-sm-2 control-label">{{Nom de l'équipement}}</label>
                    <div class="col-sm-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" >{{Objet parent}}</label>
                    <div class="col-sm-3">
                        <select class="eqLogicAttr form-control" data-l1key="object_id">
                            <option value="">{{Aucun}}</option>
                            <?php
                            foreach (object::all() as $object) {
                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">{{Catégorie}}</label>
                    <div class="col-sm-8">
                        <?php
                        foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                            echo '<label class="checkbox-inline">';
                            echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                            echo '</label>';
                        }
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-10">
                        <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Activer}}" data-l1key="isEnable" checked/>
                        <input type="checkbox" class="eqLogicAttr bootstrapSwitch" data-label-text="{{Visible}}" data-l1key="isVisible" checked/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-10">
                        <ul class="dropdown-menu">
                            <li><a href="#" class="fa-image"><span
                                        class="fa fa-stop"></span>User Profile</a></li>
                            <li><a href="#">Log Out</a></li>
                        </ul>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">{{Logo}}</label>
                    <div id="iconimgs" class="col-sm-8">
                        <select id="icon_select" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="logo">
                            <?php foreach(scandir("plugins/arduidom/doc/images/logos/") as $img ){
                                if (strpos($img, ".png") > -1) {
                                    $img2 = str_replace(".png","", $img);
                                    echo '<div class="col-sm-3">';
                                    echo '<option value="' . $img . '">' . $img2 . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <div id="my-icon-select">
                            <?php foreach(scandir("plugins/arduidom/doc/images/logos/") as $img ){
                                if (strpos($img, ".png") > -1) {
                                    echo '<img src="plugins/arduidom/doc/images/logos/' . $img . '" height="40" width="40" icone="' . $img . '" border="20" />';
                                }
                            }
                            ?>
                        </div>
                        <script>

                            $("div#my-icon-select img").click(function () {
                                $("div#my-icon-select img").attr("border","0");
                                $("div#my-icon-select img").attr("height","40");
                                $("div#my-icon-select img").attr("width","40");
                                $(this).attr("border","4");
                                $(this).attr("height","80");
                                $(this).attr("width","80");
                                $("select#icon_select").val($(this).attr("icone"));
                            });
                        </script>
                    </div>
            </fieldset>
            <legend>{{Commandes}}</legend>
            <a class="btn btn-success btn-sm cmdAction" id="addDataToTable"><i class="fa fa-plus-circle"></i> {{Commandes}}</a> &nbsp;
            <table id="table_cmd" class="table table-bordered table-condensed">
                <thead>
                <tr>
                    <th style="width: 150px;">{{Nom}}</th>
                    <th style="width: 120px;">{{N°Arduino et Pin}}</th>
                    <th style="width: 230px;">{{Données}}</th>
                    <th style="width: 130px;">{{Paramètres}}</th>
                    <th style="width: 80px;">{{Test}}</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

            <form class="form-horizontal">
                <fieldset>
                    <div class="form-actions">
                        <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                        <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                    </div>
                </fieldset>
            </form>
        </form>
    </div>
</div>


<?php include_file('desktop', 'arduidom', 'js', 'arduidom'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
