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
 * A issue view
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// This page prints a particular instance of tasks
require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once('classes/issue.php');

$id = required_param('id', PARAM_INT); // Issue ID.
$msgkey = optional_param('msg', '', PARAM_TEXT);

$issue = $DB->get_record('tasks_issues', array('id' => $id), '*', MUST_EXIST);

if (!$tasks = $DB->get_record("tasks", array("id"=>$issue->tasksid))) {
    print_error('invalidid', 'tasks');
}
if (!$course = $DB->get_record("course", array("id"=>$tasks->course))) {
    print_error('invalidcourseid');
}
if (!$cm = get_coursemodule_from_instance("tasks", $tasks->id, $course->id)) {
    print_error('invalidcoursemodule');
}

require_course_login($course->id, true, $cm);
$context = context_module::instance($cm->id);

$assignform = null;
$commentform = null;
$stateform = null;
$pagemessages = array();

$current = new \mod_tasks\issue($issue, $tasks, $cm, $course);

// Define current operations.

// Assign to a user.
if ($issue->state == TASKS_STATE_OPEN || $issue->state == TASKS_STATE_ASSIGNED) {
    if (has_capability('mod/tasks:manageall', $context) ||
            ($issue->reportedby == $USER->id && has_capability('mod/tasks:manage', $context))) {
        $data = new stdClass();
        $data->context = $context;
        $data->id = $id;
        $data->assignedto = $issue->assignedto;

        require_once ('classes/assign.php');
        $assignform = new \mod_tasks\assign_form('persist.php', array('data' => $data));
    }
}

// Assign supervisor user.
if ($issue->state != TASKS_STATE_CLOSED && $issue->state != TASKS_STATE_CANCELED) {
    if (has_capability('mod/tasks:manageall', $context)) {
        $data = new stdClass();
        $data->context = $context;
        $data->id = $id;
        $data->supervisor = $issue->supervisor;

        require_once ('classes/supervisor.php');
        $supervisorform = new \mod_tasks\supervisor_form('persist.php', array('data' => $data));
    }
}

// Comment form.
if ($issue->state != TASKS_STATE_CLOSED && $issue->state != TASKS_STATE_CANCELED) {
    if (has_capability('mod/tasks:manageall', $context) ||
            $issue->assignedto == $USER->id ||
            $issue->reportedby == $USER->id) {
        $data = new stdClass();
        $data->id = $id;

        require_once ('classes/comment.php');
        $commentform = new \mod_tasks\comment_form('persist.php', array('data' => $data));
    }
}

// Change state form.
if ($issue->state != TASKS_STATE_CLOSED && $issue->state != TASKS_STATE_CANCELED) {
    if (has_capability('mod/tasks:manageall', $context) ||
            ($issue->assignedto == $USER->id && $issue->state == TASKS_STATE_ASSIGNED) ||
            $issue->reportedby == $USER->id ||
            ($issue->supervisor == $USER->id && $issue->state == TASKS_STATE_RESOLVED)) {

        $data = new stdClass();
        $data->context = $context;
        $data->id = $id;
        $data->issue = $issue;

        require_once ('classes/state.php');
        $stateform = new \mod_tasks\state_form('persist.php', array('data' => $data));
    }
}

// Initialize $PAGE, compute blocks.
$PAGE->set_url('/mod/tasks/detail.php', array('id' => $id));

echo $OUTPUT->header();

$currenttab = '';
include 'tabs.php';

if (!empty($msgkey)) {
    echo $OUTPUT->notification(get_string($msgkey, 'mod_tasks'), 'notifysuccess');
}

if (has_capability('mod/tasks:viewall', $context) || $issue->reportedby == $USER->id ||
        $issue->assignedto == $USER->id || $issue->supervisor == $USER->id) {

    $current->printdetails();

    echo $OUTPUT->container_start('buttons');

    if (($issue->state == TASKS_STATE_OPEN && $issue->reportedby == $USER->id) ||
            has_capability('mod/tasks:manageall', $context)) {
        echo $OUTPUT->single_button(new moodle_url($CFG->wwwroot . '/mod/tasks/edit.php',
                                        array('id' => $id)), get_string('edit'), 'get');
    }

    echo $OUTPUT->container_end();

    echo $OUTPUT->heading(get_string('actions', 'mod_tasks'), 3);

    $current->printtimeline();

    echo $OUTPUT->box_start('actions');

    if ($commentform) {
        echo $OUTPUT->box_start('one-action');
        $commentform->display();
        echo $OUTPUT->box_end();
    }

    if ($assignform) {
        echo $OUTPUT->box_start('one-action');
        $assignform->display();
        echo $OUTPUT->box_end();
    }

    if ($assignform) {
        echo $OUTPUT->box_start('one-action');
        $supervisorform->display();
        echo $OUTPUT->box_end();
    }

    if ($stateform) {
        echo $OUTPUT->box_start('one-action');
        $stateform->display();
        echo $OUTPUT->box_end();
    }

    echo $OUTPUT->box_end();


} else {
    echo get_string('notdetailcapability', 'mod_tasks');
}


/// Finish the page
echo $OUTPUT->footer();
