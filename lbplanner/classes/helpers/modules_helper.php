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
/**
 * Collection of helper classes for handling modules
 *
 * @package local_lbplanner
 * @subpackage helpers
 * @copyright 2024 NecodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_lbplanner\helpers;

use block_accessreview\external\get_module_data;
use external_function_parameters;
use external_single_structure;
use external_value;
use moodle_url;
use local_lbplanner\enums\{MODULE_STATUS, MODULE_GRADE, MODULE_TYPE};

/**
 * Contains helper functions for working with modules.
 */
class modules_helper {

    /**
     * Table where modules are stored.
     */
    const ASSIGN_TABLE = 'assign';

    /**
     * Table where max. and min. grades of the modules are stored.
     */
    const GRADE_ITEMS_TABLE = 'grade_items';

    /**
     * Table where course modules are stored.
     */
    const COURSE_MODULES_TABLE = 'course_modules';

    /**
     * Table where grades of the modules are stored.
     */
    const GRADES_TABLE = 'assign_grades';

    /**
     * Table where grading scales are stored.
     */
    const SCALE_TABLE = 'scale';

    /**
     * Table where submissions of the modules are stored.
     */
    const SUBMISSIONS_TABLE = 'assign_submission';

    /**
     * Submitted status name of a submission.
     */
    const SUBMISSION_STATUS_SUBMITTED = 'submitted';

    /**
     * The return structure of a module.
     *
     * @return external_single_structure The structure of a module.
     */
    public static function structure(): external_single_structure {
        return new external_single_structure(
        [
            'moduleid' => new external_value(PARAM_INT, 'Module ID'),
            'name' => new external_value(PARAM_TEXT, 'Shortened module name (max. 5 chars)'),
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'status' => new external_value(PARAM_INT, 'Module status '.MODULE_STATUS::format()),
            'type' => new external_value(PARAM_INT, 'Module type '.MODULE_TYPE::format()),
            'url' => new external_value(PARAM_TEXT, 'URL to moodle page for module'),
            'grade' => new external_value(PARAM_INT, 'The grade of the module '.MODULE_GRADE::format()),
            'deadline' => new external_value(PARAM_INT, 'The deadline of the module set by the teacher'),
        ]
        );
    }

    /**
     * Determins the enum value for a grade.
     *
     * @param int $grade The grade of the module.
     * @param int $maxgrade The max. grade of the module.
     * @param int $mingrade The min. grade of the module.
     * @param int $gradepass The grade to pass the module.
     * @return integer The enum value for the grade.
     */
    public static function determin_uinified_grade(int $grade, int $maxgrade, int $mingrade, int $gradepass): int {
        if ($grade < $gradepass) {
            return MODULE_GRADE::RIP;
        }

        $maxgrade = $maxgrade - $mingrade;

        $p = $grade / $maxgrade;

        if ($p >= 0.9) {
            return MODULE_GRADE::EKV;
        } else if ($p >= 0.8) {
            return MODULE_GRADE::EK;
        } else if ($p >= 0.7) {
            return MODULE_GRADE::GKV;
        } else if ($p >= 0.4) {
            return MODULE_GRADE::GK;
        } else {
            return MODULE_GRADE::RIP;
        }
    }

    /**
     * Maps the given info to a module status.
     *
     * @param bool $submitted Whether the module is submitted.
     * @param bool $done Whether the module is completed.
     * @param bool $late Whether the module is late.
     * @return integer The enum value for the module status.
     */
    public static function map_status(bool $submitted, bool $done, bool $late): int {
        if ($done) {
            return MODULE_STATUS::DONE;
        } else if ($submitted) {
            return MODULE_STATUS::UPLOADED;
        } else if ($late) {
            return MODULE_STATUS::LATE;
        } else {
            return MODULE_STATUS::PENDING;
        }
    }

    /**
     * Maps the given name to a module type.
     *
     * @param string $modulename The name of the module.
     * @return integer The enum value for the module type.
     */
    public static function determin_type(string $modulename): int {
        // Convert module name to uppercase.
        $modulename = strtoupper($modulename);

        // Return TYPE_TEST if the name contains 'test' or 'sa'.
        if (strpos($modulename, '[TEST]') !== false || strpos($modulename, '[SA]') !== false) {
            return MODULE_TYPE::TEST;
        }
        // Return TYPE_GK if the name contains 'GK'.

        if (strpos($modulename, '[GK]') !== false) {
            return MODULE_TYPE::GK;
        }

        if (strpos($modulename, '[EK]') !== false) {
            return MODULE_TYPE::EK;
        }

        // Return TYPE_EK if the name contains 'M'.
        if (strpos($modulename, '[M]') !== false) {
            return MODULE_TYPE::M;
        }

        // Return TYPE_NONE elswise.
        return MODULE_TYPE::NONE;
    }

    /**
     * Returns the url of the module.
     *
     * @param int $moduleid The id of the module.
     * @param int $courseid The id of the course.
     * @return string The url of the module.
     */
    public static function get_module_url(int $moduleid, int $courseid): string {
        global $DB;

        $view = $DB->get_record(
            self::COURSE_MODULES_TABLE,
            ['course' => $courseid, 'instance' => $moduleid, 'module' => 1]
        );

        return strval(new moodle_url('/mod/assign/view.php?id='.$view->id));
    }

    /**
     * Retrieves a module of the given id for the given user.
     *
     * @param int $moduleid The id of the module.
     * @param int $userid The id of the user.
     * @return array The module.
     */
    public static function get_module(int $moduleid, int $userid): array {
        global $DB;
        date_default_timezone_set('UTC');

        // Get module data.
        $module = $DB->get_record(self::ASSIGN_TABLE, ['id' => $moduleid]);

        // Determine module type.
        $type = self::determin_type($module->name);

        if ($type == MODULE_TYPE::NONE) {
            return [];
        }
        // Check if there are any submissions or feedbacks for this module.

        $submitted = false;

        if ($DB->record_exists(self::SUBMISSIONS_TABLE, ['assignment' => $moduleid, 'userid' => $userid])) {
            $submission = $DB->get_record(
                self::SUBMISSIONS_TABLE,
                ['assignment' => $moduleid, 'userid' => $userid]
            );

            $submitted = strval($submission->status) == self::SUBMISSION_STATUS_SUBMITTED;
        }

        $done = false;
        $grade = null;

        if ($DB->record_exists(self::GRADES_TABLE, ['assignment' => $moduleid, 'userid' => $userid])) {
            $moduleboundaries = $DB->get_record(self::GRADE_ITEMS_TABLE, ['iteminstance' => $moduleid]);

            $mdlgrades = $DB->get_records(
                self::GRADES_TABLE,
                ['assignment' => $moduleid, 'userid' => $userid]
            );

            $mdlgrade = end($mdlgrades);

            if ($mdlgrade->grade > 0) {
                $done = true;

                $grade  = self::determin_uinified_grade(
                $mdlgrade->grade, $moduleboundaries->grademax,
                $moduleboundaries->grademin,
                $moduleboundaries->gradepass
                );

                $done = $grade != MODULE_GRADE::RIP;
            }
        }
        // Check if the module is late.

        $late = false;
        $planid = plan_helper::get_plan_id($userid);

        if ($DB->record_exists(plan_helper::DEADLINES_TABLE, ['planid' => $planid, 'moduleid' => $moduleid])) {
            $deadline = $DB->get_record(plan_helper::DEADLINES_TABLE, ['planid' => $planid, 'moduleid' => $moduleid]);
            $late = intval(date("Ymd", $deadline->deadlineend)) < intval(date("Ymd")) && !$done;
        }

        $status = self::map_status($submitted, $done, $late);

        // Return the appropriate data.

        return [
            'moduleid' => $moduleid,
            'name' => $module->name,
            'courseid' => $module->course,
            'status' => $status,
            'type' => $type,
            'url' => self::get_module_url($moduleid, $module->course),
            'grade' => $grade,
            'deadline' => $module->duedate > 0 ? $module->duedate : null,
        ];
    }

    /**
     * Reteruns all modules for the given course id.
     *
     * @param int $courseid The id of the course.
     * @param int $userid The id of the user.
     * @param bool $ekenabled Whether EK modules should be included.
     * @return array The modules.
     */
    public static function get_all_course_modules(int $courseid, int $userid, bool $ekenabled): array {
        global $DB;

        $mdlmodules = $DB->get_records(self::ASSIGN_TABLE, ['course' => $courseid]);

        $modules = [];

        foreach ($mdlmodules as $mdlmodule) {
            if (!$ekenabled && self::determin_type($mdlmodule->name) == MODULE_TYPE::EK) {
                continue;
            }
            $module = self::get_module($mdlmodule->id, $userid);
            if ($module != null) {
                $modules[] = $module;
            }
        }

        return $modules;
    }
}
