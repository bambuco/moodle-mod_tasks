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
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 */

// This file to be included so we can assume config.php has already been included.
// We also assume that $tasks, $cm, $currenttab have been set

defined('MOODLE_INTERNAL') || die;

if (empty($cm)) {
    print_error('cannotcallscript');
}

$context = context_module::instance($cm->id);

$row = array();

$row[] = new tabobject('dashboard', new moodle_url('/mod/tasks/view.php', array('id' => $cm->id)), get_string('dashboard','mod_tasks'));

$row[] = new tabobject('list', new moodle_url('/mod/tasks/list.php', array('t' => $tasks->id)), get_string('list','mod_tasks'));

if (isloggedin()) {
    if (has_capability('mod/tasks:report', $context)) {

        $row[] = new tabobject('edit', new moodle_url('/mod/tasks/edit.php', array('tasksid' => $tasks->id)),
                    get_string($currenttab == 'edit' && !empty($issue->id) ? 'tabedit' : 'tabnew','mod_tasks', $tasks->singularlabel));
    }
}

echo $OUTPUT->heading($tasks->name);

echo $OUTPUT->box_start();
echo format_text($tasks->intro, $tasks->introformat);
echo $OUTPUT->box_end();

// Print out the tabs and continue!
echo $OUTPUT->tabtree($row, $currenttab);
