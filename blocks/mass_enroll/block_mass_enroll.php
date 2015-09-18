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
 * Bulk enrolment block implementation
 *
 * File         mass_enroll.php
 * Encoding     UTF-8
 *
 * @package     local_mass_enroll
 *
 * @copyright   2012 onwards Patrick Pollet {@link mailto:pp@patrickpollet.net
 * @copyright   2015 onwards Rogier van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Bulk enrolment block implementation
 *
 * @package     local_mass_enroll
 *
 * @copyright   2012 onwards Patrick Pollet {@link mailto:pp@patrickpollet.net
 * @copyright   2015 onwards Rogier van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mass_enroll extends block_list {

    function init() {
        $this->title = get_string('mass_enroll', 'local_mass_enroll');
    }

    function applicable_formats() {
        // return array('site' => false, 'course-view' => true, 'my'=>false);
        return array('all' => true, 'mod' => false, 'my' => false,
            'tag' => false);
    }

    function specialization() {
        $this->title = get_string('mass_enroll', 'local_mass_enroll');
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $CFG, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (empty($this->instance) || empty($CFG->allow_mass_enroll_feature)) {
            return $this->content;
        }
        $course = $this->page->course;
        $coursecontext = $this->page->context;

        $icon = '<img src="' . $OUTPUT->pix_url('i/admin') . '"' .
                ' class="icon" alt="" />&nbsp;';

        if (has_capability('local/mass_enroll:enrol', $coursecontext)) {
            $this->content->items[] = '<a title=""
                        href="' . $CFG->wwwroot . '/local/mass_enroll/mass_enroll.php?id=' . $course->id . '">' .
                    $icon . get_string('mass_enroll', 'local_mass_enroll') . '</a>';
        }
        if (has_capability('local/mass_enroll:unenrol', $coursecontext)) {
            $this->content->items[] = '<a title=""
                        href="' . $CFG->wwwroot . '/local/mass_enroll/mass_unenroll.php?id=' . $course->id . '">' .
                    $icon . get_string('mass_unenroll', 'local_mass_enroll') . '</a>';
        }

        // sera vide donc non affiché si  USER n'a aucune des 2 capacités
        return $this->content;
    }

}
