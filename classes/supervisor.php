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
 * Class to manage the supervisor assign operation
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_tasks;

require_once($CFG->libdir . '/formslib.php');

class supervisor_form extends \moodleform {
    protected $_data;

    /**
     * Form definition.
     */
    function definition() {
        global $CFG, $PAGE, $DB;

        $mform = $this->_form;

        // this contains the data of this form
        $this->_data = $this->_customdata['data'];
        $this->_data->action = 'supervisor';

        $users = get_enrolled_users($this->_data->context);

        $options = array();
        foreach($users as $user) {
            $options[$user->id] = fullname($user) . ' (' . $user->username . ')';
        }

        $mform->addElement('select', 'supervisor', get_string('supervisor', 'mod_tasks'), $options);

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action', null);
        $mform->setType('action', PARAM_TEXT);

        $this->add_action_buttons(false, get_string('assign', 'mod_tasks'));

        // Finally set the current form data
        $this->set_data($this->_data);
    }

}

