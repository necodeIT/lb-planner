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
use external_multiple_structure;
use external_function_parameters;
use external_value;
use local_lbplanner\helpers\feedback_helper;

/**
 * Deletes feedback from the database.
 *
 * @package local_lbplanner
 * @subpackage services_feedback
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_delete_feedback extends external_api {
    /**
     * Parameters for delete_feedback.
     * @return external_function_parameters
     */
    public static function delete_feedback_parameters(): external_function_parameters {
        return new external_function_parameters([
            'feedbackid' => new external_value(PARAM_INT, 'ID of the feedback to delete', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
        ]);
    }

    /**
     * Deletes feedback from the database.
     *
     * @param int $feedbackid ID of the feedback to delete
     * @return void
     * @throws \moodle_exception when feedback wasn't found
     */
    public static function delete_feedback(int $feedbackid) {
        global $DB, $USER;

        self::validate_parameters(
            self::delete_feedback_parameters(),
            ['feedbackid' => $feedbackid]
        );

        if (!$DB->record_exists(feedback_helper::LBPLANNER_FEEDBACK_TABLE, ['id' => $feedbackid])) {
            throw new \moodle_exception('feedback_not_found');
        }

        feedback_helper::assert_admin_access();

        $DB->delete_records(feedback_helper::LBPLANNER_FEEDBACK_TABLE, ['id' => $feedbackid]);
    }

    /**
     * Returns the structure of nothing.
     * @return null
     */
    public static function delete_feedback_returns() {
        return null;
    }
}
