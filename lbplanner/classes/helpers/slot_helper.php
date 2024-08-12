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
 * Provides helper classes for any tables related with the slot booking function of the app
 *
 * @package    local_lbplanner
 * @subpackage helpers
 * @copyright  2024 NecodeIT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_lbplanner\helpers;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use external_api;
use local_lbplanner\enums\WEEKDAY;
use local_lbplanner\model\{slot, reservation, slot_filter};

/**
 * Provides helper methods for any tables related with the planning function of the app
 */
class slot_helper {
    /**
     * how far into the future a user can reserve a slot
     */
    const RESERVATION_RANGE_USER = 3;
    /**
     * how far into the future a supervisor can reserve a slot for a user
     */
    const RESERVATION_RANGE_SUPERVISOR = 7;
    /**
     * school units according to untis, in H:i format
     */
    const SCHOOL_UNITS = [
        null,
        '08:00',
        '08:50',
        '09:50',
        '10:40',
        '11:30',
        '12:30',
        '13:20',
        '14:10',
        '15:10',
        '16:00',
        '17:00',  // All units after this point are 45min long instead of the usual 50.
        '17:45',  // We will assume 50min anyway because it's easier that way.
        '18:45',
        '19:30',
        '20:15',
        '21:00',
    ];
    /**
     * local_lbplanner_slots table.
     */
    const TABLE_SLOTS = 'local_lbplanner_slots';
    /**
     * local_lbplanner_reservations table.
     */
    const TABLE_RESERVATIONS = 'local_lbplanner_reservations';
    /**
     * local_lbplanner_slot_courses table.
     */
    const TABLE_SLOT_FILTERS = 'local_lbplanner_slot_courses';
    /**
     * local_lbplanner_supervisors table.
     */
    const TABLE_SUPERVISORS = 'local_lbplanner_supervisors';

    /**
     * Returns a list of all slots.
     *
     * @return slot[] An array of the slots.
     */
    public static function get_all_slots(): array {
        global $DB;
        $slots = $DB->get_records(self::TABLE_SLOTS, []);

        $slotsobj = [];
        foreach ($slots as $slot) {
            array_push($slotsobj, new slot(...$slot));
        }

        return $slotsobj;
    }

    /**
     * Returns a list of all slots belonging to a supervisor.
     * @param int $supervisorid userid of the supervisor in question
     *
     * @return slot[] An array of the slots.
     */
    public static function get_supervisor_slots(int $supervisorid): array {
        global $DB;

        $slots = $DB->get_records_sql(
            'SELECT slot.* FROM {'.self::TABLE_SLOTS.'} as slot'.
            'INNER JOIN '.self::TABLE_SUPERVISORS.' as supervisor ON supervisor.slotid=slot.id'.
            'WHERE supervisor.userid=?',
            [$supervisorid]
        );

        $slotsobj = [];
        foreach ($slots as $slot) {
            array_push($slotsobj, new slot(...$slot));
        }

        return $slotsobj;
    }

    /**
     * Returns a singular slot.
     * @param int $slotid ID of the slot
     *
     * @return slot the requested slot
     */
    public static function get_slot(int $slotid): slot {
        global $DB;
        $slot = $DB->get_record(self::TABLE_SLOTS, ['id' => $slotid]);

        return new slot(...$slot);
    }

    /**
     * Returns reservations for a slot.
     * @param int $slotid ID of the slot
     *
     * @return reservation[] the requested reservations
     */
    public static function get_reservations_for_slot(int $slotid): array {
        global $DB;
        $reservations = $DB->get_records(self::TABLE_RESERVATIONS, ['slotid' => $slotid]);

        $reservationsobj = [];
        foreach ($reservations as $reservation) {
            $reservation['date'] = new DateTimeImmutable($reservation['date']);
            array_push($reservationsobj, new reservation(...$reservation));
        }

        return $reservationsobj;
    }

    /**
     * Returns filters for a slot.
     * @param int $slotid ID of the slot
     *
     * @return slot_filter[] the requested filters
     */
    public static function get_filters_for_slot(int $slotid): array {
        global $DB;
        $filters = $DB->get_records(self::TABLE_SLOT_FILTERS, ['slotid' => $slotid]);

        $filtersobj = [];
        foreach ($filters as $filter) {
            array_push($filtersobj, new slot_filter(...$filter));
        }

        return $filtersobj;
    }

    /**
     * Filters an array of slots for the slots that the user can theoretically reserve
     * NOTE: not taking into account time or fullness, only filters i.e. users' class and courses
     * @param slot[] $allslots the slots to filter
     * @param mixed $user a user object - e.g. $USER or a user object from the database
     * @return slot[] the filtered slot array
     */
    public static function filter_slots_for_user(array $allslots, mixed $user): array {
        $mycourses = external_api::call_external_function('local_lbplanner_courses_get_all_courses', ['userid' => $user->id]);
        $mycourseids = [];
        foreach ($mycourses as $course) {
            array_push($mycourseids, $course->courseid);
        }

        $slots = [];
        foreach ($allslots as $slot) {
            $filters = self::get_filters_for_slot($slot->id);
            foreach ($filters as $filter) {
                // Checking for course ID.
                if (!is_null($filter->courseid) && !in_array($filter->courseid, $mycourseids)) {
                    continue;
                }
                // TODO: replace address with cohorts.
                // Checking for vintage.
                if (!is_null($filter->vintage) && $user->address !== $filter->vintage) {
                    continue;
                }
                // If all filters passed, add slot to my slots and break.
                array_push($slots, $slot);
                break;
            }
        }
        return $slots;
    }

    /**
     * Filters an array of slots for a timerange around now.
     * @param slot[] $allslots the slots to filter
     * @param int $range how many days in the future the slot is allowed to be
     * @return slot[] the filtered slot array
     */
    public static function filter_slots_for_time(array $allslots, int $range): array {
        $now = new DateTimeImmutable();
        $slots = [];
        // Calculate date and time each slot happens next, and add it to the return list if within reach from today.
        foreach ($allslots as $slot) {
            $slotdatetime = self::calculate_slot_datetime($slot, $now);

            if ($now->diff($slotdatetime)->days <= $range) {
                array_push($slots, $slot->prepare_for_api());
            }
        }
        return $slots;
    }

    /**
     * calculates when a slot is to happen next
     * @param slot $slot the slot
     * @param DateTimeInterface $now the point in time representing now
     * @return DateTimeImmutable the next time this slot will occur
     */
    public static function calculate_slot_datetime(slot $slot, DateTimeInterface $now): DateTimeImmutable {
        $slotdaytime = self::SCHOOL_UNITS[$slot->startunit];
        // NOTE: format and fromFormat use different date formatting conventions
        $slotdatetime = DateTime::createFromFormat('YY-MM-DD tHH:MM', $now->format('Y-m-d ').$slotdaytime);
        // Move to next day this weekday occurs (doesn't move if it's the same as today).
        $slotdatetime->modify('this '.WEEKDAY::name_from($slot->weekday));

        // Check if slot is before now (because time of day and such) and move it a week into the future if so.
        if ($now->diff($slotdatetime)->invert === 1) {
            $slotdatetime->add(new DateInterval('P1W'));
        }

        return new DateTimeImmutable($slotdatetime);
    }

    /**
     * Returns a list of all slots belonging to a supervisor.
     * @param int $supervisorid userid of the supervisor in question
     *
     * @return slot[] An array of the slots.
     */
    public static function check_slot_supervisor(int $supervisorid, int $slotid): bool {
        global $DB;

        $result = $DB->get_record_sql(
            'SELECT supervisor.userid FROM '.self::TABLE_SUPERVISORS.' as supervisor'.
            'WHERE supervisor.userid=? AND supervisor.slotid=?',
            [$supervisorid, $slotid]
        );

        return $result !== false;
    }
}
