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

/**
 * Define all the restore steps that will be used by the restore_tasks_activity_task
 */

/**
 * Structure step to restore one tasks activity
 */
class restore_tasks_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('tasks', '/activity/tasks');

        if ($userinfo) {
            $paths[] = new restore_path_element('tasks_issues', '/activity/tasks/issues/issue');
            $paths[] = new restore_path_element('tasks_issues_log', '/activity/tasks/issues/issue/issueslog/issuelog');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_tasks($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // insert the tasks record
        $newitemid = $DB->insert_record('tasks', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_tasks_issues($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->tasksid = $this->get_new_parentid('tasks');
        $data->timereported = $this->apply_date_offset($data->timereported);
        $data->timestart = $this->apply_date_offset($data->timestart);
        $data->timefinish = $this->apply_date_offset($data->timefinish);

        if ($data->reportedby) {
            $data->reportedby = $this->get_mappingid('user', $data->reportedby);
        }

        if ($data->assignedto) {
            $data->assignedto = $this->get_mappingid('user', $data->assignedto);
        }

        if ($data->supervisor) {
            $data->supervisor = $this->get_mappingid('user', $data->supervisor);
        }

        // Insert the tasks issue record.
        $newitemid = $DB->insert_record('tasks_issues', $data);
        $this->set_mapping('tasks_issues', $oldid, $newitemid);
    }

    protected function process_tasks_issues_log($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->tasksid = $this->get_new_parentid('tasks');
        $data->issueid = $this->get_new_parentid('tasks_issues');
        $data->timelog = $this->apply_date_offset($data->timelog);

        $data->userid = $this->get_mappingid('user', $data->userid);

        // Insert the tasks issue record.
        $newitemid = $DB->insert_record('tasks_issues_log', $data);
    }

    protected function after_execute() {
        // Add tasks related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_tasks', 'intro', null);
        $this->add_related_files('mod_tasks', 'description', 'tasks_issues');
    }
}
