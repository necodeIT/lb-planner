<?php
// This file is part of the local_lbplanner.
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

namespace local_lbplanner\helpers;

use context_course;
use dml_exception;
use stdClass;

/**
 * Helper class for courses
 *
 * @package    local_lbplanner
 * @subpackage helpers
 * @copyright  2024 NecodeIT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_helper {

    /**
     * The course table used by the LP
     */
    const LBPLANNER_COURSE_TABLE = 'local_lbplanner_courses';

    /**
     * A list of nice colors to choose from :)
     */
    const COLORS
        = [
            "#f50057",
            "#536dfe",
            "#f9a826",
            "#00bfa6",
            "#9b59b6",
            "#37bbca",
            "#e67e22",
            "#37CA48",
            "#CA3737",
            "#B5CA37",
            "#37CA9E",
            "#3792CA",
            "#376ECA",
            "#8B37CA",
            "#CA37B9",
        ];
    /**
     * constant that represents a disabled course
     */
    const DISABLED_COURSE = 0;
    /**
     * constant that represents an enabled course
     */
    const ENABLED_COURSE = 1;

    /**
     * Get the current school year from the config
     * Definition of a school year: 2020/2021
     * Check in config_helper.php for more info how the date is set for defaultactiveyear
     *
     * @return string the current year the last 2 digits (20/20)
     * @throws dml_exception
     */
    public static function get_current_year(): string {
        if (strpos(get_config('local_lbplanner', 'activeyear'), '/') !== false) {
            return get_config('local_lbplanner', 'activeyear');
        }
        return get_config('local_lbplanner', 'defaultactiveyear');
    }

    /**
     * Get course from lbpanner DB
     *
     * @param int $courseid id of the course in lbplanner
     * @param int $userid   id of the user
     *
     * @return stdClass course from lbplanner
     * @throws dml_exception
     */
    public static function get_lbplanner_course(int $courseid, int $userid): stdClass {
        global $DB;
        return $DB->get_record(self::LBPLANNER_COURSE_TABLE, ['courseid' => $courseid, 'userid' => $userid]);
    }

    /**
     * Check if the user is enrolled in the course
     *
     * @param int $courseid course id
     * @param int $userid   user id
     *
     * @return bool true if the user is enrolled
     */
    public static function check_access(int $courseid, int $userid): bool {
        $context = context_course::instance($courseid);
        return is_enrolled($context, $userid, '', true);
    }

    /**
     * gets the fullname from a course
     *
     * @param int $courseid the course id
     *
     * @return string the fullname of the course
     * @throws dml_exception
     */
    public static function get_fullname(int $courseid): string {
        return get_course($courseid)->fullname;
    }

    /**
     * Check if the course is from the current year
     *
     * @param int $courseid the course id
     *
     * @return bool true if the course is from the current year
     * @throws dml_exception
     */
    public static function check_current_year(int $courseid): bool {
        if (strpos(self::get_fullname($courseid), self::get_current_year()) !== false) {
            return true;
        } else {
            return false;
        }
    }
}
