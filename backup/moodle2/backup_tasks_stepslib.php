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
            'taskprefix', 'anonymous', 'mode', 'singularlabel', 'plurallabel'));

        $issues = new backup_nested_element('issues');

        $issue = new backup_nested_element('issue', array('id'), array(
            'reportedby', 'assignedto', 'supervisor', 'name', 'description',
            'descriptionformat', 'timereported', 'timestart', 'timefinish', 'state',
            'namereportedby', 'emailreportedby'));

        $issueslog = new backup_nested_element('issueslog');

        $issuelog = new backup_nested_element('issuelog', array('id'), array(
            'userid', 'type', 'timelog', 'summary'));

        // Build the tree
        $tasks->add_child($issues);
        $issues->add_child($issuelog);

        // Define sources
        $tasks->set_source_table('tasks', array('id' => backup::VAR_ACTIVITYID));

        // All these source definitions only happen if we are including user info
        if ($userinfo) {
            $issue->set_source_table('tasks_issues', array('tasksid' => backup::VAR_ACTIVITYID));
            $issuelog->set_source_table('tasks_issues_log', array('tasksid' => backup::VAR_ACTIVITYID));
        }

        // Define id annotations
        $issue->annotate_ids('user', 'reportedby');
        $issue->annotate_ids('user', 'assignedto');
        $issue->annotate_ids('user', 'supervisor');

        $issuelog->annotate_ids('user', 'userid');

        // Define file annotations
        $tasks->annotate_files('mod_tasks', 'intro', null); // This file areas haven't itemid.
        $issue->annotate_files('mod_tasks', 'description', 'id');

        // Return the root element (tasks), wrapped into standard activity structure
        return $this->prepare_activity_structure($tasks);
    }
}
