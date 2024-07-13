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
use local_lbplanner\helpers\plan_helper;
use local_lbplanner\helpers\notifications_helper;
use local_lbplanner\enums\{PLAN_ACCESS_TYPE, PLAN_INVITE_STATE, NOTIF_TRIGGER};

/**
 * Accept an invite to the plan.
 *
 * @package local_lbplanner
 * @subpackage services_plan
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan_accept_invite extends external_api {
    /**
     * Parameters for accept_invite.
     * @return external_function_parameters
     */
    public static function accept_invite_parameters(): external_function_parameters {
        return new external_function_parameters([
        'inviteid' => new external_value(PARAM_INT, 'the ID of the invite to be accepted', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
        ]);
    }

    /**
     * Accepts an invite
     *
     * @param int $inviteid the ID of the invite to be accepted
     * @return void
     * @throws \moodle_exception when invite not found, already accepted or declined
     */
    public static function accept_invite(int $inviteid) {
        global $DB, $USER;

        self::validate_parameters(self::accept_invite_parameters(), [
        'inviteid' => $inviteid,
        ]);

        if (!$DB->record_exists(plan_helper::INVITES_TABLE, ['id' => $inviteid, 'inviteeid' => $USER->id])) {
            throw new \moodle_exception('Invite not found');
        }
        if (!$DB->record_exists(plan_helper::INVITES_TABLE,
        [ 'id' => $inviteid, 'inviteeid' => $USER->id, 'status' => PLAN_INVITE_STATE::PENDING])) {
            throw new \moodle_exception('Invite already accepted or declined');
        }

        $invite = $DB->get_record(plan_helper::INVITES_TABLE,
        [
            'id' => $inviteid,
            'inviteeid' => $USER->id,
            'status' => PLAN_INVITE_STATE::PENDING,
        ],
        '*',
        MUST_EXIST
        );

        // Notify the user that invite has been accepted.
        notifications_helper::notify_user(
            $invite->inviterid,
            $invite->id,
            NOTIF_TRIGGER::INVITE_ACCEPTED
        );

        // Deletes the old plan if the user is the owner of it.
        $oldplanid = plan_helper::get_plan_id($USER->id);
        if (plan_helper::get_owner($oldplanid) == $USER->id) {

            foreach (plan_helper::get_plan_members($oldplanid) as $member) {
                if ($member->userid != $USER->id) {
                    self::call_external_function('local_lbplanner_plan_remove_user', [
                        'planid' => $oldplanid,
                        'userid' => $member->userid,
                    ]);
                }
            }
            self::call_external_function('local_lbplanner_plan_clear_plan', [
                'planid' => $oldplanid,
                'userid' => $USER->id,
            ]);
            $DB->delete_records(plan_helper::TABLE, ['id' => $oldplanid]);
        }
        // Updates the plan access.
        $planaccess = $DB->get_record(
            plan_helper::ACCESS_TABLE,
            [
                'planid' => $oldplanid,
                'userid' => $USER->id,
            ],
            '*',
            MUST_EXIST
        );

        $invite->status = PLAN_INVITE_STATE::ACCEPTED;

        $DB->update_record(plan_helper::INVITES_TABLE, $invite);

        $planaccess->accesstype = PLAN_ACCESS_TYPE::READ;
        $planaccess->planid = $invite->planid;

        $DB->update_record(plan_helper::ACCESS_TABLE, $planaccess);
        $invites = plan_helper::get_invites_send($USER->id);
        foreach ($invites as $invite) {
            if ($invite->status == PLAN_INVITE_STATE::PENDING) {
                $invite->status = PLAN_INVITE_STATE::EXPIRED;
                $DB->update_record(plan_helper::INVITES_TABLE, $invite);
            }
        }
    }

    /**
     * Returns the structure of nothing.
     * @return null
     */
    public static function accept_invite_returns() {
        return null;
    }
}
