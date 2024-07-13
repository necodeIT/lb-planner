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
use external_multiple_structure;
use local_lbplanner\helpers\invite_helper;
use local_lbplanner\helpers\plan_helper;

/**
 * Get all the invites of the current user.
 *
 * @package local_lbplanner
 * @subpackage services_plan
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan_get_invites extends external_api {
    /**
     * Parameters for get_invites.
     * @return external_function_parameters
     */
    public static function get_invites_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Returns all invites of the current user.
     *
     * @return array
     */
    public static function get_invites(): array {
        global $DB, $USER;

        $invitesreceived = $DB->get_records(plan_helper::INVITES_TABLE, ['inviteeid' => $USER->id]);
        $invitessent = $DB->get_records(plan_helper::INVITES_TABLE, ['inviterid' => $USER->id]);

        $invites = [];

        foreach ($invitesreceived as $invite) {
            $invites[] = [
                'id' => $invite->id,
                'inviterid' => $invite->inviterid,
                'inviteeid' => $invite->inviteeid,
                'planid' => $invite->planid,
                'status' => $invite->status,
                'timestamp' => $invite->timestamp,
            ];
        }

        foreach ($invitessent as $invitesent) {
            $invites[] = [
                'id' => $invitesent->id,
                'inviterid' => $invitesent->inviterid,
                'inviteeid' => $invitesent->inviteeid,
                'planid' => $invitesent->planid,
                'status' => $invitesent->status,
                'timestamp' => $invitesent->timestamp,
            ];
        }

        return $invites;
    }

    /**
     * Returns the structure of the array of invites.
     * @return external_multiple_structure
     */
    public static function get_invites_returns(): external_multiple_structure {
        return new external_multiple_structure(
            invite_helper::structure()
        );
    }
}
