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
 * Page configuration form
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_tasks_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $this->standard_intro_elements();

        //-------------------------------------------------------
        $mform->addElement('header', 'controlhdr', get_string('controlhdr', 'mod_tasks'));

        $notificationstypeopts = array(
            TASKS_NOTITYPE_NONE => get_string('notify_none', 'mod_tasks'),
            TASKS_NOTITYPE_BEFORE => get_string('notify_before', 'mod_tasks'),
            TASKS_NOTITYPE_DAILY => get_string('notify_daily', 'mod_tasks'),
        );
        $mform->addElement('select', 'notificationstype', get_string('notificationstype', 'mod_tasks'), $notificationstypeopts);
        $mform->setType('notificationstype', PARAM_INT);

        // Number of days for notifications.
        $daysrangeopts = array();
        for ($i = 0; $i <= 30; $i++) {
            $daysrangeopts[$i] = $i;
        }
        $mform->addElement('select', 'notificationsdays', get_string('notificationsdays', 'mod_tasks'), $daysrangeopts);
        $mform->setDefault('notificationsdays', 3);

        $mform->addElement('text', 'taskprefix', get_string('taskprefix', 'mod_tasks'), array('size' => 5));
        $mform->setType('taskprefix', PARAM_TEXT);

        $options=array();
        $options[TASKS_ANONYMOUS]  = get_string('anonymous', 'mod_tasks');
        $options[TASKS_NONANONYMOUS]  = get_string('non_anonymous', 'mod_tasks');
        $mform->addElement('select',
                           'anonymous',
                           get_string('anonymous_report', 'mod_tasks'),
                           $options);
        $mform->setDefault('anonymous', 2);

        $options=array();
        $options[TASKS_MODE_ISSUES]  = get_string('mode_issues', 'mod_tasks');
        $options[TASKS_MODE_WORK]  = get_string('mode_work', 'mod_tasks');
        $mform->addElement('select',
                           'mode',
                           get_string('mode', 'mod_tasks'),
                           $options);

        $mform->addElement('text', 'singularlabel', get_string('singularlabel', 'mod_tasks'), array('size' => 15, 'maxlength' => 15));
        $mform->setType('singularlabel', PARAM_TEXT);

        $mform->addElement('text', 'plurallabel', get_string('plurallabel', 'mod_tasks'), array('size' => 15, 'maxlength' => 15));
        $mform->setType('plurallabel', PARAM_TEXT);

        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();
    }

}

