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
use local_lbplanner\helpers\{user_helper, plan_helper, notifications_helper};
use local_lbplanner\enums\{NOTIF_TRIGGER, PLAN_ACCESS_TYPE, PLAN_EK};
use moodle_exception;
use stdClass;

/**
 * Register a new user in the lbplanner app.
 *
 * @package local_lbplanner
 * @subpackage services_user
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_register_user extends external_api {
    /**
     * Parameters for register_user
     * @return external_function_parameters
     */
    public static function register_user_parameters(): external_function_parameters {
        return new external_function_parameters([
            'lang' => new external_value(
                PARAM_TEXT,
                'The language the user has selected',
                VALUE_DEFAULT,
                'en',
                NULL_NOT_ALLOWED
            ),
            'theme' => new external_value(
                PARAM_TEXT,
                'The theme the user has selected',
                VALUE_DEFAULT,
                'Light',
                NULL_NOT_ALLOWED),
            'ekenabled' => new external_value(
                PARAM_INT,
                'If the user wants to have EK-Enabled',
                VALUE_DEFAULT,
                0,
                NULL_NOT_ALLOWED,
            ),
        ]);
    }

    /**
     * Registers the given user to the lbplanner DB
     * @param string $lang language the user choose
     * @param string $theme The theme the user has selected
     * @throws dml_exception
     * @throws moodle_exception
     * @throws invalid_parameter_exception
     */
    public static function register_user(string $lang, string $theme): array {
        global $DB, $USER;

        self::validate_parameters(
            self::register_user_parameters(),
            ['lang' => $lang, 'theme' => $theme]
        );
        $userid = $USER->id;

        user_helper::assert_access($userid);

        if (user_helper::check_user_exists($userid)) {
            throw new moodle_exception('User already registered');
        }

        $lbplanneruser = new stdClass();
        $lbplanneruser->userid = $userid;
        $lbplanneruser->language = $lang;
        $lbplanneruser->theme = $theme;
        $lbplanneruser->colorblindness = "none";
        $lbplanneruser->displaytaskcount = 1;

        $DB->insert_record(user_helper::LB_PLANNER_USER_TABLE, $lbplanneruser);
        $plan = new stdClass();
        $plan->name = 'Plan for ' . $USER->username;
        $plan->enableek = PLAN_EK::ENABLED;

        $planid = $DB->insert_record(plan_helper::TABLE, $plan);

        $planaccess = new stdClass();
        $planaccess->userid = $userid;
        $planaccess->accesstype = PLAN_ACCESS_TYPE::OWNER;
        $planaccess->planid = $planid;

        $DB->insert_record(plan_helper::ACCESS_TABLE, $planaccess);

        notifications_helper::notify_user($userid, -1, NOTIF_TRIGGER::USER_REGISTERED);

        return [
            'userid' => $lbplanneruser->userid,
            'username' => $USER->username,
            'firstname' => $USER->firstname,
            'lastname' => $USER->lastname,
            'capabilities' => user_helper::get_user_capability_bitmask($userid),
            'theme' => $lbplanneruser->theme,
            'lang' => $lbplanneruser->language,
            'profileimageurl' => user_helper::get_mdl_user_picture($userid),
            'planid' => $planid,
            'colorblindness' => $lbplanneruser->colorblindness,
            'displaytaskcount' => $lbplanneruser->displaytaskcount,
        ];
    }
    /**
     * Returns the data of a user.
     * @return external_single_structure
     */
    public static function register_user_returns(): external_single_structure {
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
                'colorblindness' => new external_value(PARAM_TEXT, 'The colorblindness of the user'),
                'displaytaskcount' => new external_value(PARAM_INT, 'If the user has the taskcount-enabled 1-yes 0-no'),
            ]
        );
    }
}
