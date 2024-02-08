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
 * Strings for component 'tasks', language 'es', branch 'MOODLE_30_STABLE'
 *
 * @package mod_tasks
 * @copyright  2019 David Herney - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['modulename'] = 'Soporte';
$string['modulename_help'] = 'Reportar/gestionar reportes y tareas pendientes.';
$string['modulename_link'] = 'mod/tasks/view';
$string['modulenameplural'] = 'Lista de tareas';
$string['tasks:addinstance'] = 'Adicionar una nueva lista de tareas';
$string['tasks:viewall'] = 'Ver la lista de tareas';
$string['tasks:report'] = 'Hacer un reporte';
$string['tasks:manage'] = 'Gestionar las tareas propias';
$string['tasks:manageall'] = 'Gestionar todas las tareas';
$string['pluginadministration'] = 'Administración del módulo Soporte';
$string['pluginname'] = 'Soporte';
$string['crontasks'] = 'Cron de las tareas de soporte';
$string['controlhdr'] = 'Control';
$string['notificationstype'] = 'Tipo de notificación de los recordatorios';
$string['notify_none'] = 'Nunca';
$string['notify_before'] = 'Solo uno, antes de los días de previsión';
$string['notify_daily'] = 'Diario, durante los días de previsión';
$string['notificationsdays'] = 'Días de previsión';
$string['taskprefix'] = 'Prefijo del reporte';
$string['dashboard'] = 'Tablero';
$string['tabedit'] = 'Editar {$a}';
$string['tabnew'] = 'Crear {$a}';
$string['list'] = 'Lista';
$string['notissues'] = 'Ninguno';
$string['anonymous'] = 'Anónimo';
$string['non_anonymous'] = 'No anónimo';
$string['anonymous_report'] = 'Reporte anónimo';
$string['latestissues'] = 'Últimos';
$string['latestownissues'] = 'Últimos propios';
$string['assignedissues'] = 'Asignados';
$string['expiredissues'] = 'Expirados';
$string['openedissues'] = 'Abiertos';
$string['missingvalue'] = 'Por favor, ingrese un valor.';
$string['eventissueupdated'] = 'Reporte actualizado';
$string['eventissuecreated'] = 'Reporte creado';
$string['eventissueviewed'] = 'Reporte visualizado';
$string['notdetailcapability'] = 'Usted no puede ver este registro';
$string['name'] = 'Nombre';
$string['description'] = 'Descripción';
$string['reportedby'] = 'Reportado por';
$string['unassigned'] = 'No asignado aún';
$string['assignedto'] = 'Asignado a';
$string['timereported'] = 'Reportado el';
$string['timestart'] = 'Inicia el';
$string['timefinish'] = 'Finaliza el';
$string['state'] = 'Estado';
$string['state_open'] = 'Abierto';
$string['state_assigned'] = 'Asignado';
$string['state_resolved'] = 'Resuelto';
$string['state_closed'] = 'Cerrado';
$string['state_canceled'] = 'Cancelado';
$string['notstarted'] = 'No ha iniciado aún';
$string['notfinished'] = 'No ha finalizado aún';
$string['issuecode'] = '#';
$string['assignto'] = 'Asignado a';
$string['assign'] = 'Asignar';
$string['actions'] = 'Operaciones';
$string['mode'] = 'Modo';
$string['mode_issues'] = 'Modo Incidencias';
$string['mode_work'] = 'Modo Tareas';
$string['plurallabel'] = 'Etiqueta en plural';
$string['singularlabel'] = 'Etiqueta en singular';
$string['assignedmsg'] = 'Asignado satisfactoriamente';
$string['comment'] = 'Comentario';
$string['add'] = 'Agregar';
$string['send'] = 'Enviar';
$string['commentedmsg'] = 'Comentario guardado';
$string['editionview'] = '{$a->user} editado el {$a->timelog}';
$string['assignview'] = '{$a->user} cambió la asignación el {$a->timelog}: {$a->old} &rArr; {$a->change}';
$string['stateview'] = '{$a->user} cambió el estado el {$a->timelog}: {$a->old} &rArr; {$a->change}';
$string['commentview'] = '{$a->user} comentó el {$a->timelog}';
$string['old'] = 'Anterior';
$string['changed'] = 'Nuevo';
$string['field'] = 'Campo';
$string['notchanges'] = 'Nada cambió';
$string['statechangedmsg'] = 'El estado se cambió correctamente';
$string['change'] = 'Cambiar';
$string['onlyanonymousreports'] = 'Esta página es solamente para reportes anónimos.';
$string['anonymousreportmsg'] = 'Su reporte fue recibido. Gracias.';
$string['namereportedby'] = 'Su nombre';
$string['emailreportedby'] = 'Su correo';
$string['issuetitle'] = 'Título';
$string['reportagain'] = 'Reportar otro';
$string['security_question'] = 'Pregunta de seguridad';
$string['incorrectpleasetryagain'] = 'Incorrecto. Por favor intente de nuevo.';
$string['supervisor'] = 'Supervisor';
$string['supervisedmsg'] = 'Supervisor asignado con éxito';
$string['supervisedview'] = '{$a->user} cambió al supervisor el {$a->timelog}: {$a->old} &rArr; {$a->change}';
$string['noteditcapability'] = 'No puede editar este registro';
$string['finishbeforestart'] = 'La fecha de inicio no puede ser después de la de finalización';
$string['notsupervised'] = 'Sin supervisión';
$string['msg_created'] = '{$a->label} <strong>{$a->code}</strong> creada con éxito. Ver detalles {$a->url}.';
$string['msg_edited'] = '{$a->label} <strong>{$a->code}</strong> fue editada. Ver detalles {$a->url}.';
$string['msg_reminder'] = 'Éste es un recordatorio acerca de la {$a->label} asignada <strong>{$a->code}</strong>. Ver detalles {$a->url}.';
$string['msg_resolved'] = '{$a->label} <strong>{$a->code}</strong> fue resuelta. Ver detalles {$a->url}.';
$string['msg_closed'] = '{$a->label} <strong>{$a->code}</strong> ha sido cerrada. Ver detalles {$a->url}.';
$string['msg_canceled'] = '{$a->label} <strong>{$a->code}</strong> fue cancelada. Ver detalles {$a->url}.';
$string['msg_assigned'] = 'Usted fue asignado a la {$a->label} <strong>{$a->code}</strong>. Ver detalles {$a->url}.';
$string['msg_supervised'] = 'Usted fue asignado como supervisor a la {$a->label} <strong>{$a->code}</strong>.
Ver detalles {$a->url}.';
$string['subject_created'] = '{$a->label} {$a->code} creada';
$string['subject_edited'] = '{$a->label} {$a->code} fue modificada';
$string['subject_reminder'] = 'Recordatorio acerca de {$a->label} {$a->code}';
$string['subject_resolved'] = '{$a->label} {$a->code} resuelta';
$string['subject_closed'] = '{$a->label} {$a->code} cerrada';
$string['subject_canceled'] = '{$a->label} {$a->code} cancelada';
$string['subject_assigned'] = 'Asignado a {$a->label} {$a->code}';
$string['subject_supervised'] = 'Supervisor de {$a->label} {$a->code} ';
$string['messageprovider:created'] = 'Notificación cuando haga un reporte';
$string['messageprovider:edited'] = 'Mensaje cuando un reporte seguido es editado';
$string['messageprovider:reminder'] = 'Recordatorio acerca de reportes asignados';
$string['messageprovider:resolved'] = 'Mensaje cuando un reporte supervisado sea resuelto';
$string['messageprovider:closed'] = 'Mensaje cuando un reporte seguido sea cerrado';
$string['messageprovider:canceled'] = 'Mensaje cuando un reporte seguido sea cancelado';
$string['messageprovider:assigned'] = 'Notificación cuando sea asignado a un reporte';
$string['messageprovider:supervised'] = 'Notificación cuando sea asignado como supervisor a un reporte';
$string['anonymouslinktext'] = 'Hacer un reporte anónimamente';
$string['tofilter'] = 'Filtrar';
$string['issues'] = 'Reportes';
