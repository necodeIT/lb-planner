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
use local_lbplanner\helpers\modules_helper;
use local_lbplanner\helpers\user_helper;

/**
 * Get all the modules of the given course.
 *
 * @package local_lbplanner
 * @subpackage services_modules
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class modules_get_all_course_modules extends external_api {
    /**
     * Parameters for get_all_course_modules
     * @return external_function_parameters
     */
    public static function get_all_course_modules_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'The id of the course', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
            'userid' => new external_value(PARAM_INT, 'The id of the user', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
            'ekenabled' => new external_value(
            PARAM_BOOL,
            'Whether or not to include ek modules',
            VALUE_REQUIRED,
            false,
            NULL_NOT_ALLOWED),
        ]);
    }

    /**
     * Returns all the modules inside a course.
     *
     * @param int $courseid The ID of the course
     * @param int $userid The ID of the user
     * @param bool $ekenabled whether or not to include ek modules
     * @return array the modules
     */
    public static function get_all_course_modules(int $courseid, int $userid, bool $ekenabled): array {
        global $DB;

        self::validate_parameters(
            self::get_all_course_modules_parameters(),
            ['courseid' => $courseid, 'userid' => $userid, 'ekenabled' => $ekenabled]
        );

        user_helper::assert_access($userid);

        return modules_helper::get_all_course_modules($courseid, $userid, $ekenabled);
    }

    /**
     * Returns the structure of the module array.
     * @return external_multiple_structure
     */
    public static function get_all_course_modules_returns(): external_multiple_structure {
        return new external_multiple_structure(
            modules_helper::structure(),
        );
    }
}
