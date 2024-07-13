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
use external_value;
use gradereport_singleview\local\ui\feedback;
use local_lbplanner\helpers\feedback_helper;

/**
 * Updates feedback from the database.
 *
 * @package local_lbplanner
 * @subpackage services_feedback
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_update_feedback extends external_api {
    /**
     * Parameters for update_feedback.
     * @return external_function_parameters
     */
    public static function update_feedback_parameters(): external_function_parameters {
        return new external_function_parameters([
            'feedbackid' =>
                new external_value(PARAM_INT, 'ID of the feedback to be updated', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
            'notes' => new external_value(PARAM_TEXT, 'updated notes', VALUE_DEFAULT, null, NULL_ALLOWED),
            'status' => new external_value(PARAM_INT, 'updated status', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
        ]);
    }

    /**
     * Updates feedback from the database.
     *
     * @param int $feedbackid ID of the feedback to be updated
     * @param string $notes updated notes
     * @param int $status updated status
     * @see feedback_helper
     * @return void
     * @throws \moodle_exception when feedback not found or status invalid
     */
    public static function update_feedback(int $feedbackid, string $notes, int $status) {
        global $DB, $USER;

        self::validate_parameters(
            self::update_feedback_parameters(),
            ['feedbackid' => $feedbackid, 'notes' => $notes, 'status' => $status]
        );

        feedback_helper::assert_admin_access();

        if (!$DB->record_exists(feedback_helper::LBPLANNER_FEEDBACK_TABLE, ['id' => $feedbackid])) {
            throw new \moodle_exception('feedback_not_found');
        }

        $feedback = $DB->get_record(feedback_helper::LBPLANNER_FEEDBACK_TABLE, ['id' => $feedbackid], '*', MUST_EXIST);
        $feedback->notes = $notes;
        if ($status > 1 || $status < 0) { // TODO: use enum to validate.
            throw new \moodle_exception('Invalid status');
        }
        $feedback->status = $status;
        $feedback->lastmodified = time();
        $feedback->lastmodifiedby = $USER->id;

        $DB->update_record(feedback_helper::LBPLANNER_FEEDBACK_TABLE, $feedback);
    }

    /**
     * Returns the structure of nothing.
     * @return null
     */
    public static function update_feedback_returns() {
        return null;
    }
}
