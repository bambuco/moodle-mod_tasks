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
require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir . '/completionlib.php');

$tasksid = optional_param('tasksid', 0, PARAM_INT);
$id = optional_param('id', 0, PARAM_INT); // Issue ID.

$issue = null;
if (!empty($id)) {
    $issue = $DB->get_record('tasks_issues', array('id' => $id), '*', MUST_EXIST);
    $tasksid = $issue->tasksid;
}

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

if (!$issue) {
    $issue = new stdClass();
    $issue->tasksid = $tasksid;
}

require_course_login($course->id, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/tasks:report', $context);

// Initialize $PAGE, compute blocks.
$params = array('tasksid' => $tasksid);

// It is the edition operation.
if ($id) {
    $params['id'] = $id;

    if (!has_capability('mod/tasks:manageall', $context) &&
            ($issue->state != TASKS_STATE_OPEN || $issue->reportedby != $USER->id)) {
        print_error('noteditcapability', 'tasks');
    }
}

$PAGE->set_url('/mod/tasks/edit.php', $params);

require_once ('classes/edit.php');

// First create the form.
$data = clone $issue;
$editform = new \mod_tasks\edit_form(NULL,
                    array('data' => $data, 'anonymous' => false, 'mode' => $tasks->mode, 'context' => $context));

if ($editform->is_cancelled()) {
    if ($id) {
        $url = new moodle_url($CFG->wwwroot.'/mod/tasks/detail.php', array('id' => $id));
    } else {
        $url = new moodle_url($CFG->wwwroot.'/mod/tasks/view.php', array('id' => $cm->id));
    }
    redirect($url);
}
else if ($data = $editform->get_data()) {

    $log = new stdClass();
    $log->old = new stdClass();
    $log->change = new stdClass();
    $anychange = false;

    if (!$id) {
        $issue = new stdClass();
        $issue->state = TASKS_STATE_OPEN;
        $issue->reportedby = $USER->id;
        $issue->tasksid = $tasks->id;
        $issue->timereported = time();
    } else {

        foreach ($issue as $key => $field) {

            if (!property_exists($data, $key)) {
                continue;
            }

            if ($key == 'description') {
                if ($data->description['text'] != $field) {
                    $log->old->$key = $field;
                    $log->change->$key = is_array($data->description) ? $data->description['text'] : '';
                    $anychange = true;
                }
            } else if ($key == 'descriptionformat') {
                if ($data->description['format'] != $field) {
                    $log->old->$key = $field;
                    $log->change->$key = is_array($data->description) ? $data->description['format'] : '';
                    $anychange = true;
                }
            } else if ($data->$key != $field) {
                $log->old->$key = $field;
                $log->change->$key = $data->$key;
                $anychange = true;
            }
        }

    }

    $issue->name = $data->name;

    if (property_exists($data, 'timestart')) {
        $issue->timestart = $data->timestart;
    }

    if (property_exists($data, 'timefinish')) {
        $issue->timefinish = $data->timefinish;
    }

    if (property_exists($data, 'assignedto')) {
        $issue->assignedto = $data->assignedto;
        if ($issue->state != TASKS_STATE_ASSIGNED && !empty($issue->assignedto)) {
            $issue->state = TASKS_STATE_ASSIGNED;
        }
    }

    if (property_exists($data, 'supervisor')) {
        $issue->supervisor = $data->supervisor;
    }

    if (is_array($data->description)) {
        $issue->description = $data->description['text'];
        $issue->descriptionformat = $data->description['format'];
    }

    if (!empty($data->id)) {

        $DB->update_record('tasks_issues', $issue);

        // A specific tasks transaction log.
        if ($anychange) {
            $issueobj = new \mod_tasks\issue($issue, $tasks, $cm, $course);
            $issueobj->log(TASKS_LOG_EDIT, json_encode($log));
        }

        require_once 'classes/event/issue_updated.php';
        $event = \mod_tasks\event\issue_updated::create(array(
            'objectid' => $issue->id,
            'context' => $PAGE->context,
            'other' => array('tasksid' => $tasks->id)
        ));
        $event->trigger();
    }
    else {
        $id = $DB->insert_record('tasks_issues', $issue, true);

        require_once 'classes/event/issue_created.php';
        $event = \mod_tasks\event\issue_created::create(array(
            'objectid' => $id,
            'context' => $PAGE->context,
            'other' => array('tasksid' => $tasks->id),
        ));
        $event->trigger();
    }

    $url = new moodle_url($CFG->wwwroot.'/mod/tasks/detail.php', array('id' => $id));
    redirect($url);
    exit;
}

echo $OUTPUT->header();

$currenttab = 'edit';
include 'tabs.php';

$editform->display();

/// Finish the page
echo $OUTPUT->footer();
