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
 * Class to manage the change state operation
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_tasks;

require_once($CFG->libdir . '/formslib.php');

class state_form extends \moodleform {
    protected $_data;

    /**
     * Form definition.
     */
    function definition() {
        global $USER;

        $mform = $this->_form;

        // this contains the data of this form
        $this->_data = $this->_customdata['data'];
        $issue = $this->_data->issue;
        $this->_data->action = 'state';

        $options = array();
        $states = array();

        if (has_capability('mod/tasks:manageall', $this->_data->context)) {
            $states = array(TASKS_STATE_RESOLVED, TASKS_STATE_CLOSED, TASKS_STATE_CANCELED);
        } else if ($issue->assignedto == $USER->id && $issue->state == TASKS_STATE_ASSIGNED) {
            $states = array(TASKS_STATE_RESOLVED);
        } else if ($issue->supervisor == $USER->id && $issue->state == TASKS_STATE_RESOLVED) {
            $states = array(TASKS_STATE_CLOSED);
        } else if ($issue->reportedby == $USER->id) {
            $states = array(TASKS_STATE_CANCELED);
        }

        foreach ($states as $state) {
            $options[$state] = get_string('state_' . $state, 'mod_tasks');
        }

        $mform->addElement('select', 'state', get_string('state', 'mod_tasks'), $options);

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action', null);
        $mform->setType('action', PARAM_TEXT);

        $this->add_action_buttons(false, get_string('change', 'mod_tasks'));

        // Finally set the current form data
        $this->set_data($this->_data);
    }

}

