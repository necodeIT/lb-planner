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
 * polyfill for php8 enums
 *
 * @package local_lbplanner
 * @subpackage polyfill
 * @copyright 2024 NecodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_lbplanner\polyfill;

use ReflectionClass;
use ValueError;
use lb_planner_local\polyfill\EnumCase;

/**
 * Class which is meant to serve as a substitute for native enums.
 */
class Enum {
    /**
     * tries to match the passed value to one of the enum values
     * @param mixed $value the value to be matched
     * @param bool $try whether to return null (true) or throw an error (false) if not found
     * @return ?EnumCase the matching enum case or null if not found and $try==true
     * @throws ValueError if not found and $try==false
     */
    private static function find(mixed $value, bool $try): ?EnumCase {
        foreach (static::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        if ($try) {
            return null;
        } else {
            throw new ValueError("value {$value} cannot be represented as a value in enum ".static::class);
        }
    }
    /**
     * tries to match the passed value to one of the enum values
     * @param mixed $value the value to be matched
     * @return mixed either the matching enum value or null if not found
     */
    public static function try_from(mixed $value): mixed {
        // TODO: replace with nullsafe operator in php8.
        $case = static::find($value, true);
        if (is_null($case)) {
            return null;
        } else {
            return $case->value;
        }
    }
    /**
     * tries to match the passed value to one of the enum values
     * @param mixed $value the value to be matched
     * @return mixed the matching enum value
     * @throws ValueError if not found
     */
    public static function from(mixed $value): mixed {
        return static::find($value, false)->value;
    }
    /**
     * tries to match the passed value to one of the enum values
     * @param mixed $value the value to be matched
     * @return string the matching enum case name
     * @throws mixed either the matching enum case name or null if not found
     */
    public static function try_name_from(mixed $value): ?string {
        // TODO: replace with nullsafe operator in php8.
        $case = static::find($value, true);
        if (is_null($case)) {
            return null;
        } else {
            return $case->name;
        }
    }
    /**
     * tries to match the passed value to one of the enum values
     * @param mixed $value the value to be matched
     * @return string the matching enum case name
     * @throws ValueError if not found
     */
    public static function name_from(mixed $value): string {
        return static::find($value, false)->name;
    }
    /**
     * Returns an array of all the cases that exist in this enum
     *
     * @return EnumCase[] array of cases inside this enum
     */
    public static function cases(): array {
        $reflection = new ReflectionClass(static::class);
        $cases = [];
        foreach ($reflection->getConstants() as $name => $val) {
            array_push($cases, new EnumCase($name, $val));
        }
        return $cases;
    }
    /**
     * Formats all possible enum values into a string
     * Example:
     * [31=>RED,32=>GREEN,33=>YELLOW]
     * @return string the resulting string
     */
    public static function format(): string {
        $result = "[";
        foreach (static::cases() as $case) {
            $result .= "{$case->value}=>{$case->name},";
        }
        $result[-1] = ']';
        return $result;
    }
}
