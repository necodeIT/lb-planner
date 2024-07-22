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
 * Collection of helper classes for handling modules
 *
 * @package local_lbplanner
 * @subpackage helpers
 * @copyright 2024 NecodeIT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_lbplanner\model;

class activity {
    public $moduleid;
    public $name;
    public $courseid;
    public $status;
    public $type;
    public $url;
    public $grade;
    public $deadline;
    public $modtype;
    public function __construct($moduleid, $name, $courseid, $status, $modtype, $type, $url, $grade, $deadline) {
        $this->modtype = $modtype;
        $this->moduleid = $moduleid;
        $this->name = $name;
        $this->courseid = $courseid;
        $this->status = $status;
        $this->type = $type;
        $this->url = $url;
        $this->grade = $grade;
        $this->deadline = $deadline;
    }
}

