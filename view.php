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

$id = optional_param('id', 0, PARAM_INT); // Course Module ID.
$t = optional_param('t', 0, PARAM_INT); // Tasks ID

if (!empty($id)) {
    if (! $cm = get_coursemodule_from_id('tasks', $id)) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }
    if (! $tasks = $DB->get_record("tasks", array("id"=>$cm->instance))) {
        print_error('invalidid', 'tasks');
    }

} else if (!empty($t)) {
    if (! $tasks = $DB->get_record("tasks", array("id"=>$t))) {
        print_error('invalidid', 'tasks');
    }
    if (! $course = $DB->get_record("course", array("id"=>$tasks->course))) {
        print_error('invalidcourseid');
    }
    if (!$cm = get_coursemodule_from_instance("tasks", $tasks->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
    $id = $cm->id;
} else {
    print_error('invalidid', 'tasks');
}

require_course_login($course->id, true, $cm);
$context = context_module::instance($cm->id);

// Initialize $PAGE, compute blocks.
$PAGE->set_url('/mod/tasks/view.php', array('id' => $cm->id));

echo $OUTPUT->header();

$currenttab = 'dashboard';
include 'tabs.php';

require_once ('classes/dashboard.php');

$dashboard = new \mod_tasks\dashboard($tasks, $cm, $course);

$dashboard->printboard();

/// Finish the page
echo $OUTPUT->footer();
