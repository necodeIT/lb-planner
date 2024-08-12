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
 * Model for a reservation
 *
 * @package local_lbplanner
 * @subpackage helpers
 * @copyright 2024 NecodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_lbplanner\model;

use DateTimeImmutable;

use external_single_structure;
use external_value;

use local_lbplanner\model\slot;
use local_lbplanner\helpers\slot_helper;

/**
 * Model class for reservation
 */
class reservation {
    /**
     * @var int $id ID of reservation
     */
    public int $id;
    /**
     * @var int $slotid ID of the linked slot
     */
    public int $slotid;
    /**
     * @var DateTimeImmutable $date date this reservation is on (time will be ignored)
     */
    public DateTimeImmutable $date;
    /**
     * @var int $userid ID of the user this reservation is for
     */
    public int $userid;
    /**
     * @var int $reserverid ID of the user who submitted this reservation (either pupil or supervisor)
     */
    public int $reserverid;
    /**
     * @var ?slot $slot the linked slot (gets filled in by helper functions)
     */
    private ?slot $slot;
    /**
     * @var ?DateTimeImmutable $datetime the date this reservation is for, with time filled in
     */
    private ?DateTimeImmutable $datetime;

    /**
     * Constructs a reservation
     * @param int $id ID of reservation
     * @param int $slotid ID of the linked slot
     * @param DateTimeImmutable $date date this reservation is on (time will be ignored)
     * @param int $userid ID of the user this reservation is for
     * @param int $reserverid ID of the user who submitted this reservation (either pupil or supervisor)
     * @link slot
     */
    public function __construct(int $id, int $slotid, DateTimeImmutable $date, int $userid, int $reserverid) {
        $this->id = $id;
        $this->slotid = $slotid;
        $this->date = $date;
        $this->userid = $userid;
        $this->reserverid = $reserverid;
        $this->slot = null;
    }

    /**
     * Returns the associated slot.
     *
     * @return slot the associated slot
     */
    public function get_slot(): slot {
        if (is_null($this->slot)) {
            $this->slot = slot_helper::get_slot($this->slotid);
        }

        return $this->slot;
    }

    /**
     * Prepares data for the DB endpoint.
     *
     * @return object a representation of this reservation and its data
     */
    public function prepare_for_db(): object {
        $obj = new \stdClass();

        $obj->slotid = $this->slotid;
        $obj->date = $this->date;
        $obj->userid = $this->userid;
        $obj->reserverid = $this->reserverid;

        return $obj;
    }

    /**
     * Prepares data for the API endpoint.
     *
     * @return array a representation of this reservation and its data
     */
    public function prepare_for_api(): array {
        return [
            'id' => $this->id,
            'slotid' => $this->slotid,
            'datetime' => $this->date->format('Y-m-d'),
            'userid' => $this->userid,
            'reserverid' => $this->reserverid,
        ];
    }

    /**
     * Returns the data structure of a reservation for the API.
     *
     * @return external_single_structure The data structure of a reservation for the API.
     */
    public static function api_structure(): external_single_structure {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'reservation ID'),
                'slotid' => new external_value(PARAM_INT, 'ID of associated slot'),
                'date' => new external_value(PARAM_TEXT, 'date of the reservation in YYYY-MM-DD (as per ISO-8601)'),
                'userid' => new external_value(PARAM_INT, 'ID of the user this reservation is for'),
                'reserverid' => new external_value(PARAM_INT, 'ID of the user who submitted this reservation'),
            ]
        );
    }
}
