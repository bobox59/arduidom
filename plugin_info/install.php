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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function arduidom_install() {
    arduidom::start();
    $cron = cron::byClassAndFunction('arduidom', 'checkdaemon');
    if (!is_object($cron)) {
        $cron = new cron();
        $cron->setClass('arduidom');
        $cron->setFunction('checkdaemon');
        $cron->setEnable(1);
        $cron->setDeamon(0);
        $cron->setSchedule('* * * * *');
        $cron->save();
    }
    @exec("sudo usermod -G dialout www-data");
}

function arduidom_update() {
    arduidom::stopdaemon();
    $MigrationCheck = config::byKey('db_version', 'arduidom', 0);
    if ($MigrationCheck < 108) {
        arduidom::MigrateDatas();
        arduidom::start();
    }
    if ($MigrationCheck < 145) {
        arduidom::stopdaemon();
        $daemon_path = realpath(dirname(__FILE__) . '/../../ressources');
        log::add('arduidom', 'info', "Suppression de arduidom1.py devenu inutile => " . unlink($daemon_path . "/arduidom1.py"));
        log::add('arduidom', 'info', "Suppression de arduidom2.py devenu inutile => " . unlink($daemon_path . "/arduidom2.py"));
        log::add('arduidom', 'info', "Suppression de arduidom3.py devenu inutile => " . unlink($daemon_path . "/arduidom3.py"));
        log::add('arduidom', 'info', "Suppression de arduidom4.py devenu inutile => " . unlink($daemon_path . "/arduidom4.py"));
        log::add('arduidom', 'info', "Suppression de arduidom5.py devenu inutile => " . unlink($daemon_path . "/arduidom5.py"));
        log::add('arduidom', 'info', "Suppression de arduidom6.py devenu inutile => " . unlink($daemon_path . "/arduidom6.py"));
        log::add('arduidom', 'info', "Suppression de arduidom7.py devenu inutile => " . unlink($daemon_path . "/arduidom7.py"));
        log::add('arduidom', 'info', "Suppression de arduidom8.py devenu inutile => " . unlink($daemon_path . "/arduidom8.py"));
        config::save('db_version', 145, 'arduidom'); // Inscrit la version de migration dans la config
        arduidom::start();
    }
    arduidom::startdaemon();
    $cron = cron::byClassAndFunction('arduidom', 'checkdaemon');
    if (!is_object($cron)) {
        $cron = new cron();
        $cron->setClass('arduidom');
        $cron->setFunction('checkdaemon');
        $cron->setEnable(1);
        $cron->setDeamon(0);
        $cron->setSchedule('* * * * *');
        $cron->save();
    }
    $cron->stop();
    @exec("sudo usermod -G dialout www-data");
}

function arduidom_remove() {
    $cron = cron::byClassAndFunction('arduidom', 'checkdaemon');
    if (is_object($cron)) {
        $cron->remove();
    }
    arduidom::stopdaemon(1);
    arduidom::stopdaemon(2);
    arduidom::stopdaemon(3);
    arduidom::stopdaemon(4);
    arduidom::stopdaemon(5);
    arduidom::stopdaemon(6);
    arduidom::stopdaemon(7);
    arduidom::stopdaemon(8);
}

?>
