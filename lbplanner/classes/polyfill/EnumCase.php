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
 * case for enums
 *
 * @package local_lbplanner
 * @subpackage polyfill
 * @copyright 2024 NecodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lb_planner_local\polyfill;

defined('MOODLE_INTERNAL') || die();

// TODO: revert to native enums once we migrate to php8.

/**
 * This represents a single case within an Enum
 */
class EnumCase {
    /** @var string the name of the case */
    public string $name;
    /** @var string the value of the case */
    public mixed $value;
    /**
     * Constructs an EnumCase
     *
     * @param string $name the name of the case
     * @param mixed $value the value of the case
     */
    public function __construct(string $name, mixed $value) {
        $this->name = $name;
        $this->value = $value;
    }
};
