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
$daemonRunning = arduidom::ping_arduino($_AID,false,true);
if ($daemonRunning != 1) {
    throw new Exception(__("Action Impossible : L\'Arduino " . $_AID . " ne fonctionne pas !", __FILE__));
}
?>


<div id='pre_pb' class="progress">
    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
        60%
    </div>
</div>
<pre id='pre_ardulog' style='overflow: auto; height: 95%;width:90%;'></pre>

<script>
    getArduidomCodeLearn(1)
</script>

<script>console.log("SCRIPT END");</script>

