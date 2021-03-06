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
 * Class to manage a issue
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_tasks;

require_once($CFG->dirroot . '/mod/tasks/classes/panel.php');

class issue {

    /**
     * @var object The issue data
     */
    public $data = null;

    /**
     * @var object The tasks object
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

    /**
     * @var string Default format dates
     */
    public $defaultuserformatdate;

    /**
     * @var object The module context
     */
    public $context = null;


    public function __construct($data, $tasks = null, $cm = null, $course = null) {
        global $DB;

        $this->data = $data;

        if (!$tasks) {
            $this->tasks = $DB->get_record("tasks", array("id" => $data->tasksid));
        } else {
            $this->tasks = $tasks;
        }

        if (!$course) {
            $this->course = $DB->get_record("course", array("id" => $tasks->course));
        } else {
            $this->course = $course;
        }

        if (!$cm) {
            $this->cm = get_coursemodule_from_instance("tasks", $tasks->id, $this->course->id);
        } else {
            $this->cm = $cm;
        }

        $this->context = \context_module::instance($cm->id);

        $this->defaultuserformatdate = get_string('strftimedatetimeshort');
    }

    /**
     * Print the issue details html
     *
     * @return string
     */
    public function printdetails($return = false) {
        global $OUTPUT, $DB;

        $html = $OUTPUT->box_start('state-' . $this->data->state);

        // Issue code.
        $label = \html_writer::tag('strong', get_string('issuecode', 'mod_tasks'));
        $html .= \html_writer::tag('div', $label . ' ' . $this->codestr());

        // Name and description
        $html .= \html_writer::tag('h2', $this->data->name);

        $description = file_rewrite_pluginfile_urls($this->data->description, 'pluginfile.php',
                            $this->context->id, 'mod_tasks', 'description', $this->data->id);
        $html .= \html_writer::tag('div',
                    format_text($description, $this->data->descriptionformat, null, $this->course->id),
                    array('class' => 'details'));

        $html .= \html_writer::start_tag('ul');

        // State.
        $label = \html_writer::tag('strong', get_string('state', 'mod_tasks'));
        $html .= \html_writer::tag('li', $label . ' ' . $this->statestr());

        // Reported by.
        $label = \html_writer::tag('strong', get_string('reportedby', 'mod_tasks'));
        $html .= \html_writer::tag('li', $label . ' ' . $this->reportedbystr());

        // Assigned to.
        $label = \html_writer::tag('strong', get_string('assignedto', 'mod_tasks'));
        $html .= \html_writer::tag('li', $label . ' ' . $this->assignedtostr());

        // Supervisor.
        $label = \html_writer::tag('strong', get_string('supervisor', 'mod_tasks'));
        $html .= \html_writer::tag('li', $label . ' ' . $this->supervisorstr());

        // Time reported.
        $label = \html_writer::tag('strong', get_string('timereported', 'mod_tasks'));
        $html .= \html_writer::tag('li', $label . ' ' . $this->timereportedstr());

        // Time start.
        $label = \html_writer::tag('strong', get_string('timestart', 'mod_tasks'));
        $html .= \html_writer::tag('li', $label . ' ' . $this->timestartstr());

        // Time finish.
        $label = \html_writer::tag('strong', get_string('timefinish', 'mod_tasks'));
        $html .= \html_writer::tag('li', $label . ' ' . $this->timefinishstr());


        $html .= \html_writer::end_tag('ul');

        $html .= $OUTPUT->box_end();

        if ($return) {
            return $html;
        }

        echo $html;
    }

    public function codestr() {
        return $this->tasks->taskprefix . $this->data->id;
    }

    public function statestr() {
        return get_string('state_' . $this->data->state, 'mod_tasks');
    }

    public function reportedbystr($link = true) {
        global $DB;

        if ($this->data->reportedby == 0) {
            $reportedbyname = $this->data->namereportedby . ' - ' . $this->data->emailreportedby;
        } else {
            $reportedby = $DB->get_record('user', array('id' => $this->data->reportedby));

            if ($link) {
                $reportedbyname = \html_writer::tag('a', fullname($reportedby),
                                    array('href' => new \moodle_url('/user/profile.php', array('id' => $reportedby->id))));
            } else {
                $reportedbyname = fullname($reportedby);
            }
        }

        return $reportedbyname;
    }

    public function assignedtostr($link = true, $userid = NULL) {
        global $DB;

        if ($userid === NULL) {
            $userid = $this->data->assignedto;
        }

        if ($userid == 0) {
            $assignedtoname = get_string('unassigned', 'mod_tasks');
        } else {
            $assignedto = $DB->get_record('user', array('id' => $userid));

            if ($link) {
                $assignedtoname = \html_writer::tag('a', fullname($assignedto),
                                    array('href' => new \moodle_url('/user/profile.php', array('id' => $assignedto->id))));
            } else {
                $assignedtoname = fullname($assignedto);
            }
        }

        return $assignedtoname;
    }

    public function supervisorstr($link = true, $userid = NULL) {
        global $DB;

        if ($userid === NULL) {
            $userid = $this->data->supervisor;
        }

        if ($userid == 0) {
            $supervisorname = get_string('notsupervised', 'mod_tasks');
        } else {
            $supervisor = $DB->get_record('user', array('id' => $userid));

            if ($link) {
                $supervisorname = \html_writer::tag('a', fullname($supervisor),
                                    array('href' => new \moodle_url('/user/profile.php', array('id' => $supervisor->id))));
            } else {
                $supervisorname = fullname($supervisor);
            }
        }

        return $supervisorname;
    }

    public function timereportedstr($userformatdate = null) {

        if (!$userformatdate) {
            $userformatdate = $this->defaultuserformatdate;
        }

        return userdate($this->data->timereported, $userformatdate);
    }

    public function timestartstr($userformatdate = null) {

        if (!$userformatdate) {
            $userformatdate = $this->defaultuserformatdate;
        }

        return !$this->data->timestart ?
                        get_string('notstarted', 'mod_tasks') : userdate($this->data->timestart, $userformatdate);
    }

    public function timefinishstr($userformatdate = null) {

        if (!$userformatdate) {
            $userformatdate = $this->defaultuserformatdate;
        }

        return !$this->data->timefinish ?
                        get_string('notfinished', 'mod_tasks') : userdate($this->data->timefinish, $userformatdate);

    }

    public function namelink() {
        global $DB;

        return \html_writer::tag('a', $this->data->name,
                                    array('href' => new \moodle_url('/mod/tasks/detail.php', array('id' => $this->data->id))));

    }

    public function log($type, $summary = '') {
        global $DB, $USER;

        $data = new \stdClass();
        $data->tasksid = $this->data->tasksid;
        $data->issueid = $this->data->id;
        $data->userid = $USER->id;
        $data->type = $type;
        $data->timelog = time();
        $data->summary = $summary;
        $DB->insert_record('tasks_issues_log', $data);
    }

    public function printtimeline($return = false) {
        global $OUTPUT, $DB;

        $logs = $DB->get_records('tasks_issues_log', array('issueid' => $this->data->id), 'timelog ASC');

        $html = $OUTPUT->box_start('loghistory');

        if ($logs) {

            foreach($logs as $log) {
                $html .= $OUTPUT->box_start('one-log type-' . $log->type);
                switch($log->type) {
                    case TASKS_LOG_EDIT:
                        $html .= $this->geteditionview($log);
                        break;
                    case TASKS_LOG_ASSIGN:
                        $html .= $this->getassignview($log);
                        break;
                    case TASKS_LOG_SUPERVISED:
                        $html .= $this->getsupervisedview($log);
                        break;
                    case TASKS_LOG_STATE:
                        $html .= $this->getstateview($log);
                        break;
                    case TASKS_LOG_COMMENT:
                        $html .= $this->getcommentview($log);
                        break;
                }
                $html .= $OUTPUT->box_end();
            }
        }

        $html .= $OUTPUT->box_end();

        if ($return) {
            return $html;
        }

        echo $html;
    }

    public function geteditionview($log) {
        global $DB;

        $html = '';

        $user = $DB->get_record('user', array('id' => $log->userid));
        $summary = json_decode($log->summary);


        $a = new \stdClass();
        $a->timelog = userdate($log->timelog, $this->defaultuserformatdate);
        $a->user = \html_writer::tag('a', fullname($user),
                                    array('href' => new \moodle_url('/user/profile.php', array('id' => $user->id))));

        $label = \html_writer::tag('strong', get_string('editionview', 'mod_tasks', $a) );

        $table = new \html_table();
        $table->head = array();
        $table->head[] = get_string('field', 'mod_tasks');
        $table->head[] = get_string('old', 'mod_tasks');
        $table->head[] = get_string('changed', 'mod_tasks');

        foreach ($summary->old as $key => $field) {

            $data = array();
            $data[] = get_string($key, 'mod_tasks');

            if ($key == 'description') {
                $field = file_rewrite_pluginfile_urls($field, 'pluginfile.php',
                            $this->context->id, 'mod_tasks', 'description', $this->data->id);
                $data[] = format_text($field,
                    isset($summary->old->descriptionformat) ?
                        $summary->old->descriptionformat : $this->data->descriptionformat);

                $new = file_rewrite_pluginfile_urls($summary->change->$key, 'pluginfile.php',
                            $this->context->id, 'mod_tasks', 'description', $this->data->id);
                $data[] = format_text($new,
                    isset($summary->change->descriptionformat) ?
                        $summary->change->descriptionformat : $this->data->descriptionformat);

            } else if (in_array($key, array('timestart', 'timereported', 'timefinish'))) {
                $data[] = $field == 0 ? '' : userdate($field, $this->defaultuserformatdate);
                $data[] = $summary->change->$key == 0 ? '' : userdate($summary->change->$key, $this->defaultuserformatdate);

            } else if ($key == 'assignedto') {
                $data[] = $field == 0 ? '' : $this->assignedtostr(true, $field);
                $data[] = $summary->change->$key == 0 ? '' : $this->assignedtostr(true, $summary->change->$key);
            } else {
                $data[] = $field;
                $data[] = $summary->change->$key;
            }

            $table->data[] = $data;

        }

        $panel = new util\datapanel();
        $panel->head = $label;
        $panel->data = \html_writer::table($table);

        return $panel->get_content();
    }

    public function getassignview($log) {
        global $DB;

        $html = '';

        $user = $DB->get_record('user', array('id' => $log->userid));
        $summary = json_decode($log->summary);

        $a = new \stdClass();
        $a->timelog = userdate($log->timelog, $this->defaultuserformatdate);
        $a->user = \html_writer::tag('a', fullname($user),
                                    array('href' => new \moodle_url('/user/profile.php', array('id' => $user->id))));

        if ($summary->old > 0) {
            $usrold = $DB->get_record('user', array('id' => $summary->old));
            $a->old = \html_writer::tag('a', fullname($usrold),
                        array('href' => new \moodle_url('/user/profile.php', array('id' => $usrold->id))));
        } else {
            $a->old = '';
        }

        $usrchange = $DB->get_record('user', array('id' => $summary->change));
        $a->change = \html_writer::tag('a', fullname($usrchange),
                                    array('href' => new \moodle_url('/user/profile.php', array('id' => $usrchange->id))));

        return \html_writer::tag('span', get_string('assignview', 'mod_tasks', $a) );
    }

    public function getsupervisedview($log) {
        global $DB;

        $html = '';

        $user = $DB->get_record('user', array('id' => $log->userid));
        $summary = json_decode($log->summary);

        $a = new \stdClass();
        $a->timelog = userdate($log->timelog, $this->defaultuserformatdate);
        $a->user = \html_writer::tag('a', fullname($user),
                                    array('href' => new \moodle_url('/user/profile.php', array('id' => $user->id))));

        if ($summary->old > 0) {
            $usrold = $DB->get_record('user', array('id' => $summary->old));
            $a->old = \html_writer::tag('a', fullname($usrold),
                        array('href' => new \moodle_url('/user/profile.php', array('id' => $usrold->id))));
        } else {
            $a->old = '';
        }

        $usrchange = $DB->get_record('user', array('id' => $summary->change));
        $a->change = \html_writer::tag('a', fullname($usrchange),
                                    array('href' => new \moodle_url('/user/profile.php', array('id' => $usrchange->id))));

        return \html_writer::tag('span', get_string('supervisedview', 'mod_tasks', $a) );
    }

    public function getstateview($log) {
        global $DB;

        $html = '';

        $user = $DB->get_record('user', array('id' => $log->userid));
        $summary = json_decode($log->summary);

        $a = new \stdClass();
        $a->timelog = userdate($log->timelog, $this->defaultuserformatdate);
        $a->user = \html_writer::tag('a', fullname($user),
                                    array('href' => new \moodle_url('/user/profile.php', array('id' => $user->id))));
        $a->old = get_string('state_' . $summary->old, 'mod_tasks');
        $a->change = get_string('state_' . $summary->change, 'mod_tasks');

        return \html_writer::tag('span', get_string('stateview', 'mod_tasks', $a) );
    }

    public function getcommentview($log) {
        global $DB;

        $html = '';

        $user = $DB->get_record('user', array('id' => $log->userid));
        $summary = json_decode($log->summary);


        $a = new \stdClass();
        $a->timelog = userdate($log->timelog, $this->defaultuserformatdate);
        $a->user = \html_writer::tag('a', fullname($user),
                                    array('href' => new \moodle_url('/user/profile.php', array('id' => $user->id))));

        $label = \html_writer::tag('strong', get_string('commentview', 'mod_tasks', $a) );

        $panel = new util\datapanel();
        $panel->head = $label;
        $panel->data = format_text($summary->comment, FORMAT_MOODLE);

        return $panel->get_content();
    }

    public function sendmessage($type) {
        global $USER;

        $to = array();
        $onlymail = array();

        $a = new \stdClass();
        $a->label = $this->tasks->singularlabel;
        $a->code = $this->codestr();
        $a->url = $this->namelink();

        $msg = get_string('msg_' . $type, 'mod_tasks', $a);
        $subject = get_string('subject_' . $type, 'mod_tasks', $a);

        switch ($type) {
            case TASKS_MSG_CREATED:
                if ($this->data->reportedby > 0) {
                    $to[] = $this->data->reportedby;
                } else if (!empty($issue->emailreportedby)) {
                    $tmpuser = new \stdClass();
                    $tmpuser->id = 0;
                    $tmpuser->emailstop = $issue->emailreportedby;
                    $to[] = $tmpuser;
                }

            break;
            case TASKS_MSG_REMINDER:
            case TASKS_MSG_ASSIGNED:
                if ($this->data->assignedto) {
                    $to[] = $this->data->assignedto;
                }
            break;
            case TASKS_MSG_EDITED:
            case TASKS_MSG_CLOSED:
            case TASKS_MSG_CANCELED;

                if ($this->data->reportedby > 0) {
                    $to[] = $this->data->reportedby;
                } else if (!empty($issue->emailreportedby)) {
                    $tmpuser = new \stdClass();
                    $tmpuser->id = 0;
                    $tmpuser->emailstop = $issue->emailreportedby;
                    $to[] = $tmpuser;
                }

                if ($this->data->assignedto) {
                    $to[] = $this->data->assignedto;
                }

                if ($this->data->supervisor) {
                    $to[] = $this->data->supervisor;
                }

            break;
            case TASKS_MSG_SUPERVISED:
            case TASKS_MSG_RESOLVED:
                if ($this->data->assignedto) {
                    $to[] = $this->data->supervisor;
                }
            break;
        }

        foreach ($to as $user) {
            $eventdata = new \core\message\message();
            $eventdata->modulename       = 'tasks';
            $eventdata->userfrom         = $USER;
            $eventdata->userto           = $user;
            $eventdata->subject          = $subject;
            $eventdata->fullmessage      = format_text_email($msg, FORMAT_HTML);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml  = $msg;
            $eventdata->smallmessage     = '';
            $eventdata->contexturl       = new \moodle_url('/mod/tasks/detail.php', array('id' => $this->data->id));
            $eventdata->contexturlname   = $this->data->name;
            $eventdata->courseid         = $this->tasks->course;

            // Required for messaging framework
            $eventdata->component = 'mod_tasks';
            $eventdata->name = $type;

            message_send($eventdata);
        }

    }
}
