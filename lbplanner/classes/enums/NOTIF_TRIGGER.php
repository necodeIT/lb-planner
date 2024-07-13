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
 * enum for notif trigger
 *
 * @package local_lbplanner
 * @subpackage enums
 * @copyright 2024 NecodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_lbplanner\enums;

// TODO: revert to native enums once we migrate to php8.

use local_lbplanner\polyfill\Enum;

/**
 * Possible triggers for sending a notification
 */
class NOTIF_TRIGGER extends Enum {
    /**
     * Invitation sent
     */
    const INVITE = 0;
    /**
     * Invitation accepted
     */
    const INVITE_ACCEPTED = 1;
    /**
     * Invitation declined
     */
    const INVITE_DECLINED = 2;
    /**
     * User left the plan
     */
    const PLAN_LEFT = 3;
    /**
     * User got removed from the plan
     */
    const PLAN_REMOVED = 4;
    /**
     * User registered
     */
    const USER_REGISTERED = 5;
}
