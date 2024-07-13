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

use coding_exception;
use dml_exception;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use local_lbplanner\helpers\course_helper;
use moodle_exception;

/**
 * Get all the courses of the current year.
 *
 * @package local_lbplanner
 * @subpackage services_courses
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses_get_all_courses extends external_api {

    /**
     * Has no Parameters
     * @return external_function_parameters
     */
    public static function get_all_courses_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Get all the courses of the current year.
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function get_all_courses(): array {
        global $DB, $USER;

        $userid = $USER->id;

        $courses = enrol_get_my_courses();
        // Remove Duplicates.
        $courses = array_unique($courses, SORT_REGULAR);
        // Check this out: https://www.youtube.com/watch?v=z3Pzfi476HI .
        $catgirls = [];

        foreach ($courses as $course) {
            $courseid = $course->id;
            $name = $course->fullname;
            $shortname = $course->shortname;
            // Check if the shortname contains a space.
            if (strpos($shortname, ' ') !== false) {
                    $shortname = substr($shortname, 0, strpos($shortname, ' '));
            }
            if (strlen($shortname) >= 5) {
                    $shortname = substr($shortname, 0, 5);
            }
            // Check if the course is from the current year.
            if (course_helper::check_current_year($courseid)) {
                    continue;
            }
            // Check if the course is already in the LB Planner database.
            if ($DB->record_exists(course_helper::LBPLANNER_COURSE_TABLE, ['courseid' => $courseid, 'userid' => $userid])) {
                $fetchedcourse = course_helper::get_lbplanner_course($courseid, $userid);
            } else {
                // IF not create an Object to be put into the LB Planner database.
                $fetchedcourse = (object) [
                    'courseid' => $courseid,
                    'color' => course_helper::COLORS[array_rand(course_helper::COLORS)],
                    'shortname' => strtoupper($shortname),
                    'enabled' => course_helper::DISABLED_COURSE,
                    'userid' => $userid,
                ];
                $DB->insert_record(course_helper::LBPLANNER_COURSE_TABLE, $fetchedcourse);
            }
            // Add name to fetched Course.
            $fetchedcourse->name = $name;
            $catgirls[] = $fetchedcourse;
        }
            return $catgirls;
    }

    /**
     * Returns description of method result value
     * @return external_multiple_structure description of method result value
     */
    public static function get_all_courses_returns(): external_multiple_structure {
        return new external_multiple_structure(
        new external_single_structure(
            [
                'courseid' => new external_value(PARAM_INT, 'The id of the course'),
                'color' => new external_value(PARAM_TEXT, 'The color of the course in HEX'),
                'name' => new external_value(PARAM_TEXT, 'The name of the course'),
                'shortname' => new external_value(PARAM_TEXT, 'The shortname of the course'),
                'enabled' => new external_value(PARAM_BOOL, 'Whether the course is enabled or not'),
            ]
        )
        );
    }
}
