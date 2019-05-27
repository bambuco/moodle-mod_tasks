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
require_once ('classes/issue.php');
require_once ('classes/filter.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID.
$t = optional_param('t', 0, PARAM_INT); // Tasks ID.

$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM); //md5 confirmation hash.
$sort = optional_param('sort', 'timereported', PARAM_ALPHA);
$dir = optional_param('dir', 'DESC', PARAM_ALPHA);
$page = optional_param('spage', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT); // How many per page.
$states = optional_param_array('state', array(), PARAM_INT);
$search = optional_param('search', '', PARAM_INT);

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

// When users reset the form, redirect back to first page without filter params.
if (optional_param('resetbutton', '', PARAM_RAW) !== '') {
    redirect('list.php?id=' . $id);
}

require_course_login($course->id, true, $cm);
$context = context_module::instance($cm->id);

$where = '';
$paramswhere = array('tasksid' => $tasks->id);

if (!empty($search)) {
    $where = ' AND id = :id';
    $paramswhere['id'] = $search;
}

$filterform = new \mod_tasks\filter_form(NULL, array('id' => $id), 'get', '', array('id' => 'filterform'));
$filterdata = array('search' => $search);

$statesor = array();
foreach ($states as $key => $active) {
    if (mod_tasks\util\tools::is_state($key)) {
        $filterdata['state[' . $key . ']'] = $active;
        $paramswhere[$key] = $key;
        $statesor[] = 'state = :' . $key;
    }
}

if (count($statesor) > 0) {
    $where .= ' AND (' . implode(' OR ', $statesor) . ') ';
}

$filterform->set_data($filterdata);


// If we have received a page, recalculate offset.
$offset = 0;
if ($page != 0) {
    $offset = $page * $perpage;
}

// Initialize $PAGE, compute blocks.
$PAGE->set_url('/mod/tasks/list.php', array('id' => $cm->id));

echo $OUTPUT->header();

$currenttab = 'list';
include 'tabs.php';

if (has_capability('mod/tasks:viewall', $context)) {
    $sql = "SELECT * FROM {tasks_issues} WHERE tasksid = :tasksid " . $where .
                " ORDER BY " . $sort . " " . $dir;

    $sqlcount = "SELECT COUNT(1) FROM {tasks_issues} WHERE tasksid = :tasksid " . $where;

    $issues = $DB->get_records_sql($sql, $paramswhere, $offset, $perpage);
    $issuescount = $DB->count_records_sql($sqlcount, $paramswhere);

} else {
    $sql = "SELECT * FROM {tasks_issues} WHERE tasksid = :tasksid AND " .
                " (reportedby = :reportedby OR assignedto = :assignedto OR supervisor = :supervisor)" . $where .
                " ORDER BY " . $sort . " " . $dir;

    $sqlcount = "SELECT COUNT(1) FROM {tasks_issues} WHERE tasksid = :tasksid AND " .
                    " (reportedby = :reportedby OR assignedto = :assignedto OR supervisor = :supervisor)" . $where;

    $paramswhere['reportedby'] = $USER->id;
    $paramswhere['assignedto'] = $USER->id;
    $paramswhere['supervisor'] = $USER->id;

    $issues = $DB->get_records_sql($sql, $paramswhere, $offset, $perpage);
    $issuescount = $DB->count_records_sql($sqlcount, $paramswhere);
}


$table = new html_table();
$table->attributes['class'] = 'admintable generaltable mod_tasks_datatable';
$table->cellspacing = 0;

$table->head = array();

$columns = array();
$columns['id'] = get_string('issuecode', 'mod_tasks');
$columns['state'] = get_string('state', 'mod_tasks');
$columns['name'] = get_string('name');
$columns['reportedby'] = get_string('reportedby', 'mod_tasks');
$columns['timereported'] = get_string('timereported', 'mod_tasks');
$columns['assignedto'] = get_string('assignedto', 'mod_tasks');
$columns['timestart'] = get_string('timestart', 'mod_tasks');
$columns['timefinish'] = get_string('timefinish', 'mod_tasks');

foreach ($columns as $ckey => $column) {
    if ($sort != $ckey) {
        $columnicon = "";
        $columndir = "ASC";
    }
    else {
        $columndir = $dir == "ASC" ? "DESC":"ASC";
        $columnicon = ($dir == "ASC") ? "sort_asc" : "sort_desc";
        $columnicon = $OUTPUT->pix_icon('t/' . $columnicon, new lang_string('sort'));

    }
    $params = array('id' => $id, 'sort' => $ckey, 'dir' => $columndir, 'perpage' => $perpage, 'page' => $page,
              'search' => $search);

    foreach ($states as $key => $active) {
        if (mod_tasks\util\tools::is_state($key)) {
            $params['state[' . $key . ']'] = $active;
        }
    }
    $url = new moodle_url('/mod/tasks/list.php', $params);
    $table->head[] = html_writer::link($url, $column) . $columnicon;
}

//Operations column
$table->head[] = '';

if($issues) {
    foreach($issues as $issuedata){


        $issue = new \mod_tasks\issue($issuedata, $tasks, $cm, $course);

        $data = array ();
        $data[] = $issue->codestr();
        $data[] = $issue->statestr();
        $data[] = $issue->namelink();
        $data[] = $issue->reportedbystr();
        $data[] = $issue->timereportedstr();
        $data[] = $issue->assignedtostr();
        $data[] = $issue->timestartstr();
        $data[] = $issue->timefinishstr();

        $table->data[] = $data;

    }
}

$params = array('id' => $id, 'sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'page' => $page,
            'search' => $search);

foreach ($states as $key => $active) {
    if (mod_tasks\util\tools::is_state($key)) {
        $params['state[' . $key . ']'] = $active;
    }
}

$url = new moodle_url('/mod/tasks/list.php', $params);
$pagingbar = new paging_bar($issuescount, $page, $perpage, $url);
$pagingbar->pagevar = 'spage';

echo $filterform->display();
echo $OUTPUT->render($pagingbar);

echo html_writer::table($table);

echo $OUTPUT->render($pagingbar);

/// Finish the page
echo $OUTPUT->footer();
