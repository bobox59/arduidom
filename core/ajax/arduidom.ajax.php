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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');
    include_file('core', 'pin', 'config', 'arduidom');


    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    if (init('action') == 'pinMapping') {
        global $ARDUPINMAP;
        $result = config::searchKey('pin::', 'arduidom');
        foreach ($ARDUPINMAP as $logicalId => $pin) {
            if ($pin['reserved'] == 1 && isset($pin['user_authorized']) && $pin['user_authorized'] == 1) {
                $result[] = array('plugin' => 'arduidom', 'value' => 'out', 'key' => 'pin::'.$logicalId, 'name' => $pin['logicalId']);
            }
        }

        ajax::success($result);
    }

    if (init('action') == 'setPinMapping') {
        ajax::success(arduidom::setPinMapping());
    }

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}

