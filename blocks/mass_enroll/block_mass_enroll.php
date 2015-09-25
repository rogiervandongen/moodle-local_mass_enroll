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
 * @copyright   2012 onwards Patrick Pollet
 * @copyright   2015 onwards R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Bulk enrolment block implementation
 *
 * @package     block_mass_enroll
 *
 * @copyright   2012 onwards Patrick Pollet
 * @copyright   2015 onwards R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mass_enroll extends block_list {

    /**
     * initialize plugin
     */
    public function init() {
        $this->title = get_string('mass_enroll', 'local_mass_enroll');
    }

    /**
     * Get applicable formats
     *
     * @return array
     */
    public function applicable_formats() {
        return array('all' => true, 'mod' => false, 'my' => false,
            'tag' => false);
    }

    /**
     * Set specialization
     */
    public function specialization() {
        $this->title = get_string('mass_enroll', 'local_mass_enroll');
    }

    /**
     * Do we allow multiple instances?
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Get (cached) plugin contents
     *
     * @return stdClass
     */
    public function get_content() {
        global $CFG, $OUTPUT;

        if ($this->content !== null) {
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

        // Sera vide donc non affiché si  USER n'a aucune des 2 capacités.
        return $this->content;
    }

}
