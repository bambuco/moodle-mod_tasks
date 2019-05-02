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
 * Class to manage the comment operation
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_tasks;

require_once($CFG->dirroot . '/mod/tasks/locallib.php');

class comment_form extends \moodleform {
    protected $_data;

    /**
     * Form definition.
     */
    function definition() {
        global $CFG, $PAGE, $DB;

        $mform = $this->_form;

        // this contains the data of this form
        $this->_data = $this->_customdata['data'];
        $this->_data->action = 'comment';

        $mform->addElement('textarea', 'comment', get_string('comment', 'mod_tasks'), array('cols' => 50, 'rows' => 5));
        $mform->setType('comment', PARAM_TEXT);
        $mform->addRule('comment', get_string('missingvalue', 'mod_tasks'), 'required', null, 'client');

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action', null);
        $mform->setType('action', PARAM_TEXT);

        $this->add_action_buttons(false, get_string('add', 'mod_tasks'));

        // Finally set the current form data
        $this->set_data($this->_data);
    }

}

