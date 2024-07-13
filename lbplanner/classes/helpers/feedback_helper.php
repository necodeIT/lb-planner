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

use external_function_parameters;
use external_single_structure;
use external_value;
use stdClass;
use local_lbplanner\helpers\user_helper;

// TODO: use enums.

/**
 * Helper class for feedback
 *
 * @package    local_lbplanner
 * @subpackage helpers
 * @copyright  2024 NecodeIT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_helper {

    /**
     * Unread Feedback
     */
    const STATUS_UNREAD = 0;

    /**
     * Read Feedback
     */
    const STATUS_READ = 1;

    /**
     * The name of the table which is used by LP to store feedback in
     */
    const LBPLANNER_FEEDBACK_TABLE = 'local_lbplanner_feedback';

    /**
     * The return structure of a feedback.
     *
     * @return external_single_structure The structure of a module.
     */
    public static function structure(): external_single_structure {
        return new external_single_structure(
            [
                'content' => new external_value(PARAM_TEXT, 'Content of the feedback'),
                'userid' => new external_value(PARAM_INT, 'The id of the user'),
                'type' => new external_value(PARAM_INT, 'The Type of the feedback'),
                'notes' => new external_value(PARAM_TEXT, 'Notes of the feedback'),
                'id' => new external_value(PARAM_INT, 'The id of the feedback'),
                'status' => new external_value(PARAM_INT, 'The status of the feedback'),
                'timestamp' => new external_value(PARAM_INT, 'The time when the feedback was created'),
                'lastmodified' => new external_value(PARAM_INT, 'The time when the feedback was last modified'),
                'lastmodifiedby' => new external_value(PARAM_INT, 'The id of the user who last modified the feedback'),
                'logfile' => new external_value(PARAM_TEXT, 'The logs of the feedback'),
            ]
        );
    }

    /**
     * Gives back the feedback of the given feedbackid
     *
     * @param int $feedbackid The id of the feedback
     *
     * @return stdClass The feedback
     */
    public static function get_feedback(int $feedbackid): stdClass {
        global $DB;
        return $DB->get_record(self::LBPLANNER_FEEDBACK_TABLE, ['id' => $feedbackid]);
    }

    /**
     * Checks if the user has access to feedback
     *
     * @throws \moodle_exception when the user has no access
     */
    public static function assert_admin_access() {
        if (!has_capability(user_helper::CAPABILITY_ADMIN, \context_system::instance())
            && !has_capability(user_helper::CAPABILITY_MANAGER, \context_system::instance())
        ) {
            throw new \moodle_exception('Acces denied');
        }
    }

    /**
     * Returns all feedback records.
     *
     * @return array all feedback records
     */
    public static function get_all_feedbacks(): array {
        global $DB;
        return $DB->get_records(self::LBPLANNER_FEEDBACK_TABLE);
    }
}
