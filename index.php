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
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/tasks/lib.php');
require_once($CFG->dirroot . '/mod/tasks/classes/event/course_module_instance_list_viewed.php');

$id = optional_param('id', 0, PARAM_INT);                   // Course id

$url = new moodle_url('/mod/tasks/index.php', array('id' => $id));
$PAGE->set_url($url);

if ($id) {
    if (!$course = $DB->get_record('course', array('id' => $id))) {
        print_error('invalidcourseid');
    }
} else {
    $course = get_site();
}

require_course_login($course);
$PAGE->set_pagelayout('incourse');
$context = context_course::instance($course->id);

$event = \mod_tasks\event\course_module_instance_list_viewed::create(array(
    'context' => $context
));
$event->add_record_snapshot('course', $course);
$event->trigger();

/// Get all required strings

$strtasks  = get_string("modulename", "tasks");


/// Print the header
$PAGE->navbar->add($strtasks, "index.php?id=$course->id");
$PAGE->set_title($strtasks);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($strtasks), 2);

/// Get all the appropriate data

if (! $tasks = get_all_instances_in_course("tasks", $course)) {
    notice(get_string('thereareno', 'moodle', $strtasks), "../../course/view.php?id=$course->id");
    die;
}

$usesections = course_format_uses_sections($course->format);

/// Print the list of instances (your module will probably extend this)

$timenow = time();
$strname  = get_string("name");
$strissues  = get_string("issues", "tasks");

$table = new html_table();

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname, $strissues);
    $table->align = array ('center', 'left', 'center');
} else {
    $table->head  = array ($strname, $strissues);
    $table->align = array ('left', 'center');
}

$currentsection = "";

foreach ($tasks as $task) {
    if (!$task->visible && has_capability('moodle/course:viewhiddenactivities',
            context_module::instance($task->coursemodule))) {
        // Show dimmed if the mod is hidden.
        $link = "<a class=\"dimmed\" href=\"view.php?id=$task->coursemodule\">".format_string($task->name,true)."</a>";
    } else if ($task->visible) {
        // Show normal if the mod is visible.
        $link = "<a href=\"view.php?id=$task->coursemodule\">".format_string($task->name,true)."</a>";
    } else {
        // Don't show the tasks.
        continue;
    }
    $printsection = "";
    if ($usesections) {
        if ($task->section !== $currentsection) {
            if ($task->section) {
                $printsection = get_section_name($course, $task->section);
            }
            if ($currentsection !== "") {
                $table->data[] = 'hr';
            }
            $currentsection = $task->section;
        }
    }

    $count = $DB->count_records_sql("SELECT COUNT(*) FROM {tasks_issues} WHERE tasksid = ?", array($task->id));

    if ($usesections) {
        $linedata = array ($printsection, $link, $count);
    } else {
        $linedata = array ($link, $count);
    }

    $table->data[] = $linedata;
}

echo "<br />";

echo html_writer::table($table);

/// Finish the page

echo $OUTPUT->footer();

