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
use local_lbplanner\helpers\course_helper;
use local_lbplanner\helpers\modules_helper;
use local_lbplanner\helpers\user_helper;
use local_lbplanner\helpers\plan_helper;


/**
 * Get all the modules of the current year.
 *
 * @package local_lbplanner
 * @subpackage services_modules
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class modules_get_all_modules extends external_api {
    /**
     * Parameters for get_all_modules.
     * @return external_function_parameters
     */
    public static function get_all_modules_parameters(): external_function_parameters {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, 'The id of the user', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
        ]);
    }

    /**
     * Returns all the modules for a user.
     *
     * @param int $userid The ID of the user
     * @return array the modules
     */
    public static function get_all_modules(int $userid): array {
        global $DB;

        self::validate_parameters(self::get_all_modules_parameters(), ['userid' => $userid]);

        user_helper::assert_access($userid);

        $modules = [];

        $courses = self::call_external_function('local_lbplanner_courses_get_all_courses', ['userid' => $userid]);
        $plan = plan_helper::get_plan(plan_helper::get_plan_id($userid));
        $ekenabled = $plan["enableek"];

        foreach ($courses["data"] as $course) {
            if ($course["enabled"] == course_helper::DISABLED_COURSE) {
                continue;
            }
            $modules = array_merge(
            modules_helper::get_all_course_modules($course['courseid'], $userid, $ekenabled),
                $modules
            );
        }
        return $modules;
    }

    /**
     * Returns the structure of the module array.
     * @return external_multiple_structure
     */
    public static function get_all_modules_returns(): external_multiple_structure {
        return new external_multiple_structure(
            modules_helper::structure(),
        );
    }
}
