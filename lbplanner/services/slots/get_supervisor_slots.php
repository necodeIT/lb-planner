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
use external_multiple_structure;
use local_lbplanner\helpers\slot_helper;
use local_lbplanner\model\slot;

/**
 * Returns all slots a supervisor can see.
 *
 * @package local_lbplanner
 * @subpackage services_plan
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slots_get_supervisor_slots extends external_api {
    /**
     * Parameters for get_supervisor_slots.
     * @return external_function_parameters
     */
    public static function get_supervisor_slots_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Returns all slots a supervisor controls.
     * @param int $userid ID of the user in question (NOT the supervisor)
     */
    public static function get_supervisor_slots(int $userid): array {
        global $USER;
        self::validate_parameters(
            self::get_supervisor_slots_parameters(),
            ['userid' => $userid]
        );

        return slot_helper::get_supervisor_slots($USER->id);
    }

    /**
     * Returns the structure of the slot array
     * @return external_multiple_structure
     */
    public static function get_supervisor_slots_returns(): external_multiple_structure {
        return new external_multiple_structure(
            slot::api_structure()
        );
    }
}
