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

use dml_exception;
use external_api;
use external_function_parameters;
use external_value;
use invalid_parameter_exception;
use local_lbplanner\helpers\course_helper;
use moodle_exception;

/**
 * Update the data for a course.
 *
 * @package local_lbplanner
 * @subpackage services_courses
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses_update_course extends external_api {
    /**
     * Parameters for update_course.
     * @return external_function_parameters
     */
    public static function update_course_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'The id of the course', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
            'color' => new external_value(PARAM_TEXT, 'The color of the course in HEX', VALUE_DEFAULT, null),
            'shortname' => new external_value(PARAM_TEXT, 'The shortname of the course', VALUE_DEFAULT, null),
            'enabled' => new external_value(
                PARAM_BOOL,
                'Whether the course is enabled or not',
                VALUE_DEFAULT,
                null
            ),
        ]);
    }

    /**
     * Update the User-data for a course.
     * @param int $courseid The id of the course
     * @param string $color The color of the course
     * @param string $shortname The shortname of the course
     * @param bool $enabled Whether the course is enabled or not
     * @return void
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function update_course($courseid, $color, $shortname, $enabled): void {
        global $DB , $USER;

        self::validate_parameters(
            self::update_course_parameters(),
            [
                'courseid' => $courseid,
                'color' => $color,
                'shortname' => $shortname,
                'enabled' => $enabled,
            ]
        );

        if (strlen($shortname) > 5) {
            throw new moodle_exception('Shortname is too long');
        }

        $course = course_helper::get_lbplanner_course($courseid, $USER->id);

        if ($color !== null) {
            $course->color = $color;
        }
        if ($shortname !== null) {
            $course->shortname = $shortname;
        }
        if ($enabled !== null) {
            $course->enabled = $enabled;
        }

        $DB->update_record(course_helper::LBPLANNER_COURSE_TABLE, $course);

    }

    /**
     * Returns nothing.
     * @return null
     */
    public static function update_course_returns() {
        return null;
    }
}
