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
 * Class to manage the module dashboard
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_tasks;

require_once($CFG->dirroot . '/mod/tasks/classes/panel.php');

class dashboard {

    /**
     * @var object The tasks main object
     */
    public $tasks = null;

    /**
     * @var object The tasks course
     */
    public $course = null;

    /**
     * @var object The course module tasks
     */
    public $cm = null;


    public function __construct($tasks, $cm = null, $course = null) {
        global $DB;

        $this->tasks = $tasks;

        if (!$course) {
            $this->course = $DB->get_record("course", array("id"=>$tasks->course));
        } else {
            $this->course = $course;
        }

        if (!$cm) {
            $this->cm = get_coursemodule_from_instance("tasks", $tasks->id, $this->course->id);
        } else {
            $this->cm = $cm;
        }
    }

    /**
     * Print the Dashboard html
     *
     * @return string
     */
    public function printboard($return = false) {
        global $OUTPUT;

        $html = '';

        $context = \context_module::instance($this->cm->id);

        $html .= $OUTPUT->box_start('row');
        $html .= $OUTPUT->box_start('span12 col-12 panels');

        if (has_capability('mod/tasks:viewall', $context)) {
            $html .= $this->get_latestissues_panel()->get_content();
        }

        $html .= $this->get_latestownissues_panel()->get_content();

        if (isloggedin()) {
            $html .= $this->get_assignedissues_panel()->get_content();

            $html .= $this->get_expiredissues_panel()->get_content();
        }

        if (has_capability('mod/tasks:manage', $context)) {
            $html .= $this->get_openedissues_panel()->get_content();
        }

        $html .= $OUTPUT->box_end();
        $html .= $OUTPUT->box_end();

        if ($this->tasks->anonymous == TASKS_ANONYMOUS) {

            $html .= $OUTPUT->box_start('row');
            $html .= $OUTPUT->box_start('span12 col-12');
            $html .= \html_writer::tag('a', get_string('anonymouslinktext', 'mod_tasks'),
                        array('href' => new \moodle_url('/mod/tasks/report.php', array('tasksid' => $this->tasks->id))));
            $html .= $OUTPUT->box_end();
            $html .= $OUTPUT->box_end();
        }

        if ($return) {
            return $html;
        }

        echo $html;
    }

    /**
     * The last reported issues
     *
     * @return string
     */
    public function get_latestissues_panel() {
        global $DB, $OUTPUT;

        $panel = new util\datapanel();
        $panel->head = get_string('latestissues', 'mod_tasks');

        $issues = $DB->get_records('tasks_issues',
                    array('tasksid' => $this->tasks->id),
                    'timereported DESC', '*', 0, TASKS_PANEL_ITEMS);

        if ($issues) {
            $panel->data = array();
            foreach($issues as $issue) {
                $row = \html_writer::tag('a', $issue->name,
                        array('href' => new \moodle_url('/mod/tasks/detail.php', array('id' => $issue->id))));

                $panel->data[] = $row;
            }
        } else {
            $panel->data = get_string('notissues', 'mod_tasks');
        }

        return $panel;
    }

    /**
     * The last own reported issues
     *
     * @return string
     */
    public function get_latestownissues_panel() {
        global $DB, $OUTPUT, $USER;

        $panel = new util\datapanel();
        $panel->head = get_string('latestownissues', 'mod_tasks');

        if (!$USER || !$USER->id) {
            return $panel;
        }

        $issues = $DB->get_records('tasks_issues',
                    array('tasksid' => $this->tasks->id, 'reportedby' => $USER->id),
                    'timereported DESC', '*', 0, TASKS_PANEL_ITEMS);

        if ($issues) {
            $panel->data = array();
            foreach($issues as $issue) {
                $row = \html_writer::tag('a', $issue->name,
                        array('href' => new \moodle_url('/mod/tasks/detail.php', array('id' => $issue->id))));

                $panel->data[] = $row;
            }
        } else {
            $panel->data = get_string('notissues', 'mod_tasks');
        }

        return $panel;
    }

    /**
     * Assigned to me
     *
     * @return string
     */
    public function get_assignedissues_panel() {
        global $DB, $OUTPUT, $USER;

        $panel = new util\datapanel();
        $panel->type = 'primary';
        $panel->head = get_string('assignedissues', 'mod_tasks');

        $issues = $DB->get_records('tasks_issues',
                    array('tasksid' => $this->tasks->id, 'assignedto' => $USER->id, 'state' => TASKS_STATE_ASSIGNED),
                    'timestart DESC', '*', 0, TASKS_PANEL_ITEMS);

        if ($issues) {
            $panel->data = array();
            foreach($issues as $issue) {
                $row = \html_writer::tag('a', $issue->name,
                        array('href' => new \moodle_url('/mod/tasks/detail.php', array('id' => $issue->id))));

                $panel->data[] = $row;
            }


            if (count($issues) == TASKS_PANEL_ITEMS) {
                $row = \html_writer::tag('a', get_string('showmore', 'form'),
                        array('href' => new \moodle_url('/mod/tasks/list.php',
                                            array('t' => $this->tasks->id, 'state[' . TASKS_STATE_ASSIGNED . ']' => 1))));

                $panel->data[] = $row;
            }
        } else {
            $panel->data = get_string('notissues', 'mod_tasks');
        }

        return $panel;
    }

    /**
     * Opened issues
     *
     * @return string
     */
    public function get_openedissues_panel() {
        global $DB, $OUTPUT, $USER;

        $panel = new util\datapanel();
        $panel->type = 'warning';
        $panel->head = get_string('openedissues', 'mod_tasks');

        $issues = $DB->get_records('tasks_issues',
                    array('tasksid' => $this->tasks->id, 'state' => TASKS_STATE_OPEN),
                    'timestart DESC', '*', 0, TASKS_PANEL_ITEMS);

        if ($issues) {
            $panel->data = array();
            foreach($issues as $issue) {
                $row = \html_writer::tag('a', $issue->name,
                        array('href' => new \moodle_url('/mod/tasks/detail.php', array('id' => $issue->id))));

                $panel->data[] = $row;
            }

            if (count($issues) == TASKS_PANEL_ITEMS) {
                $row = \html_writer::tag('a', get_string('showmore', 'form'),
                        array('href' => new \moodle_url('/mod/tasks/list.php',
                                            array('t' => $this->tasks->id, 'state[' . TASKS_STATE_OPEN . ']' => 1))));

                $panel->data[] = $row;
            }
        } else {
            $panel->data = get_string('notissues', 'mod_tasks');
        }

        return $panel;
    }

    /**
     * Opened issues
     *
     * @return string
     */
    public function get_expiredissues_panel() {
        global $DB, $OUTPUT, $USER;

        $panel = new util\datapanel();
        $panel->type = 'danger';
        $panel->head = get_string('expiredissues', 'mod_tasks');

        $params = array('tasksid' => $this->tasks->id, 'state' => TASKS_STATE_ASSIGNED,
                    'assignedto' => $USER->id, 'timefinish' => time());
        $select = "tasksid = :tasksid AND state = :state AND assignedto = :assignedto AND timefinish < :timefinish";

        $issues = $DB->get_records_select('tasks_issues', $select, $params, 'timefinish ASC', '*', 0, TASKS_PANEL_ITEMS);

        if ($issues) {
            $panel->data = array();
            foreach($issues as $issue) {
                $row = \html_writer::tag('a', $issue->name,
                        array('href' => new \moodle_url('/mod/tasks/detail.php', array('id' => $issue->id))));

                $panel->data[] = $row;
            }
        } else {
            $panel->data = get_string('notissues', 'mod_tasks');
        }

        return $panel;
    }

}
