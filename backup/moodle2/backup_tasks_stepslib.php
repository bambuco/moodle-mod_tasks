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

defined('MOODLE_INTERNAL') || die;

/**
 * Define all the backup steps that will be used by the backup_tasks_activity_task
 */

/**
 * Define the complete tasks structure for backup, with file and id annotations
 */
class backup_tasks_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $tasks = new backup_nested_element('tasks', array('id'), array(
            'name', 'intro', 'introformat', 'notificationstype', 'notificationsdays',
            'taskprefix'));

        $issues = new backup_nested_element('issues');

        $issue = new backup_nested_element('issue', array('id'), array(
            'reportedby', 'assignedto', 'name', 'description',
            'descriptionformat', 'timereported', 'timestart', 'timefinish', 'state'));


//ToDo: VOY ACA, FALTAN LAS OTRAS TABLAS
//ToDo: VOY ACA, FALTAN LAS OTRAS TABLAS
//ToDo: VOY ACA, FALTAN LAS OTRAS TABLAS
//ToDo: VOY ACA, FALTAN LAS OTRAS TABLAS
//ToDo: VOY ACA, FALTAN LAS OTRAS TABLAS
//ToDo: VOY ACA, FALTAN LAS OTRAS TABLAS
//ToDo: VOY ACA, FALTAN LAS OTRAS TABLAS
//ToDo: VOY ACA, FALTAN LAS OTRAS TABLAS
//ToDo: VOY ACA, FALTAN LAS OTRAS TABLAS
//ToDo: VOY ACA, FALTAN LAS OTRAS TABLAS
//ToDo: VOY ACA, FALTAN LAS OTRAS TABLAS


        // Build the tree

        // Define sources
        $tasks->set_source_table('tasks', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        // (none)

        // Define file annotations
        $tasks->annotate_files('mod_tasks', 'intro', null); // This file areas haven't itemid
        $tasks->annotate_files('mod_tasks', 'content', null); // This file areas haven't itemid

        // Return the root element (tasks), wrapped into standard activity structure
        return $this->prepare_activity_structure($tasks);
    }
}
