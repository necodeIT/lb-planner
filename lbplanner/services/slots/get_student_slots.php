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
use external_value;
use local_lbplanner\helpers\slot_helper;
use local_lbplanner\model\slot;

/**
 * Returns all slots a supervisor can theoretically reserve for a user.
 * This does not include times the user has already reserved a slot for.
 *
 * @package local_lbplanner
 * @subpackage services_plan
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slots_get_student_slots extends external_api {
    /**
     * Parameters for get_student_slots.
     * @return external_function_parameters
     */
    public static function get_student_slots_parameters(): external_function_parameters {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, 'ID of the user to query for', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
        ]);
    }

    /**
     * Returns slots of a user the supervisor can see.
     * @param int $userid ID of the user in question (NOT the supervisor)
     */
    public static function get_student_slots(int $userid): array {
        global $USER;
        self::validate_parameters(
            self::get_student_slots_parameters(),
            ['userid' => $userid]
        );

        $superslots = slot_helper::get_supervisor_slots($USER->id);

        $myslots = slot_helper::filter_slots_for_user($superslots, $userid);

        $returnslots = slot_helper::filter_slots_for_time($myslots, slot_helper::RESERVATION_RANGE_SUPERVISOR);

        return $returnslots;
    }

    /**
     * Returns the structure of the slot array
     * @return external_multiple_structure
     */
    public static function get_student_slots_returns(): external_multiple_structure {
        return new external_multiple_structure(
            slot::api_structure()
        );
    }
}
