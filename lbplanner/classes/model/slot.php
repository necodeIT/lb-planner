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
 * Model for a slot
 *
 * @package local_lbplanner
 * @subpackage helpers
 * @copyright 2024 NecodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_lbplanner\model;

use local_lbplanner\enums\WEEKDAY;
use local_lbplanner\helpers\slot_helper;

use external_single_structure;
use external_value;

/**
 * Model class for slot
 */
class slot {
    /**
     * @var int $id ID of slot
     */
    public int $id;
    /**
     * @var int $startunit Unit this slot starts in
     */
    public int $startunit;
    /**
     * @var int $duration duration of slot in units
     */
    public int $duration;
    /**
     * @var int $weekday weekday this slot occurs in
     */
    public int $weekday;
    /**
     * @var string $room room this slot is for
     */
    public string $room;
    /**
     * @var int $size how many pupils fit in this slot
     */
    public int $size;
    /**
     * @var ?int $fullness how many pupils have already reserved this slot (gets filled in by helper functions)
     */
    private ?int $fullness;
    /**
     * @var ?bool $forcuruser whether the current user has reserved this slot (gets filled in by helper functions)
     */
    private ?bool $forcuruser;

    /**
     * Constructs a new Slot
     * @param int $id ID of slot
     * @param int $startunit Unit this slot starts in
     * @param int $duration duration of slot in units
     * @param int $weekday weekday this slot occurs in
     * @param string $room room this slot is for
     * @param int $size how many pupils fit in this slot
     * @link slot_helper::SCHOOL_UNITS
     * @link WEEKDAY
     */
    public function __construct(int $id, int $startunit, int $duration, int $weekday, string $room, int $size) {
        $this->id = $id;
        assert($startunit > 0);
        $this->startunit = $startunit;
        assert($duration > 0);
        $this->duration = $duration;
        $this->weekday = WEEKDAY::from($weekday);
        assert(strlen($room) > 0 && strlen($room) <= 7);
        $this->room = $room;
        assert($size >= 0);  // Make it technically possible to not allow any students in a room to temporarily disable the slot.
        $this->size = $size;
        $this->fullness = null;
        $this->forcuruser = null;
    }

    /**
     * Returns how many reservations there are for this slot.
     *
     * @return int fullness
     */
    public function get_fullness(): int {
        if (is_null($this->fullness)) {
            $this->check_reservations();
        }

        return $this->fullness;
    }

    /**
     * Returns whether the current user has a reservation for this slot.
     *
     * @return bool forcuruser
     */
    public function get_forcuruser(): bool {
        if (is_null($this->forcuruser)) {
            $this->check_reservations();
        }

        return $this->forcuruser;
    }

    /**
     * Prepares data for the API endpoint.
     *
     * @return array a representation of this slot and its data
     */
    public function prepare_for_api(): array {
        return [
            'id' => $this->id,
            'startunit' => $this->startunit,
            'duration' => $this->duration,
            'weekday' => $this->weekday,
            'room' => $this->room,
            'size' => $this->size,
            'fullness' => $this->get_fullness(),
            'forcuruser' => $this->get_forcuruser(),
        ];
    }

    /**
     * Returns the data structure of a slot for the API.
     *
     * @return external_single_structure The data structure of a slot for the API.
     */
    public static function api_structure(): external_single_structure {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'slot ID'),
                'startunit' => new external_value(PARAM_INT, 'unit this slot starts in (8:00 is unit 1)'),
                'duration' => new external_value(PARAM_INT, 'duration of the slot in units'),
                'weekday' => new external_value(PARAM_INT, 'The day this unit repeats weekly: '.WEEKDAY::format()),
                'room' => new external_value(PARAM_TEXT, 'The room this slot is for'),
                'size' => new external_value(PARAM_INT, 'total capacity of the slot'),
                'fullness' => new external_value(PARAM_INT, 'how many people have already reserved this slot'),
                'forcuruser' => new external_value(PARAM_BOOL, 'whether the current user has reserved this slot'),
            ]
        );
    }

    /**
     * Queries reservations for this slot and fills in internal data with that info.
     */
    private function check_reservations(): void {
        global $USER;
        $reservations = slot_helper::get_reservations_for_slot($this->id);

        $this->fullness = count($reservations);

        foreach ($reservations as $reservation) {
            if ($reservation->userid === $USER['id']) {
                $this->forcuruser = true;
                return;
            }
        }
        $this->forcuruser = false;
    }
}
