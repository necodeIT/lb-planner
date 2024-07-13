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
 * Provides helper classes for any tables related with the planning function of the app
 *
 * @package    local_lbplanner
 * @subpackage helpers
 * @copyright  2024 NecodeIT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_lbplanner\helpers;

use external_single_structure;
use external_value;
use external_multiple_structure;
use local_lbplanner\enums\PLAN_ACCESS_TYPE;

/**
 * Provides helper methods for any tables related with the planning function of the app
 */
class plan_helper {
    /**
     * local_lbplanner_plans table.
     */
    const TABLE = 'local_lbplanner_plans';

    /**
     * local_lbplanner_plan_access table.
     */
    const ACCESS_TABLE = 'local_lbplanner_plan_access';

    /**
     * local_lbplanner_deadlines table.
     */
    const DEADLINES_TABLE = 'local_lbplanner_deadlines';

    /**
     * local_lbplanner_plan_invites table.
     */
    const INVITES_TABLE = 'local_lbplanner_plan_invites';

    /**
     * Returns a list of user id's that are members of the plan.
     *
     * @param int $planid The id of the plan.
     *
     * @return array An array of user id's.
     */
    public static function get_plan_members(int $planid): array {
        global $DB;
        $members = $DB->get_records(self::ACCESS_TABLE, ['planid' => $planid]);
        return $members;
    }

    /**
     * Returns the user id of the owner of the plan.
     *
     * @param int $planid The id of the plan.
     *
     * @return integer The user id of the owner.
     */
    public static function get_owner(int $planid): int {
        global $DB;

        $owner = $DB->get_field(
            self::ACCESS_TABLE,
            'userid', ['planid' => $planid, 'accesstype' => PLAN_ACCESS_TYPE::OWNER]
        );

        return $owner;
    }

    /**
     * Returns the id of the plan that the given user is a member of.
     *
     * @param int $userid The id of the user.
     *
     * @return integer The id of the plan the given user is a member of.
     */
    public static function get_plan_id(int $userid): int {
        global $DB;
        $planid = $DB->get_field(self::ACCESS_TABLE, 'planid', ['userid' => $userid]);
        return $planid;
    }

    /**
     * Returns the access type of the given user for the given plan.
     *
     * @param int $userid The id of the user.
     * @param int $planid The id of the plan.
     *
     * @return int The access type of the given user for the given plan.
     */
    public static function get_access_type(int $userid, int $planid): int {
        global $DB;

        $field = $DB->get_field(
            self::ACCESS_TABLE,
            'accesstype',
            ['planid' => $planid, 'userid' => $userid]
        );

        if ($field === false) {
            return PLAN_ACCESS_TYPE::NONE;
        } else {
            return PLAN_ACCESS_TYPE::from($field);
        }
    }

    /**
     * Checks if the given user has editing permissions for the given plan.
     *
     * @param int $planid The id of the plan.
     * @param int $userid The id of the user.
     *
     * @return boolean True if the given user has editing permissions for the given plan.
     */
    public static function check_edit_permissions(int $planid, int $userid): bool {
        $access = self::get_access_type($userid, $planid);

        return $access === PLAN_ACCESS_TYPE::OWNER || $access === PLAN_ACCESS_TYPE::WRITE;
    }

    /**
     * Returns a list of all deadlines for the given plan.
     *
     * @param int $planid The id of the plan.
     *
     * @return array A list of all deadlines for the given plan.
     */
    public static function get_deadlines(int $planid): array {
        global $DB;

        $dbdeadlines = $DB->get_records(self::DEADLINES_TABLE, ['planid' => $planid]);

        $deadlines = [];

        foreach ($dbdeadlines as $dbdeadline) {
            $deadlines[] = [
                'deadlinestart' => $dbdeadline->deadlinestart,
                'deadlineend' => $dbdeadline->deadlineend,
                'moduleid' => $dbdeadline->moduleid,
            ];
        }

        return $deadlines;
    }

    /**
     * Retrieves all the information available about the given plan.
     *
     * @param int $planid The id of the plan.
     *
     * @return array An array containing all the information available about the given plan.
     */
    public static function get_plan(int $planid): array {
        global $DB;

        $plan = $DB->get_record(self::TABLE, ['id' => $planid]);
        $members = [];

        foreach (self::get_plan_members($planid) as $member) {
            $members[] = [
                'userid' => $member->userid,
                'accesstype' => $member->accesstype,
            ];
        }

        return [
            'name' => $plan->name,
            'planid' => $planid,
            'enableek' => $plan->enableek,
            'deadlines' => self::get_deadlines($planid),
            'members' => $members,
        ];
    }

    /**
     * The default external_multiple_structure for _returns functions of plan related api services.
     *
     * @return external_single_structure The structure.
     */
    public static function plan_structure(): external_single_structure {
        return new external_single_structure(
            [
                'name' => new external_value(PARAM_TEXT, 'Name of the plan'),
                'planid' => new external_value(PARAM_INT, 'ID of the plan'),
                'enableek' => new external_value(PARAM_BOOL, 'Whether EK is enabled'),
                'deadlines' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'moduleid' => new external_value(PARAM_INT, 'ID of the module'),
                            'deadlinestart' => new external_value(PARAM_INT, 'Start of the deadline as an UNIX timestamp'),
                            'deadlineend' => new external_value(PARAM_INT, 'End of the deadline as an UNIX timestamp'),
                        ]
                    )
                ),
                'members' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'userid' => new external_value(PARAM_INT, 'The id of the user'),
                            'accesstype' => new external_value(PARAM_INT, 'The role of the user ' . PLAN_ACCESS_TYPE::format()),
                        ]
                    )
                ),
            ]
        );
    }

    /**
     * Copies the given plan to the given user.
     *
     * @param int $planid The id of the plan.
     * @param int $userid The id of the user.
     *
     * @return integer The id of the new copy of the plan.
     */
    public static function copy_plan(int $planid, int $userid): int {
        global $DB;

        $user = user_helper::get_mdl_user_info($userid); // TODO: get_mdl_user_info doesn't exist anymore.

        $plan = $DB->get_record(self::TABLE, ['id' => $planid]);
        $plan->name = $plan->name . ' (' . $user->username . ')';
        $plan->id = null;

        $deadlines = self::get_deadlines($planid);

        $newplanid = $DB->insert_record(self::TABLE, $plan);

        foreach ($deadlines as $deadline) {
            $deadline['planid'] = $newplanid;
            $DB->insert_record(self::DEADLINES_TABLE, $deadline);
        }
        return $newplanid;
    }

    /**
     * Removes the user from the given plan.
     *
     * @param int $planid       the plan id.
     * @param int $userid       the user id.
     * @param int $removeuserid the user id to remove.
     *
     * @return int The ID of the new plan for the removed user
     */
    public static function remove_user(int $planid, int $userid, int $removeuserid): int {
        global $DB;
        if (self::get_owner($planid) != $userid) {
            throw new \moodle_exception('Access denied');
        }

        if (self::get_plan_id($removeuserid) != $planid) {
            throw new \moodle_exception('Cannot remove user from other plan');
        }

        if ($userid == $removeuserid) {
            throw new \moodle_exception('Cannot remove yourself');
        }

        if (self::get_access_type($removeuserid, $planid) === PLAN_ACCESS_TYPE::OWNER) {
            throw new \moodle_exception('Cannot remove owner');
        }

        $newplanid = self::copy_plan($planid, $removeuserid);

        $oldaccess = $DB->get_record(
            self::ACCESS_TABLE,
            ['planid' => $planid, 'userid' => $removeuserid], '*', MUST_EXIST
        );

        $oldaccess->planid = $newplanid;
        $oldaccess->accesstype = PLAN_ACCESS_TYPE::OWNER;

        $DB->update_record(self::ACCESS_TABLE, $oldaccess);

        return $newplanid;
    }

    /**
     * Get all invites that have been sent by the user.
     *
     * @param int $userid ID of the sender
     *
     * @return array an array of invites sent by the user
     */
    public static function get_invites_send(int $userid): array {
        global $DB;
        $invites = $DB->get_records(self::INVITES_TABLE, ['inviterid' => $userid]);
        return $invites;
    }

    /**
     * Get all invites that have been received by the user.
     *
     * @param int $userid ID of the receiver
     *
     * @return array an array of invites received by the user
     */
    public static function get_invites_received(int $userid): array {
        global $DB;
        $invites = $DB->get_records(self::INVITES_TABLE, ['inviteeid' => $userid]);
        return $invites;
    }
}

