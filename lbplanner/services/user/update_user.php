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

use dml_exception;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use local_lbplanner\helpers\user_helper;
use local_lbplanner\helpers\plan_helper;
use moodle_exception;

/**
 * Update the data for a user.
 *
 * @package local_lbplanner
 * @subpackage services_user
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_update_user extends external_api {
    /**
     * Parameters for update_user
     * @return external_function_parameters
     */
    public static function update_user_parameters(): external_function_parameters {
        return new external_function_parameters([
            'lang' => new external_value(PARAM_TEXT, 'The language the user has selected', VALUE_DEFAULT, null),
            'theme' => new external_value(PARAM_TEXT, 'The theme the user has selected', VALUE_DEFAULT, null),
            'colorblindness' => new external_value(
                PARAM_TEXT,
                'The colorblindness the user has selected',
                VALUE_DEFAULT,
                null),
            'displaytaskcount' => new external_value(
                PARAM_INT,
                'If the user has the taskcount-enabled 1-yes 0-no',
                VALUE_DEFAULT,
                null),
        ]);
    }

    /**
     * Updates the given user in the lbplanner DB
     * @param string $lang language the user choose
     * @param string $theme The theme the user has selected
     * @param string $colorblindness The colorblindness the user has selected
     * @param int $displaytaskcount The displaytaskcount the user has selected
     * @return array The updated user
     * @throws moodle_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function update_user($lang, $theme, $colorblindness, $displaytaskcount): array {
        global $DB, $USER;

        self::validate_parameters(
            self::update_user_parameters(),
            [
                'lang' => $lang,
                'theme' => $theme,
                'colorblindness' => $colorblindness,
                'displaytaskcount' => $displaytaskcount,
            ]
        );
        $userid = $USER->id;
        user_helper::assert_access($userid);

        // Look if User-Id is in the DB.
        if (!user_helper::check_user_exists($userid)) {
            throw new moodle_exception('User does not exist');
        }
        $user = user_helper::get_user($userid);
        if ($lang !== null) {
            $user->language = $lang;
        }
        if ($colorblindness !== null) {
            $user->colorblindness = $colorblindness;
        }
        if ($theme !== null) {
            $user->theme = $theme;
        }
        if ($displaytaskcount !== null) {
            $user->displaytaskcount = $displaytaskcount;
        }

        $DB->update_record(user_helper::LB_PLANNER_USER_TABLE, $user);

        return [
            'userid' => $userid,
            'lang' => $user->language,
            'theme' => $user->theme,
            'capabilities' => user_helper::get_user_capability_bitmask($userid),
            'username' => $USER->username,
            'firstname' => $USER->firstname,
            'lastname' => $USER->lastname,
            'profileimageurl' => user_helper::get_mdl_user_picture($userid),
            'planid' => plan_helper::get_plan_id($userid),
            'colorblindness' => $user->colorblindness,
            'displaytaskcount' => $user->displaytaskcount,
            'vintage' => $USER->address,

        ];
    }
    /**
     * Returns the data of a user.
     * @return external_single_structure
     */
    public static function update_user_returns(): external_single_structure {
        return new external_single_structure(
            [
                'userid' => new external_value(PARAM_INT, 'The id of the user'),
                'username' => new external_value(PARAM_TEXT, 'The username of the user'),
                'firstname' => new external_value(PARAM_TEXT, 'The firstname of the user'),
                'lastname' => new external_value(PARAM_TEXT, 'The lastname of the user'),
                'capabilities' => new external_value(PARAM_INT, 'The capabilities of the user represented as a bitmask value'),
                'theme' => new external_value(PARAM_TEXT, 'The theme the user has selected'),
                'lang' => new external_value(PARAM_TEXT, 'The language the user has selected'),
                'profileimageurl' => new external_value(PARAM_URL, 'The url of the profile image'),
                'planid' => new external_value(PARAM_INT, 'The id of the plan the user is assigned to'),
                'colorblindness' => new external_value(PARAM_TEXT, 'The colorblindness the user has selected'),
                'displaytaskcount' => new external_value(PARAM_INT, 'If the user has the taskcount-enabled 1-yes 0-no'),
                'vintage' => new external_value(PARAM_TEXT, 'The vintage of the user'),
            ]
        );
    }
}
