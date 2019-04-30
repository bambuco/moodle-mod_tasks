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
 * Tasks view
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// This page prints a particular instance of tasks
require_once('../../config.php');
require_once('lib.php');
require_once('locallib.php');

$tasksid = required_param('tasksid', PARAM_INT);

if (!empty($tasksid)) {
    if (!$tasks = $DB->get_record("tasks", array("id" => $tasksid))) {
        print_error('invalidid', 'tasks');
    }
    if (!$course = $DB->get_record("course", array("id" => $tasks->course))) {
        print_error('invalidcourseid');
    }
    if (!$cm = get_coursemodule_from_instance("tasks", $tasks->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
} else {
    print_error('invalidid', 'tasks');
}

if ($tasks->anonymous != TASKS_ANONYMOUS) {
    print_error('onlyanonymousreports', 'tasks');
}

$PAGE->set_context(context_system::instance());

// Initialize $PAGE, compute blocks.
$urlparams = array('tasksid' => $tasksid);
$PAGE->set_url('/mod/tasks/report.php', $urlparams);

echo $OUTPUT->header();

echo $OUTPUT->heading($tasks->name);

echo $OUTPUT->box_start();
echo format_text($tasks->intro, $tasks->introformat);
echo $OUTPUT->box_end();

require_once ('classes/edit.php');

// First create the form.
$data = new stdClass();
$data->tasksid = $tasksid;
$editform = new \mod_tasks\edit_form(NULL, array('data' => $data, 'anonymous' => true));

if ($data = $editform->get_data()) {

    $issue = new stdClass();
    $issue->state = TASKS_STATE_OPEN;
    $issue->reportedby = 0;
    $issue->namereportedby = $data->namereportedby;
    $issue->emailreportedby = $data->emailreportedby;
    $issue->tasksid = $tasks->id;
    $issue->timereported = time();
    $issue->name = $data->name;

    if (is_array($data->description)) {
        $issue->description = $data->description['text'];
        $issue->descriptionformat = $data->description['format'];
    }

    $id = $DB->insert_record('tasks_issues', $issue, true);

    echo $OUTPUT->notification(get_string('anonymousreportmsg', 'mod_tasks'), 'notifysuccess');

    $url = new moodle_url($CFG->wwwroot.'/mod/tasks/report.php', $urlparams);

    echo html_writer::tag('a', get_string('reportagain', 'mod_tasks'), array('href' => $url));


} else {
    $editform->display();
}

/// Finish the page
echo $OUTPUT->footer();
