<?php
// This file is part of Moodle - http://moodle.org/
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
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_tasks\util;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/tasks/lib.php');


class tools {

    public static function get_states() {
        return array(TASKS_STATE_OPEN => get_string('state_' . TASKS_STATE_OPEN, 'mod_tasks'),
                     TASKS_STATE_ASSIGNED => get_string('state_' . TASKS_STATE_ASSIGNED, 'mod_tasks'),
                     TASKS_STATE_RESOLVED => get_string('state_' . TASKS_STATE_RESOLVED, 'mod_tasks'),
                     TASKS_STATE_CLOSED => get_string('state_' . TASKS_STATE_CLOSED, 'mod_tasks'),
                     TASKS_STATE_CANCELED => get_string('state_' . TASKS_STATE_CANCELED, 'mod_tasks'));
    }

    public static function is_state($state) {
        switch ($state) {
            case TASKS_STATE_OPEN:
            case TASKS_STATE_ASSIGNED:
            case TASKS_STATE_RESOLVED:
            case TASKS_STATE_CLOSED:
            case TASKS_STATE_CANCELED:
                return true;
        }

        return false;
    }
}

