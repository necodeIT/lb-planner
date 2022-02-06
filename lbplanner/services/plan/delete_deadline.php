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
use external_single_structure;
use external_value;

class delete_deadline_plan extends external_api {
    public static function delete_deadline_parameters() {
        return new external_function_parameters(array(
            'userid' => new external_value(
                PARAM_INT,
                'The id of the user to get the data for',
                VALUE_REQUIRED,
                null,
                NULL_NOT_ALLOWED
            ),
            'planid' => new external_value(
                PARAM_INT,
                'The ID of the Plan',
                VALUE_REQUIRED,
                null,
                NULL_NOT_ALLOWED
            ),
            'moduleid' => new external_value(
                PARAM_INT,
                'The ID of the Module',
                VALUE_REQUIRED,
                null,
                NULL_NOT_ALLOWED
            ),
        ));
    }

    public static function delete_deadline($userid, $planid, $moduleid) {
        global $DB;
        global $USER;

        $params = self::validate_parameters(
            self::delete_deadline_parameters(),
            array(
                'userid' => $userid,
                'planid' => $planid,
                'moduleid' => $moduleid,
            )
        );

        // TODO: Check if the token is from the same User as the UserId.
        // TODO: Change the Deadline on the DB.

        return array();
    }

    public static function delete_deadline_returns() {
        return new external_single_structure(
            array('message' => new external_value(PARAM_TEXT, 'Sucessfull'))
        );
    }
}