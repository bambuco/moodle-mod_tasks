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
 * A scheduled tasks for module cron.
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_tasks\task;

require_once($CFG->dirroot . '/mod/tasks/lib.php');
require_once($CFG->dirroot . '/mod/tasks/classes/issue.php');

class cron_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontasks', 'mod_tasks');
    }

    /**
     * Run cron.
     */
    public function execute() {
        global $CFG, $USER, $DB, $PAGE;

        $select = "notificationstype != " . TASKS_NOTITYPE_NONE;
        $list = $DB->get_records_select('tasks', $select);

        if ($list) {
            foreach ($list as $tasks) {
                mtrace('Processing tasks ' . $tasks->id);

                if (!$course = $DB->get_record("course", array("id" => $tasks->course))) {
                    mtrace('Invalid course id ' . $tasks->course);
                    continue;
                }
                if (!$cm = get_coursemodule_from_instance("tasks", $tasks->id, $course->id)) {
                    mtrace('Invalid course module for ' . $tasks->course . ' - ' . $tasks->id);
                    continue;
                }

                $prevision = $tasks->notificationsdays * 24 * 60 * 60;
                $timecheck = time();
                $state = TASKS_STATE_ASSIGNED;
                $params = array('tasksid' => $tasks->id);


                if ($tasks->mode == TASKS_MODE_ISSUES) {
                    if ($tasks->notificationstype == TASKS_NOTITYPE_BEFORE) {
                        // Half day: 43200 = (60 seconds * 60 minuts * 24 hours) / 2.
                        $select = "tasksid = :tasksid AND state = '{$state}' AND (timestart + {$prevision} - 43200) <= {$timecheck} AND " .
                                  " (timestart + {$prevision} + 43200) >= {$timecheck}";
                    } else {
                        // Each day. The cron only is executed one time in the day.
                        $select = "tasksid = :tasksid AND state = '{$state}' AND (timestart + {$prevision}) <= {$timecheck}";
                    }
                } else {
                    if ($tasks->notificationstype == TASKS_NOTITYPE_BEFORE) {
                        // Half day: 43200 = (60 seconds * 60 minuts * 24 hours) / 2.
                        $select = "tasksid = :tasksid AND state = '{$state}' AND timefinish > 0 AND " .
                                  " (timefinish - {$prevision} - 43200) <= {$timecheck} AND " .
                                  " (timefinish - {$prevision} + 43200) >= {$timecheck}";


                    } else {
                        // Each day. The cron only is executed one time in the day.
                        $select = "tasksid = :tasksid AND state = '{$state}' AND (timefinish - {$prevision}) <= {$timecheck}";
                    }
                }

                $issues = $DB->get_records_select('tasks_issues', $select, $params);

                if ($issues) {
                    mtrace(count($issues) . ' issues.');

                    foreach ($issues as $issue) {
                        mtrace('Sending message: issue ' . $issue->id . '. Assigned to: ' . $issue->assignedto);

                        $issueobj = new \mod_tasks\issue($issue, $tasks, $cm, $course);
                        $issueobj->sendmessage(TASKS_MSG_REMINDER);
                    }
                } else {
                    mtrace('Not issues.');
                }
            }
        }

    }

}
