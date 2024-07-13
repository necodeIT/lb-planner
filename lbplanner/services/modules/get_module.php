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

namespace local_lbplanner_services;

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use local_lbplanner\helpers\modules_helper;
use local_lbplanner\helpers\plan_helper;
use local_lbplanner\helpers\user_helper;

/**
 * Get the data for a module.
 *
 * @package local_lbplanner
 * @subpackage services_modules
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class modules_get_module extends external_api {
    /**
     * Parameters for get_module.
     * @return external_function_parameters
     */
    public static function get_module_parameters(): external_function_parameters {
        return new external_function_parameters([
            'moduleid' => new external_value(PARAM_INT, 'The id of the module', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
            'userid' => new external_value(PARAM_INT, 'The id of the user', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
        ]);
    }

    /**
     * Returns the data for a module
     *
     * @param int $moduleid The ID of the course
     * @param int $userid The ID of the user
     * @return array the module
     */
    public static function get_module(int $moduleid, int $userid): array {
        global $DB;

        self::validate_parameters(self::get_module_parameters(), ['moduleid' => $moduleid, 'userid' => $userid]);

        user_helper::assert_access($userid);

        if (!$DB->record_exists(modules_helper::MDL_ASSIGN_TABLE, ['id' => $moduleid])) {
            throw new \moodle_exception('Module not found');
        }

        return modules_helper::get_module($moduleid, $userid);
    }

    /**
     * Returns the structure of the module.
     * @return external_single_structure
     */
    public static function get_module_returns(): external_single_structure {
        return modules_helper::structure();
    }
}
