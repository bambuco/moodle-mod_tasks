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
 * Class to manage new issues and edit exist issues
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_tasks;

require_once($CFG->dirroot . '/mod/tasks/locallib.php');
require_once($CFG->libdir . '/formslib.php');

class edit_form extends \moodleform {
    protected $_data;

    /**
     * Form definition.
     */
    function definition() {
        global $CFG, $PAGE, $DB;

        $mform = $this->_form;

        // This contains the data of this form.
        $this->_data = $this->_customdata['data'];
        $this->anonymous = $this->_customdata['anonymous'];

        if (isset($this->_data->id)) {
            $this->_data->description = array('text'=>$this->_data->description, 'format'=>$this->_data->descriptionformat);
        } else {
            $this->_data->state = TASKS_STATE_OPEN;
        }

//        $dateattributes = array('stopyear'=>date('Y', time()) + 15, 'startyear'=>date('Y', time()) - 5);
        $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);

        if ($this->anonymous) {
            $mform->addElement('text', 'namereportedby', get_string('namereportedby', 'mod_tasks'), 'maxlength="255" size="30"');
            $mform->addRule('namereportedby', get_string('missingvalue', 'mod_tasks'), 'required', null, 'client');
            $mform->setType('namereportedby', PARAM_TEXT);

            $mform->addElement('text', 'emailreportedby', get_string('emailreportedby', 'mod_tasks'), 'maxlength="255" size="30"');
            $mform->addRule('emailreportedby', get_string('missingvalue', 'mod_tasks'), 'required', null, 'client');
            $mform->setType('emailreportedby', PARAM_TEXT);
        }

        $mform->addElement('text', 'name', get_string('issuetitle', 'mod_tasks'), 'maxlength="255" size="30"');
        $mform->addRule('name', get_string('missingvalue', 'mod_tasks'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('editor', 'description', get_string('description'), null, $editoroptions);

        if ($this->anonymous && !empty($CFG->recaptchapublickey)) {
            $mform->addElement('recaptcha', 'recaptcha_element', get_string('security_question', 'mod_tasks'));
        }

        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'tasksid', null);
        $mform->setType('tasksid', PARAM_INT);

        $this->add_action_buttons(!$this->anonymous, get_string('send', 'mod_tasks'));

        // Finally set the current form data
        $this->set_data($this->_data);
    }

    function definition_after_data(){
        $mform = $this->_form;
        $mform->applyFilter('namereportedby', 'trim');
        $mform->applyFilter('emailreportedby', 'trim');
        $mform->applyFilter('name', 'trim');
    }

    function validation($data, $files) {
        global $CFG;

        $errors = parent::validation($data, $files);

        if ($this->anonymous && !empty($CFG->recaptchapublickey)) {
                        $recaptchaelement = $this->_form->getElement('recaptcha_element');
            if (!empty($this->_form->_submitValues['g-recaptcha-response'])) {
                $response = $this->_form->_submitValues['g-recaptcha-response'];
                if (!$recaptchaelement->verify($response)) {
                    $errors['recaptcha_element'] = get_string('incorrectpleasetryagain', 'mod_tasks');
                }
            } else {
                $errors['recaptcha_element'] = get_string('missingrecaptchachallengefield');
            }
        }

        return $errors;
    }
}
