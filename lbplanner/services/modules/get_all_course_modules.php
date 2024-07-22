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


use core_completion\cm_completion_details;
use core_customfield\category_controller;
use core_external\external_single_structure;
use core_h5p\core;
use course_modinfo;
use customfield_select\data_controller;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_value;
use local_lbplanner\model\activity;
use local_lbplanner\helpers\modules_helper;
use stdClass;

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
            'courseid' => new external_value(PARAM_INT, 'The id of the course', VALUE_REQUIRED, null, NULL_NOT_ALLOWED)]);
    }

    /**
     * Returns all the modules inside a course.
     *
     * @param int $courseid The ID of the course
     * @param bool $ekenabled whether or not to include ek modules
     * @return array|null the modules
     */
    public static function get_all_course_modules(int $courseid): ?array {
        global $DB, $USER, $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        // path to the module class which is in this plugin
        self::validate_parameters(
            self::get_all_course_modules_parameters(),
            ['courseid' => $courseid]
        );
        $activities = course_modinfo::get_array_of_activities(get_course($courseid));
        $modules = [];
        foreach ($activities as $activity) {
            $modtype = $activity->mod;
            if ($modtype !== 'assign' || $modtype !== 'quiz') {
                continue;
            }
            $id = $activity->id;
            $deadline = $activity->duedate;
            $name = $activity->name;
            $cmid = $activity->cm;
            $url = modules_helper::get_module_url($modtype, $cmid);
            $modules[] = new activity(
                $id,
                $name,
                $courseid,
                1,
                $modtype,
                modules_helper::determin_type($name, $cmid),
                $url,
                1,
                $deadline
            );
        }
        die(var_dump());
        $test = new stdClass();
        $test->id = 392;
        die(var_dump(assign_get_user_grades($test, $USER->id)));
        $completion = new \completion_info(get_course($courseid));
        $courseandcm = get_course_and_cm_from_cmid(392, null, $courseid, $USER->id);
        die(var_dump(get_course_and_cm_from_cmid(392, null, $courseid, $USER->id)));
        die(var_dump(cm_completion_details::get_instance(get_fast_modinfo($courseid, $USER->id))->get_details()));
        return $type;
    }

    /**
     * Returns the structure of the module array.
     * @return external_multiple_structure
     */
    public static function get_all_course_modules_returns() {
        new external_value(PARAM_INT, 'Module ID');
    }
}
