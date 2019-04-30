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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_tasks\util;

defined('MOODLE_INTERNAL') || die();


class datapanel {

    /**
     * @var string Value to use for the id attribute of the panel
     */
    public $id = null;

    /**
     * @var array Attributes of HTML attributes for the main <div> element
     */
    public $attributes = array();

    /**
     * @var string Panel title
     *
     * Example of usage:
     * $t->head = 'Course 1';
     */
    public $head;

    /**
     * @var array|string Array of items to print like a <ul> list or string with content
     *
     * Example of usage with array:
     * $t->data = array('Course'=>'Course 1', 'Grade'=>98);
     *
     * Example with string
     * $t->data = 'The grade for Course 1 is 98';
     */
    public $data;

    /**
     * @var string Panel footer
     */
    public $foot;


    /**
     * @var string Type of panel: default, primary, success, info, warning, danger
     */
    public $type = 'default';

    /**
     * Constructor
     */
    public function __construct() {
        $this->attributes['class'] = 'mod_tasks_datapanel';
    }

    public function get_content () {

        $html = '';

        $typeclass = 'tasks-panel-' . $this->type;

        if (!isset($this->attributes['id']) && !empty($this->id)) {
            $this->attributes['id'] = $this->id;
        }

        $html .= \html_writer::start_div('tasks-panel ' . $typeclass, $this->attributes);

        if (!empty($this->head)) {
            $html .= \html_writer::div($this->head, 'tasks-panel-heading');
        }

        if (is_array($this->data)) {
            $html .= \html_writer::start_tag('ul', array('class'=>'tasks-list-group'));

            foreach($this->data as $key=>$content) {
                $html .= \html_writer::start_tag('li', array('class'=>'tasks-list-group-item'));

                if (!is_numeric($key)) {
                    $html .= \html_writer::tag('label', $key, array('class'=>'label label-primary'));
                }

                $html .= \html_writer::tag('span', $content);

                $html .= \html_writer::end_tag('li');
            }

            $html .= \html_writer::end_tag('ul');
        }
        else {
            $html .= \html_writer::div($this->data, 'tasks-panel-body');
        }

        if (!empty($this->foot)) {
            $html .= \html_writer::div($this->foot, 'tasks-panel-footer');
        }

        $html .= \html_writer::end_div();

        return $html;
    }
}

