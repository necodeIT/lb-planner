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
 * enum for weekdays
 * (cringe, ik, but we need these defined concretely)
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
 * All the days of the week.
 * All seven of them.
 * Yup.
 */
class WEEKDAY extends Enum {
    /**
     * monday
     */
    const MONDAY = 1;
    /**
     * tuesday
     */
    const TUESDAY = 2;
    /**
     * wednesday
     */
    const WEDNESDAY = 3;
    /**
     * thursday
     */
    const THURSDAY = 4;
    /**
     * friday
     */
    const FRIDAY = 5;
    /**
     * saturday
     */
    const SATURDAY = 6;
    /**
     * sunday
     */
    const SUNDAY = 7;
}
