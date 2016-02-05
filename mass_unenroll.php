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
 * Mass unenrol
 *
 * File         mass_unenroll.php
 * Encoding     UTF-8
 *
 * @package     local_mass_enroll
 *
 * @copyright   1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @copyright   2012 onwards Patrick Pollet
 * @copyright   2015 onwards R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(dirname(__FILE__))) . '/config.php');

// Get params.
$id = required_param('id', PARAM_INT);
if (!$course = $DB->get_record('course', array('id' => $id))) {
    error("Course is misconfigured");
}

// Security and access check.
require_course_login($course);
$context = context_course::instance($course->id);
require_capability('local/mass_enroll:unenrol', $context);

// Start making page.
$strinscriptions = get_string('mass_unenroll', 'local_mass_enroll');
$PAGE->set_pagelayout('incourse');
$PAGE->set_url(new moodle_url($CFG->wwwroot . '/local/mass_enroll/mass_unenroll.php', array('id' => $id)));
$PAGE->set_title($course->fullname . ': ' . $strinscriptions);
$PAGE->set_heading($course->fullname . ': ' . $strinscriptions);

$renderer = $PAGE->get_renderer('local_mass_enroll');
echo $renderer->page_mass_unenrol();
exit;