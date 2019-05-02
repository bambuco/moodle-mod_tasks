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
require_once('classes/issue.php');

$id = required_param('id', PARAM_INT); // Issue ID.

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
$supervisorform = null;
$msgkey = '';

$current = new \mod_tasks\issue($issue, $tasks, $cm, $course);

// Initialize $PAGE, compute blocks.
$PAGE->set_url('/mod/tasks/detail.php', array('id' => $id));

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

if ($assignform && $data = $assignform->get_data()) {

    $log = new stdClass();
    $log->old = $issue->assignedto;
    $log->change = $data->assignedto;

    $logstate = null;
    if ($issue->state != TASKS_STATE_ASSIGNED) {
        $logstate = new stdClass();
        $logstate->old = $issue->state;
        $logstate->change = TASKS_STATE_ASSIGNED;

        $issue->state = TASKS_STATE_ASSIGNED;
    }

    $assigned = $issue->assignedto != $data->assignedto;
    $issue->assignedto = $data->assignedto;

    if (!$issue->timestart) {
        $issue->timestart = time();
    }

    $DB->update_record('tasks_issues', $issue);

    // A specific tasks transaction log.
    if ($logstate) {
        $current->log(TASKS_LOG_STATE, json_encode($logstate));
    }

    $current->log(TASKS_LOG_ASSIGN, json_encode($log));

    require_once 'classes/event/issue_updated.php';
    $event = \mod_tasks\event\issue_updated::create(array(
        'objectid' => $issue->id,
        'context' => $PAGE->context,
        'other' => array('tasksid' => $tasks->id)
    ));
    $event->trigger();

    if ($assigned) {
        $current->sendmessage(TASKS_MSG_ASSIGNED);
    }

    $msgkey = 'assignedmsg';
} else if ($supervisorform && $data = $supervisorform->get_data()) {

    $log = new stdClass();
    $log->old = $issue->supervisor;
    $log->change = $data->supervisor;

    $supervised = $issue->supervisor != $data->supervisor;
    $issue->supervisor = $data->supervisor;

    $DB->update_record('tasks_issues', $issue);

    // A specific tasks transaction log.
    $current->log(TASKS_LOG_SUPERVISED, json_encode($log));

    require_once 'classes/event/issue_updated.php';
    $event = \mod_tasks\event\issue_updated::create(array(
        'objectid' => $issue->id,
        'context' => $PAGE->context,
        'other' => array('tasksid' => $tasks->id)
    ));
    $event->trigger();

    if ($supervised) {
        $current->sendmessage(TASKS_MSG_SUPERVISED);
    }

    $msgkey = 'supervisedmsg';
} else if ($stateform && $data = $stateform->get_data()) {

    $log = new stdClass();
    $log->old = $issue->state;
    $log->change = $data->state;

    $statechange = $issue->state != $data->state;
    $issue->state = $data->state;

    if (!$issue->timefinish &&
            ($data->state == TASKS_STATE_CLOSED || $data->state == TASKS_STATE_CANCELED)) {

        $issue->timefinish = time();
    }

    $DB->update_record('tasks_issues', $issue);

    // A specific tasks transaction log.
    $current->log(TASKS_LOG_STATE, json_encode($log));

    require_once 'classes/event/issue_updated.php';
    $event = \mod_tasks\event\issue_updated::create(array(
        'objectid' => $issue->id,
        'context' => $PAGE->context,
        'other' => array('tasksid' => $tasks->id)
    ));
    $event->trigger();

    if ($statechange) {
        switch ($issue->state) {
            case TASKS_STATE_RESOLVED:
                $current->sendmessage(TASKS_MSG_RESOLVED);
            break;
            case TASKS_STATE_CLOSED:
                $current->sendmessage(TASKS_MSG_CLOSED);
            break;
            case TASKS_STATE_CANCELED:
                $current->sendmessage(TASKS_MSG_CANCELED);
            break;
        }
    }

    $msgkey = 'statechangedmsg';
} else if ($commentform && $data = $commentform->get_data()) {

    $log = new stdClass();
    $log->comment = $data->comment;
    $current->log(TASKS_LOG_COMMENT, json_encode($log));

    $current->sendmessage(TASKS_MSG_EDITED);

    $msgkey = 'commentedmsg';
}


// Redirect to the course main page.
$url = new moodle_url('/mod/tasks/detail.php', array('id' => $id, 'msg' => $msgkey));
redirect($url);
