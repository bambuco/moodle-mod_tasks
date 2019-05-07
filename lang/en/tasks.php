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
 * Strings for component 'tasks', language 'en', branch 'MOODLE_30_STABLE'
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['modulename'] = 'Tasks';
$string['modulename_help'] = 'Report/manage issues and tasks.';
$string['modulename_link'] = 'mod/tasks/view';
$string['modulenameplural'] = 'List of tasks';
$string['tasks:addinstance'] = 'Add a new tasks list';
$string['tasks:viewall'] = 'View the tasks list';
$string['tasks:report'] = 'Report a issue';
$string['tasks:manage'] = 'Manage own tasks';
$string['tasks:manageall'] = 'Manage the tasks list';
$string['pluginadministration'] = 'Tasks module administration';
$string['pluginname'] = 'Tasks';
$string['crontasks'] = 'Tasks cron';
$string['controlhdr'] = 'Control';
$string['notificationstype'] = 'Reminder notifications type';
$string['notify_none'] = 'Never';
$string['notify_before'] = 'Only one, before the prevision days';
$string['notify_daily'] = 'Daily, during the prevision days';
$string['notificationsdays'] = 'Prevision days';
$string['taskprefix'] = 'Issue prefix';
$string['dashboard'] = 'Dashboard';
$string['tabedit'] = 'Edit {$a}';
$string['tabnew'] = 'New {$a}';
$string['list'] = 'List';
$string['notissues'] = 'Nothing';
$string['anonymous'] = 'Anonymous';
$string['non_anonymous'] = 'Non anonymous';
$string['anonymous_report'] = 'Anonymous report';
$string['latestissues'] = 'Latest';
$string['latestownissues'] = 'Latest own';
$string['assignedissues'] = 'Assigned';
$string['expiredissues'] = 'Expired';
$string['openedissues'] = 'Opened';
$string['missingvalue'] = 'Please enter a value.';
$string['eventissueupdated'] = 'Issue was updated';
$string['eventissuecreated'] = 'Issue was created';
$string['eventissueviewed'] = 'Issue viewed';
$string['notdetailcapability'] = 'You can\'t view this record';
$string['name'] = 'Name';
$string['description'] = 'Description';
$string['reportedby'] = 'Reported by';
$string['unassigned'] = 'Not assigned yet';
$string['assignedto'] = 'Assigned to';
$string['timereported'] = 'Reported at';
$string['timestart'] = 'Start at';
$string['timefinish'] = 'Finish at';
$string['state'] = 'State';
$string['state_open'] = 'Open';
$string['state_assigned'] = 'Assigned';
$string['state_resolved'] = 'Resolved';
$string['state_closed'] = 'Closed';
$string['state_canceled'] = 'Canceled';
$string['notstarted'] = 'Not started yet';
$string['notfinished'] = 'Not finished yet';
$string['issuecode'] = '#';
$string['assignto'] = 'Assign to';
$string['assign'] = 'Assign';
$string['actions'] = 'Operaciones';
$string['mode'] = 'Mode';
$string['mode_issues'] = 'Issues mode';
$string['mode_work'] = 'Work mode';
$string['plurallabel'] = 'Plural label';
$string['singularlabel'] = 'Singular label';
$string['assignedmsg'] = 'Assigned success';
$string['comment'] = 'Comment';
$string['add'] = 'Add';
$string['send'] = 'Send';
$string['commentedmsg'] = 'Comment saved success';
$string['editionview'] = '{$a->user} edited on {$a->timelog}';
$string['assignview'] = '{$a->user} changed the assign on {$a->timelog}: {$a->old} &rArr; {$a->change}';
$string['stateview'] = '{$a->user} changed the state on {$a->timelog}: {$a->old} &rArr; {$a->change}';
$string['commentview'] = '{$a->user} commented on {$a->timelog}';
$string['old'] = 'Old';
$string['changed'] = 'New';
$string['field'] = 'Field';
$string['notchanges'] = 'Nothing changed';
$string['statechangedmsg'] = 'State changed success';
$string['change'] = 'Change';
$string['onlyanonymousreports'] = 'This page is only for anonymous reports.';
$string['anonymousreportmsg'] = 'You report was received. Thanks for it.';
$string['namereportedby'] = 'Your name';
$string['emailreportedby'] = 'Your email';
$string['issuetitle'] = 'Title';
$string['reportagain'] = 'Report other';
$string['security_question'] = 'Security question';
$string['incorrectpleasetryagain'] = 'Incorrect. Please try again.';
$string['supervisor'] = 'Supervisor';
$string['supervisedmsg'] = 'Supervisor assigned success';
$string['supervisedview'] = '{$a->user} changed the supervisor on {$a->timelog}: {$a->old} &rArr; {$a->change}';
$string['noteditcapability'] = 'You can\'t edit this record';
$string['finishbeforestart'] = 'Start time can\'t be after to finish time';
$string['notsupervised'] = 'Not supervised';
$string['msg_created'] = 'The {$a->label} <strong>{$a->code}</strong> was created successfully. View details {$a->url}.';
$string['msg_edited'] = 'The {$a->label} <strong>{$a->code}</strong> was edited. View details {$a->url}.';
$string['msg_reminder'] = 'It is a reminder about the assigned {$a->label} <strong>{$a->code}</strong>. View details {$a->url}.';
$string['msg_resolved'] = 'The {$a->label} <strong>{$a->code}</strong> was resolved. View details {$a->url}.';
$string['msg_closed'] = 'The {$a->label} <strong>{$a->code}</strong> was closed. View details {$a->url}.';
$string['msg_canceled'] = 'The {$a->label} <strong>{$a->code}</strong> was canceled. View details {$a->url}.';
$string['msg_assigned'] = 'You was assigned to the {$a->label} <strong>{$a->code}</strong>. View details {$a->url}.';
$string['msg_supervised'] = 'You was assigned as the supervisor to the {$a->label} <strong>{$a->code}</strong>.
View details {$a->url}.';
$string['subject_created'] = 'The {$a->label} {$a->code} was created';
$string['subject_edited'] = 'The {$a->label} {$a->code} was edited';
$string['subject_reminder'] = 'Reminder about the {$a->label} {$a->code}';
$string['subject_resolved'] = 'The {$a->label} {$a->code} was resolved';
$string['subject_closed'] = 'The {$a->label} {$a->code} was closed';
$string['subject_canceled'] = 'The {$a->label} {$a->code} was canceled';
$string['subject_assigned'] = 'Assigned to the {$a->label} {$a->code}';
$string['subject_supervised'] = 'Supervisor to the {$a->label} {$a->code} ';
$string['messageprovider:created'] = 'Notification when report a issue';
$string['messageprovider:edited'] = 'Message when a followed issue is edited';
$string['messageprovider:reminder'] = 'Reminder about assigned issues';
$string['messageprovider:resolved'] = 'Message when a supervised issue is resolved';
$string['messageprovider:closed'] = 'Message when a followed issue is closed';
$string['messageprovider:canceled'] = 'Message when a followed issue is canceled';
$string['messageprovider:assigned'] = 'Notification when is assigned to an issue';
$string['messageprovider:supervised'] = 'Notification when is assigned like supervisor to an issue';
$string['anonymouslinktext'] = 'Report an issue anonymously';
$string['tofilter'] = 'Filter';
$string['issues'] = 'Issues';
$string[''] = '';

