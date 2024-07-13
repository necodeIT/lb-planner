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
use local_lbplanner\enums\NOTIF_STATUS;
use local_lbplanner\helpers\notifications_helper;

/**
 * Update the notification status.
 *
 * @package local_lbplanner
 * @subpackage services_notifications
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notifications_update_notification extends external_api {
    /**
     * Parameters for update_notification.
     * @return external_function_parameters
     */
    public static function update_notification_parameters(): external_function_parameters {
        return new external_function_parameters([
            'status' => new external_value(
                PARAM_INT,
                'notification status ' . NOTIF_STATUS::format(),
                VALUE_REQUIRED,
                null,
                NULL_NOT_ALLOWED
            ),
            'notificationid' =>
                new external_value(PARAM_INT, 'ID of the notification to be updated', VALUE_REQUIRED, null, NULL_NOT_ALLOWED),
        ]);
    }

    /**
     * Update the notification status.
     *
     * @param int $status notification status
     * @see notifications_helper
     * @param int $notificationid ID of the notification to be updated
     * @return void
     * @throws \moodle_exception when the notification doesn't exist
     */
    public static function update_notification(int $status, int $notificationid) {
        global $DB;

        self::validate_parameters(
            self::update_notification_parameters(),
            ['status' => $status, 'notificationid' => $notificationid]
        );

        if (!$DB->record_exists(notifications_helper::TABLE, ['id' => $notificationid])) {
            throw new \moodle_exception('Notification does not exist');
        }

        $notification = $DB->get_record(notifications_helper::TABLE, ['id' => $notificationid], '*', MUST_EXIST);
        $notification->status = $status;
        $notification->timestamp_read = time();

        $DB->update_record(notifications_helper::TABLE, $notification);
    }

    /**
     * Returns the structure of nothing.
     * @return null
     */
    public static function update_notification_returns() {
        return null;
    }
}
