<?php
// This file is part of local_lbplanner.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * for upgrading the db
 *
 * @package local_lbplanner
 * @subpackage db
 * @copyright 2024 NecodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_lbplanner\helpers\config_helper;

/**
 * Upgrades the DB version
 * This function does anything necessary to upgrade the plugin from an old version to the current version.
 *
 * @param mixed $oldversion (unused) the previous version to upgrade from
 * @return bool true
 */
function xmldb_local_lbplanner_upgrade($oldversion): bool {
    if ($oldversion < 2024022703) {
        config_helper::set_default_active_year();
        try {
            config_helper::add_customfield();
        } catch (coding_exception $e) {
            new moodle_exception('error_adding_customfield', 'local_lbplanner', '', $e->getMessage());
        }
    }
    return true;
}

