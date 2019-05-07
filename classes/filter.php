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

require_once($CFG->dirroot . '/mod/tasks/classes/tools.php');
require_once($CFG->libdir . '/formslib.php');

/*
class filter_form {
    protected $_data;

    /**
     * Form construct.
     *
    public function __construct($data) {
        global $USER;

        // this contains the data of this form
        $this->_data = $data;
    }

    public function display() {
        $states = util\tools::get_states();

        $html = \html_writer::start_tag('form');
        $html .= \html_writer::tag('span', get_string('state', 'mod_tasks'), array('class'=>'subtitle'));

        foreach ($states as $key => $state) {
            $html .= \html_writer::start_tag('div', array('class' => 'one-element'));
            $html .= \html_writer::empty_tag('input', array('value' => $key, 'type' => 'checkbox'));
            $html .= \html_writer::tag('label', $state, array('class'=>'label label-primary'));
            $html .= \html_writer::end_tag('div');
        }

        $html .= \html_writer::empty_tag('input', array('value' => get_string('filter')));
        $html .= \html_writer::end_tag('form');

        return $html;
    }

}

*/


class filter_form extends \moodleform {
    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        // Text search box.
        $mform->addElement('text', 'search', get_string('issuecode', 'mod_tasks'));
        $mform->setType('search', PARAM_RAW);

        $states = util\tools::get_states();
        foreach ($states as $key => $state) {
            $mform->addElement('checkbox', "state[" . $key . "]", $state);
        }

        // Submit button does not use add_action_buttons because that adds
        // another fieldset which causes the CSS style to break in an unfixable
        // way due to fieldset quirks.
        $group = array();
        $group[] = $mform->createElement('submit', 'submitbutton', get_string('filter'));
        $group[] = $mform->createElement('submit', 'resetbutton', get_string('reset'));
        $mform->addGroup($group, 'buttons', '', ' ', false);

        // Add hidden fields required by page.
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_INT);
    }
}
