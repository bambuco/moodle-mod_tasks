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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**#@+
 * Option controlling the types of notifications
 */
define('TASKS_NOTITYPE_NONE', 0);
define('TASKS_NOTITYPE_BEFORE', 10);
define('TASKS_NOTITYPE_DAILY', 20);
/**#@-*/

/**#@+
 * Display options
 */
define('TASKS_PANEL_ITEMS', 5);
/**#@-*/

/**#@+
 * Anonymous options
 */
define('TASKS_ANONYMOUS', 1);
define('TASKS_NONANONYMOUS', 2);
/**#@-*/

/**#@+
 * Tasks mode
 */
define('TASKS_MODE_ISSUES', 1);
define('TASKS_MODE_WORK', 2);
/**#@-*/

/**#@+
 * Issues states
 */
define('TASKS_STATE_OPEN', 'open');
define('TASKS_STATE_ASSIGNED', 'assigned');
define('TASKS_STATE_RESOLVED', 'resolved');
define('TASKS_STATE_CLOSED', 'closed');
define('TASKS_STATE_CANCELED', 'canceled');
/**#@-*/

/**#@+
 * Tasks log types
 */
define('TASKS_LOG_EDIT', 'edit');
define('TASKS_LOG_ASSIGN', 'assigned');
define('TASKS_LOG_SUPERVISED', 'supervised');
define('TASKS_LOG_STATE', 'state');
define('TASKS_LOG_COMMENT', 'comment');
/**#@-*/

/**#@+
 * Tasks log types
 */
define('TASKS_MSG_CREATED', 'created');
define('TASKS_MSG_EDITED', 'edited');
define('TASKS_MSG_REMINDER', 'reminder');
define('TASKS_MSG_RESOLVED', 'resolved');
define('TASKS_MSG_CLOSED', 'closed');
define('TASKS_MSG_CANCELED', 'canceled');
define('TASKS_MSG_ASSIGNED', 'assigned');
define('TASKS_MSG_SUPERVISED', 'supervised');
/**#@-*/

/**
 * Add tasks instance.
 * @param stdClass $data
 * @param mod_page_mod_form $mform
 * @return int new tasks instance id
 */
function tasks_add_instance($data, $mform = null) {
    global $DB;
    $cmid = $data->coursemodule;

    // Process the options from the form.
    $result = tasks_process_options($data);
    if ($result && is_string($result)) {
        return $result;
    }

    // Try to store it in the database.
    $data->id = $DB->insert_record('tasks', $data);

    return $data->id;
}

/**
 * Update tasks instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function tasks_update_instance($data, $mform) {
    global $CFG, $DB;

    // Process the options from the form.
    $result = tasks_process_options($data);
    if ($result && is_string($result)) {
        return $result;
    }

    // Get the current value, so we can see what changed.
    $oldtasks = $DB->get_record('tasks', array('id' => $data->instance));

    //ToDo: Something

    // Update the database.
    $data->id = $data->instance;
    $DB->update_record('tasks', $data);

    return true;
}

/**
 * Delete tasks instance.
 * @param int $id
 * @return bool true
 */
function tasks_delete_instance($id) {
    global $DB;

    if (!$tasks = $DB->get_record('tasks', array('id'=>$id))) {
        return false;
    }

    $DB->delete_records('tasks_issues_log', array('tasksid' => $tasks->id));
    $DB->delete_records('tasks_issues', array('tasksid' => $tasks->id));
    $DB->delete_records('tasks', array('id' => $tasks->id));

    return true;
}

/**
 * Indicates API features that the tasks supports.
 *
 * @uses FEATURE_MOD_INTRO
 * @param string $feature
 * @return mixed True if yes (some features may use other values)
 */
function tasks_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Tasks periodic clean-up tasks.
 */
function tasks_cron() {
    global $CFG;

    //ToDo: check the new cron API
    return true;
}


/**
 * Pre-process the tasks options form data, making any necessary adjustments.
 * Called by add/update instance in this file.
 *
 * @param object $tasks The variables set on the form.
 */
function tasks_process_options($tasks) {
    global $CFG;

}

/**
 * Serves the attachments. Implements needed access control ;-)
 *
 * @package  mod_tasks
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function tasks_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    $areas = array(
        'description' => get_string('description', 'mod_tasks'),
    );

    // filearea must contain a real area.
    if (!isset($areas[$filearea])) {
        return false;
    }

    $issueid = (int)array_shift($args);

    if (!$issue = $DB->get_record('tasks_issues', array('id' => $issueid))) {
        return false;
    }

    if (!$tasks = $DB->get_record('tasks', array('id' => $issue->tasksid))) {
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/{$context->id}/mod_tasks/$filearea/$issueid/$relativepath";

    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // Make sure we're allowed to see it...
    if (!has_capability('mod/tasks:viewall', $context) && $issue->reportedby != $USER->id &&
        $issue->assignedto != $USER->id && $issue->supervisor != $USER->id) {
        return false;
    }

    // Finally send the file.
    send_stored_file($file, 0, 0, true, $options); // download MUST be forced - security!
}
