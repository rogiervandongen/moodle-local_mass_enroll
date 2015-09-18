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
 * A bulk enrolment plugin that allow teachers to massively enrol existing accounts to their courses,
 * with an option of adding every user to a group
 * Version for Moodle 1.9.x courtesy of Patrick POLLET & Valery FREMAUX  France, February 2010
 * Version for Moodle 2.x by pp@patrickpollet.net March 2012
 *
 * File         mass_enroll.php
 * Encoding     UTF-8
 *
 * @package     local_mass_enroll
 *
 * @copyright   1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @copyright   2012 onwards Patrick Pollet {@link mailto:pp@patrickpollet.net
 * @copyright   2015 onwards Rogier van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once ('lib.php');
require_once ('mass_enroll_form.php');

// Get params.
$id = required_param('id', PARAM_INT);
if (!$course = $DB->get_record('course', array('id' => $id))) {
    error("Course is misconfigured");
}

// Security and access check.
require_course_login($course);
$context = context_course::instance($course->id);
require_capability('local/mass_enroll:enrol', $context);

// Start making page.
$PAGE->set_pagelayout('incourse');
$PAGE->set_url('/local/mass_enroll/mass_enroll.php', array('id' => $id));

$strinscriptions = get_string('mass_enroll', 'local_mass_enroll');

$PAGE->set_title($course->fullname . ': ' . $strinscriptions);
$PAGE->set_heading($course->fullname . ': ' . $strinscriptions);

echo $OUTPUT->header();

// Add tabs.
$currenttab = 'mass_enroll';
require('tabs.php');

$mform = new mass_enroll_form($CFG->wwwroot . '/local/mass_enroll/mass_enroll.php', array(
    'course' => $course,
    'context' => $context
        ));

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', array('id' => $id)));
} else if ($data = $mform->get_data(false)) { // no magic quotes
    echo $OUTPUT->heading($strinscriptions);

    $iid = csv_import_reader::get_new_iid('uploaduser');
    $cir = new csv_import_reader($iid, 'uploaduser');

    $content = $mform->get_file_content('attachment');

    $readcount = $cir->load_csv_content($content, $data->encoding, $data->delimiter_name);
    unset($content);

    if ($readcount === false) {
        print_error('csvloaderror', '', $returnurl);
    } else if ($readcount == 0) {
        print_error('csvemptyfile', 'error', $returnurl);
    }

    $result = mass_enroll($cir, $course, $context, $data);

    $cir->close();
    $cir->cleanup(false); // Only currently uploaded CSV file.

    if ($data->mailreport) {
        $a = new stdClass();
        $a->course = $course->fullname;
        $a->report = $result;
        email_to_user($USER, $USER, get_string('mail_enrolment_subject', 'local_mass_enroll', $CFG->wwwroot), get_string('mail_enrolment', 'local_mass_enroll', $a));
        $result .= "\n" . get_string('email_sent', 'local_mass_enroll', $USER->email);
    }

    echo $OUTPUT->box(nl2br($result), 'center');

    echo $OUTPUT->continue_button($PAGE->url); // Back to this page
    echo $OUTPUT->footer($course);
    // Path must be relative to 'module name', here 'course'
    // add_to_log($course->id, 'course', 'enrol', '../local/mass_enroll/mass_enroll.php?id='.$course->id,$strinscriptions);
    // Rev 12/11/2014 : some core function (get_recent_enrolments())  expect the info field of log record to be integer
    // when action field is 'enrol'. This produced fatal SQL errors with PostGres see https://github.com/patrickpollet/moodle_local_mass_enroll/issues/5
    // so we changed action value from 'enrol' to 'massenrol'
    add_to_log($course->id, 'course', 'massenrol', '../local/mass_enroll/mass_enroll.php?id=' . $course->id, $strinscriptions);

    die();
}

echo $OUTPUT->heading_with_help($strinscriptions, 'mass_enroll', 'local_mass_enroll', 'icon', get_string('mass_enroll', 'local_mass_enroll'));
echo $OUTPUT->box(get_string('mass_enroll_info', 'local_mass_enroll'), 'center');
$mform->display();
echo $OUTPUT->footer($course);
