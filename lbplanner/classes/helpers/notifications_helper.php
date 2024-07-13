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
 * Provides helper classes for notification related stuff
 *
 * @package    local_lbplanner
 * @subpackage helpers
 * @copyright  2024 NecodeIT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_lbplanner\helpers;

use external_single_structure;
use external_value;
use local_lbplanner\enums\{NOTIF_STATUS, NOTIF_TRIGGER};

/**
 * Provides helper methods for notification related stuff
 */
class notifications_helper {
    /**
     * Name of the notifications table.
     */
    const LBPLANNER_NOTIFICATION_TABLE = 'local_lbplanner_notification';

    /**
     * Returns the data structure of a notification
     *
     * @return external_single_structure The data structure of a notification.
     */
    public static function structure(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_INT, 'The status of the notification ' . NOTIF_STATUS::format()),
            'type' =>
                new external_value(PARAM_INT, 'The type of the event that triggered the notification ' . NOTIF_TRIGGER::format()),
            'info' => new external_value(PARAM_INT, 'Additional information about the notification'),
            'userid' => new external_value(PARAM_INT, 'The ID of the user for whom the notification is for'),
            'notificationid' => new external_value(PARAM_INT, 'The ID of the notification', NULL_NOT_ALLOWED),
            'timestamp' => new external_value(PARAM_INT, 'The timestamp of the notification'),
            'timestamp_read' => new external_value(PARAM_INT, 'The timestamp of the notification when it was read'),
        ]);
    }

    /**
     * Notifies the given user about the given event, with the given info.
     *
     * @param int $userid The user to notify.
     * @param int $info   Additional information as stringified json.
     * @param int $type   The type of notification.
     *
     * @return integer The id of the notification.
     */
    public static function notify_user(int $userid, int $info, int $type): int {
        global $DB;

        $notification = new \stdClass();
        $notification->userid = $userid;
        $notification->info = $info;
        $notification->type = $type;
        $notification->status = NOTIF_STATUS::UNREAD;
        $notification->timestamp = time();
        $notification->timestamp_read = null;

        $id = $DB->insert_record(self::LBPLANNER_NOTIFICATION_TABLE, $notification);

        return $id;
    }
}
