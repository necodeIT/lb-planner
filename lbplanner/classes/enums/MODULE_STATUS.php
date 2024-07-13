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
 * enum for module status
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
 * Stati a module can be in
 */
class MODULE_STATUS extends Enum {
    /**
     * Finished module.
     */
    const DONE = 0;
    /**
     * Uploaded module.
     */
    const UPLOADED = 1;
    /**
     * Overdue module.
     */
    const LATE = 2;
    /**
     * Todo module.
     */
    const PENDING = 3;
}
