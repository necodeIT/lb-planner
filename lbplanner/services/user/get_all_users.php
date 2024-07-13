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

use core_user;
use dml_exception;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use local_lbplanner\helpers\user_helper;
use moodle_exception;

/**
 * Gets all users registered by the lbplanner app.
 *
 * @package local_lbplanner
 * @subpackage services_user
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_get_all_users extends external_api {
    /**
     * Parameters for get_all_users
     * @return external_function_parameters
     */
    public static function get_all_users_parameters(): external_function_parameters {
        return new external_function_parameters([
            'vintage' => new external_value(PARAM_TEXT, 'The vintage to filter the users by', VALUE_DEFAULT, null),
        ]);
    }

    /**
     * Gives back all users registered by the lbplanner app.
     * @param string $vintage (optional) gives back all users with the given vintage
     * @throws moodle_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function get_all_users(string $vintage): array {
        global $DB, $USER;

        self::validate_parameters(self::get_all_users_parameters(), ['vintage' => $vintage]);

        // Check if token is allowed to access this function.

        user_helper::assert_access($USER->id);

        $users = $DB->get_records(user_helper::LB_PLANNER_USER_TABLE);

        $result = [];

        foreach ($users as $user) {
            $mdluser = core_user::get_user($user->userid, '*', MUST_EXIST);
            $result[] = [
                'userid' => $user->userid,
                'username' => $mdluser->username,
                'firstname' => $mdluser->firstname,
                'lastname' => $mdluser->lastname,
                'profileimageurl' => user_helper::get_mdl_user_picture($mdluser->id),
                'vintage' => $mdluser->address,
            ];
        }
        if ($vintage != null) {
            $result = array_filter($result, function ($user) use ($vintage) {
                return $user['vintage'] == $vintage;
            });
        }
        return $result;
    }

    /**
     * Returns the structure of the data returned by the get_all_users function
     * @return external_multiple_structure
     */
    public static function get_all_users_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'userid' => new external_value(PARAM_INT, 'The id of the user'),
                    'username' => new external_value(PARAM_TEXT, 'The username of the user'),
                    'firstname' => new external_value(PARAM_TEXT, 'The firstname of the user'),
                    'lastname' => new external_value(PARAM_TEXT, 'The lastname of the user'),
                    'profileimageurl' => new external_value(PARAM_URL, 'The url of the profile image'),
                    'vintage' => new external_value(PARAM_TEXT, 'The vintage of the user'),
                ]
            )
        );
    }
}
