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
use local_lbplanner\helpers\feedback_helper;

/**
 * Add feedback to the database.
 *
 * @package local_lbplanner
 * @subpackage services_feedback
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_submit_feedback extends external_api {
    /**
     * Parameters for submit_feedback.
     * @return external_function_parameters
     */
    public static function submit_feedback_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'type' => new external_value(
                    PARAM_INT,
                    'type of Feedback (bug, typo, feature, other)', // TODO: use enums.
                    VALUE_REQUIRED,
                    null,
                    NULL_NOT_ALLOWED,
                ),
                'content' => new external_value(
                    PARAM_TEXT,
                    'feedback contents',
                    VALUE_REQUIRED,
                    null,
                    NULL_NOT_ALLOWED,
                ),
                'logfile' => new external_value(
                    PARAM_TEXT,
                    'file name of the associated log file',
                    VALUE_DEFAULT,
                    null,
                    NULL_NOT_ALLOWED,
                ),
            ]
        );
    }

    /**
     * Add feedback to the database.
     *
     * @param int $type type of Feedback
     * @see feedback_helper
     * @param string $content feedback contents
     * @param string $logfile file name of the associated log file
     * @return int The ID of the new feedback
     */
    public static function submit_feedback(int $type, string $content, string $logfile): int {
        global $DB, $USER;

        self::validate_parameters(
            self::submit_feedback_parameters(),
            ['type' => $type, 'content' => $content, 'logfile' => $logfile]
        );

        // TODO: validate $type.

        $id = $DB->insert_record(feedback_helper::LBPLANNER_FEEDBACK_TABLE, [
            'content' => $content,
            'userid' => $USER->id,
            'type' => $type,
            'status' => feedback_helper::STATUS_UNREAD,
            'timestamp' => time(),
            'logfile' => $logfile,
        ]);

        return $id;
    }

    /**
     * Returns the structure of the feedback ID.
     * @return external_value
     */
    public static function submit_feedback_returns(): external_value {
        return new external_value(PARAM_INT, "The ID of the new feedback");
    }
}
