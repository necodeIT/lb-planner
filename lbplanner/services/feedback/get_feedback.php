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
use local_lbplanner\helpers\user_helper;
use local_lbplanner\helpers\feedback_helper;

/**
 * Get feedback from the database.
 */
class feedback_get_feedback extends external_api {
    public static function get_feedback_parameters() {
        return new external_function_parameters(array(
            'feedbackid' => new external_value(PARAM_INT, 'The id of the course', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
            'userid' => new external_value(PARAM_INT, 'The id of the user', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
        ));
    }

    public static function get_feedback($feedbackid, $userid) {
        global $DB;

        self::validate_parameters(
            self::get_feedback_parameters(),
            array('feedbackid' => $feedbackid, 'userid' => $userid)
        );

        user_helper::assert_access($userid);

        feedback_helper::assert_access($userid);

        return feedback_helper::get_feedback($feedbackid);
    }

    public static function get_feedback_returns() {
        return feedback_helper::structure();
    }
}