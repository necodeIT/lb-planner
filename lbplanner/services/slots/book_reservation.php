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

use DateTimeImmutable;

use core_user;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;

use local_lbplanner\helpers\slot_helper;
use local_lbplanner\model\reservation;

/**
 * Returns all slots the user can theoretically reserve.
 * This does not include times the user has already reserved a slot for.
 *
 * @package local_lbplanner
 * @subpackage services_plan
 * @copyright 2024 necodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slots_book_reservation extends external_api {
    /**
     * Parameters for book_reservation.
     * @return external_function_parameters
     */
    public static function book_reservation_parameters(): external_function_parameters {
		global $USER;
        return new external_function_parameters([
            'slotid' => new external_value(
                PARAM_INT,
                'ID of the slot for which a reservation is being requested',
                VALUE_REQUIRED,
                null,
                NULL_NOT_ALLOWED
            ),
            'date' => new external_value(
                PARAM_TEXT,
                'date of the reservation in YYYY-MM-DD (as per ISO-8601)',
                VALUE_REQUIRED,
                null,
                NULL_NOT_ALLOWED
            ),
            'userid' => new external_value(
                PARAM_INT,
                'the user to reserve this slot for',
                VALUE_OPTIONAL,
                $USER->id,
                NULL_NOT_ALLOWED
            ),
        ]);
    }

    /**
     * Returns slots the current user is supposed to see
     */
    public static function book_reservation(int $slotid, string $date, int $userid): array {
        global $USER, $DB;

        $now = new DateTimeImmutable();
        $dateobj = DateTimeImmutable::createFromFormat("YY-MM-DD", $date);
        $td = $dateobj->diff($now);

        if($td->invert){
            throw new \moodle_exception('Can\'t reserve date in the past');
        }

        $maxdays = null;
        $student = null;

        if ($userid === $USER->id) {
            // student reserving slot for themself

            $maxdays = slot_helper::RESERVATION_RANGE_USER;
            $student = $USER;
        } else {
            // supervisor reserving slot for student

            if (!slot_helper::check_slot_supervisor($USER->id, $slotid)) {
                throw new \moodle_exception('Forbidden: you\'re not a supervisor of this slot');
            }

            $maxdays = slot_helper::RESERVATION_RANGE_USER;
            $student = core_user::get_user($userid, '*', MUST_EXIST);
        }

        if ($td->days > $maxdays) {
            throw new \moodle_exception("Date is past allowed date ({$maxdays} days in the future)");
        }

        $slot = slot_helper::get_slot($slotid);

        // check if user has access to slot
        if (sizeof(slot_helper::filter_slots_for_user([$slot], $student)) === 0) {
            throw new \moodle_exception('Student does not have access to this slot');
        }

        // check if user is already in slot
        foreach (slot_helper::get_reservations_for_slot($slotid) as $_reservation) {
            if ($_reservation->userid === $userid){
                throw new \moodle_exception('Student is already in slot');
            }
        }

        // check if slot is full
        if ($slot->get_fullness() > $slot->size){
            throw new \moodle_exception('Slot is already full');
        }

        $reservation = new reservation(0, $slotid, $dateobj, $userid, $USER->id);

        $id = $DB->insert_record(slot_helper::TABLE_RESERVATIONS, $reservation->prepare_for_db());
        $reservation->id = $id;

        return $reservation->prepare_for_api();
    }

    /**
     * Returns the structure of the slot array
     * @return external_multiple_structure
     */
    public static function book_reservation_returns(): external_single_structure {
        return reservation::api_structure();
    }
}
