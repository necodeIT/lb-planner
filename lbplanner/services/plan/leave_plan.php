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
use local_lbplanner\helpers\plan_helper;
use local_lbplanner\helpers\notifications_helper;
use local_lbplanner\enums\{NOTIF_TRIGGER, PLAN_ACCESS_TYPE, PLAN_INVITE_STATE};

/**
 * Leave your plan
 *
 * if no other user exists in the plan, the user can't leave
 *
 * @package local_lbplanner
 * @subpackage services_plan
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan_leave_plan extends external_api {
    /**
     * Parameters for leave_plan.
     * @return external_function_parameters
     */
    public static function leave_plan_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Leave your plan
     *
     * @return void
     * @throws \moodle_exception when user is only member left in plan
     */
    public static function leave_plan() {
        global $DB, $USER;

        $planid = plan_helper::get_plan_id($USER->id);

        // TODO: remove useless check.
        if (plan_helper::get_access_type($USER->id, $planid) === PLAN_ACCESS_TYPE::NONE) {
            throw new \moodle_exception('User is not a member of this plan');
        }

        if (plan_helper::get_access_type($USER->id, $planid) === PLAN_ACCESS_TYPE::OWNER) {
            $members = plan_helper::get_plan_members($planid);

            if (count($members) == 1) {
                throw new \moodle_exception('Cannot Leave Plan: Plan must have at least one other member');
            }

            $writemembers = [];
            $allmembers = [];
            foreach ($members as $member) {
                if ($member->userid == $USER->id) {
                    continue;
                }
                if ($member->accesstype == PLAN_ACCESS_TYPE::WRITE) {
                    $writemembers[] = $member;
                }
                $allmembers[] = $member;
            }
            if (count($writemembers) > 0) {
                $newowner = $writemembers[rand( 0, count($writemembers) - 1)]->userid;
            } else {
                $newowner = $allmembers[rand( 0, count($allmembers) - 1)]->userid;
            }
            $newowneraccess = $DB->get_record(
                plan_helper::ACCESS_TABLE,
                ['planid' => $planid, 'userid' => $newowner], '*', MUST_EXIST
            );
            $newowneraccess->accesstype = PLAN_ACCESS_TYPE::OWNER;
            $DB->update_record(plan_helper::ACCESS_TABLE, $newowneraccess);
        }

        $newplanid = plan_helper::copy_plan($planid, $USER->id);

        $oldaccess = $DB->get_record(
            plan_helper::ACCESS_TABLE,
            ['planid' => $planid, 'userid' => $USER->id], '*', MUST_EXIST
        );

        $oldaccess->planid = $newplanid;
        $oldaccess->accesstype = PLAN_ACCESS_TYPE::OWNER;

        $DB->update_record(plan_helper::ACCESS_TABLE, $oldaccess);

        // Notify plan owner that user has left his plan.
        $invites = plan_helper::get_invites_send($USER->id);
        foreach ($invites as $invite) {
            if ($invite->status == PLAN_INVITE_STATE::PENDING) {
                $invite->status = PLAN_INVITE_STATE::EXPIRED;
                $DB->update_record(plan_helper::INVITES_TABLE, $invite);
            }
        }

        notifications_helper::notify_user(
            plan_helper::get_owner($planid),
            $USER->id,
            NOTIF_TRIGGER::PLAN_LEFT
        );
    }

    /**
     * Returns the structure of nothing.
     * @return null
     */
    public static function leave_plan_returns() {
        return null;
    }
}
